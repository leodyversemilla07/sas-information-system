# Directory Structure
## MinSU Bongabong Information System

**Document Version:** 1.0  
**Date:** October 4, 2025  
**Full Name:** Mindoro State University - Bongabong Campus Information System  
**Purpose:** Single source of truth for project directory structure

---

## Overview

This document defines the complete directory structure for the MinSU Bongabong Information System. The project follows Laravel 12's streamlined structure with a modular monolith architecture, organizing code into three main modules: **SAS** (Student Affairs Services), **Registrar**, and **USG** (University Student Government).

### Key Organizational Principles

1. **Module Separation**: Each module (SAS, Registrar, USG) has dedicated subdirectories
2. **Shared Code**: Common functionality in root or `Shared/` folders
3. **Laravel 12 Structure**: No `app/Console/Kernel.php`, commands auto-register
4. **Event-Driven**: Events and Listeners for cross-module communication
5. **Type Safety**: TypeScript frontend, PHP 8.2 type declarations backend

---

## Complete Directory Structure

```
sas-information-system/
├── app/
│   ├── Console/
│   │   └── Commands/                    # Custom Artisan commands (auto-registered)
│   │       ├── SAS/
│   │       │   └── GenerateScholarshipReport.php
│   │       ├── Registrar/
│   │       │   └── ArchiveOldRequests.php
│   │       └── USG/
│   │           └── PublishMonthlyReport.php
│   │
│   ├── Events/                          # Domain events for module communication
│   │   ├── SAS/
│   │   │   ├── EventCreated.php
│   │   │   ├── EventUpdated.php
│   │   │   ├── ScholarshipApproved.php
│   │   │   ├── ScholarshipRejected.php
│   │   │   └── OrganizationRegistered.php
│   │   ├── Registrar/
│   │   │   ├── DocumentRequestCreated.php
│   │   │   ├── PaymentConfirmed.php
│   │   │   └── DocumentReady.php
│   │   └── USG/
│   │       └── AnnouncementPublished.php
│   │
│   ├── Exceptions/
│   │   └── Handler.php                  # Global exception handler
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                    # Authentication controllers
│   │   │   │   ├── LoginController.php
│   │   │   │   └── RegisterController.php
│   │   │   │
│   │   │   ├── SAS/                     # Student Affairs controllers
│   │   │   │   ├── ScholarshipController.php
│   │   │   │   ├── EventController.php
│   │   │   │   ├── OrganizationController.php
│   │   │   │   ├── InsuranceController.php
│   │   │   │   └── DigitalArchiveController.php
│   │   │   │
│   │   │   ├── Registrar/               # Registrar controllers
│   │   │   │   ├── DocumentRequestController.php
│   │   │   │   ├── PaymentController.php
│   │   │   │   ├── DeliveryController.php
│   │   │   │   └── TrackingController.php
│   │   │   │
│   │   │   └── USG/                     # USG Portal controllers
│   │   │       ├── HomeController.php
│   │   │       ├── OfficerController.php
│   │   │       ├── AnnouncementController.php
│   │   │       ├── ResolutionController.php
│   │   │       ├── VMGOController.php
│   │   │       └── CalendarController.php
│   │   │
│   │   ├── Middleware/                  # Custom middleware (if needed)
│   │   │   ├── EnsureSASAdmin.php
│   │   │   ├── EnsureRegistrarStaff.php
│   │   │   └── EnsureUSGOfficer.php
│   │   │
│   │   ├── Requests/                    # Form Request validation
│   │   │   ├── SAS/
│   │   │   │   ├── StoreScholarshipRequest.php
│   │   │   │   ├── UpdateScholarshipRequest.php
│   │   │   │   ├── StoreEventRequest.php
│   │   │   │   ├── StoreOrganizationRequest.php
│   │   │   │   └── UpdateOrganizationRequest.php
│   │   │   │
│   │   │   ├── Registrar/
│   │   │   │   ├── StoreDocumentRequestRequest.php
│   │   │   │   └── UpdateDocumentRequestRequest.php
│   │   │   │
│   │   │   └── USG/
│   │   │       ├── StoreAnnouncementRequest.php
│   │   │       ├── StoreResolutionRequest.php
│   │   │       └── StoreOfficerRequest.php
│   │   │
│   │   └── Resources/                   # API Resources (future mobile app)
│   │       └── API/
│   │           └── V1/
│   │               ├── ScholarshipResource.php
│   │               └── DocumentRequestResource.php
│   │
│   ├── Jobs/                            # Queued background jobs
│   │   ├── SAS/
│   │   │   ├── ProcessScholarshipApplication.php
│   │   │   ├── ProcessBulkDigitalisation.php
│   │   │   └── SendEventReminders.php
│   │   │
│   │   ├── Registrar/
│   │   │   ├── GenerateTranscript.php
│   │   │   ├── GenerateCertificate.php
│   │   │   ├── GenerateCOG.php
│   │   │   ├── GenerateCOE.php
│   │   │   └── SendDocumentNotification.php
│   │   │
│   │   └── Shared/
│   │       └── SendEmailNotification.php
│   │
│   ├── Listeners/                       # Event listeners
│   │   ├── SAS/
│   │   │   ├── NotifyScholarshipApproval.php
│   │   │   └── SendEventNotificationEmail.php
│   │   │
│   │   ├── Registrar/
│   │   │   ├── GenerateDocument.php
│   │   │   ├── QueueDocumentGeneration.php
│   │   │   └── SendDocumentReadyEmail.php
│   │   │
│   │   └── USG/
│   │       ├── SyncEventToPublicCalendar.php
│   │       └── BroadcastAnnouncement.php
│   │
│   ├── Mail/                            # Mailable classes
│   │   ├── ScholarshipApprovedMail.php
│   │   ├── ScholarshipRejectedMail.php
│   │   ├── DocumentReadyMail.php
│   │   └── EventReminderMail.php
│   │
│   ├── Models/                          # Eloquent models
│   │   ├── User.php                     # Shared auth model
│   │   │
│   │   ├── SAS/
│   │   │   ├── Scholarship.php
│   │   │   ├── ScholarshipApplication.php
│   │   │   ├── Event.php
│   │   │   ├── Organization.php
│   │   │   ├── OrganizationMember.php
│   │   │   ├── InsuranceRecord.php
│   │   │   └── DigitalDocument.php
│   │   │
│   │   ├── Registrar/
│   │   │   ├── DocumentType.php
│   │   │   ├── DocumentRequest.php
│   │   │   ├── Transaction.php
│   │   │   └── DocumentDelivery.php
│   │   │
│   │   └── USG/
│   │       ├── Officer.php
│   │       ├── Announcement.php
│   │       ├── Resolution.php
│   │       ├── VMGO.php
│   │       └── CachedEvent.php
│   │
│   ├── Notifications/                   # Laravel notifications
│   │   ├── ScholarshipStatusNotification.php
│   │   ├── DocumentReadyNotification.php
│   │   └── EventReminderNotification.php
│   │
│   ├── Policies/                        # Authorization policies
│   │   ├── SAS/
│   │   │   ├── ScholarshipPolicy.php
│   │   │   ├── EventPolicy.php
│   │   │   └── OrganizationPolicy.php
│   │   │
│   │   ├── Registrar/
│   │   │   └── DocumentRequestPolicy.php
│   │   │
│   │   └── USG/
│   │       ├── ResolutionPolicy.php
│   │       └── AnnouncementPolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── FortifyServiceProvider.php
│   │
│   └── Services/                        # Business logic layer
│       ├── Auth/
│       │   └── StudentVerificationService.php
│       │
│       ├── SAS/
│       │   ├── ScholarshipService.php
│       │   ├── EventService.php
│       │   ├── EventPublishingService.php
│       │   ├── OrganizationService.php
│       │   └── DigitalArchiveService.php
│       │
│       ├── Registrar/
│       │   ├── DocumentRequestService.php
│       │   ├── PaymentService.php
│       │   ├── PDFGenerationService.php
│       │   └── PaymongoService.php
│       │
│       ├── USG/
│       │   ├── CalendarSyncService.php
│       │   └── AnnouncementService.php
│       │
│       └── Shared/
│           ├── NotificationService.php
│           └── FileStorageService.php
│
├── bootstrap/
│   ├── app.php                          # Laravel 12 bootstrap configuration
│   ├── providers.php                    # Service provider registration
│   └── cache/
│       ├── packages.php
│       └── services.php
│
├── config/                              # Configuration files
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── fortify.php
│   ├── inertia.php
│   ├── logging.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
│
├── database/
│   ├── database.sqlite                  # Local SQLite database for development
│   │
│   ├── factories/                       # Model factories for testing
│   │   ├── UserFactory.php
│   │   │
│   │   ├── SAS/
│   │   │   ├── ScholarshipFactory.php
│   │   │   ├── ScholarshipApplicationFactory.php
│   │   │   ├── EventFactory.php
│   │   │   ├── OrganizationFactory.php
│   │   │   └── InsuranceRecordFactory.php
│   │   │
│   │   ├── Registrar/
│   │   │   ├── DocumentRequestFactory.php
│   │   │   ├── TransactionFactory.php
│   │   │   └── DocumentDeliveryFactory.php
│   │   │
│   │   └── USG/
│   │       ├── OfficerFactory.php
│   │       ├── AnnouncementFactory.php
│   │       └── ResolutionFactory.php
│   │
│   ├── migrations/                      # Database migrations (chronological)
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2025_08_26_100418_add_two_factor_columns_to_users_table.php
│   │   ├── 2025_10_05_000001_create_permission_tables.php
│   │   ├── 2025_10_05_000002_create_sas_scholarships_table.php
│   │   ├── 2025_10_05_000003_create_sas_scholarship_applications_table.php
│   │   ├── 2025_10_05_000004_create_sas_events_table.php
│   │   ├── 2025_10_05_000005_create_sas_organizations_table.php
│   │   ├── 2025_10_05_000006_create_sas_organization_members_table.php
│   │   ├── 2025_10_05_000007_create_sas_insurance_records_table.php
│   │   ├── 2025_10_05_000008_create_sas_digital_documents_table.php
│   │   ├── 2025_10_05_000009_create_registrar_document_types_table.php
│   │   ├── 2025_10_05_000010_create_registrar_document_requests_table.php
│   │   ├── 2025_10_05_000011_create_registrar_transactions_table.php
│   │   ├── 2025_10_05_000012_create_registrar_document_deliveries_table.php
│   │   ├── 2025_10_05_000013_create_usg_officers_table.php
│   │   ├── 2025_10_05_000014_create_usg_announcements_table.php
│   │   ├── 2025_10_05_000015_create_usg_resolutions_table.php
│   │   └── 2025_10_05_000016_create_usg_vmgo_table.php
│   │
│   └── seeders/                         # Database seeders
│       ├── DatabaseSeeder.php
│       ├── RoleAndPermissionSeeder.php
│       │
│       ├── SAS/
│       │   ├── ScholarshipSeeder.php
│       │   └── OrganizationSeeder.php
│       │
│       ├── Registrar/
│       │   └── DocumentTypeSeeder.php
│       │
│       └── USG/
│           └── VMGOSeeder.php
│
├── docs/                                # Project documentation
│   ├── API_SPECIFICATIONS.md
│   ├── ARCHITECTURE.md
│   ├── CHANGELOG_DOCUMENTATION.md
│   ├── DATA_MODELS.md
│   ├── DIRECTORY_STRUCTURE.md           # This file
│   ├── NFR.md
│   ├── PRD.md
│   └── USER_STORIES.md
│
├── public/                              # Publicly accessible files
│   ├── build/                           # Vite build output
│   │   ├── manifest.json
│   │   └── assets/
│   ├── apple-touch-icon.png
│   ├── favicon.ico
│   ├── favicon.svg
│   ├── hot                              # Vite HMR indicator
│   ├── index.php                        # Application entry point
│   ├── logo.svg
│   └── robots.txt
│
├── resources/
│   ├── css/
│   │   └── app.css                      # Tailwind CSS entry point
│   │
│   ├── js/
│   │   ├── actions/                     # Server actions (if using RSC patterns)
│   │   │
│   │   ├── components/
│   │   │   ├── ui/                      # shadcn/ui components
│   │   │   │   ├── button.tsx
│   │   │   │   ├── card.tsx
│   │   │   │   ├── dialog.tsx
│   │   │   │   ├── form.tsx
│   │   │   │   ├── input.tsx
│   │   │   │   ├── select.tsx
│   │   │   │   ├── table.tsx
│   │   │   │   └── toast.tsx
│   │   │   │
│   │   │   ├── shared/                  # Shared across all modules
│   │   │   │   ├── Header.tsx
│   │   │   │   ├── Footer.tsx
│   │   │   │   ├── Sidebar.tsx
│   │   │   │   ├── Navbar.tsx
│   │   │   │   ├── LoadingSpinner.tsx
│   │   │   │   └── StatusBadge.tsx
│   │   │   │
│   │   │   ├── sas/                     # SAS-specific components
│   │   │   │   ├── ScholarshipCard.tsx
│   │   │   │   ├── ScholarshipForm.tsx
│   │   │   │   ├── OrganizationList.tsx
│   │   │   │   └── EventCalendar.tsx
│   │   │   │
│   │   │   ├── registrar/               # Registrar-specific components
│   │   │   │   ├── DocumentRequestForm.tsx
│   │   │   │   ├── PaymentModal.tsx
│   │   │   │   └── DeliveryTracker.tsx
│   │   │   │
│   │   │   └── usg/                     # USG-specific components
│   │   │       ├── OfficerCard.tsx
│   │   │       ├── ResolutionList.tsx
│   │   │       └── AnnouncementBanner.tsx
│   │   │
│   │   ├── hooks/                       # Custom React hooks
│   │   │   ├── useAuth.ts
│   │   │   ├── usePermissions.ts
│   │   │   ├── useToast.ts
│   │   │   └── useDebounce.ts
│   │   │
│   │   ├── layouts/                     # Page layouts
│   │   │   ├── AuthLayout.tsx
│   │   │   ├── GuestLayout.tsx
│   │   │   ├── PublicLayout.tsx
│   │   │   ├── DashboardLayout.tsx
│   │   │   ├── SASLayout.tsx
│   │   │   ├── RegistrarLayout.tsx
│   │   │   └── USGLayout.tsx
│   │   │
│   │   ├── lib/                         # Utility functions
│   │   │   ├── utils.ts
│   │   │   ├── api.ts
│   │   │   ├── validation.ts
│   │   │   └── formatters.ts
│   │   │
│   │   ├── pages/                       # Inertia pages (routes)
│   │   │   ├── Auth/
│   │   │   │   ├── Login.tsx
│   │   │   │   ├── Register.tsx
│   │   │   │   ├── ForgotPassword.tsx
│   │   │   │   └── ResetPassword.tsx
│   │   │   │
│   │   │   ├── Dashboard.tsx
│   │   │   │
│   │   │   ├── SAS/
│   │   │   │   ├── Index.tsx
│   │   │   │   │
│   │   │   │   ├── Scholarships/
│   │   │   │   │   ├── Index.tsx
│   │   │   │   │   ├── Create.tsx
│   │   │   │   │   ├── Show.tsx
│   │   │   │   │   └── Edit.tsx
│   │   │   │   │
│   │   │   │   ├── Organizations/
│   │   │   │   │   ├── Index.tsx
│   │   │   │   │   ├── Create.tsx
│   │   │   │   │   └── Show.tsx
│   │   │   │   │
│   │   │   │   ├── Events/
│   │   │   │   │   ├── Index.tsx
│   │   │   │   │   ├── Create.tsx
│   │   │   │   │   └── Show.tsx
│   │   │   │   │
│   │   │   │   └── Insurance/
│   │   │   │       ├── Index.tsx
│   │   │   │       └── Create.tsx
│   │   │   │
│   │   │   ├── Registrar/
│   │   │   │   ├── Index.tsx
│   │   │   │   │
│   │   │   │   ├── DocumentRequests/
│   │   │   │   │   ├── Index.tsx
│   │   │   │   │   ├── Create.tsx
│   │   │   │   │   └── Show.tsx
│   │   │   │   │
│   │   │   │   ├── Payments/
│   │   │   │   │   └── Index.tsx
│   │   │   │   │
│   │   │   │   └── History/
│   │   │   │       └── Index.tsx
│   │   │   │
│   │   │   └── USG/
│   │   │       ├── Index.tsx
│   │   │       │
│   │   │       ├── Officers/
│   │   │       │   └── Index.tsx
│   │   │       │
│   │   │       ├── Resolutions/
│   │   │       │   ├── Index.tsx
│   │   │       │   └── Show.tsx
│   │   │       │
│   │   │       ├── Announcements/
│   │   │       │   ├── Index.tsx
│   │   │       │   └── Show.tsx
│   │   │       │
│   │   │       ├── VMGO/
│   │   │       │   └── Index.tsx
│   │   │       │
│   │   │       └── Calendar/
│   │   │           └── Index.tsx
│   │   │
│   │   ├── routes/                      # Frontend route definitions (Wayfinder)
│   │   │
│   │   ├── types/                       # TypeScript type definitions
│   │   │   ├── index.d.ts
│   │   │   ├── models.d.ts              # Model types matching backend
│   │   │   ├── api.d.ts
│   │   │   └── inertia.d.ts             # Inertia page props
│   │   │
│   │   ├── wayfinder/                   # Generated type-safe routes
│   │   │
│   │   ├── app.tsx                      # Frontend entry point
│   │   └── ssr.tsx                      # SSR entry point
│   │
│   └── views/
│       └── app.blade.php                # Inertia root template
│
├── routes/
│   ├── web.php                          # Main web routes
│   ├── auth.php                         # Authentication routes (Fortify)
│   ├── settings.php                     # User settings routes
│   ├── console.php                      # Artisan console routes
│   └── api.php                          # API routes (webhooks, mobile future)
│
├── storage/
│   ├── app/
│   │   ├── private/                     # Private file storage
│   │   └── public/                      # Public file storage (symlinked to public/storage)
│   │
│   ├── framework/
│   │   ├── cache/
│   │   ├── sessions/
│   │   ├── testing/
│   │   └── views/
│   │
│   └── logs/
│       └── laravel.log
│
├── tests/
│   ├── Feature/                         # Feature tests (integration)
│   │   ├── Auth/
│   │   │   ├── LoginTest.php
│   │   │   ├── RegistrationTest.php
│   │   │   └── PasswordResetTest.php
│   │   │
│   │   ├── SAS/
│   │   │   ├── ScholarshipTest.php
│   │   │   ├── ScholarshipApplicationTest.php
│   │   │   ├── OrganizationTest.php
│   │   │   └── EventTest.php
│   │   │
│   │   ├── Registrar/
│   │   │   ├── DocumentRequestTest.php
│   │   │   ├── PaymentTest.php
│   │   │   └── DocumentGenerationTest.php
│   │   │
│   │   └── USG/
│   │       ├── AnnouncementTest.php
│   │       ├── ResolutionTest.php
│   │       └── CalendarTest.php
│   │
│   ├── Unit/                            # Unit tests (isolated)
│   │   ├── Services/
│   │   │   ├── ScholarshipServiceTest.php
│   │   │   ├── DocumentGenerationServiceTest.php
│   │   │   └── PaymentServiceTest.php
│   │   │
│   │   └── Policies/
│   │       ├── ScholarshipPolicyTest.php
│   │       └── DocumentRequestPolicyTest.php
│   │
│   ├── Pest.php                         # Pest configuration
│   └── TestCase.php                     # Base test case
│
├── vendor/                              # Composer dependencies (not tracked in git)
│
├── .editorconfig
├── .env                                 # Environment variables (not tracked in git)
├── .env.example                         # Example environment variables
├── .gitattributes
├── .gitignore
├── artisan                              # Artisan CLI
├── boost.json                           # Laravel Boost configuration
├── composer.json                        # PHP dependencies
├── composer.lock                        # PHP dependency lock file
├── components.json                      # shadcn/ui configuration
├── CONTRIBUTING.md                      # Contribution guidelines
├── eslint.config.js                     # ESLint configuration
├── package.json                         # Node.js dependencies
├── package-lock.json                    # Node.js dependency lock file
├── phpunit.xml                          # PHPUnit configuration
├── README.md                            # Project readme
├── tsconfig.json                        # TypeScript configuration
└── vite.config.ts                       # Vite bundler configuration
```

---

## Module-Specific Organization

### SAS Module Files

**Controllers**: `app/Http/Controllers/SAS/`
- ScholarshipController.php
- EventController.php
- OrganizationController.php
- InsuranceController.php
- DigitalArchiveController.php

**Models**: `app/Models/SAS/`
- Scholarship.php
- ScholarshipApplication.php
- Event.php
- Organization.php
- OrganizationMember.php
- InsuranceRecord.php
- DigitalDocument.php

**Frontend Pages**: `resources/js/pages/SAS/`
- Scholarships/ (Index, Create, Show, Edit)
- Organizations/ (Index, Create, Show)
- Events/ (Index, Create, Show)
- Insurance/ (Index, Create)

**Tests**: `tests/Feature/SAS/`
- ScholarshipTest.php
- ScholarshipApplicationTest.php
- OrganizationTest.php
- EventTest.php

### Registrar Module Files

**Controllers**: `app/Http/Controllers/Registrar/`
- DocumentRequestController.php
- PaymentController.php
- DeliveryController.php
- TrackingController.php

**Models**: `app/Models/Registrar/`
- DocumentType.php
- DocumentRequest.php
- Transaction.php
- DocumentDelivery.php

**Frontend Pages**: `resources/js/pages/Registrar/`
- DocumentRequests/ (Index, Create, Show)
- Payments/ (Index)
- History/ (Index)

**Tests**: `tests/Feature/Registrar/`
- DocumentRequestTest.php
- PaymentTest.php
- DocumentGenerationTest.php

### USG Module Files

**Controllers**: `app/Http/Controllers/USG/`
- HomeController.php
- OfficerController.php
- AnnouncementController.php
- ResolutionController.php
- VMGOController.php
- CalendarController.php

**Models**: `app/Models/USG/`
- Officer.php
- Announcement.php
- Resolution.php
- VMGO.php
- CachedEvent.php

**Frontend Pages**: `resources/js/pages/USG/`
- Officers/ (Index)
- Resolutions/ (Index, Show)
- Announcements/ (Index, Show)
- VMGO/ (Index)
- Calendar/ (Index)

**Tests**: `tests/Feature/USG/`
- AnnouncementTest.php
- ResolutionTest.php
- CalendarTest.php

---

## Naming Conventions

### Backend (PHP/Laravel)

- **Classes**: PascalCase (e.g., `ScholarshipController`, `DocumentRequest`)
- **Methods**: camelCase (e.g., `createRequest`, `confirmPayment`)
- **Variables**: camelCase (e.g., `$documentRequest`, `$studentId`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_FILE_SIZE`)
- **Database Tables**: snake_case with module prefix (e.g., `sas_scholarships`, `registrar_document_requests`)
- **Database Columns**: snake_case (e.g., `student_id`, `created_at`)

### Frontend (TypeScript/React)

- **Components**: PascalCase (e.g., `ScholarshipCard`, `PaymentModal`)
- **Files**: PascalCase for components (e.g., `ScholarshipCard.tsx`)
- **Functions**: camelCase (e.g., `handleSubmit`, `fetchData`)
- **Variables**: camelCase (e.g., `studentName`, `isLoading`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `API_BASE_URL`)
- **Types/Interfaces**: PascalCase (e.g., `Scholarship`, `DocumentRequest`)

### Routes

- **Web Routes**: kebab-case (e.g., `/sas/scholarships`, `/registrar/document-requests`)
- **Named Routes**: dot notation (e.g., `sas.scholarships.index`, `registrar.payment.show`)

---

## Important Notes

### Laravel 12 Specific

1. **No Kernel Classes**: No `app/Console/Kernel.php` or `app/Http/Kernel.php`
2. **Bootstrap Configuration**: Middleware and routing configured in `bootstrap/app.php`
3. **Auto-Registration**: Commands in `app/Console/Commands/` automatically register
4. **Service Providers**: Registered in `bootstrap/providers.php`

### Module Boundaries

1. **No Direct Coupling**: Modules communicate via Events, not direct method calls
2. **Shared Code**: Common functionality goes in `Shared/` folders
3. **Table Prefixing**: Database tables use module prefixes (`sas_`, `registrar_`, `usg_`)

### Type Safety

1. **PHP 8.2**: Use constructor property promotion and type declarations
2. **TypeScript**: All frontend code strictly typed
3. **Shared Types**: Backend model types mirrored in `resources/js/types/models.d.ts`

### Testing

1. **Feature Tests**: Test user-facing functionality with HTTP requests
2. **Unit Tests**: Test isolated business logic in Services
3. **Factories**: Use factories for all model creation in tests
4. **Pest**: All tests written using Pest framework

---

## Related Documentation

- **ARCHITECTURE.md**: Technical architecture and design patterns
- **DATA_MODELS.md**: Database schema and relationships
- **API_SPECIFICATIONS.md**: REST API contracts
- **PRD.md**: Product requirements and features
- **USER_STORIES.md**: User stories and acceptance criteria

---

**Document Maintained By**: Development Team  
**Last Updated**: October 4, 2025  
**Next Review**: When adding new modules or restructuring
