<?php

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
});

// Public pin detail page (after auth routes to avoid conflicts with /pins/create etc.)
Route::get('/pins/{pin}', [PinController::class, 'show'])->name('pins.show');