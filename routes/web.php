<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// General authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Main dashboard - redirect based on role
    Route::get('dashboard', function () {
        $user = auth()->user();

        // System admin
        if ($user->hasRole('system_admin')) {
            return redirect()->route('admin.dashboard');
        }

        // SAS module
        if ($user->hasAnyRole(['sas_staff', 'sas_admin'])) {
            return redirect()->route('sas.dashboard');
        }

        // Registrar module
        if ($user->hasAnyRole(['registrar_staff', 'registrar_admin'])) {
            return redirect()->route('registrar.dashboard');
        }

        // USG module
        if ($user->hasAnyRole(['usg_officer', 'usg_admin'])) {
            return redirect()->route('usg.dashboard');
        }

        // Default student dashboard
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// Module-specific route files
require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
require __DIR__.'/sas.php';
require __DIR__.'/registrar.php';
require __DIR__.'/usg.php';
require __DIR__.'/admin.php';
