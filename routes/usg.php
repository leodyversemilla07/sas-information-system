<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| University Student Government (USG) Portal Routes
|--------------------------------------------------------------------------
|
| These routes handle VMGO, officer management, announcements, resolutions,
| and student engagement features within the USG Portal module.
| Public routes for students, restricted routes for USG officers/admin.
|
*/

// Public USG landing page (public-facing)
Route::get('/usg', function () {
    return inertia('usg/home');
})->name('usg.landing');

// Public VMGO viewing
Route::get('vmgo', function () {
    return inertia('usg/vmgo');
})->name('vmgo');

// Public officers listing
Route::get('officers', function () {
    return inertia('usg/officers/index');
})->name('officers.index');

Route::get('officers/{officer}', function () {
    return inertia('usg/officers/show');
})->name('officers.show');

// Public announcements
Route::get('announcements', function () {
    return inertia('usg/announcements/index');
})->name('announcements.index');

Route::get('announcements/{announcement}', function () {
    return inertia('usg/announcements/show');
})->name('announcements.show');

// Public resolutions
Route::get('resolutions', function () {
    return inertia('usg/resolutions/index');
})->name('resolutions.index');

Route::get('resolutions/{resolution}', function () {
    return inertia('usg/resolutions/show');
})->name('resolutions.show');

// Student feedback/concerns
Route::get('feedback', function () {
    return inertia('usg/feedback/create');
})->middleware('permission:submit_feedback')
    ->name('feedback.create');

Route::post('feedback', function () {
    // Controller will handle
})->middleware('permission:submit_feedback')
    ->name('feedback.store');

// USG Officer routes (usg_officer role)
Route::middleware(['auth', 'verified', 'role:usg_officer|usg_admin'])->group(function () {
    Route::prefix('usg')->name('usg.')->group(function () {
        // Officer dashboard
        Route::get('dashboard', function () {
            return inertia('usg/dashboard');
        })->name('dashboard');

        // Announcement management
        Route::prefix('manage/announcements')->name('manage.announcements.')->group(function () {
            Route::get('/', function () {
                return inertia('usg/manage/announcements/index');
            })->middleware('permission:create_announcements')
                ->name('index');

            Route::get('create', function () {
                return inertia('usg/manage/announcements/create');
            })->middleware('permission:create_announcements')
                ->name('create');

            Route::post('/', function () {
                // Controller will handle
            })->middleware('permission:create_announcements')
                ->name('store');

            Route::get('{announcement}/edit', function () {
                return inertia('usg/manage/announcements/edit');
            })->middleware('permission:edit_announcements')
                ->name('edit');

            Route::patch('{announcement}', function () {
                // Controller will handle
            })->middleware('permission:edit_announcements')
                ->name('update');

            Route::delete('{announcement}', function () {
                // Controller will handle
            })->middleware('permission:delete_announcements')
                ->name('destroy');

            // Publish/unpublish
            Route::patch('{announcement}/publish', function () {
                // Controller will handle
            })->middleware('permission:publish_announcements')
                ->name('publish');

            Route::patch('{announcement}/unpublish', function () {
                // Controller will handle
            })->middleware('permission:publish_announcements')
                ->name('unpublish');
        });

        // Resolution management
        Route::prefix('manage/resolutions')->name('manage.resolutions.')->group(function () {
            Route::get('/', function () {
                return inertia('usg/manage/resolutions/index');
            })->middleware('permission:create_resolutions')
                ->name('index');

            Route::get('create', function () {
                return inertia('usg/manage/resolutions/create');
            })->middleware('permission:create_resolutions')
                ->name('create');

            Route::post('/', function () {
                // Controller will handle
            })->middleware('permission:create_resolutions')
                ->name('store');

            Route::get('{resolution}/edit', function () {
                return inertia('usg/manage/resolutions/edit');
            })->middleware('permission:edit_resolutions')
                ->name('edit');

            Route::patch('{resolution}', function () {
                // Controller will handle
            })->middleware('permission:edit_resolutions')
                ->name('update');

            Route::delete('{resolution}', function () {
                // Controller will handle
            })->middleware('permission:delete_resolutions')
                ->name('destroy');

            // Publish/archive
            Route::patch('{resolution}/publish', function () {
                // Controller will handle
            })->middleware('permission:publish_resolutions')
                ->name('publish');

            Route::patch('{resolution}/archive', function () {
                // Controller will handle
            })->middleware('permission:publish_resolutions')
                ->name('archive');
        });

        // Student feedback management
        Route::prefix('manage/feedback')->name('manage.feedback.')->group(function () {
            Route::get('/', function () {
                return inertia('usg/manage/feedback/index');
            })->middleware('permission:view_feedback')
                ->name('index');

            Route::get('{feedback}', function () {
                return inertia('usg/manage/feedback/show');
            })->middleware('permission:view_feedback')
                ->name('show');

            Route::patch('{feedback}/respond', function () {
                // Controller will handle
            })->middleware('permission:respond_to_feedback')
                ->name('respond');

            Route::patch('{feedback}/resolve', function () {
                // Controller will handle
            })->middleware('permission:respond_to_feedback')
                ->name('resolve');
        });
    });
});

// USG Admin routes (usg_admin role only)
Route::middleware(['auth', 'verified', 'role:usg_admin'])->group(function () {
    Route::prefix('usg/admin')->name('usg.admin.')->group(function () {
        // VMGO management
        Route::prefix('vmgo')->name('vmgo.')->group(function () {
            Route::get('edit', function () {
                return inertia('usg/admin/vmgo/edit');
            })->middleware('permission:manage_vmgo')
                ->name('edit');

            Route::patch('/', function () {
                // Controller will handle
            })->middleware('permission:manage_vmgo')
                ->name('update');

            Route::post('history', function () {
                // Controller will handle version history
            })->middleware('permission:manage_vmgo')
                ->name('history.store');
        });

        // Officer management
        Route::prefix('officers')->name('officers.')->group(function () {
            Route::get('/', function () {
                return inertia('usg/admin/officers/index');
            })->middleware('permission:manage_officers')
                ->name('index');

            Route::get('create', function () {
                return inertia('usg/admin/officers/create');
            })->middleware('permission:manage_officers')
                ->name('create');

            Route::post('/', function () {
                // Controller will handle
            })->middleware('permission:manage_officers')
                ->name('store');

            Route::get('{officer}/edit', function () {
                return inertia('usg/admin/officers/edit');
            })->middleware('permission:manage_officers')
                ->name('edit');

            Route::patch('{officer}', function () {
                // Controller will handle
            })->middleware('permission:manage_officers')
                ->name('update');

            Route::delete('{officer}', function () {
                // Controller will handle
            })->middleware('permission:manage_officers')
                ->name('destroy');

            // Term management
            Route::patch('{officer}/end-term', function () {
                // Controller will handle
            })->middleware('permission:manage_officers')
                ->name('end-term');
        });

        // Analytics and reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('engagement', function () {
                return inertia('usg/admin/reports/engagement');
            })->name('engagement');

            Route::get('announcements', function () {
                return inertia('usg/admin/reports/announcements');
            })->name('announcements');

            Route::get('resolutions', function () {
                return inertia('usg/admin/reports/resolutions');
            })->name('resolutions');

            Route::get('feedback', function () {
                return inertia('usg/admin/reports/feedback');
            })->name('feedback');
        });

        // Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', function () {
                return inertia('usg/admin/settings/index');
            })->middleware('permission:manage_usg_settings')
                ->name('index');

            Route::patch('/', function () {
                // Controller will handle
            })->middleware('permission:manage_usg_settings')
                ->name('update');
        });
    });
});
