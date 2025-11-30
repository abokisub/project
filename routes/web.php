<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Admin login route
Route::prefix('auth')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
});

// Admin protected routes
Route::prefix('secure')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/app', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});
