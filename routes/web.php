<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\PinUpdateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Route;


// Public dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// Always accessible: pins JSON endpoint
Route::get('/pins/json', [PinController::class, 'json'])->name('pins.json');

// Public profile pages
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('profile.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return Socialite::driver('authentik')->redirect();
    })->name('login');
    Route::get('/auth/callback', [AuthController::class, 'callback']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/pins', [PinController::class, 'index'])->name('pins.index');
    Route::get('/pins/create', [PinController::class, 'create'])->name('pins.create');
    Route::get('/pins/{pin}/edit', [PinController::class, 'edit'])->name('pins.edit');
    Route::put('/pins/{pin}', [PinController::class, 'update'])->name('pins.update');
    Route::delete('/pins/{pin}', [PinController::class, 'destroy'])->name('pins.destroy');
    Route::post('/pins', [PinController::class, 'store'])->name('pins.store');

    // Pin updates (timeline)
    Route::post('/pins/{pin}/updates', [PinUpdateController::class, 'store'])->name('pins.updates.store');
    Route::delete('/pins/{pin}/updates/{update}', [PinUpdateController::class, 'destroy'])->name('pins.updates.destroy');

    // Reminders
    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::post('/pins/{pin}/check', [ReminderController::class, 'check'])->name('pins.check');
    Route::post('/reminders/bulk-check', [ReminderController::class, 'bulkCheck'])->name('reminders.bulk-check');

    // Admin routes (permission-gated)
    Route::prefix('admin')->middleware('permission:admin.access')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

        Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    });
});

// Public pin detail page (after auth routes to avoid conflicts with /pins/create etc.)
Route::get('/pins/{pin}', [PinController::class, 'show'])->name('pins.show');