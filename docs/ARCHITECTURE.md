# MinSU Bongabong Information System - Technical Architecture

## Document Information
- **Version:** 1.0
- **Last Updated:** October 4, 2025
- **Status:** Living Document
- **Institution:** Mindoro State University - Bongabong Campus
- **Deployment Scope:** Single Campus (Bongabong only)
- **Audience:** Technical Leads, Software Architects, Senior Developers

## Executive Summary

The MinSU Bongabong Information System is a comprehensive university management platform built specifically for **Mindoro State University - Bongabong Campus**. This is a **single-campus deployment**, which significantly simplifies the architecture by eliminating geographic distribution complexity, cross-site data replication, and network partition handling.

The system is built as a **modular monolith** using Laravel 12 and Inertia.js v2 (React 19). This architecture supports three distinct bounded contexts (Student Affairs Services, Registrar Operations, and USG Transparency Portal) within a single deployable application, achieving the benefits of clear domain boundaries without the operational complexity of microservices.


**Key Architectural Decisions:**
- Modular monolith for single-campus deployment simplicity
- Event-driven integration for loose coupling between modules
- Schema-based isolation using table prefixing for logical database separation
- Queue-based asynchronous processing for resource-intensive operations
- Type-safe full-stack development with TypeScript and PHP 8.2
- API-first design for future mobile app integration

**Single Campus Benefits:**
- **No Geographic Distribution:** All services run on single server or co-located infrastructure
- **Unified Database:** Single MySQL instance, no cross-site replication needed
- **Simplified Networking:** No VPN, WAN optimization, or site-to-site connectivity required
- **Centralized Administration:** Single IT team manages one deployment
- **Lower Operational Costs:** ~$20-40/month hosting vs $100+ for multi-site distributed systems
- **Faster Development:** No need to handle network partitions, eventual consistency across sites, or conflict resolution

**Note on Future Multi-Campus Support:** While this system is designed for Bongabong Campus only, the clean module boundaries and event-driven architecture provide a migration path if Mindoro State University decides to expand deployment to other campuses (Calapan, Victoria, etc.) in the future. This would require architectural changes documented in the "Scalability Considerations" section.

## Architecture Style: Modular Monolith

### Rationale

We chose a **modular monolith** over microservices for the following reasons:

1. **Single Campus Deployment** - No geographic distribution complexity, all users access one central system
2. **Team Size** - 2-3 developers can manage entire codebase effectively
3. **Operational Simplicity** - Single deployment, single database, unified monitoring
4. **Development Velocity** - Faster iteration without inter-service contracts
5. **Resource Efficiency** - Lower hosting costs (~$20/month vs $100+ for distributed services)
6. **Future Migration Path** - Clean module boundaries enable extraction to microservices if needed

### Modular Monolith Principles

- **Single Deployable:** One Laravel application, one deployment pipeline
- **Clear Module Boundaries:** Each domain (SAS, Registrar, USG) maintains its own:
  - Controllers and routes
  - Services and business logic
  - Database tables (via prefixing)
  - Policies and permissions
- **Shared Infrastructure:** Common authentication, authorization, caching, and queue systems
- **Event-Driven Integration:** Modules communicate through Laravel events, not direct coupling
- **Independent Evolution:** Modules can be refactored without affecting others


## Technology Stack

### Backend Framework & Language

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| **Framework** | Laravel | 12.x | Full-stack PHP framework with batteries-included approach |
| **Language** | PHP | 8.2+ | Modern PHP with type declarations, attributes, and performance improvements |
| **ORM** | Eloquent | Built-in | Active Record pattern for database interactions |
| **Authentication** | Laravel Fortify | 1.x | Headless authentication backend (login, registration, 2FA, session-based auth) |
| **Authorization** | Spatie Permission | 6.x | Role and permission management (RBAC) |
| **Navigation** | Laravel Wayfinder | 0.x | Type-safe route generation for frontend |
| **Task Scheduling** | Laravel Scheduler | Built-in | Cron job management for periodic tasks |
| **Queue System** | Laravel Queue | Built-in | Background job processing (database driver, Redis-ready) |

### Frontend Framework & Tooling

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| **SPA Framework** | Inertia.js | 2.x | Server-driven SPA without building APIs |
| **UI Library** | React | 19.x | Component-based UI rendering |
| **Language** | TypeScript | 5.x | Type-safe JavaScript with compile-time checking |
| **Styling** | Tailwind CSS | 4.x | Utility-first CSS framework |
| **Component Library** | shadcn/ui | Latest | Radix primitives + Tailwind styling |
| **Build Tool** | Vite | 6.x | Fast bundler with HMR and SSR support |
| **State Management** | React Hooks | Built-in | Local state, no global state manager needed |
| **Form Handling** | Inertia Forms | Built-in | Form state management with validation errors |

### Database & Caching

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| **Primary Database** | MySQL | 8.0+ | Relational data with ACID compliance |
| **Cache Driver** | Redis | 7.x (optional) | Session storage, queue backend, cache store |
| **Fallback Cache** | File/Database | Built-in | Simple caching without Redis requirement |
| **Search** | MySQL Full-Text | Built-in | Basic search functionality (upgrade to Meilisearch later) |

### External Services & Integrations

| Service | Provider | Purpose |
|---------|----------|---------|
| **Payment Gateway** | Paymongo | Philippine payment methods (GCash, bank transfers, cards) |
| **File Storage** | Local/S3/Cloudinary | Document uploads, generated PDFs, scanned files |
| **Email Service** | SMTP/Mailgun/SES | Transactional emails (notifications, password resets) |
| **SMS (Future)** | Semaphore/Twilio | SMS notifications for document ready alerts |

### Development & Quality Tools

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| **Testing Framework** | Pest | 3.x | BDD-style testing (built on PHPUnit 11) |
| **PHP Code Style** | Laravel Pint | 1.x | Opinionated PHP formatter (PSR-12 compliant) |
| **JS/TS Linting** | ESLint | 9.x | JavaScript/TypeScript code quality |
| **Code Formatting** | Prettier | 3.x | Consistent code formatting |
| **Type Checking** | TypeScript Compiler | 5.x | Static type analysis |
| **Git Hooks** | Husky (optional) | Latest | Pre-commit linting and testing |


## System Architecture Overview

### High-Level Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        External Users                            │
│        (Students, Staff, Public, USG Officers)                   │
└────────────────────────┬────────────────────────────────────────┘
                         │ HTTPS
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│                     Web Server (Nginx)                           │
│                   SSL/TLS Termination                            │
│                   Static Asset Serving                           │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│              Laravel Application (Modular Monolith)              │
├─────────────────────────────────────────────────────────────────┤
│                 Authentication & Authorization                   │
│              (Fortify + Spatie Permissions)                      │
├────────────┬──────────────────────┬─────────────────────────────┤
│  USG       │    Registrar        │         SAS                  │
│  Module    │    Module           │         Module               │
│            │                     │                              │
│ • VMGO     │ • Doc Requests      │ • Scholarships              │
│ • Officers │ • Payments          │ • Insurance                 │
│ • Calendar │ • PDF Generation    │ • Organizations             │
│ • Resolutions│ • Tracking         │ • Events                    │
│ • Announcements│                 │ • Digital Archive           │
└────────────┴──────────────────────┴─────────────────────────────┘
                         │
         ┌───────────────┼───────────────┐
         │               │               │
         ▼               ▼               ▼
┌────────────────┐ ┌──────────┐ ┌────────────────┐
│  MySQL 8.0+    │ │  Redis   │ │ File Storage   │
│                │ │          │ │ (Local/S3/     │
│ • auth_*       │ │ • Cache  │ │  Cloudinary)   │
│ • sas_*        │ │ • Session│ │                │
│ • registrar_*  │ │ • Queue  │ │ • PDFs         │
│ • usg_*        │ │          │ │ • Scanned Docs │
└────────────────┘ └──────────┘ └────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│      Background Workers              │
│                                      │
│ • PDF Generation Queue               │
│ • Email Notification Queue           │
│ • Event Processing Queue             │
└─────────────────────────────────────┘
         │
         ▼
┌─────────────────────────────────────┐
│      External Services               │
│                                      │
│ • Paymongo (Payments)               │
│ • SMTP/Mailgun (Email)              │
│ • Cloudinary (File Storage)         │
└─────────────────────────────────────┘
```

### Request Flow Examples

**1. Student Requests Document (Registrar Module)**
```
Student → Nginx → Laravel Router → Auth Middleware → 
RegistrarController → DocumentRequestService → 
PaymentService → Paymongo API → Redirect to Payment → 
Webhook → Queue Job → PDF Generation → 
Email Notification → Student
```

**2. SAS Creates Event (Event-Driven Integration)**
```
SAS Admin → Nginx → Laravel Router → Auth + Role Middleware → 
EventController → EventService → Save to Database → 
Dispatch EventCreated → USG Listener → Cache Event → 
Public Calendar Updated
```

**3. Public Views USG Calendar (Read-Heavy)**
```
Public User → Nginx → Laravel Router → 
USGController → Read from Cache → Return View → 
Inertia Response → React Renders Calendar
```


## Module Architecture

### Directory Structure

```
app/
├── Console/
│   └── Commands/                    # Custom Artisan commands (auto-registered in Laravel 12)
├── Events/                          # Domain events for module communication
│   ├── SAS/
│   │   ├── EventCreated.php
│   │   ├── EventUpdated.php
│   │   ├── ScholarshipApproved.php
│   │   ├── ScholarshipRejected.php
│   │   └── OrganizationRegistered.php
│   ├── Registrar/
│   │   ├── DocumentRequestCreated.php
│   │   ├── PaymentConfirmed.php
│   │   └── DocumentReady.php
│   └── USG/
│       └── AnnouncementPublished.php
├── Exceptions/
│   └── Handler.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                    # Authentication controllers
│   │   │   ├── LoginController.php
│   │   │   └── RegisterController.php
│   │   ├── SAS/                     # Student Affairs controllers
│   │   │   ├── ScholarshipController.php
│   │   │   ├── EventController.php
│   │   │   ├── OrganizationController.php
│   │   │   ├── InsuranceController.php
│   │   │   └── DigitalArchiveController.php
│   │   ├── Registrar/               # Registrar controllers
│   │   │   ├── DocumentRequestController.php
│   │   │   ├── PaymentController.php
│   │   │   ├── DeliveryController.php
│   │   │   └── TrackingController.php
│   │   └── USG/                     # USG Portal controllers
│   │       ├── HomeController.php
│   │       ├── OfficerController.php
│   │       ├── AnnouncementController.php
│   │       ├── ResolutionController.php
│   │       ├── VMGOController.php
│   │       └── CalendarController.php
│   ├── Middleware/                  # Custom middleware (if needed)
│   │   ├── EnsureSASAdmin.php
│   │   ├── EnsureRegistrarStaff.php
│   │   └── EnsureUSGOfficer.php
│   ├── Requests/                    # Form Request validation
│   │   ├── SAS/
│   │   │   ├── StoreScholarshipRequest.php
│   │   │   ├── UpdateScholarshipRequest.php
│   │   │   ├── StoreEventRequest.php
│   │   │   ├── StoreOrganizationRequest.php
│   │   │   └── UpdateOrganizationRequest.php
│   │   ├── Registrar/
│   │   │   ├── StoreDocumentRequestRequest.php
│   │   │   └── UpdateDocumentRequestRequest.php
│   │   └── USG/
│   │       ├── StoreAnnouncementRequest.php
│   │       ├── StoreResolutionRequest.php
│   │       └── StoreOfficerRequest.php
│   └── Resources/                   # API Resources (future mobile app)
│       └── API/
│           └── V1/
├── Jobs/                            # Queued background jobs
│   ├── SAS/
│   │   ├── ProcessScholarshipApplication.php
│   │   ├── ProcessBulkDigitalisation.php
│   │   └── SendEventReminders.php
│   ├── Registrar/
│   │   ├── GenerateTranscript.php
│   │   ├── GenerateCertificate.php
│   │   ├── GenerateCOG.php
│   │   ├── GenerateCOE.php
│   │   └── SendDocumentNotification.php
│   └── Shared/
│       └── SendEmailNotification.php
├── Listeners/                       # Event listeners
│   ├── SAS/
│   │   ├── NotifyScholarshipApproval.php
│   │   └── SendEventNotificationEmail.php
│   ├── Registrar/
│   │   ├── GenerateDocument.php
│   │   ├── QueueDocumentGeneration.php
│   │   └── SendDocumentReadyEmail.php
│   └── USG/
│       ├── SyncEventToPublicCalendar.php
│       └── BroadcastAnnouncement.php
├── Mail/                            # Mailable classes
│   ├── ScholarshipApprovedMail.php
│   ├── ScholarshipRejectedMail.php
│   ├── DocumentReadyMail.php
│   └── EventReminderMail.php
├── Models/                          # Eloquent models
│   ├── User.php                     # Shared auth model
│   ├── SAS/
│   │   ├── Scholarship.php
│   │   ├── ScholarshipApplication.php
│   │   ├── Event.php
│   │   ├── Organization.php
│   │   ├── OrganizationMember.php
│   │   ├── InsuranceRecord.php
│   │   └── DigitalDocument.php
│   ├── Registrar/
│   │   ├── DocumentType.php
│   │   ├── DocumentRequest.php
│   │   ├── Transaction.php
│   │   └── DocumentDelivery.php
│   └── USG/
│       ├── Officer.php
│       ├── Announcement.php
│       ├── Resolution.php
│       ├── VMGO.php
│       └── CachedEvent.php
├── Notifications/                   # Laravel notifications
│   ├── ScholarshipStatusNotification.php
│   ├── DocumentReadyNotification.php
│   └── EventReminderNotification.php
├── Policies/                        # Authorization policies
│   ├── SAS/
│   │   ├── ScholarshipPolicy.php
│   │   ├── EventPolicy.php
│   │   └── OrganizationPolicy.php
│   ├── Registrar/
│   │   └── DocumentRequestPolicy.php
│   └── USG/
│       ├── ResolutionPolicy.php
│       └── AnnouncementPolicy.php
├── Providers/
│   ├── AppServiceProvider.php
│   └── FortifyServiceProvider.php
└── Services/                        # Business logic layer
    ├── Auth/
    │   └── StudentVerificationService.php
    ├── SAS/
    │   ├── ScholarshipService.php
    │   ├── EventService.php
    │   ├── EventPublishingService.php
    │   ├── OrganizationService.php
    │   └── DigitalArchiveService.php
    ├── Registrar/
    │   ├── DocumentRequestService.php
    │   ├── PaymentService.php
    │   ├── PDFGenerationService.php
    │   └── PaymongoService.php
    ├── USG/
    │   ├── CalendarSyncService.php
    │   └── AnnouncementService.php
    └── Shared/
        ├── NotificationService.php
        └── FileStorageService.php

resources/
├── css/
│   └── app.css                      # Tailwind CSS entry point
├── js/
│   ├── actions/                     # Server actions (if using RSC patterns)
│   ├── app.tsx                      # Frontend entry point
│   ├── ssr.tsx                      # SSR entry point
│   ├── components/
│   │   ├── ui/                      # shadcn/ui components
│   │   │   ├── button.tsx
│   │   │   ├── card.tsx
│   │   │   ├── form.tsx
│   │   │   ├── input.tsx
│   │   │   ├── table.tsx
│   │   │   ├── dialog.tsx
│   │   │   └── ...
│   │   ├── shared/                  # Shared across all modules
│   │   │   ├── Header.tsx
│   │   │   ├── Footer.tsx
│   │   │   ├── Sidebar.tsx
│   │   │   ├── Navbar.tsx
│   │   │   ├── LoadingSpinner.tsx
│   │   │   └── StatusBadge.tsx
│   │   ├── sas/                     # SAS-specific components
│   │   │   ├── ScholarshipCard.tsx
│   │   │   ├── OrganizationList.tsx
│   │   │   └── EventCalendar.tsx
│   │   ├── registrar/               # Registrar-specific components
│   │   │   ├── DocumentRequestForm.tsx
│   │   │   ├── PaymentModal.tsx
│   │   │   └── DeliveryTracker.tsx
│   │   └── usg/                     # USG-specific components
│   │       ├── OfficerCard.tsx
│   │       ├── ResolutionList.tsx
│   │       └── AnnouncementBanner.tsx
│   ├── hooks/                       # Custom React hooks
│   │   ├── useAuth.ts
│   │   ├── usePermissions.ts
│   │   ├── useToast.ts
│   │   └── useDebounce.ts
│   ├── layouts/                     # Page layouts
│   │   ├── AuthLayout.tsx
│   │   ├── GuestLayout.tsx
│   │   ├── PublicLayout.tsx
│   │   ├── DashboardLayout.tsx
│   │   ├── SASLayout.tsx
│   │   ├── RegistrarLayout.tsx
│   │   └── USGLayout.tsx
│   ├── lib/                         # Utility functions
│   │   ├── utils.ts
│   │   ├── api.ts
│   │   ├── validation.ts
│   │   └── formatters.ts
│   ├── pages/                       # Inertia pages (routes)
│   │   ├── Auth/
│   │   │   ├── Login.tsx
│   │   │   ├── Register.tsx
│   │   │   └── ForgotPassword.tsx
│   │   ├── Dashboard.tsx
│   │   ├── SAS/
│   │   │   ├── Index.tsx
│   │   │   ├── Scholarships/
│   │   │   │   ├── Index.tsx
│   │   │   │   ├── Create.tsx
│   │   │   │   ├── Show.tsx
│   │   │   │   └── Edit.tsx
│   │   │   ├── Organizations/
│   │   │   │   ├── Index.tsx
│   │   │   │   ├── Create.tsx
│   │   │   │   └── Show.tsx
│   │   │   ├── Events/
│   │   │   │   ├── Index.tsx
│   │   │   │   ├── Create.tsx
│   │   │   │   └── Show.tsx
│   │   │   └── Insurance/
│   │   │       ├── Index.tsx
│   │   │       └── Create.tsx
│   │   ├── Registrar/
│   │   │   ├── Index.tsx
│   │   │   ├── DocumentRequests/
│   │   │   │   ├── Index.tsx
│   │   │   │   ├── Create.tsx
│   │   │   │   └── Show.tsx
│   │   │   ├── Payments/
│   │   │   │   └── Index.tsx
│   │   │   └── History/
│   │   │       └── Index.tsx
│   │   └── USG/
│   │       ├── Index.tsx
│   │       ├── Officers/
│   │       │   └── Index.tsx
│   │       ├── Resolutions/
│   │       │   ├── Index.tsx
│   │       │   └── Show.tsx
│   │       ├── Announcements/
│   │       │   ├── Index.tsx
│   │       │   └── Show.tsx
│   │       ├── VMGO/
│   │       │   └── Index.tsx
│   │       └── Calendar/
│   │           └── Index.tsx
│   ├── routes/                      # Frontend route definitions (Wayfinder)
│   ├── types/                       # TypeScript type definitions
│   │   ├── index.d.ts
│   │   ├── models.d.ts              # Model types matching backend
│   │   ├── api.d.ts
│   │   └── inertia.d.ts             # Inertia page props
│   └── wayfinder/                   # Generated type-safe routes
└── views/
    └── app.blade.php                # Inertia root template

routes/
├── web.php                          # Main web routes
├── auth.php                         # Authentication routes (Fortify)
├── settings.php                     # User settings routes
├── console.php                      # Artisan console routes
└── api.php                          # API routes (webhooks, mobile future)

database/
├── database.sqlite                  # Local SQLite database for development
├── factories/                       # Model factories for testing
│   ├── UserFactory.php
│   ├── SAS/
│   │   ├── ScholarshipFactory.php
│   │   ├── ScholarshipApplicationFactory.php
│   │   ├── EventFactory.php
│   │   └── OrganizationFactory.php
│   ├── Registrar/
│   │   ├── DocumentRequestFactory.php
│   │   └── TransactionFactory.php
│   └── USG/
│       ├── OfficerFactory.php
│       └── AnnouncementFactory.php
├── migrations/                      # Database migrations (chronological)
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2025_10_05_000001_create_permission_tables.php
│   ├── 2025_10_05_000002_create_sas_scholarships_table.php
│   ├── 2025_10_05_000003_create_sas_scholarship_applications_table.php
│   ├── 2025_10_05_000004_create_sas_events_table.php
│   ├── 2025_10_05_000005_create_sas_organizations_table.php
│   ├── 2025_10_05_000006_create_sas_insurance_records_table.php
│   ├── 2025_10_05_000007_create_registrar_document_types_table.php
│   ├── 2025_10_05_000008_create_registrar_document_requests_table.php
│   ├── 2025_10_05_000009_create_registrar_transactions_table.php
│   ├── 2025_10_05_000010_create_usg_officers_table.php
│   ├── 2025_10_05_000011_create_usg_announcements_table.php
│   ├── 2025_10_05_000012_create_usg_resolutions_table.php
│   └── 2025_10_05_000013_create_usg_vmgo_table.php
└── seeders/                         # Database seeders
    ├── DatabaseSeeder.php
    ├── RoleAndPermissionSeeder.php
    ├── SAS/
    │   ├── ScholarshipSeeder.php
    │   └── OrganizationSeeder.php
    ├── Registrar/
    │   └── DocumentTypeSeeder.php
    └── USG/
        └── VMGOSeeder.php

tests/
├── Feature/                         # Feature tests (integration)
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── RegistrationTest.php
│   ├── SAS/
│   │   ├── ScholarshipTest.php
│   │   ├── ScholarshipApplicationTest.php
│   │   ├── OrganizationTest.php
│   │   └── EventTest.php
│   ├── Registrar/
│   │   ├── DocumentRequestTest.php
│   │   ├── PaymentTest.php
│   │   └── DocumentGenerationTest.php
│   └── USG/
│       ├── AnnouncementTest.php
│       ├── ResolutionTest.php
│       └── CalendarTest.php
├── Unit/                            # Unit tests (isolated)
│   ├── Services/
│   │   ├── ScholarshipServiceTest.php
│   │   ├── DocumentGenerationServiceTest.php
│   │   └── PaymentServiceTest.php
│   └── Policies/
│       ├── ScholarshipPolicyTest.php
│       └── DocumentRequestPolicyTest.php
├── Pest.php                         # Pest configuration
└── TestCase.php                     # Base test case
```


## Domain Module Details

### SAS (Student Affairs Services) Module

**Focus:** Scholarships, insurance, student organizations, events

**Primary Users:** SAS Staff, SAS Admin, Students

**Key Features:**
- Scholarship application and approval workflows (TES, TDP, private scholarships)
- Organization registry (11 minor + 12 major organizations) with member tracking
- Event calendar management with automatic USG portal synchronization
- Insurance record submission and tracking (NSTP, ROTC)
- Document digitalization archive with metadata and retention policies
- Batch upload for scanning physical records

**Database Schema (Table Prefixing: `sas_`)**

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `sas_scholarships` | Scholarship master data | `type`, `amount`, `semester`, `academic_year` |
| `sas_scholarship_applications` | Student applications | `student_id`, `scholarship_id`, `status`, `application_date` |
| `sas_approval_workflows` | Multi-step approvals | `application_id`, `approver_id`, `status`, `comments` |
| `sas_events` | Event calendar (source of truth) | `title`, `date`, `location`, `category`, `organizer_id` |
| `sas_organizations` | Student organizations | `name`, `type` (minor/major), `advisor_id`, `status` |
| `sas_organization_members` | Membership tracking | `organization_id`, `student_id`, `position`, `joined_date` |
| `sas_insurance_records` | Insurance submissions | `student_id`, `policy_number`, `coverage_type`, `document_url` |
| `sas_digitalized_documents` | Scanned records | `original_type`, `file_url`, `metadata`, `retention_policy` |

**Business Logic Highlights:**
- **Scholarship Eligibility:** GPA >= 2.5, enrolled full-time, no failing grades
- **Approval Workflow:** Application → SAS Staff Review → SAS Admin Approval → Disbursement
- **Event Publishing:** Events automatically sync to USG portal via Laravel events
- **Retention Policies:** 7-10 year archival based on document type

### Registrar Module

**Focus:** Document requests, payment processing, delivery tracking

**Primary Users:** Registrar Staff, Students

**Key Features:**
- Document request forms (COG, COE, TOR, Diploma Copy, Good Moral Certificate)
- Paymongo payment integration (GCash, bank transfers, credit/debit cards)
- Automated PDF document generation using queue-based background jobs
- Real-time request status tracking (pending → payment → processing → ready → delivered)
- Email/SMS notifications for status updates
- Pickup and delivery options with tracking numbers

**Database Schema (Table Prefixing: `registrar_`)**

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `registrar_document_types` | Available documents | `code`, `name`, `price`, `processing_days` |
| `registrar_document_requests` | Student requests | `student_id`, `document_type_id`, `status`, `request_date` |
| `registrar_transactions` | Payment records | `request_id`, `amount`, `payment_method`, `paymongo_id`, `idempotency_key` |
| `registrar_document_deliveries` | Generated files | `request_id`, `file_url`, `generated_at`, `delivery_method` |
| `registrar_status_history` | Audit trail | `request_id`, `old_status`, `new_status`, `changed_by`, `timestamp` |

**Business Logic Highlights:**
- **Idempotent Payments:** `idempotency_key` prevents duplicate charges from retries
- **State Machine:** Strict status transitions (cannot skip from pending to ready)
- **Queue Priority:** Urgent requests (COE for scholarship applications) get high priority
- **Document Generation:** Background jobs process PDFs asynchronously (2-5 minutes)
- **Webhook Security:** Paymongo webhook signatures verified before processing

**Payment Flow:**
```
Student submits request → Redirect to Paymongo → 
Student pays via GCash/Bank → Paymongo webhook confirms → 
Queue PDF generation job → Worker generates PDF → 
Email notification sent → Student downloads/picks up
```

### USG (University Student Government) Portal Module

**Focus:** Public transparency and information dissemination for Mindoro State University - Bongabong Campus

**Primary Users:** Public visitors, students, USG officers, prospective students

**Key Features:**
- Vision, Mission, Goals, Objectives (VMGO) display with version tracking
- Officer directory with photos, positions, departments, and term information
- Public announcements with priority levels (urgent, normal, info) and expiration
- Resolutions archive with searchable PDF documents
- Public event calendar synchronized from SAS module (read-only)
- Campus-specific content (no multi-campus data aggregation needed)

**Database Schema (Table Prefixing: `usg_`)**

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `usg_vmgo` | VMGO content | `type` (vision/mission/goal/objective), `content`, `order` |
| `usg_officers` | Officer directory | `user_id`, `position`, `department`, `term_start`, `term_end`, `photo_url` |
| `usg_announcements` | Public notices | `title`, `content`, `priority`, `published_at`, `expires_at` |
| `usg_resolutions` | Resolution archive | `resolution_number`, `title`, `date_filed`, `status`, `document_url` |
| `usg_cached_events` | Event calendar cache | `event_id` (from SAS), `cached_data`, `updated_at` |

**Business Logic Highlights:**
- **Read-Heavy Optimization:** Extensive caching (5-15 minutes) for public pages
- **Event Synchronization:** Listens to SAS `EventCreated` event, caches locally for Bongabong campus
- **Content Expiration:** Announcements automatically hidden after expiration date
- **Search Functionality:** Full-text search on resolutions by number, title, date
- **Single Campus Focus:** All content is specific to Bongabong campus, no filtering or aggregation across campuses needed

**Integration Pattern:**
```
SAS creates event → Fires EventCreated → 
USG listener receives → Caches event data → 
Public calendar displays (no database join needed)
```


## Database Architecture

### Schema Isolation Strategy

Since MySQL doesn't natively support PostgreSQL-style schemas, we use **table prefixing** to create logical isolation:

```sql
-- Shared authentication domain
CREATE TABLE auth_users (...);
CREATE TABLE auth_students (...);
CREATE TABLE auth_roles (...);
CREATE TABLE auth_permissions (...);

-- SAS domain
CREATE TABLE sas_scholarships (...);
CREATE TABLE sas_events (...);
CREATE TABLE sas_organizations (...);

-- Registrar domain
CREATE TABLE registrar_document_requests (...);
CREATE TABLE registrar_transactions (...);

-- USG domain
CREATE TABLE usg_officers (...);
CREATE TABLE usg_announcements (...);
```

### Laravel Configuration

```php
// config/database.php
'connections' => [
    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'database' => env('DB_DATABASE', 'minsu_bongabong'),
        'prefix' => '', // No global prefix
    ],
    
    // Optional: Separate read replica for reports
    'mysql_read' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST_READ', '127.0.0.1'),
        'database' => env('DB_DATABASE', 'minsu_bongabong'),
        'prefix' => '',
    ],
];
```

### Eloquent Models with Table Assignment

```php
// Models specify their table explicitly
namespace App\Models\SAS;

class Scholarship extends Model
{
    protected $table = 'sas_scholarships'; // Explicit table name
    protected $connection = 'mysql';        // Connection (default)
    
    // Relationships can cross module boundaries when needed
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class);
    }
}
```

### Cross-Schema Query Rules

**Allowed:**
- Foreign keys can reference across prefixes (e.g., `sas_scholarships.student_id → auth_students.id`)
- Service layer can query multiple domains for orchestration
- Reports can join data from multiple modules

**Discouraged:**
- Direct Eloquent relationships across module boundaries (use services instead)
- Complex joins in controllers (move to service layer)

**Example: Proper Cross-Module Access**
```php
// ❌ BAD: Direct join in controller
$scholarships = Scholarship::with(['student', 'student.user'])->get();

// ✅ GOOD: Service layer orchestration
class ScholarshipReportService
{
    public function getScholarshipsWithStudentInfo(): Collection
    {
        $scholarships = Scholarship::all();
        $studentIds = $scholarships->pluck('student_id');
        $students = Student::whereIn('id', $studentIds)->get()->keyBy('id');
        
        return $scholarships->map(function ($scholarship) use ($students) {
            return [
                'scholarship' => $scholarship,
                'student' => $students[$scholarship->student_id] ?? null,
            ];
        });
    }
}
```

### Database Performance Optimizations

**Indexes:**
```sql
-- Foreign key indexes
CREATE INDEX idx_sas_scholarships_student_id ON sas_scholarships(student_id);
CREATE INDEX idx_registrar_requests_student_id ON registrar_document_requests(student_id);

-- Status filtering
CREATE INDEX idx_registrar_requests_status ON registrar_document_requests(status);
CREATE INDEX idx_sas_applications_status ON sas_scholarship_applications(status);

-- Date range queries
CREATE INDEX idx_sas_events_date ON sas_events(date);
CREATE INDEX idx_usg_announcements_published ON usg_announcements(published_at);

-- Composite indexes for common queries
CREATE INDEX idx_requests_student_status ON registrar_document_requests(student_id, status);
```

**Query Optimization:**
```php
// Prevent N+1 queries with eager loading
$requests = DocumentRequest::with(['student', 'documentType', 'transaction'])
    ->where('status', 'pending')
    ->get();

// Use select() to load only needed columns
$events = Event::select(['id', 'title', 'date', 'location'])
    ->where('date', '>=', now())
    ->orderBy('date')
    ->get();

// Chunk large datasets to avoid memory issues
Event::where('date', '<', now()->subYears(2))
    ->chunk(100, function ($events) {
        foreach ($events as $event) {
            // Process old events
        }
    });
```


## Integration Patterns

### 1. Event-Driven Communication

**Pattern:** Modules communicate asynchronously through Laravel's event system.

**Benefits:**
- **Loose Coupling:** Modules don't know about each other's existence
- **Scalability:** Events can be queued for async processing
- **Extensibility:** Add new listeners without modifying publishers
- **Testability:** Mock events in tests without complex setup

**Implementation:**

```php
// 1. Define event (SAS Module)
namespace App\Events\SAS;

use App\Models\SAS\Event;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCreated
{
    use Dispatchable, SerializesModels;
    
    public function __construct(
        public Event $event
    ) {}
}

// 2. Dispatch event when creating event
namespace App\Http\Controllers\SAS;

class EventController extends Controller
{
    public function store(EventCreationRequest $request)
    {
        $event = Event::create($request->validated());
        
        // Fire event - don't wait for listeners
        EventCreated::dispatch($event);
        
        return redirect()->route('sas.events.index')
            ->with('success', 'Event created successfully');
    }
}

// 3. Listen to event (USG Module)
namespace App\Listeners\USG;

use App\Events\SAS\EventCreated;
use Illuminate\Support\Facades\Cache;

class SyncEventToPublicCalendar
{
    public function handle(EventCreated $event): void
    {
        // Cache event for public calendar
        Cache::put(
            "usg:calendar:event:{$event->event->id}",
            [
                'id' => $event->event->id,
                'title' => $event->event->title,
                'date' => $event->event->date,
                'location' => $event->event->location,
                'category' => $event->event->category,
            ],
            now()->addHours(24)
        );
        
        // Invalidate calendar cache to force refresh
        Cache::forget('usg:calendar:all');
    }
}

// 4. Register listener (EventServiceProvider)
protected $listen = [
    \App\Events\SAS\EventCreated::class => [
        \App\Listeners\USG\SyncEventToPublicCalendar::class,
        \App\Listeners\SAS\SendEventNotificationEmail::class, // Multiple listeners OK
    ],
    \App\Events\Registrar\PaymentConfirmed::class => [
        \App\Listeners\Registrar\QueueDocumentGeneration::class,
    ],
];
```

**Other Events in the System:**

| Event | Publisher | Listeners | Purpose |
|-------|-----------|-----------|---------|
| `EventCreated` | SAS | USG Calendar Sync | Publish event to public portal |
| `EventUpdated` | SAS | USG Calendar Sync | Update cached event data |
| `ScholarshipApproved` | SAS | Email Notification | Notify student of approval |
| `ScholarshipRejected` | SAS | Email Notification | Notify student with reason |
| `PaymentConfirmed` | Registrar | Document Generation | Trigger PDF creation |
| `DocumentReady` | Registrar | Email/SMS Notification | Notify student document is ready |

### 2. Service Layer Pattern

**Pattern:** Business logic extracted into dedicated service classes.

**Benefits:**
- **Reusability:** Services used by multiple controllers
- **Testability:** Easy to unit test without HTTP layer
- **Transaction Management:** Services handle database transactions
- **Fat Models Prevention:** Keep models focused on data, services on logic

**Implementation:**

```php
// Service class
namespace App\Services\Registrar;

use App\Models\Registrar\DocumentRequest;
use App\Models\Registrar\Transaction;
use App\Services\Registrar\PaymongoService;
use App\Events\Registrar\PaymentConfirmed;
use Illuminate\Support\Facades\DB;

class DocumentRequestService
{
    public function __construct(
        private PaymongoService $paymongo
    ) {}
    
    public function createRequest(int $studentId, string $documentType): DocumentRequest
    {
        return DB::transaction(function () use ($studentId, $documentType) {
            // Create request
            $request = DocumentRequest::create([
                'student_id' => $studentId,
                'document_type_id' => $documentType,
                'status' => 'pending_payment',
                'request_date' => now(),
            ]);
            
            // Create payment intent
            $paymentIntent = $this->paymongo->createPaymentIntent(
                $request->id,
                $request->documentType->price
            );
            
            // Record transaction
            Transaction::create([
                'request_id' => $request->id,
                'amount' => $request->documentType->price,
                'payment_method' => 'pending',
                'paymongo_id' => $paymentIntent['data']['id'],
                'idempotency_key' => uniqid('req_', true),
                'status' => 'pending',
            ]);
            
            return $request->load('transaction');
        });
    }
    
    public function confirmPayment(string $paymongoId, array $webhookData): void
    {
        DB::transaction(function () use ($paymongoId, $webhookData) {
            $transaction = Transaction::where('paymongo_id', $paymongoId)->firstOrFail();
            
            // Update transaction
            $transaction->update([
                'status' => 'completed',
                'payment_method' => $webhookData['attributes']['source']['type'],
                'payment_reference' => $webhookData['attributes']['source']['id'],
                'paid_at' => now(),
            ]);
            
            // Update request status
            $transaction->request->update(['status' => 'payment_confirmed']);
            
            // Fire event to trigger document generation
            PaymentConfirmed::dispatch($transaction->request);
        });
    }
}

// Controller uses service
namespace App\Http\Controllers\Registrar;

class DocumentRequestController extends Controller
{
    public function __construct(
        private DocumentRequestService $service
    ) {}
    
    public function store(DocumentRequestRequest $request)
    {
        $documentRequest = $this->service->createRequest(
            auth()->user()->student->id,
            $request->document_type_id
        );
        
        return redirect()->route('registrar.payment', $documentRequest);
    }
}
```

### 3. Queue-Based Async Processing

**Pattern:** Long-running tasks processed in background workers.

**Benefits:**
- **Responsiveness:** HTTP responses return immediately
- **Resource Management:** CPU-intensive tasks don't block web requests
- **Retry Logic:** Failed jobs automatically retried
- **Scalability:** Add more workers to handle load

**Implementation:**

```php
// Job definition
namespace App\Jobs\Registrar;

use App\Models\Registrar\DocumentRequest;
use App\Services\Registrar\PDFGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateTranscript implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;      // Retry 3 times on failure
    public $timeout = 300;  // 5 minute timeout
    
    public function __construct(
        public DocumentRequest $request
    ) {}
    
    public function handle(PDFGenerationService $pdfService): void
    {
        // Generate PDF (CPU-intensive operation)
        $pdfPath = $pdfService->generateTranscript(
            $this->request->student,
            $this->request->student->grades
        );
        
        // Update request with file
        $this->request->update([
            'status' => 'ready_for_pickup',
            'document_url' => $pdfPath,
            'generated_at' => now(),
        ]);
        
        // Fire event for notification
        DocumentReady::dispatch($this->request);
    }
    
    public function failed(\Throwable $exception): void
    {
        // Handle failure (log, notify admin, etc.)
        $this->request->update([
            'status' => 'generation_failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}

// Dispatch job from listener
namespace App\Listeners\Registrar;

use App\Events\Registrar\PaymentConfirmed;
use App\Jobs\Registrar\GenerateTranscript;

class QueueDocumentGeneration
{
    public function handle(PaymentConfirmed $event): void
    {
        $request = $event->request;
        
        // Dispatch appropriate job based on document type
        match ($request->documentType->code) {
            'TOR' => GenerateTranscript::dispatch($request)
                ->onQueue('documents')
                ->delay(now()->addMinutes(2)), // Wait for payment settlement
            'COE' => GenerateCertificate::dispatch($request)
                ->onQueue('documents'),
            'COG' => GenerateGrades::dispatch($request)
                ->onQueue('documents'),
            default => null,
        };
    }
}
```

**Queue Configuration:**

```php
// config/queue.php
'connections' => [
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
    ],
    
    'redis' => [ // Production upgrade path
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for' => null,
    ],
],

'queues' => [
    'default' => 10,      // Low priority
    'documents' => 5,     // Medium priority (PDF generation)
    'notifications' => 1, // High priority (emails/SMS)
],
```

**Running Workers:**

```bash
# Development (single worker)
php artisan queue:work --queue=notifications,documents,default

# Production (Supervisor configuration)
# /etc/supervisor/conf.d/laravel-worker.conf
[program:laravel-worker]
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
numprocs=4
autostart=true
autorestart=true
```

### 4. Webhook Integration Pattern

**Pattern:** External services (Paymongo) notify us of events via HTTP callbacks.

**Implementation:**

```php
// Webhook controller
namespace App\Http\Controllers\Webhooks;

use App\Services\Registrar\PaymongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymongoWebhookController extends Controller
{
    public function __construct(
        private PaymongoService $paymongo
    ) {}
    
    public function handle(Request $request)
    {
        // 1. Verify webhook signature (CRITICAL for security)
        $signature = $request->header('Paymongo-Signature');
        $payload = $request->getContent();
        
        if (!$this->paymongo->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Paymongo webhook signature', [
                'ip' => $request->ip(),
                'payload' => $payload,
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        
        // 2. Extract event data
        $event = $request->input('data');
        $eventType = $event['attributes']['type'];
        
        // 3. Handle different event types
        match ($eventType) {
            'payment.paid' => $this->handlePaymentPaid($event),
            'payment.failed' => $this->handlePaymentFailed($event),
            'source.chargeable' => $this->handleSourceChargeable($event),
            default => Log::info('Unhandled Paymongo event', ['type' => $eventType]),
        };
        
        // 4. Always return 200 OK to acknowledge receipt
        return response()->json(['received' => true], 200);
    }
    
    private function handlePaymentPaid(array $event): void
    {
        $paymentIntentId = $event['attributes']['data']['id'];
        
        // Process payment confirmation
        $this->paymongo->confirmPayment($paymentIntentId, $event);
    }
}
```

**Security Measures:**
- Signature verification prevents replay attacks
- IP whitelisting (if Paymongo provides static IPs)
- Rate limiting on webhook endpoint
- Idempotency: Handle duplicate webhooks gracefully

### 5. Caching Strategy

**Pattern:** Cache expensive operations to improve performance.

**Cache Keys Convention:**
```
{module}:{entity}:{identifier}:{detail}

Examples:
usg:calendar:event:123
usg:calendar:all
registrar:request:456:status
sas:scholarships:student:789
```

**Implementation:**

```php
// Cache with tag support (Redis only)
Cache::tags(['usg', 'calendar'])->put('all_events', $events, 900); // 15 minutes

// Simple cache (works with database driver)
Cache::remember('usg:calendar:all', 900, function () {
    return Event::where('date', '>=', now())
        ->where('status', 'published')
        ->orderBy('date')
        ->get();
});

// Cache invalidation
Cache::forget('usg:calendar:all');
Cache::tags(['usg', 'calendar'])->flush(); // Redis only
```

**Caching Rules:**
- **USG Public Pages:** 5-15 minutes (read-heavy, rarely changes)
- **Student Dashboards:** 5 minutes (balance freshness vs performance)
- **Admin Panels:** No caching (always fresh data)
- **API Responses:** 1-5 minutes (mobile app consumption)


## Frontend Architecture

### Inertia.js Pattern

**Overview:** Inertia provides a SPA experience without building a separate API. Server-side routing with client-side rendering.

**How it Works:**

1. **Server Side:** Controllers return Inertia responses instead of Blade views
```php
// Laravel Controller
return Inertia::render('SAS/Scholarships/Index', [
    'scholarships' => Scholarship::paginate(20),
    'filters' => $request->only('search', 'status'),
]);
```

2. **Client Side:** React components receive props and render
```typescript
// React Component
import { Head } from '@inertiajs/react';

interface Props {
    scholarships: PaginatedData<Scholarship>;
    filters: { search?: string; status?: string };
}

export default function Index({ scholarships, filters }: Props) {
    return (
        <>
            <Head title="Scholarships" />
            <h1>Scholarships</h1>
            {/* Render scholarships */}
        </>
    );
}
```

3. **Navigation:** Uses Inertia's `router` for SPA-like transitions
```typescript
import { router } from '@inertiajs/react';

// Navigate programmatically
router.visit('/sas/scholarships');

// Or use Link component
<Link href="/sas/scholarships">View Scholarships</Link>
```

### Type Safety Strategy

**Shared Types:**

```typescript
// resources/js/types/models.d.ts
export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    roles: Role[];
}

export interface Student extends User {
    student_id: string;
    program: string;
    year_level: number;
    enrollment_status: 'enrolled' | 'graduated' | 'dropped';
}

export interface Scholarship {
    id: number;
    student_id: number;
    type: 'TES' | 'TDP' | 'Private';
    amount: number;
    semester: string;
    status: 'pending' | 'approved' | 'rejected' | 'disbursed';
    application_date: string;
    student?: Student; // Eager loaded relationship
}

export interface PaginatedData<T> {
    data: T[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}
```

**Page Props:**

```typescript
// resources/js/types/inertia.d.ts
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import { User } from './models';

export interface PageProps extends InertiaPageProps {
    auth: {
        user: User;
    };
    flash: {
        success?: string;
        error?: string;
        info?: string;
    };
    errors: Record<string, string>;
}
```

### Component Architecture

**Layout System:**

```typescript
// resources/js/layouts/AuthenticatedLayout.tsx
import { PropsWithChildren } from 'react';
import { User } from '@/types/models';
import Navbar from '@/components/shared/Navbar';
import Sidebar from '@/components/shared/Sidebar';

interface Props extends PropsWithChildren {
    user: User;
    header?: string;
}

export default function AuthenticatedLayout({ user, header, children }: Props) {
    return (
        <div className="min-h-screen bg-gray-50">
            <Navbar user={user} />
            <div className="flex">
                <Sidebar user={user} />
                <main className="flex-1 p-6">
                    {header && <h1 className="text-2xl font-bold mb-6">{header}</h1>}
                    {children}
                </main>
            </div>
        </div>
    );
}
```

**Shared Components (shadcn/ui):**

```typescript
// resources/js/components/ui/button.tsx
import * as React from 'react';
import { Slot } from '@radix-ui/react-slot';
import { cva, type VariantProps } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const buttonVariants = cva(
    'inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors',
    {
        variants: {
            variant: {
                default: 'bg-blue-600 text-white hover:bg-blue-700',
                destructive: 'bg-red-600 text-white hover:bg-red-700',
                outline: 'border border-gray-300 bg-white hover:bg-gray-50',
                ghost: 'hover:bg-gray-100',
            },
            size: {
                default: 'h-10 px-4 py-2',
                sm: 'h-9 px-3',
                lg: 'h-11 px-8',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'default',
        },
    }
);

export interface ButtonProps
    extends React.ButtonHTMLAttributes<HTMLButtonElement>,
        VariantProps<typeof buttonVariants> {
    asChild?: boolean;
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
    ({ className, variant, size, asChild = false, ...props }, ref) => {
        const Comp = asChild ? Slot : 'button';
        return (
            <Comp
                className={cn(buttonVariants({ variant, size, className }))}
                ref={ref}
                {...props}
            />
        );
    }
);

export { Button, buttonVariants };
```

**Form Handling:**

```typescript
// Example: Scholarship application form
import { useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export default function ScholarshipApplication() {
    const { data, setData, post, processing, errors } = useForm({
        scholarship_type: '',
        gpa: '',
        statement: '',
    });
    
    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post('/sas/scholarships/apply');
    };
    
    return (
        <form onSubmit={submit}>
            <div>
                <Label htmlFor="scholarship_type">Scholarship Type</Label>
                <select
                    id="scholarship_type"
                    value={data.scholarship_type}
                    onChange={(e) => setData('scholarship_type', e.target.value)}
                >
                    <option value="">Select...</option>
                    <option value="TES">TES</option>
                    <option value="TDP">TDP</option>
                </select>
                {errors.scholarship_type && (
                    <p className="text-sm text-red-600">{errors.scholarship_type}</p>
                )}
            </div>
            
            <div>
                <Label htmlFor="gpa">GPA</Label>
                <Input
                    id="gpa"
                    type="number"
                    step="0.01"
                    value={data.gpa}
                    onChange={(e) => setData('gpa', e.target.value)}
                />
                {errors.gpa && <p className="text-sm text-red-600">{errors.gpa}</p>}
            </div>
            
            <Button type="submit" disabled={processing}>
                {processing ? 'Submitting...' : 'Submit Application'}
            </Button>
        </form>
    );
}
```

### State Management

**Inertia Form State:**
- Form data, errors, processing state managed by `useForm` hook
- No need for Redux/Zustand for form handling

**Local Component State:**
- `useState` for UI state (modals, dropdowns, filters)
- `useEffect` for side effects (data fetching from APIs if needed)

**Global State (Minimal):**
- User auth state provided via Inertia shared props
- Flash messages passed via Inertia responses

### Asset Building (Vite)

**Configuration:**

```typescript
// vite.config.ts
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
});
```

**Build Commands:**
```bash
npm run dev         # Development with HMR
npm run build       # Production build
npm run build:ssr   # SSR build for server-side rendering
```


## Security Architecture

### Authentication System

**Laravel Fortify** provides headless authentication backend:

```php
// config/fortify.php
'features' => [
    Features::registration(),
    Features::resetPasswords(),
    Features::emailVerification(),
    Features::updateProfileInformation(),
    Features::updatePasswords(),
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

**Two-Factor Authentication Flow:**
1. User enables 2FA in settings
2. System generates QR code (TOTP)
3. User scans with authenticator app
4. User provides 6-digit code to confirm
5. Recovery codes generated and displayed once
6. Future logins require 2FA code

### Authorization (RBAC)

> **📚 Complete Documentation:** See [RBAC.md](./RBAC.md) for comprehensive role-based access control documentation including permissions matrix, policy examples, frontend authorization, and testing guide.

**Package:** Spatie Laravel Permission v6.21.0

**8 User Roles:**

| Role | Permission Count | Module Access | Description |
|------|-----------------|---------------|-------------|
| **Student** | 16 | All modules (limited) | View own data, submit applications/requests |
| **SAS Staff** | 18 | SAS module | Review scholarships, manage organizations, events |
| **SAS Admin** | 23 | SAS module (full) | Approve high-value scholarships (≥₱20k), reports |
| **Registrar Staff** | 8 | Registrar module | Process document requests, verify payments |
| **Registrar Admin** | 11 | Registrar module (full) | Manage enrollment, issue refunds, edit records |
| **USG Officer** | 14 | USG module | Create announcements/resolutions, respond to feedback |
| **USG Admin** | 24 | USG module (full) | Manage VMGO, officers, publish content |
| **System Admin** | 79 (all) | All modules | Full system access, user/role management |

**Permission Examples:**
- `submit_scholarship_application`
- `approve_scholarships_under_20k`
- `approve_scholarships_over_20k` (SAS Admin only)
- `process_document_requests`
- `issue_refunds` (Registrar Admin only)
- `manage_vmgo` (USG Admin only)
- `manage_users` (System Admin only)

**Type-Safe Enums:**

```php
// app/Enums/Role.php
enum Role: string
{
    case Student = 'student';
    case SasStaff = 'sas_staff';
    case SasAdmin = 'sas_admin';
    case RegistrarStaff = 'registrar_staff';
    case RegistrarAdmin = 'registrar_admin';
    case UsgOfficer = 'usg_officer';
    case UsgAdmin = 'usg_admin';
    case SystemAdmin = 'system_admin';
}

// app/Enums/Permission.php
enum Permission: string
{
    // SAS Permissions
    case SubmitScholarshipApplication = 'submit_scholarship_application';
    case ApproveScholarshipsUnder20k = 'approve_scholarships_under_20k';
    case ApproveScholarshipsOver20k = 'approve_scholarships_over_20k';
    // ... 76 more permissions
}
```

**Policy-Based Authorization with Business Logic:**

```php
// app/Policies/SAS/ScholarshipPolicy.php
namespace App\Policies\SAS;

use App\Models\User;
use App\Enums\Permission;

class ScholarshipPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['sas_staff', 'sas_admin', 'system_admin']);
    }
    
    public function view(User $user, $scholarship): bool
    {
        // Students can view their own applications
        if ($user->id === $scholarship->student_id) {
            return true;
        }
        
        // Staff can view all
        return $user->hasAnyRole(['sas_staff', 'sas_admin', 'system_admin']);
    }
    
    /**
     * Amount-based approval authorization
     * Business Rule: <₱20k = sas_staff, ≥₱20k = sas_admin only
     */
    public function approve(User $user, $scholarship): bool
    {
        $amount = $scholarship->amount;
        
        // Under ₱20,000 - either staff or admin
        if ($amount < 20000) {
            return $user->can(Permission::ApproveScholarshipsUnder20k->value);
        }
        
        // ≥ ₱20,000 - only admin
        return $user->can(Permission::ApproveScholarshipsOver20k->value);
    }
}

// Usage in controller
public function approve(Request $request, Scholarship $scholarship)
{
    $this->authorize('approve', $scholarship);
    
    $scholarship->update(['status' => 'approved', 'approved_by' => $request->user()->id]);
    
    return back()->with('success', 'Scholarship approved');
}
```

**Route Protection with Middleware:**

```php
// routes/sas.php - Role-based protection
Route::middleware(['auth', 'verified', 'role:sas_staff|sas_admin'])->group(function () {
    Route::get('sas/dashboard', [DashboardController::class, 'index'])->name('sas.dashboard');
    
    // Permission-based protection
    Route::patch('sas/scholarships/{scholarship}/approve', [ScholarshipController::class, 'approve'])
        ->middleware('permission:approve_scholarships_under_20k|approve_scholarships_over_20k')
        ->name('sas.scholarships.approve');
});

// routes/admin.php - System admin only
Route::middleware(['auth', 'verified', 'role:system_admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [UserController::class, 'index'])
            ->middleware('permission:manage_users')
            ->name('users.index');
    });
});
```

**Frontend Authorization (Inertia + React):**

```typescript
// resources/js/hooks/use-permissions.ts
export function usePermissions() {
    const { auth } = usePage<SharedData>().props;
    
    const hasRole = (role: string) => auth.roles.includes(role);
    const hasPermission = (permission: string) => auth.permissions.includes(permission);
    const can = (permission: string) => hasPermission(permission);
    
    // Convenience methods
    const isSasAdmin = () => hasRole('sas_admin');
    const isSystemAdmin = () => hasRole('system_admin');
    
    return { hasRole, hasPermission, can, isSasAdmin, isSystemAdmin };
}

// Usage in component
import { Can, HasRole } from '@/components/authorization';

function ScholarshipCard({ scholarship }) {
    const { can, isSasAdmin } = usePermissions();
    
    return (
        <div>
            <h2>{scholarship.title}</h2>
            
            {/* Conditional rendering with hook */}
            {can('approve_scholarships_over_20k') && <ApprovalButton />}
            
            {/* Or use component */}
            <Can permission="approve_scholarships_over_20k">
                <ApprovalButton />
            </Can>
            
            <HasRole role={['sas_staff', 'sas_admin']}>
                <StaffControls />
            </HasRole>
        </div>
    );
}
```

**Testing Authorization:**

```php
// tests/Unit/Policies/ScholarshipPolicyTest.php
it('allows sas staff to approve scholarships under 20k', function () {
    $staff = User::factory()->sasStaff()->create();
    $scholarship = (object) ['amount' => 15000];
    $policy = new ScholarshipPolicy;
    
    expect($policy->approve($staff, $scholarship))->toBeTrue();
});

it('denies sas staff from approving scholarships 20k and above', function () {
    $staff = User::factory()->sasStaff()->create();
    $scholarship = (object) ['amount' => 20000];
    $policy = new ScholarshipPolicy;
    
    expect($policy->approve($staff, $scholarship))->toBeFalse();
});

// tests/Feature/MiddlewareProtectionTest.php
it('allows sas admin to access reports', function () {
    $admin = User::factory()->sasAdmin()->create();
    
    $response = $this->actingAs($admin)->get('/sas/reports/scholarships');
    
    $response->assertSuccessful();
});
```

**Super Admin Pattern:**

System administrators have access to all permissions:

```php
// In app/Providers/AppServiceProvider.php
Gate::before(function (User $user, string $ability) {
    return $user->hasRole('system_admin') ? true : null;
});
```
```

### Input Validation

**Form Request Validation:**

```php
namespace App\Http\Requests\Registrar;

use Illuminate\Foundation\Http\FormRequest;

class DocumentRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->student !== null;
    }
    
    public function rules(): array
    {
        return [
            'document_type_id' => ['required', 'exists:registrar_document_types,id'],
            'purpose' => ['required', 'string', 'max:500'],
            'delivery_method' => ['required', 'in:pickup,delivery'],
            'delivery_address' => ['required_if:delivery_method,delivery', 'string', 'max:255'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'document_type_id.required' => 'Please select a document type.',
            'document_type_id.exists' => 'Invalid document type selected.',
            'delivery_address.required_if' => 'Delivery address is required for delivery option.',
        ];
    }
}
```

### CSRF Protection

**Automatic Protection:**
- All POST/PUT/PATCH/DELETE requests require CSRF token
- Inertia.js automatically includes token in headers
- Exempt webhooks that use signature verification instead

```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'webhooks/paymongo', // Verified via signature instead
];
```

### XSS Prevention

**React Auto-Escaping:**
```typescript
// Safe - React escapes by default
<p>{user.name}</p>

// Dangerous - only use for trusted content
<div dangerouslySetInnerHTML={{ __html: htmlContent }} />
```

**Backend Escaping:**
```php
// Blade (if used)
{{ $variable }} // Escaped
{!! $variable !!} // Raw (dangerous)

// Eloquent casts
protected $casts = [
    'content' => 'string', // Always treated as string, not HTML
];
```

### SQL Injection Prevention

**Eloquent ORM:**
```php
// ✅ SAFE - Parameterized query
User::where('email', $request->input('email'))->first();

// ✅ SAFE - Parameter binding
DB::select('SELECT * FROM users WHERE email = ?', [$email]);

// ❌ DANGEROUS - Never do this
DB::select("SELECT * FROM users WHERE email = '$email'");
```

### Rate Limiting

**API Routes:**

```php
// routes/api.php
Route::middleware(['throttle:api'])->group(function () {
    // 60 requests per minute per user
});

// Custom rate limits
Route::middleware(['throttle:registrar'])->group(function () {
    // 10 document requests per hour per student
});

// config/routing.php - Rate limiters
RateLimiter::for('registrar', function (Request $request) {
    return $request->user()
        ? Limit::perHour(10)->by($request->user()->id)
        : Limit::perHour(5)->by($request->ip());
});
```

**Login Attempts:**

```php
// Fortify automatically rate limits login attempts
// 5 attempts per minute by email + IP
```

### File Upload Security

**Validation:**

```php
public function rules(): array
{
    return [
        'document' => [
            'required',
            'file',
            'mimes:pdf,jpg,jpeg,png',
            'max:5120', // 5MB
        ],
        'photo' => [
            'required',
            'image',
            'dimensions:min_width=200,min_height=200',
            'max:2048', // 2MB
        ],
    ];
}
```

**Storage:**

```php
// Store with unique filename
$path = $request->file('document')->store('documents', 'private');

// Generate URL with expiration
$url = Storage::disk('private')->temporaryUrl(
    $path,
    now()->addMinutes(5)
);
```

### Audit Logging

**Critical Operations Logged:**

```php
namespace App\Services\SAS;

use Illuminate\Support\Facades\Log;

class ScholarshipService
{
    public function approve(ScholarshipApplication $application, User $approver): void
    {
        $application->update(['status' => 'approved']);
        
        // Audit log
        Log::channel('audit')->info('Scholarship approved', [
            'application_id' => $application->id,
            'student_id' => $application->student_id,
            'approver_id' => $approver->id,
            'approver_name' => $approver->name,
            'amount' => $application->scholarship->amount,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
```

**Audit Trail Table:**

```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    action VARCHAR(255),
    auditable_type VARCHAR(255),
    auditable_id BIGINT UNSIGNED,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP,
    INDEX idx_auditable (auditable_type, auditable_id),
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
);
```

### HTTPS Enforcement

**Production Configuration:**

```php
// app/Http/Middleware/TrustProxies.php
protected $proxies = '*'; // For load balancers

protected $headers = Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO;

// Force HTTPS in production
if (app()->environment('production')) {
    URL::forceScheme('https');
}
```

### Webhook Security

**Signature Verification:**

```php
namespace App\Services\Registrar;

class PaymongoService
{
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        $secret = config('services.paymongo.webhook_secret');
        
        // Compute expected signature
        $computed = hash_hmac('sha256', $payload, $secret);
        
        // Timing-safe comparison
        return hash_equals($computed, $signature);
    }
}
```

### Session Security

```php
// config/session.php
'lifetime' => 120, // 2 hours
'expire_on_close' => false,
'secure' => env('SESSION_SECURE_COOKIE', true), // HTTPS only in production
'http_only' => true, // Prevent JavaScript access
'same_site' => 'lax', // CSRF protection
```


## Testing Strategy

### Test Pyramid

```
         /\
        /  \       E2E Tests (10%)
       /────\      - Critical user journeys
      /      \     - Browser automation
     /────────\    Integration Tests (20%)
    /          \   - API endpoints
   /────────────\  - Cross-module events
  /              \ Unit Tests (70%)
 /________________\- Services, models, utilities
```

### Unit Tests (Pest)

**Testing Services:**

```php
// tests/Unit/Services/ScholarshipServiceTest.php
use App\Services\SAS\ScholarshipService;
use App\Models\SAS\Scholarship;
use App\Models\Student;

describe('ScholarshipService', function () {
    it('determines eligibility correctly', function () {
        $service = new ScholarshipService();
        $student = Student::factory()->create(['gpa' => 3.5]);
        
        expect($service->isEligible($student))->toBeTrue();
    });
    
    it('rejects students with low GPA', function () {
        $service = new ScholarshipService();
        $student = Student::factory()->create(['gpa' => 2.0]);
        
        expect($service->isEligible($student))->toBeFalse();
    });
});
```

**Testing Models:**

```php
// tests/Unit/Models/DocumentRequestTest.php
use App\Models\Registrar\DocumentRequest;

it('calculates processing time correctly', function () {
    $request = DocumentRequest::factory()->create([
        'request_date' => now()->subDays(3),
        'generated_at' => now(),
    ]);
    
    expect($request->processing_days)->toBe(3);
});

it('has correct status transitions', function () {
    $request = DocumentRequest::factory()->create(['status' => 'pending_payment']);
    
    expect($request->canTransitionTo('payment_confirmed'))->toBeTrue();
    expect($request->canTransitionTo('ready_for_pickup'))->toBeFalse();
});
```

### Feature Tests

**Testing Controllers:**

```php
// tests/Feature/Registrar/DocumentRequestTest.php
use App\Models\User;
use App\Models\Registrar\DocumentType;

it('allows enrolled students to request documents', function () {
    $student = User::factory()->student()->create();
    $documentType = DocumentType::factory()->create(['code' => 'COE']);
    
    $response = $this->actingAs($student)
        ->post('/registrar/requests', [
            'document_type_id' => $documentType->id,
            'purpose' => 'Scholarship application',
            'delivery_method' => 'pickup',
        ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('registrar_document_requests', [
        'student_id' => $student->student->id,
        'document_type_id' => $documentType->id,
        'status' => 'pending_payment',
    ]);
});

it('prevents non-students from requesting documents', function () {
    $user = User::factory()->create(); // No student profile
    $documentType = DocumentType::factory()->create();
    
    $response = $this->actingAs($user)
        ->post('/registrar/requests', [
            'document_type_id' => $documentType->id,
        ]);
    
    $response->assertForbidden();
});
```

**Testing Events:**

```php
// tests/Feature/SAS/EventPublishingTest.php
use App\Models\SAS\Event;
use App\Events\SAS\EventCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;

it('publishes events to USG calendar', function () {
    Event::fake();
    
    $event = Event::factory()->create([
        'title' => 'Campus Festival',
        'date' => now()->addWeek(),
    ]);
    
    EventCreated::dispatch($event);
    
    Event::assertDispatched(EventCreated::class, function ($e) use ($event) {
        return $e->event->id === $event->id;
    });
});

it('caches events in USG module', function () {
    $event = Event::factory()->create();
    
    EventCreated::dispatch($event);
    
    expect(Cache::has("usg:calendar:event:{$event->id}"))->toBeTrue();
});
```

### Integration Tests

**Testing Payment Flow:**

```php
// tests/Feature/Registrar/PaymentFlowTest.php
use App\Models\Registrar\DocumentRequest;
use App\Jobs\Registrar\GenerateTranscript;
use Illuminate\Support\Facades\Queue;

it('generates document after payment confirmation', function () {
    Queue::fake();
    
    $request = DocumentRequest::factory()->create([
        'status' => 'pending_payment',
    ]);
    
    // Simulate payment webhook
    $this->post('/webhooks/paymongo', [
        'data' => [
            'id' => 'payment_123',
            'attributes' => [
                'type' => 'payment.paid',
                'data' => ['id' => $request->transaction->paymongo_id],
                'source' => ['type' => 'gcash'],
            ],
        ],
    ], [
        'Paymongo-Signature' => 'valid_signature',
    ]);
    
    Queue::assertPushed(GenerateTranscript::class, function ($job) use ($request) {
        return $job->request->id === $request->id;
    });
});
```

### E2E Tests (Playwright/Dusk)

```php
// tests/Browser/ScholarshipApplicationTest.php
use Laravel\Dusk\Browser;

it('allows students to apply for scholarships', function () {
    $this->browse(function (Browser $browser) {
        $browser->loginAs(User::factory()->student()->create())
                ->visit('/sas/scholarships')
                ->clickLink('Apply for Scholarship')
                ->select('scholarship_type', 'TES')
                ->type('gpa', '3.5')
                ->type('statement', 'I am a deserving student...')
                ->attach('supporting_documents', storage_path('test-files/transcript.pdf'))
                ->press('Submit Application')
                ->assertSee('Application submitted successfully');
    });
});
```

### Test Coverage

**Running Tests:**

```bash
# All tests
php artisan test

# With coverage report
php artisan test --coverage --min=70

# Specific suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Specific file
php artisan test tests/Feature/Registrar/DocumentRequestTest.php

# Filter by name
php artisan test --filter=scholarship
```

**Coverage Goals:**
- **Overall:** >70%
- **Services:** >85% (business logic)
- **Controllers:** >75% (feature tests)
- **Models:** >80% (unit tests)

## Performance Optimization

### Database Query Optimization

**N+1 Query Prevention:**

```php
// ❌ BAD - N+1 queries
$requests = DocumentRequest::all();
foreach ($requests as $request) {
    echo $request->student->name; // Separate query each iteration
}

// ✅ GOOD - Eager loading
$requests = DocumentRequest::with('student')->get();
foreach ($requests as $request) {
    echo $request->student->name; // Already loaded
}
```

**Query Monitoring:**

```php
// Enable query log in development
if (app()->environment('local')) {
    DB::listen(function ($query) {
        if ($query->time > 100) { // Log slow queries (>100ms)
            Log::warning('Slow query detected', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ]);
        }
    });
}
```

### Caching Strategy

**Query Result Caching:**

```php
// Cache expensive queries
$topScholarships = Cache::remember('sas:scholarships:top', 3600, function () {
    return Scholarship::with('student')
        ->where('amount', '>', 50000)
        ->orderBy('amount', 'desc')
        ->limit(10)
        ->get();
});
```

**View Caching:**

```php
// Cache rendered views
Route::get('/usg/home', function () {
    return Cache::remember('usg:home:html', 900, function () {
        return view('usg.home')->render();
    });
});
```

### Asset Optimization

**Vite Production Build:**
- Automatic code splitting
- Tree shaking (removes unused code)
- Minification (HTML/CSS/JS)
- Image optimization

```bash
npm run build  # Optimized production assets
```

**Image Optimization:**

```php
// Intervention Image for resizing/compression
use Intervention\Image\Facades\Image;

$image = Image::make($request->file('photo'))
    ->resize(800, 600, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    })
    ->encode('jpg', 85); // 85% quality

Storage::put('photos/' . $filename, $image);
```

### Performance Targets (from NFR.md)

| Metric | Target | Measurement | Notes |
|--------|--------|-------------|-------|
| **Page Load Time** | <2s (95th percentile) | Lighthouse, Web Vitals | Single campus = lower latency |
| **API Response Time** | <200ms (avg) | Laravel Telescope | No cross-site queries |
| **Time to First Byte (TTFB)** | <500ms | Server monitoring | Direct server access |
| **Concurrent Users** | 500 simultaneous | Load testing (k6) | ~5,000 total campus users |
| **Database Queries** | <50 per page | Query log analysis | Single database, no federation |

**Single Campus Performance Advantages:**
- No network latency between campuses
- No distributed query coordination
- Simpler caching strategy (no cache invalidation across sites)
- Predictable load patterns (academic hours: 7 AM - 9 PM PHT)

### Performance Monitoring

**Laravel Telescope (Development):**
```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Production Monitoring:**
- **Application:** Sentry (errors), New Relic (APM)
- **Server:** Datadog, Prometheus + Grafana
- **Uptime:** UptimeRobot, Pingdom

## Deployment Architecture

### Development Environment

```bash
# Local development
php artisan serve        # Laravel on :8000
npm run dev             # Vite HMR on :5173

# Database
mysql -u root -p        # Local MySQL

# Queue worker
php artisan queue:work --queue=default
```

### Staging Environment

**Purpose:** QA testing, client demos, performance testing

**Configuration:**
- Separate database from production
- Same infrastructure as production (to catch deployment issues)
- Real payment gateway (test mode)
- Real email service (test domain)

### Production Environment

**Deployment Location:** Mindoro State University - Bongabong Campus (single site)

**Hosting Options:**

**Option 1: VPS (DigitalOcean/Linode) - Recommended for Single Campus**
```
Server Specs:
- 4 vCPUs
- 8GB RAM
- 160GB SSD
- $40/month
- Data center: Singapore (closest to Philippines)

Stack:
- Ubuntu 22.04 LTS
- Nginx 1.24
- PHP 8.2-FPM
- MySQL 8.0
- Redis 7.0
- Supervisor (queue workers)

Single Campus Benefits:
- No site-to-site networking needed
- No geo-replication complexity
- Simple backup strategy (one database)
- Predictable resource usage
```

**Option 2: Platform-as-a-Service (Railway/Render)**
```
Services:
- Web (Laravel app): $20/month
- Database (MySQL): Included
- Redis: Included
- Automatic SSL
- GitHub deployment
- Single region deployment (no multi-region needed)
```

**Option 3: On-Campus Hosting (Future Consideration)**
```
If MinSU Bongabong has IT infrastructure:
- Host on campus servers
- Direct LAN access for staff
- Lower internet bandwidth costs
- Requires: Power backup, cooling, IT staff

Considerations:
- Internet uptime for external access
- Backup/disaster recovery planning
- Security (firewall, intrusion detection)
```

### Deployment Pipeline (GitHub Actions)

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
  
  deploy:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Deploy to Server
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /var/www/minsu-bongabong
            git pull origin main
            composer install --no-dev --optimize-autoloader
            npm ci
            npm run build
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            sudo systemctl reload php8.2-fpm
            sudo systemctl restart supervisor
```

### Zero-Downtime Deployment

**Laravel Envoy Script:**

```php
@servers(['web' => 'deploy@minsu-bongabong.edu.ph'])

@task('deploy', ['on' => 'web'])
    cd /var/www/minsu-bongabong
    git pull origin main
    composer install --no-dev --optimize-autoloader
    npm ci && npm run build
    
    # Run migrations
    php artisan migrate --force
    
    # Clear and rebuild caches
    php artisan config:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Reload services
    sudo systemctl reload php8.2-fpm
    sudo supervisorctl restart laravel-worker:*
@endtask
```

### Backup Strategy

**Database Backups:**

```bash
# Daily automated backup (cron job)
0 2 * * * cd /var/www/minsu-bongabong && php artisan backup:run >> /dev/null 2>&1
```

**Laravel Backup Package:**

```php
// config/backup.php
'backup' => [
    'name' => 'minsu-bongabong',
    'source' => [
        'files' => [
            'include' => [
                storage_path('app/private'), // Uploaded documents
            ],
        ],
        'databases' => ['mysql'],
    ],
    'destination' => [
        'disks' => ['s3', 'local'], // Redundant storage
    ],
],
'cleanup' => [
    'defaultStrategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,
    'keep_all_backups_for_days' => 7,
    'keep_daily_backups_for_days' => 30,
    'keep_weekly_backups_for_weeks' => 12,
    'keep_monthly_backups_for_months' => 12,
],
```

## Scalability Considerations

### Current Architecture (Single Campus - Bongabong)

The current architecture is optimized for single-campus deployment:
- **One Database:** All data in single MySQL instance
- **One Application Server:** Single Laravel deployment
- **Local File Storage:** Documents stored on same server or S3 bucket
- **Single Redis Instance:** For cache and queue management

**Current Capacity:**
- 5,000 students and staff
- 500 concurrent users (peak hours)
- 100 document requests per day
- 50 scholarship applications per semester

### Vertical Scaling (Near-term Growth)

For growth within Bongabong campus (5,000 → 7,500 users):

**Upgrade Server Resources:**
```
Current: 4 vCPU, 8GB RAM
Growth:  8 vCPU, 16GB RAM, NVMe SSD
Cost:    $40/month → $80/month
```

**Database Optimization:**
- Add database indexes for common queries
- Implement query result caching
- Optimize slow queries identified by monitoring

### Horizontal Scaling (Multi-Campus Future)

**⚠️ IMPORTANT:** The following is for **future reference only** if Mindoro State University expands deployment to other campuses (Calapan, Victoria, Pinamalayan, etc.).

**Load Balancer Setup (Multi-Server):**
```
               Cloudflare (CDN)
                      │
                   Nginx LB
                   /      \
            App Server 1  App Server 2
             (Bongabong)  (Bongabong Replica)
                   \      /
                  MySQL Primary
                      │
                  MySQL Replica (Read)
                      │
                    Redis Cluster
```

**Multi-Campus Architecture (Future):**
```
                  Central Load Balancer
                          │
        ┌─────────────────┼─────────────────┐
        │                 │                 │
   Bongabong          Calapan          Victoria
   Campus             Campus            Campus
   (Primary)          (Node)            (Node)
        │                 │                 │
        └─────────────────┴─────────────────┘
                          │
              Centralized Database Cluster
                (with geo-replication)
```

**Changes Required for Multi-Campus:**
1. Add `campus_id` field to relevant tables
2. Implement campus-based data filtering
3. Set up database replication across sites
4. Implement campus-aware caching
5. Add campus selection in authentication flow
6. Cross-campus reporting and analytics

**Estimated Multi-Campus Cost:** $500-1,000/month (3-5 campuses)

### Session Management:**
- Move from file-based to Redis sessions (already single-server friendly)
- Enables horizontal scaling if needed (multiple app servers)

**File Storage:**
- Move from local disk to S3/Cloudinary
- Shared storage accessible from multiple servers (if scaling horizontally)

### Database Scaling (Single Campus Context)

**Read Replicas (Optional for Heavy Reporting):**
```php
// config/database.php
'read' => [
    'host' => ['replica1.mysql.db'], // Still single campus, just replicated for read load
],
'write' => [
    'host' => ['primary.mysql.db'],
],
```

**Query Optimization:**
```php
// Use read replica for reports (doesn't affect data freshness requirements)
$stats = DB::connection('mysql_read')
    ->table('registrar_document_requests')
    ->selectRaw('COUNT(*) as total, status')
    ->groupBy('status')
    ->get();
```

**Note:** For single-campus deployment with <10,000 users, a single MySQL instance is typically sufficient. Read replicas only needed if heavy reporting workload impacts transactional performance.

### Queue Scaling

**Multiple Workers:**
```ini
[program:laravel-worker]
numprocs=8  # 8 concurrent workers
process_name=%(program_name)s_%(process_num)02d
```

**Priority Queues:**
```php
// High priority: notifications
SendDocumentReadyEmail::dispatch($request)->onQueue('notifications');

// Medium priority: document generation
GenerateTranscript::dispatch($request)->onQueue('documents');

// Low priority: cleanup tasks
CleanupOldFiles::dispatch()->onQueue('maintenance');
```

## Future Enhancements

### Phase 2 Features (Bongabong Campus Enhancements)
- **Mobile app** (React Native) using existing API - for students to check status on-the-go
- **Advanced reporting and analytics dashboard** - enrollment trends, scholarship utilization, document request patterns
- **Document version control and audit trail** - track changes to digitalized documents
- **Real-time notifications** (Laravel Echo + WebSockets) - instant updates for document status, scholarship approvals
- **SMS notifications via Semaphore API** - alert students when documents are ready for pickup

### Phase 3 Features (Advanced Capabilities)
- **Multi-campus support** - if Mindoro State University expands deployment to Calapan, Victoria, Pinamalayan campuses
  - Campus-based data filtering
  - Cross-campus student transfer tracking
  - Centralized reporting across campuses
  - Campus-specific announcements and events
- **API for third-party integrations** - connect with MinSU accounting system, CHED reporting, enrollment systems
- **Advanced search with Meilisearch or Algolia** - fast full-text search across documents, announcements, resolutions
- **AI-powered chatbot** for student inquiries - answer common questions about requirements, procedures, office hours
- **Blockchain-backed document verification** - third parties can verify authenticity of MinSU Bongabong documents

### Technical Debt Management
- Refactor legacy code identified during code reviews
- Improve test coverage for edge cases
- Performance profiling and optimization
- Security audit (penetration testing)
- Accessibility audit (WCAG 2.1 AA compliance)

### Campus-Specific Considerations
- **Bongabong-specific features** that may not generalize to other campuses
- **Local regulatory compliance** (MinSU policies, CHED requirements for Bongabong)
- **Integration with campus-specific systems** (if Bongabong has unique infrastructure)

**Decision Point:** If multi-campus deployment becomes a requirement, conduct architectural review to assess:
- Data partitioning strategy (separate databases vs shared with filtering)
- Network topology (site-to-site VPN, WAN optimization)
- Replication and consistency models
- Cost-benefit analysis (multi-campus vs separate deployments per campus)

---

## Document Maintenance

**Last Updated:** October 4, 2025

**Institution:** Mindoro State University - Bongabong Campus

**Deployment Scope:** Single campus deployment only (not multi-campus)

**Review Schedule:** Quarterly or after major architectural changes

**Maintainers:**
- Technical Lead: Responsible for architectural decisions
- Development Team: Implement and follow patterns
- DevOps Engineer: Infrastructure and deployment
- MinSU Bongabong ICT Office: Campus IT infrastructure and support

**Change Log:**
- **2025-10-04:** Initial comprehensive architecture documentation for single-campus deployment
- **TBD:** Future updates as system evolves

**Important Notes:**
- This architecture is specifically designed for **Bongabong Campus only**
- Multi-campus references are for future consideration if MinSU central administration decides to expand
- Current user base: ~5,000 students and staff at Bongabong campus
- System accessible via: https://sis.minsu-bongabong.edu.ph (proposed domain)

---

**For questions or clarifications about this architecture, please contact:**
- **Technical Team:** development@minsu.edu.ph
- **MinSU Bongabong ICT Office:** ict.bongabong@minsu.edu.ph
- **Project Repository:** [GitHub Issues](https://github.com/<org>/sas-information-system/issues)

