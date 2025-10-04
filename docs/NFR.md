# Non-Functional Requirements (NFRs)
## MinSU Bongabong Information System

**Document Version:** 1.0  
**Date:** October 4, 2025  
**Full Name:** Mindoro State University - Bongabong Campus Information System  
**Priority Framework:** MUST HAVE (P0), SHOULD HAVE (P1), NICE TO HAVE (P2)

> **Note:** All domain names (minsubongabong.edu.ph, api.minsubongabong.edu.ph) used in this document are placeholders for demonstration purposes. Actual production domains will be provided by Mindoro State University IT Services.

---

## Overview

Non-functional requirements define **how well** the system performs its functions, as opposed to functional requirements which define **what** the system does. These requirements ensure the system is usable, secure, performant, and maintainable in production.

---

## 1. Performance Requirements

### 1.1 Response Time

**Priority:** P0 (Critical)

| Metric | Target | Measurement Method |
|--------|--------|-------------------|
| **Page Load Time (Desktop)** | <2 seconds for 95th percentile | Google Lighthouse, WebPageTest |
| **Page Load Time (Mobile)** | <3 seconds for 95th percentile | Google Lighthouse Mobile |
| **API Response Time** | <200ms for 95th percentile | Laravel Telescope, APM tools |
| **Database Query Time** | <100ms for 95th percentile | MySQL slow query log |
| **PDF Generation** | <10 seconds for transcript | Background job monitoring |
| **Image Upload** | <3 seconds for 5MB file | Frontend timing API |

**Testing Methodology:**
```bash
# Load testing with k6
k6 run --vus 50 --duration 30s load-test.js

# Example k6 script
import http from 'k6/http';
import { check } from 'k6';

export default function () {
  const res = http.get('https://api.minsubongabong.edu.ph/v1/usg/calendar');
  check(res, {
    'status is 200': (r) => r.status === 200,
    'response time < 200ms': (r) => r.timings.duration < 200,
  });
}
```

**Acceptance Criteria:**
- 95% of page loads complete within target time
- No endpoint averages >500ms response time
- Background jobs (PDF generation) complete within SLA

---

### 1.2 Throughput & Concurrency

**Priority:** P0

| Metric | Target | Testing Method |
|--------|--------|----------------|
| **Concurrent Users** | 500 simultaneous users without degradation | Load testing |
| **Requests per Second** | 100 RPS sustained | Apache Bench, k6 |
| **Database Connections** | Max 100 concurrent connections | MySQL `SHOW PROCESSLIST` |
| **Peak Load Handling** | 1000 users during enrollment peaks | Stress testing |

**Peak Load Scenarios:**
- Scholarship application deadline (day before): 800-1000 concurrent users
- Document request surge (graduation season): 600-800 concurrent users
- USG announcement release: 500-700 concurrent viewers

**Scaling Strategy:**
- Vertical: Upgrade VPS to 4 vCPU, 8GB RAM if sustained >300 concurrent users
- Horizontal: Load balancer + 2 app servers if sustained >500 concurrent users
- Database: Read replicas if query load >1000 queries/second

**Testing Command:**
```bash
# Apache Bench stress test
ab -n 10000 -c 100 https://minsubongabong.edu.ph/usg/calendar

# Expected Output:
# Requests per second: 120 [#/sec] (mean)
# Time per request: 8.333 [ms] (mean, across all concurrent requests)
# 95th percentile: <200ms
```

---

### 1.3 Resource Utilization

**Priority:** P1

| Resource | Target | Monitoring |
|----------|--------|-----------|
| **Server CPU** | <70% average, <90% peak | `htop`, Laravel Horizon dashboard |
| **Server Memory** | <75% average, <85% peak | `free -m`, server monitoring |
| **Database CPU** | <60% average | MySQL `SHOW STATUS` |
| **Disk I/O** | <80% utilization | `iostat`, `iotop` |
| **Network Bandwidth** | <50% of available (100 Mbps expected) | `iftop`, `vnstat` |

**Optimization Strategies:**
- **Caching**: Redis for session, query results (50% cache hit rate target)
- **Image CDN**: Cloudinary for static assets (reduces server load 70%)
- **Database Indexing**: All FK columns, status fields, date ranges
- **Query Optimization**: N+1 prevention, eager loading, pagination

---

## 2. Scalability Requirements

### 2.1 Data Volume Growth

**Priority:** P1

| Data Type | Year 1 Estimate | Year 3 Estimate | Storage Strategy |
|-----------|-----------------|-----------------|------------------|
| **Users** | 5,000 students + 50 staff | 7,500 students + 60 staff | Hot database |
| **Scholarship Applications** | 2,000/semester = 4,000/year | 6,000/semester = 12,000/year | Archive after 2 years |
| **Document Requests** | 8,000/year | 15,000/year | Archive after 1 year |
| **Events** | 200/year | 400/year | Keep all (historical) |
| **File Storage (PDFs/Images)** | 50 GB | 200 GB | Cloudinary, lifecycle to cold storage |

**Database Growth Handling:**
- **Partitioning**: Partition `sas_scholarships` by academic_year after 50k rows
- **Archiving**: Move old records to `_archive` tables quarterly
- **Purging**: Soft-delete expired data (PDFs >1 year old, logs >6 months)

**Acceptance Criteria:**
- Database grows <20 GB/year
- Query performance maintained with 5x data growth
- Disk space alerts at 80% capacity

---

### 2.2 Feature Expansion

**Priority:** P2

The system architecture must support future features without major refactoring:

**Planned Features (Year 2-3):**
- Mobile native apps (iOS/Android) via same API
- SMS notifications (integrate with Semaphore API)
- Advanced analytics dashboard (Chart.js, export to Excel)
- Multiple campus support (add `campus_id` foreign key to all modules)
- Integration with university LMS (Moodle/Canvas API)

**Architectural Requirements:**
- **API-First Design**: All features accessible via REST API (enables mobile apps)
- **Modular Code**: New modules added without modifying existing code
- **Database Extensibility**: Use JSON columns for flexible metadata (e.g., `metadata JSON` in transactions)
- **Configuration Over Code**: Feature flags in `config/features.php`

---

## 3. Availability & Reliability

### 3.1 Uptime Requirements

**Priority:** P0

| Metric | Target | Downtime Allowed | Monitoring |
|--------|--------|------------------|------------|
| **System Availability** | 99.5% uptime/month | ~3.6 hours/month | UptimeRobot, Pingdom |
| **Planned Maintenance** | Max 4 hours/month, scheduled | Weekends 12-4 AM | Maintenance mode page |
| **Database Availability** | 99.9% uptime | ~43 minutes/month | MySQL replication monitoring |

**Unplanned Downtime Scenarios:**
- **Server crash**: Auto-restart via systemd, restore within 15 minutes
- **Database failure**: Restore from latest backup (max 24-hour data loss acceptable)
- **Network outage**: ISP redundancy (primary + fallback), <2 hour recovery

**Acceptance Criteria:**
- Zero critical outages during peak periods (enrollment, graduation)
- Maintenance communicated 48 hours in advance
- Status page available during outages: `status.minsubongabong.edu.ph`

---

### 3.2 Disaster Recovery

**Priority:** P0

| Component | Backup Frequency | Recovery Time Objective (RTO) | Recovery Point Objective (RPO) |
|-----------|------------------|-------------------------------|--------------------------------|
| **Database** | Daily (full), hourly (incremental) | <4 hours | <1 hour |
| **File Storage (Cloudinary)** | Vendor-managed, 99.9% durability | <1 hour | N/A (CDN cached) |
| **Application Code** | Git version control | <30 minutes (redeploy) | Latest commit |
| **Configuration** | Environment variables in vault | <15 minutes | Latest config |

**Disaster Recovery Plan:**
1. **Database Corruption**: Restore from last good backup (automated script)
2. **Server Failure**: Provision new VPS, restore from backup, update DNS (<4 hours)
3. **Data Center Outage**: Migrate to backup region (requires multi-region setup - Year 2)

**Testing Schedule:**
- Quarterly disaster recovery drill (restore from backup to staging)
- Annual full failover test

---

### 3.3 Data Integrity

**Priority:** P0

| Requirement | Implementation | Validation |
|-------------|----------------|------------|
| **ACID Transactions** | MySQL InnoDB with transactions | Database tests with rollback scenarios |
| **Referential Integrity** | Foreign key constraints enforced | Migration tests |
| **Data Validation** | Laravel Form Requests, server-side validation | Unit tests for validators |
| **Duplicate Prevention** | Unique constraints on business keys | Integration tests |
| **Audit Trail** | Log all critical operations (scholarship approval, payment) | Audit log review |

**Business-Critical Data:**
- **Scholarships**: Cannot lose approved scholarship records (financial liability)
- **Payments**: All payment transactions must be immutable and traceable
- **Transcripts**: Generated transcripts must match student records exactly
- **Resolutions**: Published resolutions cannot be altered (integrity of public record)

**Acceptance Criteria:**
- Zero data corruption incidents in production
- 100% payment reconciliation accuracy (no missing transactions)
- Audit logs retained for 7 years (compliance)

---

## 4. Security Requirements

### 4.1 Authentication & Authorization

**Priority:** P0

| Requirement | Implementation | Standard |
|-------------|----------------|----------|
| **Password Security** | Bcrypt hashing (Laravel default), min 8 chars, 1 uppercase, 1 number, 1 symbol | OWASP Password Guidelines |
| **Session Management** | Secure httpOnly cookies, 2-week expiration with "Remember Me" | OWASP Session Management |
| **Multi-Factor Auth (2FA)** | Optional for admin roles via TOTP (Google Authenticator) | NIST 800-63B |
| **Role-Based Access Control** | Spatie Permissions, principle of least privilege | RBAC Standard |
| **Account Lockout** | 5 failed attempts → 15 minute lockout | OWASP Authentication |

**Implementation:**
```php
// config/auth.php - Session security
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
        'expire_on_close' => false, // Persist session
        'timeout' => 20160, // 2 weeks in minutes
    ],
],

'passwords' => [
    'users' => [
        'min' => 8,
        'mixedCase' => true,
        'numbers' => true,
        'symbols' => true,
    ],
];
```

**Acceptance Criteria:**
- All passwords hashed, never stored plaintext
- Admin accounts require 2FA enrollment
- Session tokens invalidated on logout
- Zero successful brute-force attacks (rate limiting enforced)

---

### 4.2 Data Protection

**Priority:** P0

| Requirement | Implementation | Standard |
|-------------|----------------|----------|
| **Data Encryption at Rest** | Cloudinary encrypted storage for files | AES-256 |
| **Data Encryption in Transit** | HTTPS enforced (TLS 1.3), HSTS headers | TLS Best Practices |
| **PII Protection** | Student records accessible only to authorized users | GDPR-inspired |
| **Payment Data** | Never store full credit card numbers (tokenized via Paymongo) | PCI-DSS Compliance |
| **Database Credentials** | Stored in `.env`, never committed to Git | 12-Factor App |

**Sensitive Data Fields:**
- Student ID, GPA, family income, addresses, contact numbers
- Payment transactions, financial records
- Uploaded documents (birth certificates, IDs)

**Implementation:**
```php
// Force HTTPS in production
// app/Providers/AppServiceProvider.php
if (app()->environment('production')) {
    URL::forceScheme('https');
}

// HSTS Header (middleware)
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
```

**Acceptance Criteria:**
- 100% of traffic over HTTPS (no mixed content warnings)
- Zero PII leaks in logs or error messages
- Payment data never touches our database (tokenized via Paymongo)

---

### 4.3 Input Validation & Injection Prevention

**Priority:** P0

| Attack Vector | Prevention | Testing |
|---------------|------------|---------|
| **SQL Injection** | Eloquent ORM (parameterized queries), never raw SQL with user input | SQLMap scanning |
| **XSS (Cross-Site Scripting)** | Laravel Blade auto-escaping, CSP headers | OWASP ZAP scanning |
| **CSRF (Cross-Site Request Forgery)** | Laravel CSRF tokens on all forms | Manual penetration testing |
| **File Upload Attacks** | Whitelist file types, size limits, malware scanning | Upload malicious file tests |
| **Command Injection** | Never use `exec()`, `shell_exec()` with user input | Code review |

**Implementation:**
```php
// Laravel Form Request Validation
public function rules()
{
    return [
        'email' => 'required|email|max:255',
        'student_id' => 'required|regex:/^\d{4}-\d{5}$/',
        'document' => 'required|file|mimes:pdf,jpg,png|max:5120', // 5MB max
    ];
}

// CSP Header (Content Security Policy)
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net; img-src 'self' data: https:");
```

**Acceptance Criteria:**
- All forms protected by CSRF token
- All database queries use ORM (no raw SQL with user input)
- File uploads validated by type and scanned for malware
- Zero successful injection attacks in penetration tests

---

### 4.4 API Security

**Priority:** P0

| Requirement | Implementation | Standard |
|-------------|----------------|----------|
| **Authentication** | Laravel Sanctum bearer tokens | OAuth 2.0 inspired |
| **Rate Limiting** | 60 req/min per user, 300 req/min per IP | API Best Practices |
| **CORS (Cross-Origin)** | Whitelist only approved domains | CORS Standard |
| **Webhook Verification** | HMAC signature validation (Paymongo) | Webhook Security |
| **API Versioning** | `/v1/` in URL, deprecation notices 6 months prior | Semantic Versioning |

**Implementation:**
```php
// Rate Limiting (routes/api.php)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/sas/scholarships/applications', [ScholarshipController::class, 'index']);
});

// CORS (config/cors.php)
'allowed_origins' => [
    'https://minsubongabong.edu.ph',
    'https://www.minsubongabong.edu.ph',
],
```

**Acceptance Criteria:**
- Rate limiting enforced (429 status after limit)
- Webhook signatures validated (reject invalid signatures)
- CORS errors on unauthorized domains

---

## 5. Usability Requirements

### 5.1 User Interface

**Priority:** P0

| Requirement | Target | Testing Method |
|-------------|--------|----------------|
| **Learnability** | New users complete first task within 5 minutes | User testing with 10 students |
| **Efficiency** | Experienced users complete common tasks in <3 clicks | Task analysis |
| **Error Tolerance** | Clear error messages, no data loss on errors | Usability testing |
| **Consistency** | Uniform UI patterns across modules | Design system review |
| **Feedback** | Loading indicators for all async operations | Manual testing |

**UI Principles:**
- **Progressive Disclosure**: Show only relevant information, hide complexity
- **Affordances**: Buttons look clickable, links underlined, forms clearly labeled
- **Feedback**: Success/error messages for all actions, loading states visible
- **Consistency**: Same color scheme, typography, button styles across all pages

**Example Success Messages:**
- ✅ "Scholarship application submitted successfully! Reference: SA-2025-0001"
- ✅ "Payment received. Your document will be ready within 24 hours."

**Example Error Messages:**
- ❌ "Please upload your birth certificate (PDF or image, max 5MB)"
- ❌ "Cannot request transcript due to outstanding library fees. Clear obligations first."

**Acceptance Criteria:**
- 90% of users complete registration without assistance
- <5% support requests due to confusing UI
- Error messages tested with non-technical users for clarity

---

### 5.2 Accessibility

**Priority:** P0

| Standard | Requirement | Testing |
|----------|-------------|---------|
| **WCAG 2.1 Level AA** | Minimum compliance | Automated scan (axe, WAVE) |
| **Keyboard Navigation** | All features accessible without mouse | Manual keyboard-only testing |
| **Screen Reader** | ARIA labels, semantic HTML | NVDA/JAWS testing |
| **Color Contrast** | 4.5:1 for normal text, 3:1 for large text | Contrast checker tools |
| **Font Size** | Minimum 16px, scalable to 200% | Browser zoom testing |

**Implementation:**
```html
<!-- Semantic HTML -->
<main role="main">
  <h1>Scholarship Application</h1>
  <form aria-labelledby="scholarship-form">
    <label for="scholarship-type">Scholarship Type</label>
    <select id="scholarship-type" aria-required="true">
      <option value="TES">TES</option>
      <option value="TDP">TDP</option>
    </select>
    
    <button type="submit" aria-label="Submit scholarship application">
      Submit Application
    </button>
  </form>
</main>

<!-- Skip to content link for keyboard users -->
<a href="#main-content" class="sr-only">Skip to main content</a>
```

**Color Palette (AA Compliant):**
- Primary: `#2563EB` (Blue 600) - contrast ratio 4.7:1 on white
- Success: `#10B981` (Green 600) - contrast ratio 4.8:1 on white
- Error: `#DC2626` (Red 600) - contrast ratio 5.1:1 on white
- Text: `#1F2937` (Gray 800) - contrast ratio 12:1 on white

**Acceptance Criteria:**
- Zero critical accessibility violations (axe scan)
- All interactive elements keyboard-navigable
- Screen reader announces page changes

---

### 5.3 Mobile Responsiveness

**Priority:** P0

| Requirement | Target | Testing |
|-------------|--------|---------|
| **Mobile Traffic** | >60% of users on mobile devices | Analytics |
| **Touch Targets** | Minimum 48x48px (WCAG guideline) | Manual testing |
| **Viewport Sizes** | Support 320px to 2560px width | Responsive testing |
| **Mobile Performance** | Lighthouse Mobile score >70 | Google Lighthouse |
| **Offline Graceful Degradation** | Show error message if offline | Network throttling test |

**Breakpoints (Tailwind CSS):**
```css
/* Mobile-first approach */
sm: 640px   /* Large phones */
md: 768px   /* Tablets */
lg: 1024px  /* Laptops */
xl: 1280px  /* Desktops */
2xl: 1536px /* Large desktops */
```

**Mobile Optimizations:**
- **Forms**: Large input fields (min 16px font to prevent zoom on iOS)
- **Navigation**: Hamburger menu on mobile, full navbar on desktop
- **Tables**: Horizontal scroll or card layout on mobile
- **Images**: Responsive images (srcset) for bandwidth optimization

**Acceptance Criteria:**
- All pages usable on iPhone SE (375x667) and larger
- Touch targets meet 48x48px minimum
- Forms submit successfully on mobile browsers

---

## 6. Maintainability Requirements

### 6.1 Code Quality

**Priority:** P1

| Metric | Target | Tool |
|--------|--------|------|
| **Test Coverage** | >70% for critical paths | PHPUnit/Pest coverage |
| **Code Duplication** | <5% duplicate code | PHP Copy/Paste Detector |
| **Cyclomatic Complexity** | <10 per method | PHPMetrics |
| **Code Style Compliance** | 100% Laravel Pint pass | Laravel Pint |
| **Type Safety** | PHP 8.2 type hints on all methods | PHPStan level 5 |

**Code Standards:**
- **PSR-12**: PHP coding standard
- **Laravel Conventions**: Eloquent models, Blade templates, service providers
- **Single Responsibility**: Each class/method has one clear purpose
- **DRY (Don't Repeat Yourself)**: Extract reusable logic into services

**Example:**
```php
// Good: Single Responsibility
class ScholarshipApprovalService
{
    public function approve(Scholarship $scholarship, User $approver, float $amount): void
    {
        $this->validateApproval($scholarship);
        $scholarship->approve($approver, $amount);
        $this->notifyStudent($scholarship);
    }
}

// Bad: God class with multiple responsibilities (avoid)
class ScholarshipManager
{
    public function doEverything() { /* 500 lines of mixed logic */ }
}
```

**Acceptance Criteria:**
- All new code passes Laravel Pint (`vendor/bin/pint --test`)
- Critical modules (scholarships, payments) have >80% test coverage
- No methods exceed 50 lines

---

### 6.2 Documentation

**Priority:** P1

| Document Type | Requirement | Location |
|---------------|-------------|----------|
| **README** | Setup instructions, architecture overview | `README.md` |
| **API Documentation** | All endpoints documented with examples | `docs/API_SPECIFICATIONS.md` |
| **Code Comments** | Complex business logic explained | Inline comments |
| **Database Schema** | ERD and table descriptions | `docs/DATA_MODELS.md` |
| **User Guides** | Step-by-step for students and staff | `docs/USER_GUIDE.md` |
| **Deployment Guide** | Server setup, environment config | `docs/DEPLOYMENT.md` |

**Documentation Standards:**
- **Code Comments**: Explain *why*, not *what* (code explains itself)
- **PHPDoc Blocks**: On all public methods with `@param` and `@return`
- **README Updates**: Update within same PR that changes setup process
- **Changelog**: Track all breaking changes and deprecations

**Example PHPDoc:**
```php
/**
 * Approve a scholarship application and notify the student.
 *
 * @param Scholarship $scholarship The scholarship to approve
 * @param User $approver The staff member approving
 * @param float $amount The approved scholarship amount
 * @return void
 * @throws \Exception If scholarship is not in approvable state
 */
public function approve(Scholarship $scholarship, User $approver, float $amount): void
{
    // Implementation
}
```

**Acceptance Criteria:**
- All public API endpoints documented with request/response examples
- New developers can set up local environment in <30 minutes using README
- Complex business rules explained in comments

---

### 6.3 Logging & Monitoring

**Priority:** P0

| Log Level | Use Case | Retention |
|-----------|----------|-----------|
| **ERROR** | Uncaught exceptions, failed payment webhooks | 90 days |
| **WARNING** | Deprecated API usage, slow queries | 30 days |
| **INFO** | User actions (scholarship submitted, payment received) | 60 days |
| **DEBUG** | Detailed request/response (dev only) | 7 days |

**Monitored Metrics:**
- **Application Errors**: Track error rate, alert if >10 errors/hour
- **Response Time**: Alert if p95 >500ms for 5 minutes
- **Queue Depth**: Alert if >100 pending jobs for 10 minutes
- **Disk Space**: Alert at 80% capacity
- **Database Connections**: Alert if >80 active connections

**Tools:**
- **Laravel Logs**: `storage/logs/laravel.log` rotated daily
- **Sentry**: Error tracking with stack traces, user context
- **Laravel Telescope**: Local development debugging
- **UptimeRobot**: Uptime monitoring (ping every 5 minutes)

**Log Format (JSON):**
```json
{
  "timestamp": "2025-10-04T10:30:00+08:00",
  "level": "INFO",
  "message": "Scholarship application approved",
  "context": {
    "scholarship_id": "sa-2025-0001",
    "approved_by": "user-123",
    "amount": 10000.00
  },
  "extra": {
    "ip": "203.0.113.45",
    "user_agent": "Mozilla/5.0..."
  }
}
```

**Acceptance Criteria:**
- All errors logged with stack trace and user context
- Critical errors trigger email alert to admin within 5 minutes
- Logs searchable and filterable by level, user, time range

---

## 7. Compatibility Requirements

### 7.1 Browser Support

**Priority:** P0

| Browser | Minimum Version | Market Share (Target) | Testing Frequency |
|---------|-----------------|----------------------|-------------------|
| **Chrome** | Latest 2 versions | 60% | Every release |
| **Firefox** | Latest 2 versions | 15% | Every release |
| **Safari** | Latest 2 versions | 10% | Every release |
| **Edge** | Latest 2 versions | 10% | Every release |
| **Mobile Safari (iOS)** | iOS 14+ | 30% | Every release |
| **Chrome Mobile (Android)** | Android 10+ | 40% | Every release |

**Not Supported:**
- Internet Explorer 11 (EOL, <1% market share)
- Opera Mini (limited JavaScript support)

**Testing Strategy:**
- **BrowserStack**: Cross-browser testing (manual)
- **Playwright**: Automated E2E tests on Chrome, Firefox, Safari
- **Real Devices**: Test on actual iPhone and Android devices

**Acceptance Criteria:**
- Core functionality works on all supported browsers
- Layout doesn't break on supported screen sizes
- Polyfills included for modern JS features (Vite auto-handles)

---

### 7.2 Device Support

**Priority:** P0

| Device Category | Support Level | Screen Size Range | Testing |
|-----------------|---------------|-------------------|---------|
| **Mobile Phones** | Full support | 375px - 428px width | iPhone 12, Samsung Galaxy |
| **Tablets** | Full support | 768px - 1024px width | iPad, Android tablet |
| **Laptops** | Full support | 1280px - 1920px width | MacBook, Windows laptop |
| **Desktops** | Full support | 1920px - 3840px width | 1080p, 4K monitors |

**Performance Targets by Device:**
- **Mobile (4G)**: Page load <3s
- **Mobile (3G)**: Page load <5s (graceful degradation)
- **Desktop (Broadband)**: Page load <1.5s

---

### 7.3 Database Compatibility

**Priority:** P1

| Database | Minimum Version | Use Case | Migration Path |
|----------|-----------------|----------|----------------|
| **MySQL** | 8.0+ | Production (primary) | Current setup |
| **SQLite** | 3.35+ | Testing, local dev | Switch `DB_CONNECTION=sqlite` |
| **MariaDB** | 10.6+ | Alternative to MySQL (compatible) | Drop-in replacement |

**Not Supported:**
- PostgreSQL (Laravel supports it, but our migrations are MySQL-specific)
- SQL Server (not a target for this project)

---

## 8. Compliance & Legal Requirements

### 8.1 Data Privacy

**Priority:** P0

| Requirement | Implementation | Standard |
|-------------|----------------|----------|
| **Data Minimization** | Collect only necessary PII | GDPR Principle |
| **User Consent** | Terms of Service checkbox on registration | Privacy by Design |
| **Right to Access** | Students can export their data (JSON/CSV) | GDPR Article 15 |
| **Right to Erasure** | Students can request account deletion (anonymization) | GDPR Article 17 |
| **Data Breach Notification** | Notify users within 72 hours of breach | GDPR Article 33 |

**Implementation:**
```php
// Export user data (GDPR compliance)
public function exportData(User $user)
{
    return response()->json([
        'personal_info' => $user->only(['name', 'email', 'student_id']),
        'scholarships' => $user->scholarshipApplications,
        'document_requests' => $user->documentRequests,
    ])->download("user_data_{$user->id}.json");
}

// Anonymize user (right to erasure)
public function anonymizeUser(User $user)
{
    $user->update([
        'name' => 'Deleted User',
        'email' => "deleted_{$user->id}@anonymized.local",
        'student_id' => null,
    ]);
    // Keep scholarship/payment records for audit (business requirement)
}
```

---

### 8.2 Financial Compliance

**Priority:** P0

| Requirement | Implementation | Standard |
|-------------|----------------|----------|
| **Payment Records** | Immutable transaction logs | Accounting standards |
| **Audit Trail** | All payment actions logged with timestamp, user | SOX compliance |
| **Receipt Generation** | Official receipts with sequential OR numbers | BIR regulations (Philippines) |
| **Refund Policy** | Documented, tracked in system | Consumer protection |

**Acceptance Criteria:**
- All transactions logged with tamper-proof audit trail
- Official receipts downloadable as PDF
- Refunds processed within 5-7 business days

---

### 8.3 Academic Records Integrity

**Priority:** P0

| Requirement | Implementation | Enforcement |
|-------------|----------------|-------------|
| **Transcript Accuracy** | Generated from authoritative registrar database | Registrar staff review |
| **Document Tampering Prevention** | Digital watermarks, signatures | PDF security |
| **Historical Accuracy** | No retroactive grade changes without audit trail | Business rule enforcement |

---

## 9. Deployment & Operations Requirements

### 9.1 Deployment Process

**Priority:** P1

| Requirement | Target | Process |
|-------------|--------|---------|
| **Deployment Frequency** | Weekly updates (non-critical) | Git push to `main` triggers Railway deploy |
| **Deployment Duration** | <5 minutes downtime | Blue-green deployment (future) |
| **Rollback Time** | <10 minutes | Git revert + redeploy |
| **Zero-Downtime Updates** | P2 (nice to have) | Requires load balancer |

**Deployment Checklist:**
- [ ] All tests pass (`php artisan test`)
- [ ] Linter passes (`vendor/bin/pint --test`)
- [ ] Database migrations tested on staging
- [ ] `.env` variables updated (if needed)
- [ ] Cache cleared (`php artisan config:cache`)
- [ ] Queue workers restarted
- [ ] Smoke tests pass (login, submit application, request document)

---

### 9.2 Environment Configuration

**Priority:** P0

| Environment | Purpose | Database | URL |
|-------------|---------|----------|-----|
| **Local** | Developer machines | SQLite | `localhost:8000` |
| **Staging** | Pre-production testing | MySQL (shared) | `staging.minsubongabong.edu.ph` |
| **Production** | Live system | MySQL (dedicated) | `minsubongabong.edu.ph` |

**Environment Parity:**
- Staging mirrors production (same OS, PHP version, database version)
- Sensitive data anonymized in staging (use faker for student names)
- No production credentials in staging

---

## 10. Testing Requirements

### 10.1 Test Coverage Targets

**Priority:** P1

| Test Type | Coverage Target | Tool | Frequency |
|-----------|----------------|------|-----------|
| **Unit Tests** | >70% code coverage | Pest | On every commit |
| **Integration Tests** | All critical workflows | Pest | Daily |
| **E2E Tests** | Top 10 user journeys | Playwright | Pre-release |
| **Load Tests** | Peak load scenarios | k6 | Monthly |
| **Security Tests** | OWASP Top 10 | OWASP ZAP | Quarterly |
| **Accessibility Tests** | WCAG 2.1 AA | axe, manual | Per feature |

**Critical Workflows to Test:**
1. User registration and login
2. Scholarship application submission
3. Scholarship approval by staff
4. Document request with payment
5. Payment webhook processing
6. Document generation and download
7. Event creation and USG sync
8. Organization registration
9. VMGO content publishing
10. Resolution search

---

## Summary: NFR Compliance Checklist

### Pre-Launch Checklist

- [ ] **Performance**: Load test passes with 500 concurrent users, p95 <200ms
- [ ] **Security**: Penetration test completed, no high-severity issues
- [ ] **Accessibility**: WCAG 2.1 AA compliance verified (axe scan clean)
- [ ] **Browser Support**: Tested on Chrome, Firefox, Safari, Edge (desktop + mobile)
- [ ] **Backup**: Automated daily backups configured and tested
- [ ] **Monitoring**: Error tracking (Sentry), uptime monitoring (UptimeRobot) active
- [ ] **Documentation**: README, API docs, user guides complete
- [ ] **Compliance**: Privacy policy published, GDPR rights implemented

### Ongoing Operations

- **Daily**: Monitor error logs, check queue depth
- **Weekly**: Review performance metrics, deploy updates
- **Monthly**: Load testing, review security alerts
- **Quarterly**: Disaster recovery drill, security audit, accessibility review
- **Annually**: Full penetration test, compliance audit

---

**Document Maintenance:**
- Update NFRs when new features impact performance/security
- Review NFR compliance quarterly with operations team
- Adjust targets based on actual usage patterns and growth

