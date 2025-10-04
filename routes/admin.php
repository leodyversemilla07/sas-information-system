<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| System Administration Routes
|--------------------------------------------------------------------------
|
| These routes handle system-wide administration including user management,
| role/permission management, system settings, and audit logs.
| All routes require system_admin role.
|
*/

Route::middleware(['auth', 'verified', 'role:system_admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // Admin dashboard
        Route::get('dashboard', function () {
            return inertia('admin/dashboard');
        })->name('dashboard');

        // User management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', function () {
                return inertia('admin/users/index');
            })->middleware('permission:manage_users')
                ->name('index');

            Route::get('create', function () {
                return inertia('admin/users/create');
            })->middleware('permission:manage_users')
                ->name('create');

            Route::post('/', function () {
                // Controller will handle
            })->middleware('permission:manage_users')
                ->name('store');

            Route::get('{user}/edit', function () {
                return inertia('admin/users/edit');
            })->middleware('permission:manage_users')
                ->name('edit');

            Route::patch('{user}', function () {
                // Controller will handle
            })->middleware('permission:manage_users')
                ->name('update');

            Route::delete('{user}', function () {
                // Controller will handle
            })->middleware('permission:manage_users')
                ->name('destroy');

            // Bulk operations
            Route::post('bulk-import', function () {
                // Controller will handle CSV/Excel import
            })->middleware('permission:manage_users')
                ->name('bulk-import');

            Route::post('bulk-assign-roles', function () {
                // Controller will handle
            })->middleware('permission:manage_users')
                ->name('bulk-assign-roles');

            // Account actions
            Route::patch('{user}/suspend', function () {
                // Controller will handle
            })->middleware('permission:manage_users')
                ->name('suspend');

            Route::patch('{user}/activate', function () {
                // Controller will handle
            })->middleware('permission:manage_users')
                ->name('activate');

            Route::post('{user}/reset-password', function () {
                // Controller will handle
            })->middleware('permission:manage_users')
                ->name('reset-password');
        });

        // Role & Permission management
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', function () {
                return inertia('admin/roles/index');
            })->middleware('permission:manage_roles')
                ->name('index');

            Route::get('{role}', function () {
                return inertia('admin/roles/show');
            })->middleware('permission:manage_roles')
                ->name('show');

            Route::patch('{role}/permissions', function () {
                // Controller will handle permission assignment
            })->middleware('permission:manage_roles')
                ->name('update-permissions');

            // User role assignments
            Route::get('{role}/users', function () {
                return inertia('admin/roles/users');
            })->middleware('permission:manage_roles')
                ->name('users');

            Route::post('{role}/users/{user}', function () {
                // Controller will handle adding user to role
            })->middleware('permission:manage_roles')
                ->name('assign-user');

            Route::delete('{role}/users/{user}', function () {
                // Controller will handle removing user from role
            })->middleware('permission:manage_roles')
                ->name('remove-user');
        });

        // Permission management
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', function () {
                return inertia('admin/permissions/index');
            })->middleware('permission:manage_permissions')
                ->name('index');

            Route::get('{permission}', function () {
                return inertia('admin/permissions/show');
            })->middleware('permission:manage_permissions')
                ->name('show');

            // Direct user permission assignment
            Route::post('users/{user}/assign', function () {
                // Controller will handle direct permission assignment
            })->middleware('permission:manage_permissions')
                ->name('assign-to-user');

            Route::delete('users/{user}/revoke', function () {
                // Controller will handle permission revocation
            })->middleware('permission:manage_permissions')
                ->name('revoke-from-user');
        });

        // System settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', function () {
                return inertia('admin/settings/index');
            })->middleware('permission:manage_system_settings')
                ->name('index');

            Route::get('general', function () {
                return inertia('admin/settings/general');
            })->middleware('permission:manage_system_settings')
                ->name('general');

            Route::get('email', function () {
                return inertia('admin/settings/email');
            })->middleware('permission:manage_system_settings')
                ->name('email');

            Route::get('security', function () {
                return inertia('admin/settings/security');
            })->middleware('permission:manage_system_settings')
                ->name('security');

            Route::patch('/', function () {
                // Controller will handle
            })->middleware('permission:manage_system_settings')
                ->name('update');

            // Test email configuration
            Route::post('email/test', function () {
                // Controller will handle sending test email
            })->middleware('permission:manage_system_settings')
                ->name('email.test');
        });

        // Audit logs
        Route::prefix('audit')->name('audit.')->group(function () {
            Route::get('/', function () {
                return inertia('admin/audit/index');
            })->middleware('permission:view_audit_logs')
                ->name('index');

            Route::get('{log}', function () {
                return inertia('admin/audit/show');
            })->middleware('permission:view_audit_logs')
                ->name('show');

            Route::get('users/{user}', function () {
                return inertia('admin/audit/user-activity');
            })->middleware('permission:view_audit_logs')
                ->name('user-activity');

            // Export audit logs
            Route::post('export', function () {
                // Controller will handle CSV/Excel export
            })->middleware('permission:view_audit_logs')
                ->name('export');
        });

        // System health & monitoring
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('health', function () {
                return inertia('admin/system/health');
            })->middleware('permission:view_system_health')
                ->name('health');

            Route::get('logs', function () {
                return inertia('admin/system/logs');
            })->middleware('permission:view_system_health')
                ->name('logs');

            Route::get('cache', function () {
                return inertia('admin/system/cache');
            })->middleware('permission:manage_system_settings')
                ->name('cache');

            Route::post('cache/clear', function () {
                // Controller will handle cache clearing
            })->middleware('permission:manage_system_settings')
                ->name('cache.clear');

            Route::post('optimize', function () {
                // Controller will handle running optimization commands
            })->middleware('permission:manage_system_settings')
                ->name('optimize');
        });

        // Database management
        Route::prefix('database')->name('database.')->group(function () {
            Route::get('/', function () {
                return inertia('admin/database/index');
            })->middleware('permission:manage_system_settings')
                ->name('index');

            Route::post('backup', function () {
                // Controller will handle database backup
            })->middleware('permission:manage_system_settings')
                ->name('backup');

            Route::get('backups', function () {
                return inertia('admin/database/backups');
            })->middleware('permission:manage_system_settings')
                ->name('backups');

            Route::get('backups/{backup}/download', function () {
                // Controller will handle backup download
            })->middleware('permission:manage_system_settings')
                ->name('backups.download');

            Route::delete('backups/{backup}', function () {
                // Controller will handle backup deletion
            })->middleware('permission:manage_system_settings')
                ->name('backups.destroy');
        });

        // Reports & Analytics
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', function () {
                return inertia('admin/reports/index');
            })->name('index');

            Route::get('user-activity', function () {
                return inertia('admin/reports/user-activity');
            })->middleware('permission:view_audit_logs')
                ->name('user-activity');

            Route::get('system-usage', function () {
                return inertia('admin/reports/system-usage');
            })->middleware('permission:view_system_health')
                ->name('system-usage');

            Route::get('module-statistics', function () {
                return inertia('admin/reports/module-statistics');
            })->name('module-statistics');

            // Cross-module reports
            Route::get('comprehensive', function () {
                return inertia('admin/reports/comprehensive');
            })->name('comprehensive');
        });
    });
});
