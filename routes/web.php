<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RegistrationHistoryController;
use App\Http\Controllers\WorkshopController;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', fn () => redirect()->route('login'));

// Auth routes (guests only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Authenticated + active user routes
Route::middleware(['auth', 'active'])->group(function () {

    // Admin only — User management
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('users', UserController::class)->except(['destroy', 'show']);

            Route::get('/audit', [AuditLogController::class, 'index'])->name('audit');
        });

    // Manager + Staff — Workshops and registrations
    Route::middleware('role:manager,staff')->group(function () {
        Route::resource('workshops', WorkshopController::class)->except(['destroy']);

        Route::post('/workshops/{workshop}/registrations', [RegistrationController::class, 'store'])
            ->name('registrations.store');

        Route::post('/registrations/{registration}/cancel', [RegistrationController::class, 'cancel'])
            ->name('registrations.cancel');

        Route::get('/registrations', [RegistrationHistoryController::class, 'index'])
            ->name('registrations.index');
    });

    // Manager only — Create and edit workshops (additional policy enforcement)
    // (WorkshopPolicy handles this at the controller level)

    // Audit log (Admin + Manager)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/audit', [AuditLogController::class, 'index'])->name('admin.audit');
    });
});
