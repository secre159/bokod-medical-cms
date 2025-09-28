<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
// Email verification controllers removed - not needed
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
// RegisteredUserController removed - all registration goes through PatientRegistrationController
use App\Http\Controllers\PatientRegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Patient registration is now the main /register route
    Route::get('register', [PatientRegistrationController::class, 'showRegistrationForm'])
        ->name('register');

    Route::post('register', [PatientRegistrationController::class, 'register'])
        ->name('register.submit');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    // Old patient registration routes (redirect to main register)
    Route::get('patient-register', function() {
        return redirect()->route('register');
    })->name('patient.register');
    
    Route::post('patient-register', function() {
        return redirect()->route('register');
    })->name('patient.register.submit');

    Route::get('registration-success', [PatientRegistrationController::class, 'registrationSuccess'])
        ->name('patient.registration.success');
});

Route::middleware('auth')->group(function () {
    // Email verification routes removed - users receive credentials via email
    
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
