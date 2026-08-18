<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Auth\Authentication;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Auth\ConfirmPassword;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', Authentication::class)
        ->name('register');

    Route::get('login', Authentication::class)
        ->name('login');

    Route::get('forgot-password', Authentication::class)
        ->name('password.request');

    Route::get('reset-password/{token}', ResetPassword::class)
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', VerifyEmail::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', ConfirmPassword::class)
        ->name('password.confirm');
});
