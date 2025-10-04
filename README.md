
# MinSU Bongabong Information System

A comprehensive Laravel 12 + Inertia (React) platform integrating three core university management systems for Mindoro State University – Bongabong Campus. Built as a modular monolith, this application provides seamless student services, registrar operations, and student government transparency through a unified, event-driven architecture.

## 🎯 Project Vision

Transform MinSU Bongabong's administrative operations through digital-first workflows that reduce transaction times by 90%, eliminate paper-based bottlenecks, and provide transparent, accessible services to students, faculty, and the university community.

## ✨ Key Features

### 🎓 Student Affairs Services (SAS)
- **Scholarship Management**: TES/TDP application processing with approval workflows
- **Insurance Administration**: Digital submission and tracking for NSTP/ROTC insurance
- **Student Organizations**: Registry for 11 minor and 12 major organizations with member tracking
- **Event Management**: Campus-wide calendar with automatic USG portal synchronization
- **Digital Archive**: Paperless document storage with 7-10 year retention policies

### 📋 Registrar Services
- **Document Requests**: Streamlined COG, COE, TOR, and diploma copy requests
- **Online Payments**: Integrated Paymongo gateway supporting GCash, bank transfers, and cards
- **Automated Generation**: Background PDF creation with email delivery notifications
- **Request Tracking**: Real-time status updates from submission to pickup/delivery
- **Payment Reconciliation**: Idempotent transaction handling with audit trails

### 🏛️ USG Transparency Portal
- **VMGO Display**: Vision, mission, goals, and objectives with historical tracking
- **Officer Directory**: Photo gallery with position, department, and term information
- **Public Announcements**: Priority-based notices with expiration management
- **Resolutions Archive**: Searchable repository of USG resolutions with PDF viewer
- **Public Calendar**: Synchronized event feed from SAS with filtering capabilities

## 🏗️ Architecture Highlights
- **Modular Monolith**: Three bounded contexts (SAS, Registrar, USG) with clear domain boundaries
- **Event-Driven Integration**: Asynchronous communication between modules via Laravel events
- **Schema Isolation**: Logical database separation using table prefixing (`auth_`, `sas_`, `registrar_`, `usg_`)
- **Type-Safe Frontend**: React 19 with TypeScript and shadcn/ui component library
- **Queue-Based Processing**: Background jobs for PDF generation, email notifications, and webhook handling
- **Role-Based Access Control**: Spatie permissions with 6 distinct user roles


## 🛠️ Technology Stack

| Layer | Technologies |
| --- | --- |
| **Backend Framework** | Laravel 12 (PHP 8.2) |
| **Authentication** | Laravel Fortify v1, Laravel Sanctum |
| **Authorization** | Spatie Laravel Permission |
| **Frontend Framework** | Inertia.js v2 with React 19 |
| **UI Components** | shadcn/ui (Radix primitives + Tailwind CSS 4) |
| **Type Safety** | TypeScript 5 |
| **Styling** | Tailwind CSS 4 with custom design tokens |
| **Build Tool** | Vite with Laravel plugin |
| **Database** | MySQL 8+ with Eloquent ORM |
| **Queue System** | Laravel Queue (database driver, Redis-ready) |
| **Payment Gateway** | Paymongo (Philippine payment methods) |
| **PDF Generation** | DomPDF via Laravel wrapper |
| **File Storage** | Laravel Filesystem (local/S3/Cloudinary) |
| **Testing** | Pest 3 (BDD-style), PHPUnit 11 runner |
| **Code Quality** | Laravel Pint (PHP), ESLint 9 + Prettier 3 (JS/TS) |
| **Navigation** | Laravel Wayfinder v0 |

## 📊 System Metrics

- **Target Users**: 5,000+ students, 300+ faculty/staff
- **Performance Target**: <2s page load (95th percentile), 500 concurrent users
- **Availability**: 99.5% uptime (Academic hours: 7 AM - 9 PM)
- **Security**: OWASP Top 10 compliance, WCAG 2.1 AA accessibility
- **Test Coverage**: >70% code coverage requirement

## 🧭 Module Overview

```
┌─────────────────────────────────────────────────────┐
│         Authentication & Authorization               │
│    (Fortify + Sanctum + Spatie Permissions)         │
└───────────────────┬─────────────────────────────────┘
                    │
        ┌───────────┼───────────┐
        │           │           │
┌───────▼──────┐ ┌──▼──────┐ ┌─▼──────────┐
│  USG Portal  │ │Registrar│ │    SAS     │
│   (Public)   │ │ (Trans) │ │  (Admin)   │
└──────────────┘ └─────────┘ └────────────┘
        │           │           │
        └───────────┼───────────┘
                    │
        ┌───────────▼───────────┐
        │   MySQL Database      │
        │  (Schema Isolation)   │
        └───────────────────────┘
```

**Integration Pattern**: Event-driven synchronization
- SAS publishes `EventCreated` → USG subscribes and displays in public calendar
- Registrar publishes `PaymentConfirmed` → Triggers PDF generation job
- All modules share `User` and `Student` models from `auth` schema


## 🚀 Quick Start

### Prerequisites
- **PHP** 8.2 or higher
- **Composer** 2.7+
- **Node.js** 20+ and npm 10+
- **MySQL** 8+ (or MariaDB 10.6+)
- **Git** and OpenSSL

### Installation Steps

1. **Clone and install PHP dependencies**
   ```powershell
   git clone https://github.com/<org>/sas-information-system.git
   cd sas-information-system
   composer install
   ```

2. **Environment configuration**
   ```powershell
   copy .env.example .env
   php artisan key:generate
   ```

3. **Database setup**
   - Create MySQL database: `CREATE DATABASE minsu_bongabong;`
   - Update `.env` with your database credentials:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=minsu_bongabong
     DB_USERNAME=root
     DB_PASSWORD=your_password
     ```

4. **Run migrations and seed sample data**
   ```powershell
   php artisan migrate --seed
   ```

5. **Install JavaScript dependencies**
   ```powershell
   npm install
   ```

6. **Start development servers**
   
   Terminal 1 (Laravel):
   ```powershell
   php artisan serve
   ```
   
   Terminal 2 (Vite):
   ```powershell
   npm run dev
   ```
   
   Visit `http://127.0.0.1:8000` to see the application.

### Default Test Credentials

After seeding, you can log in with:

| Role | Email | Password |
|------|-------|----------|
| Student | student@minsu.edu.ph | password |
| SAS Staff | sas.staff@minsu.edu.ph | password |
| SAS Admin | sas.admin@minsu.edu.ph | password |
| Registrar Staff | registrar.staff@minsu.edu.ph | password |
| USG President | usg.president@minsu.edu.ph | password |
| System Admin | admin@minsu.edu.ph | password |

### Development Commands

```powershell
# Database management
php artisan migrate:fresh --seed  # Rebuild database with fresh data

# Code quality
vendor/bin/pint --dirty           # Fix PHP code style issues
npm run lint                      # Check JavaScript/TypeScript
npm run format:check              # Check code formatting
npm run types                     # TypeScript type checking

# Testing
php artisan test                  # Run all tests
php artisan test --filter=ExampleTest  # Run specific test

# Queue workers (for background jobs)
php artisan queue:work            # Process queued jobs
php artisan queue:listen          # Auto-reload on code changes

# Asset building
npm run build                     # Production build
npm run build:ssr                 # Server-side rendering build
```


## 📁 Project Structure

The MinSU Bongabong Information System follows **Laravel 12's streamlined structure** with a **modular monolith architecture**, organizing code into three main modules: **SAS** (Student Affairs Services), **Registrar**, and **USG** (University Student Government).

### High-Level Organization

```
sas-information-system/
├── app/                     # Application core
│   ├── Console/Commands/    # Custom Artisan commands (auto-registered)
│   ├── Events/              # Domain events (organized by module)
│   ├── Http/                # Controllers, Middleware, Requests, Resources
│   ├── Jobs/                # Queued background jobs (organized by module)
│   ├── Listeners/           # Event listeners (organized by module)
│   ├── Mail/                # Mailable classes
│   ├── Models/              # Eloquent models (organized by module)
│   ├── Notifications/       # Laravel notifications
│   ├── Policies/            # Authorization policies (organized by module)
│   ├── Providers/           # Service providers
│   └── Services/            # Business logic layer (organized by module)
├── bootstrap/               # Laravel 12 bootstrap configuration
├── config/                  # Configuration files
├── database/                # Migrations, factories, seeders (organized by module)
├── docs/                    # 📚 **Comprehensive Documentation**
├── public/                  # Public assets and entry point
├── resources/               # Frontend assets
│   ├── css/                 # Tailwind CSS entry point
│   ├── js/                  # TypeScript/React source
│   │   ├── components/      # UI components (ui/, shared/, sas/, registrar/, usg/)
│   │   ├── hooks/           # Custom React hooks
│   │   ├── layouts/         # Page layouts
│   │   ├── lib/             # Utility functions
│   │   ├── pages/           # Inertia pages (organized by module)
│   │   ├── types/           # TypeScript type definitions
│   │   └── wayfinder/       # Generated type-safe routes
│   └── views/               # Blade templates (minimal usage)
├── routes/                  # Route definitions (web, auth, api, console, settings)
├── storage/                 # Application storage (logs, cache, uploads)
├── tests/                   # Feature & Unit tests (organized by module)
└── vendor/                  # Composer dependencies
```

### Key Structural Principles

1. **Module Separation**: Each module (SAS, Registrar, USG) has dedicated subdirectories in `app/Http/Controllers/`, `app/Models/`, `app/Services/`, etc.
2. **Event-Driven**: Cross-module communication via Events and Listeners (no direct coupling)
3. **Laravel 12 Specific**: No `Kernel.php` files; commands auto-register from `app/Console/Commands/`
4. **Type Safety**: Shared types between backend models and frontend TypeScript
5. **Testing**: Feature and Unit tests organized by module in `tests/Feature/` and `tests/Unit/`

### 📖 Complete Structure Reference

**For the complete, detailed directory structure including all files and subdirectories**, see **[DIRECTORY_STRUCTURE.md](docs/DIRECTORY_STRUCTURE.md)**, which includes:
- Complete project tree from root to leaf files
- Module-specific file organization (SAS, Registrar, USG)
- Naming conventions (backend PHP/Laravel & frontend TypeScript/React)
- Laravel 12 specific notes and auto-registration details
- Testing organization guidelines

## 📚 Documentation

Comprehensive documentation is available in the `docs/` directory:

| Document | Purpose | Audience |
|----------|---------|----------|
| **[PRD.md](docs/PRD.md)** | Product vision, goals, personas, feature overview, success metrics, roadmap | Product Managers, Stakeholders, Leadership |
| **[USER_STORIES.md](docs/USER_STORIES.md)** | 21 user stories with Given-When-Then acceptance criteria across 8 epics | Developers, QA, Product Owners |
| **[API_SPECIFICATIONS.md](docs/API_SPECIFICATIONS.md)** | 32 REST API endpoints with request/response examples, authentication, webhooks | Backend Developers, Integration Teams |
| **[DATA_MODELS.md](docs/DATA_MODELS.md)** | Database schema, ERD, entity relationships, business rules, migrations | Database Architects, Backend Developers |
| **[NFR.md](docs/NFR.md)** | Performance, security, scalability, accessibility, compliance requirements | DevOps, Security, QA, Architects |
| **[ARCHITECTURE.md](docs/ARCHITECTURE.md)** | System architecture, integration patterns, technology decisions | Technical Leads, Architects |
| **[DIRECTORY_STRUCTURE.md](docs/DIRECTORY_STRUCTURE.md)** | Complete file organization, naming conventions, module structure | All Developers |
| **[INERTIA_PATTERNS.md](docs/INERTIA_PATTERNS.md)** | Frontend conventions, React patterns, Inertia best practices | Frontend Developers |

### Getting Started with Documentation

1. **For Product Understanding**: Start with `PRD.md` to understand the vision and scope
2. **For Development**: Review `USER_STORIES.md` for requirements, then `API_SPECIFICATIONS.md` and `DATA_MODELS.md`
3. **For Architecture**: Read `ARCHITECTURE.md` for system design decisions and `DIRECTORY_STRUCTURE.md` for file organization
4. **For Production Readiness**: Check `NFR.md` for quality gates and performance targets

## 🧭 Module Feature Overview

| Module | Focus | Notable Features |
| --- | --- | --- |
| **SAS (Student Affairs)** | Scholarships, insurance, organizations, events | Multi-step approval workflows, digital document archive with retention policies, event publishing with USG sync, batch upload for digitalization, 23 organization registry |
| **Registrar** | Document requests, payments, delivery | Idempotent payment processing, async PDF generation (queue-based), Paymongo webhook handling, email/SMS notifications, request status tracking, pickup/delivery options |
| **USG Portal** | Public transparency, information | VMGO display, officer directory with photos, resolution archive with PDF viewer, priority announcements with expiration, synchronized event calendar, multi-campus visibility (future) |

### Cross-Module Integration

- **Shared Authentication**: Single sign-on across all modules via Laravel Fortify
- **Event-Driven Sync**: SAS events automatically appear in USG public calendar
- **Identity Verification**: Registrar verifies student enrollment status before processing requests
- **Role-Based Access**: 6 distinct roles with granular permissions (Student, SAS Staff, SAS Admin, Registrar Staff, USG President, System Admin)
- **Unified UI/UX**: Consistent design system using shadcn/ui components across all modules

## ✅ Quality Assurance

### Testing Strategy
- **Unit Tests**: Model logic, service methods, business rules validation
- **Feature Tests**: Complete user workflows (scholarship application, document request, payment flow)
- **Integration Tests**: Cross-module event handling, webhook processing
- **E2E Tests**: Critical paths (student registers → applies for scholarship → receives approval)

```powershell
php artisan test                    # Run all tests
php artisan test --coverage         # Generate coverage report
php artisan test --filter=Scholarship  # Run specific test suite
```

### Code Quality Enforcement
- **PHP**: Laravel Pint (PSR-12 standard) - `vendor/bin/pint --dirty`
- **JavaScript/TypeScript**: ESLint + Prettier - `npm run lint && npm run format:check`
- **Type Safety**: TypeScript strict mode, Psalm/PHPStan static analysis
- **Pre-commit Hooks**: Automated linting and testing before commits

### Performance Monitoring
- **Target Metrics**: <2s page load, <200ms API response, 500 concurrent users
- **Tools**: Laravel Telescope (development), Laravel Horizon (queue monitoring)
- **Database**: Query optimization, eager loading to prevent N+1 queries
- **Caching**: Redis for session storage, view caching, API response caching

## 🔒 Security Features

- ✅ **Authentication**: Laravel Fortify with two-factor authentication support
- ✅ **Authorization**: Spatie permissions with role hierarchies and permission gates
- ✅ **Input Validation**: Form request validation, XSS prevention, SQL injection protection
- ✅ **CSRF Protection**: Automatic CSRF token validation on all POST/PUT/DELETE requests
- ✅ **Rate Limiting**: API throttling (60 req/min per user, 300 req/min per IP)
- ✅ **Audit Logging**: Sensitive operations tracked (payment processing, scholarship approvals)
- ✅ **Data Encryption**: Database encryption for sensitive fields, HTTPS enforcement
- ✅ **Payment Security**: Webhook signature verification, idempotency key handling
- ✅ **File Upload**: Type validation, size limits, virus scanning ready

## 📦 Deployment

### Production Checklist
- [ ] Environment configured (`.env` with production values)
- [ ] Database migrations run (`php artisan migrate --force`)
- [ ] Assets built (`npm run build && npm run build:ssr`)
- [ ] Config cached (`php artisan config:cache`)
- [ ] Routes cached (`php artisan route:cache`)
- [ ] Views cached (`php artisan view:cache`)
- [ ] Queue worker running (`php artisan queue:work --daemon`)
- [ ] Scheduler configured (cron job for `php artisan schedule:run`)
- [ ] SSL certificate installed (Let's Encrypt recommended)
- [ ] Backups configured (daily database dumps, 30-day retention)
- [ ] Monitoring enabled (Sentry for errors, UptimeRobot for availability)

### Recommended Hosting

**Option 1: VPS (Full Control)**
- DigitalOcean Droplet or Linode ($20-40/month)
- Laravel Forge for deployment automation ($12/month)
- MySQL, Redis, Nginx on single server
- Cloudinary for file storage (free tier: 25GB)

**Option 2: Platform-as-a-Service (Easier)**
- Railway.app or Render.com ($20/month)
- Automatic deployments from GitHub
- Managed PostgreSQL/MySQL and Redis
- Built-in SSL and CDN

**Option 3: Traditional Shared Hosting**
- PHP 8.2+ support required
- SSH access for Composer and Artisan commands
- Cron job access for Laravel scheduler
- Not recommended for high-traffic production

### Queue Worker Setup

Production requires persistent queue workers:

```ini
# /etc/supervisor/conf.d/laravel-worker.conf
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
stopwaitsecs=3600
```

## 🤝 Contributing

We welcome contributions from the MinSU Bongabong community and external developers!

**📖 For comprehensive contribution guidelines, see [CONTRIBUTING.md](CONTRIBUTING.md)**

The contributing guide includes:
- **Getting Started**: Prerequisites and 9-step local setup process
- **Development Workflow**: Branch naming conventions and Git workflow
- **Coding Standards**: PHP PSR-12, TypeScript/React, migrations, APIs
- **Testing Requirements**: Pest examples and coverage goals (>70%)
- **Commit Guidelines**: Conventional Commits format with examples
- **Pull Request Process**: Pre-submission checklist and review criteria
- **Common Tasks**: Step-by-step instructions for features, bugs, docs, APIs
- **Getting Help**: Resources, communication channels, bug reporting

### Quick Start

1. **Fork** the repository and clone your fork
2. **Create a feature branch**: `git checkout -b feature/your-feature-name`
3. **Make your changes**: Keep commits focused and atomic
4. **Run quality checks**:
   ```powershell
   vendor/bin/pint --dirty      # Fix PHP code style
   npm run lint                 # Check JS/TS code
   npm run types                # TypeScript validation
   php artisan test             # Run test suite
   ```
5. **Commit with descriptive messages**: Follow [Conventional Commits](https://www.conventionalcommits.org/)
   ```
   feat(sas): add scholarship batch approval
   fix(registrar): prevent duplicate payment processing
   docs(api): update webhook signature verification
   ```
6. **Push to your fork** and **open a Pull Request**
7. **Respond to code review feedback**

📝 **See [CONTRIBUTING.md](CONTRIBUTING.md) for detailed code standards, testing requirements, and PR guidelines.**

## 🐛 Troubleshooting

### Common Issues

**Issue**: "Vite manifest not found"
```powershell
# Solution: Build assets
npm run build
```

**Issue**: Queue jobs not processing
```powershell
# Solution: Start queue worker
php artisan queue:work
```

**Issue**: Permission denied errors
```powershell
# Solution: Fix storage permissions (Windows)
icacls storage /grant Users:F /T
icacls bootstrap/cache /grant Users:F /T
```

**Issue**: Database connection failed
```powershell
# Solution: Verify MySQL is running and credentials are correct
mysql -u root -p -e "SHOW DATABASES;"
```

**Issue**: Payments not processing
- Check Paymongo API keys in `.env` (test vs production keys)
- Verify webhook URL is publicly accessible (use ngrok for local testing)
- Check `storage/logs/laravel.log` for webhook errors

## 📧 Support & Community

- **Issues**: [GitHub Issues](https://github.com/<org>/sas-information-system/issues)
- **Discussions**: [GitHub Discussions](https://github.com/<org>/sas-information-system/discussions)
- **Email**: ict@minsu.edu.ph
- **University IT**: MinSU Bongabong ICT Office

## 📄 License

This project is proprietary software developed for Mindoro State University – Bongabong Campus. Unauthorized distribution or modification is prohibited.

© 2025 Mindoro State University – Bongabong Campus. All rights reserved.

## 🙏 Acknowledgments

- **MinSU Bongabong Administration**: For vision and support
- **Student Affairs Office**: For requirements gathering and testing
- **Registrar Office**: For workflow documentation and validation
- **USG Officers**: For transparency requirements and feedback
- **Development Team**: For dedication to modernizing university services
- **Laravel Community**: For the excellent framework and ecosystem
- **Open Source Contributors**: For the packages that power this platform

---

**Built with ❤️ for MinSU Bongabong by the ICT Development Team**
