<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OtpLoginController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'admin.home' : 'login'));

Route::get('/maintenance', fn () => view('maintenance'))->name('maintenance');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/login-otp', [OtpLoginController::class, 'show'])->name('login-otp.show');
Route::post('/login-otp', [OtpLoginController::class, 'login'])->name('login-otp');
Route::get('/login-otp/setup', [OtpLoginController::class, 'showSetup'])->name('login-otp.setup.show');
Route::post('/login-otp/setup', [OtpLoginController::class, 'setup'])->name('login-otp.setup');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequest'])->name('password.request.show');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendCode'])->name('password.request');
Route::get('/forgot-password/verify', [ForgotPasswordController::class, 'showVerify'])->name('password.verify.show');
Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verifyCode'])->name('password.verify');
Route::get('/reset-password', [ForgotPasswordController::class, 'showReset'])->name('password.reset.show');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.reset');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware('auth')->group(base_path('routes/admin.php'));
