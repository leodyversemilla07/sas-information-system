# Contributing to MinSU Bongabong Information System

Thank you for your interest in contributing to the Mindoro State University - Bongabong Campus Information System! This guide will help you get started with contributing to this project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Testing Requirements](#testing-requirements)
- [Commit Message Guidelines](#commit-message-guidelines)
- [Pull Request Process](#pull-request-process)
- [Project Structure](#project-structure)
- [Common Tasks](#common-tasks)
- [Getting Help](#getting-help)

## Code of Conduct

### Our Pledge

We are committed to providing a welcoming and inclusive environment for all contributors, regardless of experience level, background, or identity.

### Expected Behavior

- Be respectful and considerate in all interactions
- Welcome newcomers and help them get started
- Accept constructive criticism gracefully
- Focus on what is best for the MinSU Bongabong community
- Show empathy towards other community members

### Unacceptable Behavior

- Harassment, discrimination, or offensive comments
- Trolling, insulting/derogatory comments, or personal attacks
- Publishing others' private information without permission
- Any conduct that could reasonably be considered inappropriate in a professional setting

## Getting Started

### Prerequisites

Before you begin, ensure you have the following installed:

- **PHP 8.2+** - [Download PHP](https://www.php.net/downloads)
- **Composer 2.7+** - [Install Composer](https://getcomposer.org/download/)
- **Node.js 20+** and npm 10+ - [Download Node.js](https://nodejs.org/)
- **MySQL 8.0+** - [Download MySQL](https://dev.mysql.com/downloads/)
- **Git** - [Download Git](https://git-scm.com/downloads)

### Fork and Clone

1. **Fork the repository** on GitHub by clicking the "Fork" button

2. **Clone your fork** to your local machine:
   ```bash
   git clone https://github.com/YOUR_USERNAME/sas-information-system.git
   cd sas-information-system
   ```

3. **Add the upstream remote** to track the original repository:
   ```bash
   git remote add upstream https://github.com/minsu-bongabong/sas-information-system.git
   ```

### Local Development Setup

1. **Install PHP dependencies:**
   ```bash
   composer install
   ```

2. **Install JavaScript dependencies:**
   ```bash
   npm install
   ```

3. **Set up environment:**
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

4. **Configure database** in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=minsu_bongabong_dev
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Create database:**
   ```bash
   mysql -u root -p -e "CREATE DATABASE minsu_bongabong_dev;"
   ```

6. **Run migrations and seed data:**
   ```bash
   php artisan migrate --seed
   ```

7. **Start development servers:**
   
   Terminal 1 (Laravel):
   ```bash
   php artisan serve
   ```
   
   Terminal 2 (Vite):
   ```bash
   npm run dev
   ```

8. **Visit the application** at `http://127.0.0.1:8000`

9. **Login with test credentials:**
   - Student: `student@minsu.edu.ph` / `password`
   - SAS Admin: `sas.admin@minsu.edu.ph` / `password`
   - Registrar Staff: `registrar.staff@minsu.edu.ph` / `password`

## Development Workflow

### 1. Create a Feature Branch

Always create a new branch for your work. Never commit directly to `main`.

```bash
# Update your local main branch
git checkout main
git pull upstream main

# Create a new feature branch
git checkout -b feature/your-feature-name

# Examples:
# git checkout -b feature/scholarship-approval-workflow
# git checkout -b fix/document-request-payment-bug
# git checkout -b docs/update-api-documentation
```

### Branch Naming Conventions

Use descriptive branch names with prefixes:

| Prefix | Purpose | Example |
|--------|---------|---------|
| `feature/` | New features | `feature/event-calendar-sync` |
| `fix/` | Bug fixes | `fix/payment-webhook-timeout` |
| `docs/` | Documentation updates | `docs/add-deployment-guide` |
| `refactor/` | Code refactoring | `refactor/scholarship-service-layer` |
| `test/` | Adding or updating tests | `test/registrar-integration-tests` |
| `chore/` | Maintenance tasks | `chore/update-dependencies` |

### 2. Make Your Changes

- Write clean, maintainable code following our [coding standards](#coding-standards)
- Keep changes focused on a single issue or feature
- Write or update tests for your changes
- Update documentation if needed

### 3. Test Your Changes

Before committing, ensure all tests pass and code meets quality standards:

```bash
# Run PHP tests
php artisan test

# Run specific test file
php artisan test tests/Feature/SAS/ScholarshipTest.php

# Check PHP code style
vendor/bin/pint --test

# Fix PHP code style automatically
vendor/bin/pint

# Check JavaScript/TypeScript code
npm run lint
npm run types

# Check code formatting
npm run format:check

# Fix code formatting
npm run format
```

### 4. Commit Your Changes

Follow our [commit message guidelines](#commit-message-guidelines):

```bash
git add .
git commit -m "feat(sas): add scholarship batch approval feature"
```

### 5. Push to Your Fork

```bash
git push origin feature/your-feature-name
```

### 6. Create a Pull Request

1. Go to your fork on GitHub
2. Click "Compare & pull request"
3. Fill out the pull request template (see [Pull Request Process](#pull-request-process))
4. Submit the pull request

## Coding Standards

### PHP Code Style (PSR-12)

We use **Laravel Pint** to enforce PSR-12 coding standards.

**Key Rules:**
- 4 spaces for indentation (no tabs)
- Opening braces on the same line for methods and functions
- Always use curly braces for control structures
- Use type declarations for all method parameters and return types

**Example:**

```php
<?php

namespace App\Services\SAS;

use App\Models\SAS\Scholarship;
use Illuminate\Support\Collection;

class ScholarshipService
{
    public function __construct(
        private StudentVerificationService $verifier
    ) {}
    
    public function getEligibleStudents(): Collection
    {
        return Student::query()
            ->where('gpa', '>=', 2.5)
            ->where('enrollment_status', 'enrolled')
            ->get();
    }
}
```

**Run Pint before committing:**

```bash
vendor/bin/pint
```

### TypeScript/React Code Style

We use **ESLint** and **Prettier** for JavaScript/TypeScript formatting.

**Key Rules:**
- 2 spaces for indentation
- Single quotes for strings
- Semicolons required
- Arrow functions preferred
- Functional components with TypeScript

**Example:**

```typescript
import { FormEventHandler } from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

interface Props {
  scholarships: Scholarship[];
}

export default function ScholarshipIndex({ scholarships }: Props) {
  const { data, setData, post, processing } = useForm({
    scholarship_type: '',
  });

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post('/sas/scholarships/apply');
  };

  return (
    <div className="container mx-auto py-8">
      <h1 className="text-2xl font-bold mb-6">Scholarships</h1>
      {/* Component content */}
    </div>
  );
}
```

**Run linting before committing:**

```bash
npm run lint
npm run format
npm run types
```

### Database Migrations

**Rules:**
- Never modify existing migration files after they've been committed
- Always create new migrations for schema changes
- Use descriptive migration names
- Include both `up()` and `down()` methods
- Use table prefixes (`auth_`, `sas_`, `registrar_`, `usg_`)

**Example:**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sas_scholarship_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('auth_students')->onDelete('cascade');
            $table->foreignId('scholarship_id')->constrained('sas_scholarships');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('statement')->nullable();
            $table->decimal('gpa', 3, 2);
            $table->date('application_date');
            $table->timestamps();
            
            $table->index('status');
            $table->index('application_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sas_scholarship_applications');
    }
};
```

### API Routes and Controllers

**RESTful Conventions:**

| HTTP Method | URI | Action | Purpose |
|-------------|-----|--------|---------|
| GET | `/sas/scholarships` | `index` | List all scholarships |
| GET | `/sas/scholarships/create` | `create` | Show creation form |
| POST | `/sas/scholarships` | `store` | Create new scholarship |
| GET | `/sas/scholarships/{id}` | `show` | Show single scholarship |
| GET | `/sas/scholarships/{id}/edit` | `edit` | Show edit form |
| PUT/PATCH | `/sas/scholarships/{id}` | `update` | Update scholarship |
| DELETE | `/sas/scholarships/{id}` | `destroy` | Delete scholarship |

**Controller Example:**

```php
<?php

namespace App\Http\Controllers\SAS;

use App\Http\Controllers\Controller;
use App\Http\Requests\SAS\ScholarshipRequest;
use App\Models\SAS\Scholarship;
use Inertia\Inertia;
use Inertia\Response;

class ScholarshipController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Scholarship::class);
        
        $scholarships = Scholarship::query()
            ->with('student')
            ->latest()
            ->paginate(20);
        
        return Inertia::render('SAS/Scholarships/Index', [
            'scholarships' => $scholarships,
        ]);
    }
    
    public function store(ScholarshipRequest $request)
    {
        $this->authorize('create', Scholarship::class);
        
        $scholarship = Scholarship::create($request->validated());
        
        return redirect()
            ->route('sas.scholarships.show', $scholarship)
            ->with('success', 'Scholarship created successfully');
    }
}
```

## Testing Requirements

All code contributions must include appropriate tests. We aim for **>70% code coverage**.

### Test Types

#### 1. Feature Tests (Primary)

Test complete user workflows from HTTP request to response.

**Location:** `tests/Feature/`

**Example:**

```php
<?php

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
    
    $response = $this->actingAs($user)
        ->post('/registrar/requests', [
            'document_type_id' => 1,
        ]);
    
    $response->assertForbidden();
});
```

#### 2. Unit Tests

Test individual methods and classes in isolation.

**Location:** `tests/Unit/`

**Example:**

```php
<?php

use App\Services\SAS\ScholarshipService;
use App\Models\Student;

describe('ScholarshipService', function () {
    it('determines eligibility correctly for qualified students', function () {
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

#### 3. Integration Tests

Test interactions between multiple components.

**Example:**

```php
<?php

use App\Events\SAS\EventCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Cache;

it('syncs SAS events to USG calendar', function () {
    Event::fake();
    
    $event = \App\Models\SAS\Event::factory()->create([
        'title' => 'Campus Festival',
        'date' => now()->addWeek(),
    ]);
    
    EventCreated::dispatch($event);
    
    Event::assertDispatched(EventCreated::class);
    
    // Manually trigger listener for testing
    (new \App\Listeners\USG\SyncEventToPublicCalendar())->handle(
        new EventCreated($event)
    );
    
    expect(Cache::has("usg:calendar:event:{$event->id}"))->toBeTrue();
});
```

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run specific test file
php artisan test tests/Feature/SAS/ScholarshipTest.php

# Run with coverage
php artisan test --coverage

# Run specific test by name
php artisan test --filter=scholarship
```

### Test Coverage Goals

| Layer | Target Coverage | Priority |
|-------|----------------|----------|
| Services | >85% | High |
| Controllers | >75% | High |
| Models | >80% | Medium |
| Helpers | >90% | Medium |
| Overall | >70% | Required |

## Commit Message Guidelines

We follow the **Conventional Commits** specification for clear and structured commit messages.

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Type

Must be one of the following:

- **feat**: A new feature
- **fix**: A bug fix
- **docs**: Documentation only changes
- **style**: Changes that don't affect code meaning (formatting, whitespace)
- **refactor**: Code change that neither fixes a bug nor adds a feature
- **perf**: Performance improvement
- **test**: Adding or updating tests
- **chore**: Changes to build process or auxiliary tools

### Scope

The scope should indicate which module is affected:

- `sas` - Student Affairs Services module
- `registrar` - Registrar module
- `usg` - USG Portal module
- `auth` - Authentication/authorization
- `api` - API endpoints
- `db` - Database changes
- `deps` - Dependencies

### Examples

```bash
# Feature addition
git commit -m "feat(sas): add scholarship batch approval functionality"

# Bug fix
git commit -m "fix(registrar): prevent duplicate payment processing on webhook retry"

# Documentation
git commit -m "docs(api): update webhook signature verification examples"

# Refactoring
git commit -m "refactor(sas): extract scholarship eligibility logic to service"

# Multiple changes
git commit -m "feat(registrar): implement PDF generation queue

- Add GenerateTranscript job
- Configure document queue with priority
- Add email notification on completion
- Update DocumentRequestService to dispatch job

Closes #123"
```

### Breaking Changes

If your commit introduces breaking changes, add `BREAKING CHANGE:` in the footer:

```bash
git commit -m "feat(api): change payment webhook response format

BREAKING CHANGE: Webhook now returns JSON instead of plain text.
Update your webhook handlers to parse JSON response."
```

## Pull Request Process

### Before Submitting

1. ✅ **Update your branch** with the latest changes from `main`:
   ```bash
   git fetch upstream
   git rebase upstream/main
   ```

2. ✅ **Run all quality checks**:
   ```bash
   vendor/bin/pint
   npm run lint
   npm run format
   php artisan test
   ```

3. ✅ **Test manually** in your browser if UI changes are involved

4. ✅ **Update documentation** if you changed APIs, added features, or modified architecture

### Pull Request Template

When creating a PR, include the following information:

```markdown
## Description
Brief description of what this PR does.

## Related Issue
Closes #123

## Type of Change
- [ ] Bug fix (non-breaking change that fixes an issue)
- [ ] New feature (non-breaking change that adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update
- [ ] Code refactoring
- [ ] Performance improvement

## Module(s) Affected
- [ ] SAS (Student Affairs)
- [ ] Registrar
- [ ] USG Portal
- [ ] Authentication
- [ ] Infrastructure

## How Has This Been Tested?
Describe the tests you ran and how to reproduce them.

- [ ] Feature tests added/updated
- [ ] Unit tests added/updated
- [ ] Manual testing completed
- [ ] Browser tested (Chrome, Firefox, Safari)

## Screenshots (if applicable)
Add screenshots for UI changes.

## Checklist
- [ ] My code follows the project's code style (Pint, ESLint, Prettier)
- [ ] I have performed a self-review of my code
- [ ] I have commented my code, particularly in hard-to-understand areas
- [ ] I have updated the documentation accordingly
- [ ] My changes generate no new warnings or errors
- [ ] I have added tests that prove my fix is effective or that my feature works
- [ ] New and existing unit tests pass locally with my changes
- [ ] Any dependent changes have been merged and published

## Additional Notes
Any other information reviewers should know.
```

### Review Process

1. **Automated Checks**: GitHub Actions will run tests and code quality checks
2. **Code Review**: At least one maintainer will review your PR
3. **Address Feedback**: Make requested changes and push updates
4. **Approval**: Once approved, a maintainer will merge your PR

### Review Criteria

Reviewers will check:
- ✅ Code follows project conventions and standards
- ✅ Tests are comprehensive and passing
- ✅ Documentation is updated
- ✅ No security vulnerabilities introduced
- ✅ Performance impact is acceptable
- ✅ Changes are scoped appropriately (not too large)
- ✅ Commit messages are clear and follow conventions

## Project Structure

Understanding the project structure helps you know where to make changes:

```
sas-information-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Organize by module (SAS/, Registrar/, USG/)
│   │   ├── Middleware/         # Custom middleware
│   │   └── Requests/           # Form validation requests
│   ├── Models/                 # Eloquent models (SAS/, Registrar/, USG/)
│   ├── Services/               # Business logic layer
│   ├── Events/                 # Domain events
│   ├── Listeners/              # Event listeners
│   ├── Jobs/                   # Queue jobs
│   └── Policies/               # Authorization policies
├── database/
│   ├── migrations/             # Database migrations (timestamped)
│   ├── factories/              # Model factories for testing
│   └── seeders/                # Database seeders
├── docs/                       # Project documentation
│   ├── PRD.md                  # Product Requirements
│   ├── USER_STORIES.md         # User stories
│   ├── API_SPECIFICATIONS.md   # API documentation
│   ├── DATA_MODELS.md          # Database schema
│   ├── NFR.md                  # Non-functional requirements
│   └── ARCHITECTURE.md         # Technical architecture
├── resources/
│   ├── css/                    # Stylesheets (Tailwind entry)
│   ├── js/
│   │   ├── components/         # Reusable React components
│   │   ├── layouts/            # Page layouts
│   │   ├── pages/              # Inertia page components (SAS/, Registrar/, USG/)
│   │   ├── types/              # TypeScript definitions
│   │   └── lib/                # Utility functions
│   └── views/                  # Blade templates (minimal)
├── routes/
│   ├── web.php                 # Web routes (Inertia)
│   ├── auth.php                # Authentication routes
│   └── api.php                 # API routes (webhooks)
├── tests/
│   ├── Feature/                # Feature tests
│   └── Unit/                   # Unit tests
└── storage/                    # Application storage (logs, cache, uploads)
```

## Common Tasks

### Adding a New Feature

1. **Check existing documentation**: Review `docs/USER_STORIES.md` and `docs/PRD.md`
2. **Create a branch**: `git checkout -b feature/your-feature`
3. **Create migration** (if needed): `php artisan make:migration create_table_name`
4. **Create model** (if needed): `php artisan make:model ModelName`
5. **Create controller**: `php artisan make:controller ModuleNameController`
6. **Create form request**: `php artisan make:request ModuleNameRequest`
7. **Create service**: `app/Services/Module/FeatureService.php`
8. **Create Inertia page**: `resources/js/pages/Module/Feature.tsx`
9. **Add routes**: `routes/web.php`
10. **Write tests**: `tests/Feature/Module/FeatureTest.php`
11. **Update documentation**: Add to relevant docs in `docs/`

### Fixing a Bug

1. **Create an issue** describing the bug (if not already reported)
2. **Create a branch**: `git checkout -b fix/bug-description`
3. **Write a failing test** that reproduces the bug
4. **Fix the bug**
5. **Verify the test passes**
6. **Create PR** with reference to the issue

### Updating Documentation

1. **Create a branch**: `git checkout -b docs/update-description`
2. **Update relevant files** in `docs/` or root directory
3. **Check for broken links** and formatting
4. **Create PR** with clear description of changes

### Adding Database Schema Changes

1. **Never modify existing migrations** that have been deployed
2. **Create new migration**: `php artisan make:migration add_field_to_table`
3. **Implement `up()` and `down()` methods**
4. **Test migration**: `php artisan migrate` and `php artisan migrate:rollback`
5. **Update model** if relationships or casts changed
6. **Update factory** if needed for testing
7. **Update `docs/DATA_MODELS.md`** with schema changes
8. **Write tests** for new functionality

### Adding API Endpoints

1. **Document API first** in `docs/API_SPECIFICATIONS.md`
2. **Create controller method**
3. **Add route** in `routes/api.php` or `routes/web.php`
4. **Add authentication/authorization** (middleware, policies)
5. **Add input validation** (form request)
6. **Write tests** for all response scenarios (success, errors, unauthorized)
7. **Add rate limiting** if needed

## Getting Help

### Resources

- **Documentation**: Check the `docs/` directory for comprehensive guides
- **README**: Start with the main README.md for setup instructions
- **Architecture**: Review `docs/ARCHITECTURE.md` for system design
- **User Stories**: See `docs/USER_STORIES.md` for feature requirements

### Communication Channels

- **GitHub Issues**: For bug reports and feature requests
- **GitHub Discussions**: For questions and general discussion
- **Email**: development@minsu.edu.ph for project-related inquiries
- **MinSU Bongabong ICT**: ict.bongabong@minsu.edu.ph for campus-specific matters

### Asking Questions

When asking for help:

1. **Search existing issues** and discussions first
2. **Provide context**: What are you trying to do?
3. **Show your code**: Share relevant code snippets
4. **Describe what you've tried**: What solutions have you attempted?
5. **Include error messages**: Full stack traces help diagnose issues
6. **Specify your environment**: OS, PHP version, browser, etc.

### Reporting Bugs

Use the GitHub issue template and include:

- **Clear title**: Descriptive summary of the issue
- **Description**: What happened vs. what you expected
- **Steps to reproduce**: Numbered list of exact steps
- **Environment**: OS, PHP version, browser
- **Screenshots**: If applicable
- **Error messages**: Full stack traces
- **Related issues**: Link to similar issues

## Recognition

Contributors will be recognized in:
- **README.md**: Contributors section
- **Release notes**: Feature credits
- **GitHub insights**: Contribution graphs

Significant contributions may be acknowledged in:
- Project documentation
- University publications (with permission)
- Academic credits (if applicable)

## License

By contributing, you agree that your contributions will be licensed under the same proprietary license as the project (© 2025 Mindoro State University - Bongabong Campus).

---

Thank you for contributing to the MinSU Bongabong Information System! Your efforts help improve education technology for students and staff at Mindoro State University - Bongabong Campus. 🎓
