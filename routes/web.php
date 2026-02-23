<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StickerTypeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\PinUpdateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\StickerTypeSwitchController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Route;


// Public dashboard
Route::get('/', DashboardController::class)->name('dashboard');

// Always accessible: pins JSON endpoint
Route::get('/pins/json', [PinController::class, 'json'])->name('pins.json');

// Profile pages (require auth + permission)
Route::get('/users/{user}', [ProfileController::class, 'show'])->middleware(['auth', 'permission:users.view_profiles'])->name('profile.show');

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

    // Sticker type switcher
    Route::post('/switch-sticker-type', StickerTypeSwitchController::class)->name('sticker-type.switch');

    // Admin routes (permission-gated)
    Route::prefix('admin')->middleware('permission:admin.access')->group(function () {
        Route::middleware('permission:roles.manage')->group(function () {
            Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
            Route::get('/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
        });

        Route::middleware('permission:users.manage')->group(function () {
            Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
            Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
            Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
        });

        Route::middleware('permission:sticker_types.manage')->group(function () {
            Route::get('/sticker-types', [StickerTypeController::class, 'index'])->name('admin.sticker-types.index');
            Route::get('/sticker-types/create', [StickerTypeController::class, 'create'])->name('admin.sticker-types.create');
            Route::post('/sticker-types', [StickerTypeController::class, 'store'])->name('admin.sticker-types.store');
            Route::get('/sticker-types/{stickerType}/edit', [StickerTypeController::class, 'edit'])->name('admin.sticker-types.edit');
            Route::put('/sticker-types/{stickerType}', [StickerTypeController::class, 'update'])->name('admin.sticker-types.update');
            Route::delete('/sticker-types/{stickerType}', [StickerTypeController::class, 'destroy'])->name('admin.sticker-types.destroy');
        });
    });
});

// Public pin detail page (after auth routes to avoid conflicts with /pins/create etc.)
Route::get('/pins/{pin}', [PinController::class, 'show'])->name('pins.show');