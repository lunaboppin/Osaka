<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Route;


// Public dashboard (read-only)
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return Socialite::driver('authentik')->redirect();
    })->name('login');
    Route::get('/pins/json', [\App\Http\Controllers\PinController::class, 'json'])->name('pins.json');
    
    Route::get('/auth/callback', [AuthController::class, 'callback']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/pins', [\App\Http\Controllers\PinController::class, 'index'])->name('pins.index');
    Route::get('/pins/{pin}/edit', [\App\Http\Controllers\PinController::class, 'edit'])->name('pins.edit');
    Route::put('/pins/{pin}', [\App\Http\Controllers\PinController::class, 'update'])->name('pins.update');
    Route::delete('/pins/{pin}', [\App\Http\Controllers\PinController::class, 'destroy'])->name('pins.destroy');
    Route::get('/pins/json', [\App\Http\Controllers\PinController::class, 'json'])->name('pins.json');
    Route::get('/pins/create', [\App\Http\Controllers\PinController::class, 'create'])->name('pins.create');
    Route::post('/pins', [\App\Http\Controllers\PinController::class, 'store'])->name('pins.store');
});
