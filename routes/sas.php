<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Affairs Services (SAS) Routes
|--------------------------------------------------------------------------
|
| These routes handle scholarship applications, organization management,
| and event coordination within the Student Affairs Services module.
| All routes require authentication and appropriate SAS role/permissions.
|
*/

// Public SAS routes (authenticated students)
Route::middleware(['auth', 'verified'])->group(function () {
    // Student-accessible routes
    Route::prefix('sas')->name('sas.')->group(function () {
        // Events - public viewing
        Route::get('events', function () {
            return inertia('sas/events/index');
        })->name('events.index');

        Route::get('events/{event}', function () {
            return inertia('sas/events/show');
        })->name('events.show');

        // Organizations - public viewing
        Route::get('organizations', function () {
            return inertia('sas/organizations/index');
        })->name('organizations.index');

        Route::get('organizations/{organization}', function () {
            return inertia('sas/organizations/show');
        })->name('organizations.show');

        // Student scholarship applications
        Route::middleware(['role:student'])->group(function () {
            Route::get('scholarships/my-applications', function () {
                return inertia('sas/scholarships/my-applications');
            })->name('scholarships.my-applications');

            Route::get('scholarships/apply', function () {
                return inertia('sas/scholarships/apply');
            })->name('scholarships.apply');

            Route::post('scholarships/applications', function () {
                // Controller will handle
            })->name('scholarships.applications.store');

            Route::get('scholarships/applications/{application}', function () {
                return inertia('sas/scholarships/application-details');
            })->name('scholarships.applications.show');
        });
    });
});

// SAS Staff routes (sas_staff and sas_admin)
Route::middleware(['auth', 'verified', 'role:sas_staff|sas_admin'])->group(function () {
    Route::prefix('sas')->name('sas.')->group(function () {
        // Staff dashboard
        Route::get('dashboard', function () {
            return inertia('sas/dashboard');
        })->name('dashboard');

        // Scholarship management
        Route::prefix('scholarships')->name('scholarships.')->group(function () {
            // View all applications
            Route::get('applications', function () {
                return inertia('sas/scholarships/applications/index');
            })->name('applications.index');

            Route::get('applications/{application}', function () {
                return inertia('sas/scholarships/applications/show');
            })->name('applications.show');

            // Review applications (both staff and admin)
            Route::patch('applications/{application}/review', function () {
                // Controller will handle
            })->middleware('permission:review_scholarships')
                ->name('applications.review');

            // Approve applications - amount-based permission check
            Route::patch('applications/{application}/approve', function () {
                // Controller will handle with policy check for amount threshold
            })->middleware('permission:approve_scholarships_under_20k|approve_scholarships_over_20k')
                ->name('applications.approve');

            // Bulk approve (admin only)
            Route::post('applications/bulk-approve', function () {
                // Controller will handle
            })->middleware('role:sas_admin')
                ->name('applications.bulk-approve');

            Route::patch('applications/{application}/reject', function () {
                // Controller will handle
            })->middleware('permission:reject_scholarships')
                ->name('applications.reject');

            // Disbursement tracking
            Route::get('disbursements', function () {
                return inertia('sas/scholarships/disbursements/index');
            })->middleware('permission:view_disbursements')
                ->name('disbursements.index');

            Route::patch('disbursements/{disbursement}/mark-released', function () {
                // Controller will handle
            })->middleware('permission:manage_disbursements')
                ->name('disbursements.mark-released');
        });

        // Organization management
        Route::prefix('organizations')->name('organizations.')->group(function () {
            Route::get('manage', function () {
                return inertia('sas/organizations/manage');
            })->middleware('permission:manage_organizations')
                ->name('manage');

            Route::post('/', function () {
                // Controller will handle
            })->middleware('permission:manage_organizations')
                ->name('store');

            Route::patch('{organization}', function () {
                // Controller will handle
            })->middleware('permission:manage_organizations')
                ->name('update');

            Route::delete('{organization}', function () {
                // Controller will handle
            })->middleware('permission:manage_organizations')
                ->name('destroy');

            // Member management
            Route::post('{organization}/members', function () {
                // Controller will handle
            })->middleware('permission:manage_organizations')
                ->name('members.add');

            Route::delete('{organization}/members/{user}', function () {
                // Controller will handle
            })->middleware('permission:manage_organizations')
                ->name('members.remove');
        });

        // Event management
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('manage', function () {
                return inertia('sas/events/manage');
            })->middleware('permission:create_events')
                ->name('manage');

            Route::post('/', function () {
                // Controller will handle
            })->middleware('permission:create_events')
                ->name('store');

            Route::patch('{event}', function () {
                // Controller will handle
            })->middleware('permission:create_events')
                ->name('update');

            Route::delete('{event}', function () {
                // Controller will handle
            })->middleware('permission:create_events')
                ->name('destroy');

            // Attendance tracking
            Route::post('{event}/attendance', function () {
                // Controller will handle
            })->middleware('permission:create_events')
                ->name('attendance.store');

            Route::get('{event}/attendance/export', function () {
                // Controller will handle
            })->middleware('permission:create_events')
                ->name('attendance.export');
        });

        // Reports (admin only)
        Route::middleware(['role:sas_admin'])->prefix('reports')->name('reports.')->group(function () {
            Route::get('scholarships', function () {
                return inertia('sas/reports/scholarships');
            })->name('scholarships');

            Route::get('organizations', function () {
                return inertia('sas/reports/organizations');
            })->name('organizations');

            Route::get('events', function () {
                return inertia('sas/reports/events');
            })->name('events');

            Route::get('financial', function () {
                return inertia('sas/reports/financial');
            })->name('financial');
        });
    });
});
