<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\TotpController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return \Inertia\Inertia::render('Auth/Login', [
        'microsoftUrl' => route('auth.microsoft'),
    ]);
})->name('login');

// Terms / CGU
Route::get('/terms', function () {
    return \Inertia\Inertia::render('Terms');
})->name('terms');

// Microsoft OAuth
Route::get('/auth/microsoft', [MicrosoftController::class, 'redirect'])->name('auth.microsoft');
Route::get('/auth/microsoft/callback', [MicrosoftController::class, 'callback']);
Route::post('/logout', [MicrosoftController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard - list TOTP accounts
    Route::get('/dashboard', [TotpController::class, 'index'])->name('dashboard');

    // TOTP management
    Route::get('/totp/create', [TotpController::class, 'create'])->name('totp.create');
    Route::post('/totp', [TotpController::class, 'store'])->name('totp.store');
    Route::get('/totp/scan', [TotpController::class, 'scan'])->name('totp.scan');
    Route::post('/totp/scan', [TotpController::class, 'storeScan'])->name('totp.scan.store');
    Route::get('/totp/{totp}', [TotpController::class, 'show'])->name('totp.show');
    Route::delete('/totp/{totp}', [TotpController::class, 'destroy'])->name('totp.destroy');
    Route::get('/totp/{totp}/code', [TotpController::class, 'getCode'])->name('totp.code');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/users/{user}', [AdminController::class, 'userAccounts'])->name('user.accounts');
        Route::get('/users/{user}/totp/create', [AdminController::class, 'createAccount'])->name('user.totp.create');
        Route::post('/users/{user}/totp', [AdminController::class, 'storeAccount'])->name('user.totp.store');
        Route::delete('/totp/{totp}', [AdminController::class, 'destroyAccount'])->name('totp.destroy');
        Route::patch('/totp/{totp}/toggle-lock', [AdminController::class, 'toggleLock'])->name('totp.toggle-lock');
        Route::get('/totp/{totp}', [AdminController::class, 'showAccount'])->name('totp.show');
        Route::get('/totp/{totp}/edit', [AdminController::class, 'editAccount'])->name('totp.edit');
        Route::put('/totp/{totp}', [AdminController::class, 'updateAccount'])->name('totp.update');
        Route::patch('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('user.toggle-admin');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('user.destroy');
    });
});
