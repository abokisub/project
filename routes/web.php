<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Login route (for auth middleware redirect)
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Admin login route
Route::prefix('auth')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
});

// Admin protected routes
Route::prefix('secure')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/app', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/virtual-accounts', [AdminDashboardController::class, 'virtualAccounts'])->name('virtual-accounts');
    Route::post('/virtual-accounts/create', [AdminDashboardController::class, 'createVirtualAccount'])->name('virtual-accounts.create');
    Route::delete('/virtual-accounts/{accountId}', [AdminDashboardController::class, 'deleteVirtualAccount'])->name('virtual-accounts.delete');
    Route::get('/virtual-accounts/{accountId}', [AdminDashboardController::class, 'virtualAccountDetails'])->name('virtual-account-details');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});
