# Role-Based Access Control (RBAC) Documentation
## MinSU Bongabong Information System

**Document Version:** 1.0  
**Date:** October 4, 2025  
**Package:** Spatie Laravel Permission v6.21.0  
**Full Name:** Mindoro State University - Bongabong Campus Information System

---

## Table of Contents

1. [Overview](#overview)
2. [Role Hierarchy](#role-hierarchy)
3. [Permissions Matrix](#permissions-matrix)
4. [Implementation Guide](#implementation-guide)
5. [Policy Examples](#policy-examples)
6. [Frontend Authorization](#frontend-authorization)
7. [Testing](#testing)
8. [Best Practices](#best-practices)

---

## Overview

The MinSU Bongabong Information System implements a comprehensive Role-Based Access Control (RBAC) system using Spatie Laravel Permission. This system manages access across three modules:

- **Student Affairs Services (SAS)** - Scholarships, organizations, events
- **Registrar** - Document requests, payments, enrollment, academic records
- **University Student Government (USG)** - Announcements, resolutions, student engagement

### Key Features

✅ **8 User Roles** with hierarchical permissions  
✅ **79+ Granular Permissions** organized by module  
✅ **Type-Safe Enums** for roles and permissions  
✅ **Policy-Based Authorization** with business logic  
✅ **Middleware Protection** for routes  
✅ **Frontend Permission Checks** via Inertia.js  
✅ **Comprehensive Test Coverage** with Pest

---

## Role Hierarchy

### Role Structure

```
System Administrator
├── Module Administrators
│   ├── SAS Administrator
│   ├── Registrar Administrator
│   └── USG Administrator
├── Module Staff
│   ├── SAS Staff
│   ├── Registrar Staff
│   └── USG Officer
└── Students
```

### Role Definitions

#### 1. **Student** (`student`)
- **Access Level:** Own records only
- **Description:** Regular students who can submit applications, request documents, and view their own data
- **Permission Count:** 16 permissions
- **Key Capabilities:**
  - Submit scholarship applications
  - Request academic documents
  - View own records and status
  - Provide feedback to USG

#### 2. **SAS Staff** (`sas_staff`)
- **Access Level:** SAS module read/write
- **Description:** Student Affairs staff who manage day-to-day operations
- **Permission Count:** 18 permissions
- **Key Capabilities:**
  - Review scholarship applications
  - Approve scholarships under ₱20,000
  - Manage organizations and events
  - View reports and analytics

#### 3. **SAS Administrator** (`sas_admin`)
- **Access Level:** Full SAS module access
- **Description:** Student Affairs administrators with elevated privileges
- **Permission Count:** 23 permissions
- **Key Capabilities:**
  - All SAS Staff permissions
  - Approve high-value scholarships (≥₱20,000)
  - Bulk operations
  - Financial reports and reconciliation

#### 4. **Registrar Staff** (`registrar_staff`)
- **Access Level:** Registrar module processing
- **Description:** Registrar staff who process document requests and payments
- **Permission Count:** 8 permissions
- **Key Capabilities:**
  - Process document requests
  - Verify payments
  - Issue documents
  - View academic records

#### 5. **Registrar Administrator** (`registrar_admin`)
- **Access Level:** Full Registrar module access
- **Description:** Registrar administrators managing enrollment and records
- **Permission Count:** 11 permissions
- **Key Capabilities:**
  - All Registrar Staff permissions
  - Manage enrollment periods
  - Issue refunds
  - Manual payment reconciliation
  - Edit academic records

#### 6. **USG Officer** (`usg_officer`)
- **Access Level:** USG content management
- **Description:** USG officers who manage announcements and student engagement
- **Permission Count:** 14 permissions
- **Key Capabilities:**
  - Create and edit announcements
  - Create and edit resolutions
  - Respond to student feedback
  - View engagement analytics

#### 7. **USG Administrator** (`usg_admin`)
- **Access Level:** Full USG module access
- **Description:** USG administrators with complete portal control
- **Permission Count:** 24 permissions
- **Key Capabilities:**
  - All USG Officer permissions
  - Manage VMGO (Vision, Mission, Goals, Objectives)
  - Manage officer profiles and terms
  - Publish/unpublish content
  - Full analytics and reports

#### 8. **System Administrator** (`system_admin`)
- **Access Level:** Full system access
- **Description:** IT administrators with complete system control
- **Permission Count:** 79 permissions (all)
- **Key Capabilities:**
  - User management
  - Role and permission management
  - System settings and configuration
  - Audit logs and monitoring
  - Database backups
  - All module permissions

---

## Permissions Matrix

### SAS Module Permissions (16 total)

| Permission | Student | SAS Staff | SAS Admin | System Admin |
|------------|---------|-----------|-----------|--------------|
| `submit_scholarship_application` | ✅ | ✅ | ✅ | ✅ |
| `view_own_scholarships` | ✅ | ✅ | ✅ | ✅ |
| `edit_own_scholarships` | ✅ | ❌ | ❌ | ✅ |
| `view_all_scholarships` | ❌ | ✅ | ✅ | ✅ |
| `review_scholarships` | ❌ | ✅ | ✅ | ✅ |
| `approve_scholarships_under_20k` | ❌ | ✅ | ✅ | ✅ |
| `approve_scholarships_over_20k` | ❌ | ❌ | ✅ | ✅ |
| `reject_scholarships` | ❌ | ✅ | ✅ | ✅ |
| `view_disbursements` | ❌ | ✅ | ✅ | ✅ |
| `manage_disbursements` | ❌ | ❌ | ✅ | ✅ |
| `manage_organizations` | ❌ | ✅ | ✅ | ✅ |
| `view_organizations` | ✅ | ✅ | ✅ | ✅ |
| `create_events` | ❌ | ✅ | ✅ | ✅ |
| `view_events` | ✅ | ✅ | ✅ | ✅ |
| `track_attendance` | ❌ | ✅ | ✅ | ✅ |
| `view_sas_reports` | ❌ | ❌ | ✅ | ✅ |

### Registrar Module Permissions (11 total)

| Permission | Student | Registrar Staff | Registrar Admin | System Admin |
|------------|---------|-----------------|-----------------|--------------|
| `request_documents` | ✅ | ✅ | ✅ | ✅ |
| `view_own_requests` | ✅ | ✅ | ✅ | ✅ |
| `process_document_requests` | ❌ | ✅ | ✅ | ✅ |
| `view_all_requests` | ❌ | ✅ | ✅ | ✅ |
| `view_payments` | ✅ | ✅ | ✅ | ✅ |
| `verify_payments` | ❌ | ✅ | ✅ | ✅ |
| `issue_refunds` | ❌ | ❌ | ✅ | ✅ |
| `manual_reconciliation` | ❌ | ❌ | ✅ | ✅ |
| `manage_enrollment` | ❌ | ❌ | ✅ | ✅ |
| `view_academic_records` | ❌ | ✅ | ✅ | ✅ |
| `manage_academic_records` | ❌ | ❌ | ✅ | ✅ |

### USG Module Permissions (14 total)

| Permission | Student | USG Officer | USG Admin | System Admin |
|------------|---------|-------------|-----------|--------------|
| `view_vmgo` | ✅ | ✅ | ✅ | ✅ |
| `manage_vmgo` | ❌ | ❌ | ✅ | ✅ |
| `view_officers` | ✅ | ✅ | ✅ | ✅ |
| `manage_officers` | ❌ | ❌ | ✅ | ✅ |
| `view_announcements` | ✅ | ✅ | ✅ | ✅ |
| `create_announcements` | ❌ | ✅ | ✅ | ✅ |
| `edit_announcements` | ❌ | ✅ | ✅ | ✅ |
| `delete_announcements` | ❌ | ✅ | ✅ | ✅ |
| `publish_announcements` | ❌ | ❌ | ✅ | ✅ |
| `view_resolutions` | ✅ | ✅ | ✅ | ✅ |
| `create_resolutions` | ❌ | ✅ | ✅ | ✅ |
| `edit_resolutions` | ❌ | ✅ | ✅ | ✅ |
| `delete_resolutions` | ❌ | ✅ | ✅ | ✅ |
| `publish_resolutions` | ❌ | ❌ | ✅ | ✅ |
| `submit_feedback` | ✅ | ✅ | ✅ | ✅ |
| `view_feedback` | ❌ | ✅ | ✅ | ✅ |
| `respond_to_feedback` | ❌ | ✅ | ✅ | ✅ |
| `manage_usg_settings` | ❌ | ❌ | ✅ | ✅ |

### System Administration Permissions (38 total)

| Category | Permissions |
|----------|-------------|
| **User Management** | `manage_users`, `view_users`, `create_users`, `edit_users`, `delete_users`, `suspend_users`, `activate_users` |
| **Role Management** | `manage_roles`, `view_roles`, `assign_roles`, `remove_roles` |
| **Permission Management** | `manage_permissions`, `view_permissions`, `assign_permissions`, `revoke_permissions` |
| **System Settings** | `manage_system_settings`, `view_system_settings`, `edit_email_settings`, `edit_security_settings` |
| **Audit & Monitoring** | `view_audit_logs`, `export_audit_logs`, `view_system_health`, `view_system_logs` |
| **Database** | `manage_database`, `create_backups`, `restore_backups`, `view_backups` |
| **Cache & Optimization** | `clear_cache`, `optimize_system` |

---

## Implementation Guide

### Backend Implementation

#### 1. Assigning Roles to Users

```php
use App\Enums\Role;
use App\Models\User;

// Single role assignment
$user->assignRole(Role::Student->value);

// Multiple roles
$user->assignRole([Role::Student->value, Role::SasStaff->value]);

// Sync roles (remove all existing, add new)
$user->syncRoles([Role::SasAdmin->value]);
```

#### 2. Checking Permissions in Controllers

```php
namespace App\Http\Controllers\SAS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        // Method 1: Using authorize() with policy
        $this->authorize('viewAny', Scholarship::class);
        
        // Method 2: Using can() helper
        if ($request->user()->can('view_all_scholarships')) {
            // Show all scholarships
        }
        
        // Method 3: Using Gate facade
        if (Gate::allows('view_all_scholarships')) {
            // Show all scholarships
        }
        
        return inertia('sas/scholarships/index');
    }
    
    public function approve(Request $request, Scholarship $scholarship)
    {
        // Policy method with business logic
        $this->authorize('approve', $scholarship);
        
        // Approval logic...
        
        return back()->with('success', 'Scholarship approved');
    }
}
```

#### 3. Route Protection with Middleware

```php
// Role-based protection
Route::middleware(['role:sas_staff|sas_admin'])->group(function () {
    Route::get('/sas/dashboard', [DashboardController::class, 'index']);
});

// Permission-based protection
Route::middleware(['permission:approve_scholarships_over_20k'])->group(function () {
    Route::patch('/sas/scholarships/{id}/approve', [ScholarshipController::class, 'approve']);
});

// Combined protection
Route::middleware(['role:registrar_admin', 'permission:issue_refunds'])->group(function () {
    Route::post('/registrar/payments/{id}/refund', [PaymentController::class, 'refund']);
});
```

---

## Policy Examples

### ScholarshipPolicy - Amount-Based Approval

```php
namespace App\Policies\SAS;

use App\Models\User;
use App\Enums\Permission;

class ScholarshipPolicy
{
    /**
     * Determine if user can approve scholarship based on amount
     * 
     * Business Rule: 
     * - Amounts < ₱20,000: sas_staff or sas_admin
     * - Amounts ≥ ₱20,000: sas_admin only
     */
    public function approve(User $user, $scholarship): bool
    {
        $amount = $scholarship->amount;
        
        // Under ₱20,000 - either staff or admin can approve
        if ($amount < 20000) {
            return $user->can(Permission::ApproveScholarshipsUnder20k->value);
        }
        
        // ≥ ₱20,000 - only admin can approve
        return $user->can(Permission::ApproveScholarshipsOver20k->value);
    }
}
```

### DocumentRequestPolicy - Status-Based Authorization

```php
namespace App\Policies\Registrar;

use App\Models\User;
use App\Enums\Permission;

class DocumentRequestPolicy
{
    public function update(User $user, $documentRequest): bool
    {
        // Students can only update their own pending requests
        if ($user->hasRole('student')) {
            return $documentRequest->student_id === $user->id 
                && $documentRequest->status === 'pending';
        }
        
        // Staff can update any request
        return $user->can(Permission::ProcessDocumentRequests->value);
    }
    
    public function cancel(User $user, $documentRequest): bool
    {
        // Students can cancel their own non-completed requests
        if ($documentRequest->student_id === $user->id) {
            return !in_array($documentRequest->status, ['completed', 'released']);
        }
        
        // Admins can cancel any request
        return $user->hasRole('registrar_admin');
    }
}
```

---

## Frontend Authorization

### 1. TypeScript Type Definitions

```typescript
// resources/js/types/index.d.ts
export interface Auth {
    user: User;
    roles: string[];
    permissions: string[];
}
```

### 2. usePermissions Hook

```typescript
import { usePermissions } from '@/hooks/use-permissions';

function ScholarshipCard({ scholarship }) {
    const { can, hasRole, isSasAdmin } = usePermissions();
    
    return (
        <div>
            <h2>{scholarship.title}</h2>
            
            {can('approve_scholarships_over_20k') && (
                <button>Approve</button>
            )}
            
            {hasRole('sas_admin') && (
                <button>Delete</button>
            )}
            
            {isSasAdmin() && (
                <Link href="/sas/reports">View Reports</Link>
            )}
        </div>
    );
}
```

### 3. Authorization Components

```typescript
import { Can, HasRole } from '@/components/authorization';

function Dashboard() {
    return (
        <div>
            <Can permission="approve_scholarships_over_20k">
                <ApprovalPanel />
            </Can>
            
            <HasRole role={['sas_staff', 'sas_admin']}>
                <StaffControls />
            </HasRole>
            
            <Can permission={['edit_announcements', 'delete_announcements']}>
                <AnnouncementManager />
            </Can>
        </div>
    );
}
```

---

## Testing

### Role Assignment Tests

```php
use App\Enums\Role;
use App\Models\User;

it('can assign role to user', function () {
    $user = User::factory()->create();
    
    $user->assignRole(Role::Student->value);
    
    expect($user->hasRole(Role::Student->value))->toBeTrue();
});
```

### Permission Tests

```php
it('sas admin has approve over 20k permission', function () {
    $user = User::factory()->sasAdmin()->create();
    
    expect($user->can('approve_scholarships_over_20k'))->toBeTrue();
});
```

### Policy Tests

```php
use App\Policies\SAS\ScholarshipPolicy;

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
```

### Middleware Protection Tests

```php
it('allows sas admin to access reports', function () {
    $admin = User::factory()->sasAdmin()->create();
    
    $response = $this->actingAs($admin)->get('/sas/reports/scholarships');
    
    $response->assertSuccessful();
});

it('denies students from accessing sas dashboard', function () {
    $student = User::factory()->student()->create();
    
    $response = $this->actingAs($student)->get('/sas/dashboard');
    
    $response->assertForbidden();
});
```

---

## Best Practices

### 1. **Always Use Enums**
```php
// ✅ Good - Type-safe
$user->assignRole(Role::Student->value);

// ❌ Bad - String magic values
$user->assignRole('student');
```

### 2. **Use Policies for Business Logic**
```php
// ✅ Good - Business logic in policy
$this->authorize('approve', $scholarship);

// ❌ Bad - Business logic in controller
if ($user->hasRole('sas_admin') && $scholarship->amount >= 20000) {
    // approval logic
}
```

### 3. **Prefer Middleware for Routes**
```php
// ✅ Good - Middleware protection
Route::middleware(['role:sas_admin'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
});

// ❌ Bad - Manual checks in controller
public function index(Request $request) {
    if (!$request->user()->hasRole('sas_admin')) {
        abort(403);
    }
}
```

### 4. **Cache Permission Checks**
```php
// Permission checks are automatically cached by Spatie
// Clear cache after role/permission changes:
php artisan permission:cache-reset
```

### 5. **Test Authorization Logic**
```php
// Always write tests for:
// - Role assignments
// - Permission checks
// - Policy methods
// - Middleware protection
// - Frontend authorization
```

---

## Super Admin Pattern

System administrators have access to all permissions. To implement super admin logic:

```php
// In a service provider or Gate::before()
Gate::before(function (User $user, string $ability) {
    return $user->hasRole('system_admin') ? true : null;
});
```

This allows system admins to bypass all authorization checks.

---

## Database Schema

The Spatie package creates 5 tables:

```sql
-- Core tables
roles                     -- 8 roles
permissions              -- 79+ permissions
role_has_permissions     -- Permission assignments to roles

-- User relationships
model_has_roles          -- User role assignments
model_has_permissions    -- Direct permission assignments (optional)
```

---

## API Authorization Patterns

For API endpoints, include authorization checks:

```php
// API Controller
class ApiScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Scholarship::class);
        
        $scholarships = Scholarship::query()
            ->when($request->user()->hasRole('student'), function ($query) use ($request) {
                $query->where('student_id', $request->user()->id);
            })
            ->get();
            
        return ScholarshipResource::collection($scholarships);
    }
}
```

---

## Troubleshooting

### Permission Not Working

1. Clear permission cache:
```bash
php artisan permission:cache-reset
```

2. Verify role has permission:
```php
$role = Role::findByName('sas_staff');
dd($role->permissions->pluck('name'));
```

3. Check user has role:
```php
dd($user->getRoleNames());
```

### Middleware Not Protecting Routes

1. Verify middleware is registered in `bootstrap/app.php`
2. Check route middleware order
3. Ensure user is authenticated before role/permission check

---

## Migration Guide

If updating from a previous authorization system:

```bash
# 1. Backup database
php artisan db:backup

# 2. Run migrations
php artisan migrate

# 3. Seed roles and permissions
php artisan db:seed --class=RoleAndPermissionSeeder

# 4. Migrate existing user roles
# (Create custom migration script)

# 5. Clear caches
php artisan permission:cache-reset
php artisan optimize:clear
```

---

## Support & Resources

- **Spatie Documentation:** https://spatie.be/docs/laravel-permission/
- **Project Repository:** See README.md
- **Internal Wiki:** [Link to internal documentation]

---

**Document Maintained By:** Development Team  
**Last Updated:** October 4, 2025  
**Next Review:** Quarterly or when authorization requirements change
