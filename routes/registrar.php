<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Registrar Module Routes
|--------------------------------------------------------------------------
|
| These routes handle document requests, payment processing, enrollment,
| and academic records management within the Registrar module.
| All routes require authentication and appropriate Registrar role/permissions.
|
*/

// Student document request routes
Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::prefix('registrar')->name('registrar.')->group(function () {
        // Document requests
        Route::get('requests', function () {
            return inertia('registrar/requests/index');
        })->name('requests.index');

        Route::get('requests/create', function () {
            return inertia('registrar/requests/create');
        })->name('requests.create');

        Route::post('requests', function () {
            // Controller will handle
        })->middleware('permission:request_documents')
            ->name('requests.store');

        Route::get('requests/{request}', function () {
            return inertia('registrar/requests/show');
        })->name('requests.show');

        // Payment
        Route::get('requests/{request}/payment', function () {
            return inertia('registrar/requests/payment');
        })->middleware('permission:view_payments')
            ->name('requests.payment');

        Route::post('requests/{request}/payment', function () {
            // Controller will handle payment submission
        })->middleware('permission:view_payments')
            ->name('requests.payment.store');

        // Download documents
        Route::get('documents/{document}/download', function () {
            // Controller will handle file download
        })->middleware('permission:request_documents')
            ->name('documents.download');
    });
});

// Registrar Staff routes (registrar_staff and registrar_admin)
Route::middleware(['auth', 'verified', 'role:registrar_staff|registrar_admin'])->group(function () {
    Route::prefix('registrar')->name('registrar.')->group(function () {
        // Staff dashboard
        Route::get('dashboard', function () {
            return inertia('registrar/dashboard');
        })->name('dashboard');

        // Manage all document requests
        Route::prefix('manage')->name('manage.')->group(function () {
            Route::get('requests', function () {
                return inertia('registrar/manage/requests/index');
            })->middleware('permission:process_document_requests')
                ->name('requests.index');

            Route::get('requests/{request}', function () {
                return inertia('registrar/manage/requests/show');
            })->middleware('permission:process_document_requests')
                ->name('requests.show');

            // Process requests
            Route::patch('requests/{request}/process', function () {
                // Controller will handle
            })->middleware('permission:process_document_requests')
                ->name('requests.process');

            Route::patch('requests/{request}/ready', function () {
                // Controller will handle
            })->middleware('permission:process_document_requests')
                ->name('requests.ready');

            Route::patch('requests/{request}/release', function () {
                // Controller will handle
            })->middleware('permission:process_document_requests')
                ->name('requests.release');

            Route::patch('requests/{request}/reject', function () {
                // Controller will handle
            })->middleware('permission:process_document_requests')
                ->name('requests.reject');

            // Bulk operations
            Route::post('requests/bulk-process', function () {
                // Controller will handle
            })->middleware('permission:process_document_requests')
                ->name('requests.bulk-process');
        });

        // Payment management
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', function () {
                return inertia('registrar/payments/index');
            })->middleware('permission:view_payments')
                ->name('index');

            Route::get('pending', function () {
                return inertia('registrar/payments/pending');
            })->middleware('permission:view_payments')
                ->name('pending');

            // Verify payments
            Route::patch('{payment}/verify', function () {
                // Controller will handle
            })->middleware('permission:verify_payments')
                ->name('verify');

            Route::patch('{payment}/reject', function () {
                // Controller will handle
            })->middleware('permission:verify_payments')
                ->name('reject');

            // Refunds (admin only)
            Route::post('{payment}/refund', function () {
                // Controller will handle
            })->middleware('role:registrar_admin', 'permission:issue_refunds')
                ->name('refund');

            // Manual reconciliation (admin only)
            Route::post('reconcile', function () {
                // Controller will handle
            })->middleware('role:registrar_admin', 'permission:manual_reconciliation')
                ->name('reconcile');
        });

        // Enrollment management (admin only)
        Route::middleware(['role:registrar_admin'])->group(function () {
            Route::prefix('enrollment')->name('enrollment.')->group(function () {
                Route::get('/', function () {
                    return inertia('registrar/enrollment/index');
                })->middleware('permission:manage_enrollment')
                    ->name('index');

                Route::get('periods', function () {
                    return inertia('registrar/enrollment/periods');
                })->middleware('permission:manage_enrollment')
                    ->name('periods');

                Route::post('periods', function () {
                    // Controller will handle
                })->middleware('permission:manage_enrollment')
                    ->name('periods.store');

                Route::patch('periods/{period}/open', function () {
                    // Controller will handle
                })->middleware('permission:manage_enrollment')
                    ->name('periods.open');

                Route::patch('periods/{period}/close', function () {
                    // Controller will handle
                })->middleware('permission:manage_enrollment')
                    ->name('periods.close');
            });

            // Academic records
            Route::prefix('records')->name('records.')->group(function () {
                Route::get('/', function () {
                    return inertia('registrar/records/index');
                })->middleware('permission:manage_academic_records')
                    ->name('index');

                Route::get('students/{student}', function () {
                    return inertia('registrar/records/show');
                })->middleware('permission:view_academic_records')
                    ->name('show');

                Route::patch('students/{student}', function () {
                    // Controller will handle
                })->middleware('permission:manage_academic_records')
                    ->name('update');

                Route::get('students/{student}/transcript', function () {
                    // Controller will handle PDF generation
                })->middleware('permission:view_academic_records')
                    ->name('transcript');
            });

            // Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('requests', function () {
                    return inertia('registrar/reports/requests');
                })->name('requests');

                Route::get('payments', function () {
                    return inertia('registrar/reports/payments');
                })->name('payments');

                Route::get('enrollment', function () {
                    return inertia('registrar/reports/enrollment');
                })->name('enrollment');

                Route::get('financial', function () {
                    return inertia('registrar/reports/financial');
                })->name('financial');
            });
        });
    });
});
