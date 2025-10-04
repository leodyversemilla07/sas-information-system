# Data Models & Database Schema
## MinSU Bongabong Information System

**Document Version:** 1.0  
**Date:** October 4, 2025  
**Full Name:** Mindoro State University - Bongabong Campus Information System  
**Database Engine:** MySQL 8.0+  
**ORM:** Laravel Eloquent

---

## Database Architecture Overview

### Database Naming Conventions

**Database Name:** `minsu_bongabong` (production), `minsu_bongabong_test` (testing)

**Character Set:** UTF-8 (utf8mb4) for full Unicode support including emojis  
**Collation:** utf8mb4_unicode_ci for case-insensitive comparisons  
**Engine:** InnoDB for ACID compliance and foreign key support

### Table Naming Strategy

All tables use **logical schema separation through prefixing** to maintain module boundaries while using a single MySQL database:

- **No prefix**: Shared authentication and authorization tables (`users`, `roles`, `permissions`)
- **`sas_` prefix**: Student Affairs Services module tables
- **`registrar_` prefix**: Registrar operations module tables
- **`usg_` prefix**: University Student Government portal tables

**Benefits of this approach:**
- Clear module ownership and boundaries
- Single database simplifies backups and transactions
- Easy to identify which module owns which data
- Simpler than multi-database approach for single-campus deployment

### Multi-Module Schema Design

The system uses **logical schema separation** through table prefixing to maintain module boundaries while using a single MySQL database:

```
sas_information_system (database)
├── Shared Schema (auth_ prefix)
│   ├── users
│   ├── password_resets
│   ├── sessions
│   └── roles & permissions
│
├── SAS Schema (sas_ prefix)
│   ├── scholarships
│   ├── organizations
│   ├── events
│   ├── insurance_records
│   └── digital_documents
│
├── Registrar Schema (registrar_ prefix)
│   ├── document_requests
│   ├── transactions
│   └── document_deliveries
│
└── USG Schema (usg_ prefix)
    ├── officers
    ├── resolutions
    ├── announcements
    └── vmgo_content
```

---

## Shared Auth Schema

### Entity: User

**Table:** `users`  
**Purpose:** Core identity and authentication for all system users  
**Relationships:** Central hub connecting to students, staff, officers

#### Schema Definition

```sql
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_student_id (student_id),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Business Rules
- **Student ID**: Optional (null for non-students like staff/faculty)
- **Email**: Must be unique, format validated (@minsubongabong.edu.ph for students)
- **Password**: Hashed using bcrypt (Laravel default), min 8 characters
- **Email Verification**: Required before full system access
- **Soft Deletes**: Users never hard-deleted (GDPR compliance - right to erasure uses anonymization)

#### Laravel Model

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles;

    protected $fillable = [
        'student_id', 'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function scholarshipApplications()
    {
        return $this->hasMany(ScholarshipApplication::class);
    }

    public function documentRequests()
    {
        return $this->hasMany(DocumentRequest::class);
    }
}
```

---

### Entity: Student (Extended Profile)

**Table:** `students`  
**Purpose:** Extended profile for enrolled students  
**Relationships:** Belongs to User, has many scholarship applications, document requests

```sql
CREATE TABLE students (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    program VARCHAR(100) NOT NULL,
    year_level TINYINT NOT NULL,
    enrollment_status ENUM('enrolled', 'loa', 'graduated', 'dropped') DEFAULT 'enrolled',
    gpa DECIMAL(3,2) NULL,
    mobile_number VARCHAR(20) NULL,
    address TEXT NULL,
    emergency_contact_name VARCHAR(255) NULL,
    emergency_contact_number VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_student_id (student_id),
    INDEX idx_enrollment_status (enrollment_status)
) ENGINE=InnoDB;
```

#### Business Rules
- **Year Level**: 1-5 for most programs, 1-6 for engineering
- **GPA**: Null for first semester freshmen, required thereafter
- **Enrollment Status**: Auto-updated from registrar system each semester
- **Graduation**: Status changes to 'graduated' when TOR issued with completion

---

### Entity: Role & Permission

**Tables:** `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`  
**Package:** Spatie Laravel Permission  
**Purpose:** Role-based access control (RBAC)

#### Roles Defined

| Role | Permissions | Access Scope |
|------|-------------|--------------|
| `student` | Submit applications, request documents, view own data | Own records only |
| `sas_staff` | Review scholarships, manage organizations, create events | SAS module |
| `sas_admin` | All SAS permissions + approve high-value scholarships | SAS module + admin |
| `registrar_staff` | Process document requests, view payments | Registrar module |
| `registrar_admin` | All registrar permissions + refunds, manual reconciliation | Registrar module + admin |
| `usg_officer` | Manage VMGO, officers, announcements, resolutions | USG module |
| `usg_admin` | All USG permissions + user management | USG module + admin |
| `system_admin` | Full system access | All modules |

#### Laravel Setup

```php
// Seeder: database/seeders/RoleSeeder.php
public function run()
{
    $roles = [
        'student' => [
            'submit_scholarship_application',
            'request_documents',
            'view_own_records',
        ],
        'sas_staff' => [
            'review_scholarships',
            'manage_organizations',
            'create_events',
            'access_sas_module',
        ],
        // ... other roles
    ];

    foreach ($roles as $roleName => $permissions) {
        $role = Role::create(['name' => $roleName]);
        
        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            $role->givePermissionTo($permission);
        }
    }
}
```

---

## SAS Schema

### Entity: Scholarship Application

**Table:** `sas_scholarships`  
**Purpose:** Track scholarship application lifecycle from submission to disbursement  
**Relationships:** Belongs to User/Student, has many documents

```sql
CREATE TABLE sas_scholarships (
    id CHAR(36) PRIMARY KEY,
    reference_number VARCHAR(50) UNIQUE NOT NULL,
    user_id CHAR(36) NOT NULL,
    student_id CHAR(36) NOT NULL,
    scholarship_type ENUM('TES', 'TDP', 'institutional') NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    semester TINYINT NOT NULL,
    status ENUM(
        'pending_review',
        'under_review',
        'pending_documents',
        'approved',
        'rejected',
        'disbursed'
    ) DEFAULT 'pending_review',
    family_income DECIMAL(10,2) NOT NULL,
    gpa DECIMAL(3,2) NOT NULL,
    amount DECIMAL(10,2) NULL,
    disbursement_schedule VARCHAR(50) NULL,
    reviewed_by CHAR(36) NULL,
    reviewed_at TIMESTAMP NULL,
    approved_by CHAR(36) NULL,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    comments TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    
    INDEX idx_reference (reference_number),
    INDEX idx_status (status),
    INDEX idx_scholarship_type (scholarship_type),
    INDEX idx_academic_year (academic_year, semester),
    INDEX idx_student (student_id)
) ENGINE=InnoDB;
```

#### Business Rules
- **Reference Number**: Auto-generated: `SA-{YEAR}-{SEQUENCE}` (e.g., SA-2025-0001)
- **GPA Threshold**: TES ≥2.5, TDP ≥3.0, Institutional varies
- **Income Threshold**: TES ≤₱300,000/year family income
- **Duplicate Prevention**: Unique constraint on (student_id, scholarship_type, academic_year, semester)
- **Status Transitions**: 
  - `pending_review` → `under_review` → `approved`/`rejected`
  - `approved` → `disbursed` (after finance office confirms)
  - `under_review` → `pending_documents` → `under_review` (if docs needed)
- **Approval Authority**: TES/TDP <₱20k: `sas_staff`, ≥₱20k: `sas_admin`

#### Laravel Model

```php
namespace App\Modules\SAS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Scholarship extends Model
{
    use HasUuids;

    protected $table = 'sas_scholarships';

    protected $fillable = [
        'reference_number', 'user_id', 'student_id', 'scholarship_type',
        'academic_year', 'semester', 'family_income', 'gpa', 'amount',
    ];

    protected $casts = [
        'family_income' => 'decimal:2',
        'gpa' => 'decimal:2',
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function documents()
    {
        return $this->hasMany(ScholarshipDocument::class);
    }

    // Scopes
    public function scopePendingReview($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeForAcademicYear($query, $year, $semester)
    {
        return $query->where('academic_year', $year)
                     ->where('semester', $semester);
    }

    // Business Logic Methods
    public function approve(User $approver, float $amount, string $comments = null)
    {
        if (!$this->canBeApproved()) {
            throw new \Exception('Scholarship is not in reviewable state');
        }

        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'amount' => $amount,
            'comments' => $comments,
        ]);

        event(new ScholarshipApproved($this));
    }

    public function requestAdditionalDocuments(array $documentTypes, string $message)
    {
        $this->update(['status' => 'pending_documents']);
        
        event(new AdditionalDocumentsRequested($this, $documentTypes, $message));
    }

    private function canBeApproved(): bool
    {
        return in_array($this->status, ['pending_review', 'under_review', 'pending_documents']);
    }
}
```

---

### Entity: Organization

**Table:** `sas_organizations`  
**Purpose:** Registry of student organizations (minor and major)  
**Relationships:** Has many members, has many events

```sql
CREATE TABLE sas_organizations (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    type ENUM('minor', 'major') NOT NULL,
    status ENUM('pending_approval', 'active', 'inactive') DEFAULT 'pending_approval',
    description TEXT NULL,
    advisor_id CHAR(36) NULL,
    president_id CHAR(36) NOT NULL,
    constitution_url VARCHAR(500) NULL,
    logo_url VARCHAR(500) NULL,
    registration_date DATE NOT NULL,
    last_activity_report_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (advisor_id) REFERENCES users(id),
    FOREIGN KEY (president_id) REFERENCES users(id),
    
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_advisor (advisor_id)
) ENGINE=InnoDB;
```

#### Business Rules
- **Type**: Minor (club-level), Major (college-wide or SAMAHAN)
- **Advisor Required**: Major orgs must have faculty advisor, minor orgs optional
- **Advisor Limit**: Faculty can advise max 3 organizations
- **President Eligibility**: GPA ≥2.5, enrolled status
- **Constitution**: Required for major orgs, stored as PDF in cloud
- **Compliance**: Activity report required quarterly, auto-inactive if 2 consecutive quarters missed
- **Naming**: Must be unique, no profanity filter

---

### Entity: Organization Member

**Table:** `sas_organization_members`  
**Purpose:** Membership roster with position tracking  
**Relationships:** Belongs to Organization and User

```sql
CREATE TABLE sas_organization_members (
    id CHAR(36) PRIMARY KEY,
    organization_id CHAR(36) NOT NULL,
    user_id CHAR(36) NOT NULL,
    position VARCHAR(100) NOT NULL,
    joined_at DATE NOT NULL,
    left_at DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organization_id) REFERENCES sas_organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    
    UNIQUE KEY unique_active_membership (organization_id, user_id, is_active),
    INDEX idx_organization (organization_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB;
```

#### Business Rules
- **Positions**: President, Vice President, Secretary, Treasurer, Auditor, PIO, Member
- **Multiple Memberships**: Student can be member of multiple orgs
- **Officer Limits**: Max 1 president role per student, max 3 officer roles total
- **Soft Delete**: Members not hard-deleted (left_at + is_active=false)
- **Historical Tracking**: Past members retained for reports

---

### Entity: Event

**Table:** `sas_events`  
**Purpose:** Campus event management (source of truth for calendar)  
**Relationships:** Belongs to Organization (optional), syncs to USG calendar

```sql
CREATE TABLE sas_events (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('academic', 'social', 'sports', 'cultural', 'workshop', 'seminar') NOT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    organizer_type ENUM('sas', 'organization', 'department') NOT NULL,
    organizer_id CHAR(36) NULL,
    banner_url VARCHAR(500) NULL,
    capacity INT NULL,
    registration_required BOOLEAN DEFAULT FALSE,
    registration_url VARCHAR(500) NULL,
    status ENUM('draft', 'pending_approval', 'published', 'cancelled') DEFAULT 'published',
    created_by CHAR(36) NOT NULL,
    approved_by CHAR(36) NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (organizer_id) REFERENCES sas_organizations(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    
    INDEX idx_datetime (start_datetime, end_datetime),
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_organizer (organizer_id)
) ENGINE=InnoDB;
```

#### Business Rules
- **Venue Conflicts**: System checks for overlapping (location + time) before saving
- **Approval Required**: Events with budget >₱5000 or capacity >200 need SAS director approval
- **Auto-Publish**: Events meeting criteria auto-publish to USG within 5 minutes (event-driven sync)
- **Past Events**: Not deleted, status remains 'published' for historical calendar
- **Capacity**: Optional, if set enables registration tracking

#### Event Sync to USG

```php
// Event: App\Modules\SAS\Events\EventCreated
class EventCreated
{
    public function __construct(public Event $event) {}
}

// Listener: App\Modules\USG\Listeners\SyncEventToPublicCalendar
class SyncEventToPublicCalendar
{
    public function handle(EventCreated $event): void
    {
        Cache::tags(['usg', 'calendar'])->put(
            "usg:event:{$event->event->id}",
            $event->event->toArray(),
            now()->addDays(90)
        );
        
        // Refresh materialized view or cache layer
        $this->refreshPublicCalendar();
    }
}
```

---

## Registrar Schema

### Entity: Document Request

**Table:** `registrar_document_requests`  
**Purpose:** Track student document requests from submission to delivery  
**Relationships:** Belongs to User/Student, has one Transaction

```sql
CREATE TABLE registrar_document_requests (
    id CHAR(36) PRIMARY KEY,
    request_number VARCHAR(50) UNIQUE NOT NULL,
    user_id CHAR(36) NOT NULL,
    student_id CHAR(36) NOT NULL,
    document_type ENUM('certificate_of_grades', 'certificate_of_enrollment', 'transcript_of_records', 'diploma', 'certification_authentication_verification') NOT NULL,
    copies TINYINT DEFAULT 1,
    purpose VARCHAR(255) NOT NULL,
    delivery_method ENUM('digital_download', 'pickup', 'courier') DEFAULT 'digital_download',
    delivery_address TEXT NULL,
    status ENUM(
        'pending_payment',
        'payment_confirmed',
        'processing',
        'quality_check',
        'ready',
        'downloaded',
        'picked_up',
        'completed',
        'cancelled'
    ) DEFAULT 'pending_payment',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    amount DECIMAL(8,2) NOT NULL,
    payment_reference VARCHAR(100) NULL,
    paid_at TIMESTAMP NULL,
    document_url VARCHAR(500) NULL,
    generated_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    downloaded_at TIMESTAMP NULL,
    processing_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    
    INDEX idx_request_number (request_number),
    INDEX idx_student (student_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;
```

#### Business Rules
- **Request Number**: Auto-generated: `REQ-{YEAR}-{SEQUENCE}` (e.g., REQ-2025-0123)
- **Pricing**:
  - COG: ₱50/copy
  - COE: ₱30/copy
  - TOR: ₱100/copy (₱150 for rush <24hr)
  - Diploma: ₱200/copy
  - CAV: ₱100/document
- **Status Flow**:
  - `pending_payment` → `payment_confirmed` → `processing` → `quality_check` → `ready` → `downloaded`/`picked_up` → `completed`
- **Expiration**: Digital downloads expire 30 days after ready, reissue costs ₱50
- **Clearance Check**: TOR requires no outstanding obligations (checked before payment)

#### Laravel Model

```php
namespace App\Modules\Registrar\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    protected $table = 'registrar_document_requests';

    protected $fillable = [
        'request_number', 'user_id', 'student_id', 'document_type',
        'copies', 'purpose', 'delivery_method', 'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    // State Machine
    public function confirmPayment(string $paymentReference)
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_reference' => $paymentReference,
            'paid_at' => now(),
            'status' => 'payment_confirmed',
        ]);

        // Dispatch background job
        \App\Modules\Registrar\Jobs\GenerateDocument::dispatch($this)
            ->onQueue('documents')
            ->delay(now()->addMinutes(2));
    }

    public function markAsReady(string $documentUrl)
    {
        $this->update([
            'status' => 'ready',
            'document_url' => $documentUrl,
            'generated_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        event(new DocumentReady($this));
    }

    public function recordDownload()
    {
        $this->update([
            'status' => 'downloaded',
            'downloaded_at' => now(),
        ]);
    }
}
```

---

### Entity: Transaction

**Table:** `registrar_transactions`  
**Purpose:** Payment records for document requests  
**Relationships:** Belongs to DocumentRequest

```sql
CREATE TABLE registrar_transactions (
    id CHAR(36) PRIMARY KEY,
    request_id CHAR(36) NOT NULL,
    idempotency_key VARCHAR(100) UNIQUE NOT NULL,
    payment_intent_id VARCHAR(100) NULL,
    amount DECIMAL(8,2) NOT NULL,
    payment_method VARCHAR(50) NULL,
    payment_provider VARCHAR(50) DEFAULT 'paymongo',
    status ENUM('pending', 'processing', 'succeeded', 'failed', 'refunded') DEFAULT 'pending',
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (request_id) REFERENCES registrar_document_requests(id),
    
    INDEX idx_request (request_id),
    INDEX idx_payment_intent (payment_intent_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;
```

#### Business Rules
- **Idempotency Key**: Format: `idem-{request_id}-{timestamp}` prevents duplicate charges
- **Payment Methods**: GCash, PayMaya, Credit Card, Debit Card, Bank Transfer
- **Metadata**: JSON field stores Paymongo raw response for audit
- **Reconciliation**: Daily job matches Paymongo transactions with database records

---

## USG Schema

### Entity: VMGO Content

**Table:** `usg_vmgo_content`  
**Purpose:** Version-controlled VMGO (Vision, Mission, Goals, Objectives) content  
**Relationships:** Version history with User (editor)

```sql
CREATE TABLE usg_vmgo_content (
    id CHAR(36) PRIMARY KEY,
    section ENUM('vision', 'mission', 'goals', 'objectives') NOT NULL,
    content TEXT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    version INT DEFAULT 1,
    edited_by CHAR(36) NOT NULL,
    published_by CHAR(36) NULL,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (edited_by) REFERENCES users(id),
    FOREIGN KEY (published_by) REFERENCES users(id),
    
    INDEX idx_section_status (section, status),
    INDEX idx_version (section, version)
) ENGINE=InnoDB;
```

#### Business Rules
- **Version Control**: Each edit creates new row with incremented version
- **Only Latest Published**: Public sees only status='published' with MAX(version)
- **Rollback**: Restore old version by republishing it (creates new version entry)
- **Approval**: Content editor creates draft, USG President/Advisor publishes

---

### Entity: Officer

**Table:** `usg_officers`  
**Purpose:** USG officer directory with terms  
**Relationships:** Belongs to User

```sql
CREATE TABLE usg_officers (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    position VARCHAR(100) NOT NULL,
    department ENUM('executive', 'legislative', 'administrative') NOT NULL,
    term_start DATE NOT NULL,
    term_end DATE NOT NULL,
    photo_url VARCHAR(500) NULL,
    email VARCHAR(255) NULL,
    office_hours VARCHAR(255) NULL,
    bio TEXT NULL,
    status ENUM('current', 'past') DEFAULT 'current',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    
    INDEX idx_user (user_id),
    INDEX idx_status_term (status, term_start, term_end)
) ENGINE=InnoDB;
```

#### Business Rules
- **Auto-Status Update**: Daily cron job checks term_end, updates status to 'past' if expired
- **No Overlapping Terms**: Same user cannot have 2 current terms (validated at create)
- **Position**: President, Vice President, Secretary, Treasurer, Auditor, etc.

---

### Entity: Resolution

**Table:** `usg_resolutions`  
**Purpose:** Public record of USG resolutions  
**Relationships:** None (standalone documents)

```sql
CREATE TABLE usg_resolutions (
    id CHAR(36) PRIMARY KEY,
    resolution_number VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    category ENUM('academic', 'financial', 'student_welfare', 'organizational', 'policy') NOT NULL,
    date_filed DATE NOT NULL,
    status ENUM('pending', 'approved', 'implemented', 'rejected', 'superseded') DEFAULT 'pending',
    summary TEXT NOT NULL,
    pdf_url VARCHAR(500) NOT NULL,
    visibility ENUM('public', 'restricted') DEFAULT 'public',
    superseded_by CHAR(36) NULL,
    created_by CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (superseded_by) REFERENCES usg_resolutions(id),
    
    INDEX idx_resolution_number (resolution_number),
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_visibility (visibility),
    FULLTEXT INDEX ft_search (title, summary)
) ENGINE=InnoDB;
```

#### Business Rules
- **Resolution Number**: Format: `{YEAR}-{SEQUENCE}` (e.g., 2025-015)
- **PDF Required**: Must be searchable text (OCR applied if scanned)
- **Immutability**: Published resolutions cannot be deleted, only superseded
- **Full-Text Search**: MySQL FULLTEXT index on title + summary

---

## Data Relationships Diagram

```
┌─────────────┐
│    User     │◄─────────────┐
└──────┬──────┘              │
       │                     │
       │ 1:1                 │ Many:1
       ▼                     │
┌─────────────┐              │
│   Student   │              │
└──────┬──────┘              │
       │                     │
       │ 1:Many              │
       ├─────────────────────┤
       │                     │
       ▼                     ▼
┌─────────────────┐   ┌──────────────────┐
│  Scholarship    │   │ Document Request │
└─────────────────┘   └──────────────────┘
       │                     │
       │ 1:Many              │ 1:1
       ▼                     ▼
┌─────────────────┐   ┌──────────────────┐
│ ScholarshipDoc  │   │   Transaction    │
└─────────────────┘   └──────────────────┘

┌─────────────┐       ┌─────────────┐
│    User     │──1:M─►│   Officer   │
└─────────────┘       └─────────────┘

┌─────────────┐
│    User     │──┐
└─────────────┘  │ Many:Many
                 │
                 ▼
         ┌──────────────┐
         │ Organization │
         └──────────────┘
                 │ 1:Many
                 ▼
         ┌──────────────┐
         │    Event     │──(synced to)─►USG Calendar
         └──────────────┘
```

---

## Indexes Strategy

### Performance Optimization

**Foreign Keys**: All FK columns indexed automatically  
**Status Fields**: Indexed for filtering (used in dashboards)  
**Date Ranges**: Composite indexes on date fields for calendar queries  
**Full-Text**: FULLTEXT indexes on searchable text columns  
**Unique Constraints**: Prevent duplicate business-critical records

---

## Migration Scripts

### Example: Create Scholarships Table

```php
// database/migrations/2025_10_04_100000_create_sas_scholarships_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sas_scholarships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_number', 50)->unique();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('student_id')->constrained('students');
            $table->enum('scholarship_type', ['TES', 'TDP', 'institutional']);
            $table->string('academic_year', 10);
            $table->tinyInteger('semester');
            $table->enum('status', [
                'pending_review',
                'under_review',
                'pending_documents',
                'approved',
                'rejected',
                'disbursed'
            ])->default('pending_review');
            $table->decimal('family_income', 10, 2);
            $table->decimal('gpa', 3, 2);
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('disbursement_schedule', 50)->nullable();
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignUuid('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('scholarship_type');
            $table->index(['academic_year', 'semester']);
            $table->index('student_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sas_scholarships');
    }
};
```

---

## Factory & Seeder Examples

### Scholarship Factory

```php
// database/factories/ScholarshipFactory.php

namespace Database\Factories;

use App\Models\User;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScholarshipFactory extends Factory
{
    public function definition()
    {
        return [
            'reference_number' => 'SA-' . date('Y') . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'student_id' => Student::factory(),
            'scholarship_type' => fake()->randomElement(['TES', 'TDP', 'institutional']),
            'academic_year' => '2025-2026',
            'semester' => fake()->numberBetween(1, 2),
            'status' => 'pending_review',
            'family_income' => fake()->randomFloat(2, 50000, 300000),
            'gpa' => fake()->randomFloat(2, 2.5, 4.0),
        ];
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
                'amount' => 10000.00,
                'approved_by' => User::factory(),
                'approved_at' => now(),
            ];
        });
    }
}
```

---

## Backup & Retention Policies

### Data Retention

| Table | Retention Period | Backup Frequency | Archive Strategy |
|-------|------------------|------------------|------------------|
| Users | Indefinite (anonymize on request) | Daily | Hot storage |
| Scholarships | 7 years (audit compliance) | Daily | Archive to cold storage after 2 years |
| Document Requests | 5 years | Daily | Archive to cold storage after 1 year |
| Events | Indefinite (historical record) | Weekly | Hot storage |
| Transactions | 10 years (financial audit) | Daily | Archive after 3 years |

### Backup Strategy

```bash
# Daily automated backup (cron: 0 2 * * *)
mysqldump --single-transaction sas_information_system \
  | gzip > /backups/sas_$(date +\%Y\%m\%d).sql.gz

# Restore command
gunzip < /backups/sas_20251004.sql.gz \
  | mysql sas_information_system
```

---

**Document Maintenance:**
- Update ERD when new entities added
- Version migrations in Git with clear commit messages
- Test migrations on staging before production
- Document all business rules as database constraints where possible

