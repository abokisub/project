<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1 - All routes are prefixed with /api/v1
Route::prefix('v1')->name('v1.')->group(function () {
    // Health check
    Route::get('/healthz', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
        ]);
    })->name('healthz');

    // Authentication routes (public)
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [App\Http\Controllers\Api\V1\AuthController::class, 'register'])->name('register');
        Route::post('/login', [App\Http\Controllers\Api\V1\AuthController::class, 'login'])->name('login');
        Route::post('/verify-otp', [App\Http\Controllers\Api\V1\AuthController::class, 'verifyOtp'])->name('verify-otp');
        Route::post('/resend-otp', [App\Http\Controllers\Api\V1\AuthController::class, 'resendOtp'])->name('resend-otp');
        Route::post('/forgot-password', [App\Http\Controllers\Api\V1\AuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('/reset-password', [App\Http\Controllers\Api\V1\AuthController::class, 'resetPassword'])->name('reset-password');
        
        // Protected routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout'])->name('logout');
            Route::get('/user', [App\Http\Controllers\Api\V1\AuthController::class, 'user'])->name('user');
        });
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // User routes
        Route::get('/user', [App\Http\Controllers\Api\V1\AuthController::class, 'user'])->name('user');
        Route::put('/user', [App\Http\Controllers\Api\V1\UserController::class, 'update'])->name('user.update');
        Route::post('/user/regenerate-keys', [App\Http\Controllers\Api\V1\UserController::class, 'regenerateKeys'])->name('user.regenerate-keys');
        Route::post('/user/set-transaction-pin', [App\Http\Controllers\Api\V1\UserController::class, 'setTransactionPin'])->name('user.set-transaction-pin');
        
        // Wallet routes
        Route::prefix('wallet')->name('wallet.')->group(function () {
            Route::get('/balance', [App\Http\Controllers\Api\V1\WalletController::class, 'balance'])->name('balance');
            Route::post('/fund', [App\Http\Controllers\Api\V1\WalletController::class, 'fund'])->name('fund');
            Route::post('/transfer', [App\Http\Controllers\Api\V1\WalletController::class, 'transfer'])->name('transfer');
            Route::get('/transactions', [App\Http\Controllers\Api\V1\WalletController::class, 'transactions'])->name('transactions');
            Route::get('/virtual-account', [App\Http\Controllers\Api\V1\WalletController::class, 'virtualAccount'])->name('virtual-account');
            Route::get('/virtual-accounts', [App\Http\Controllers\Api\V1\WalletController::class, 'listVirtualAccounts'])->name('virtual-accounts.list');
            Route::get('/virtual-accounts/{clientId}', [App\Http\Controllers\Api\V1\WalletController::class, 'getClientAccount'])->name('virtual-accounts.show');
            Route::get('/virtual-account/transactions', [App\Http\Controllers\Api\V1\WalletController::class, 'virtualAccountTransactions'])->name('virtual-account.transactions');
        });
        
        // Thrift routes
        Route::prefix('thrift')->name('thrift.')->group(function () {
            Route::post('/create', [App\Http\Controllers\Api\V1\ThriftController::class, 'create'])->name('create');
            Route::post('/{id}/join', [App\Http\Controllers\Api\V1\ThriftController::class, 'join'])->name('join');
            Route::post('/{id}/contribute', [App\Http\Controllers\Api\V1\ThriftController::class, 'contribute'])->name('contribute');
            Route::get('/{id}', [App\Http\Controllers\Api\V1\ThriftController::class, 'show'])->name('show');
        });
        // KYC routes
        Route::prefix('kyc')->name('kyc.')->group(function () {
            Route::post('/submit', [App\Http\Controllers\Api\V1\KycController::class, 'submit'])->name('submit');
            Route::get('/status', [App\Http\Controllers\Api\V1\KycController::class, 'status'])->name('status');
            Route::post('/verify-bvn', [App\Http\Controllers\Api\V1\KycController::class, 'verifyBvn'])->name('verify-bvn');
            Route::post('/upgrade-virtual-account', [App\Http\Controllers\Api\V1\KycController::class, 'upgradeVirtualAccount'])->name('upgrade-virtual-account');
        });
        
        // Savings routes
        Route::prefix('savings')->name('savings.')->group(function () {
            Route::post('/create', [App\Http\Controllers\Api\V1\SavingsController::class, 'create'])->name('create');
            Route::get('/', [App\Http\Controllers\Api\V1\SavingsController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\Api\V1\SavingsController::class, 'show'])->name('show');
            Route::post('/{id}/deposit', [App\Http\Controllers\Api\V1\SavingsController::class, 'deposit'])->name('deposit');
            Route::post('/{id}/withdraw', [App\Http\Controllers\Api\V1\SavingsController::class, 'withdraw'])->name('withdraw');
        });
        
        // Payment routes
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/banks', [App\Http\Controllers\Api\V1\PaymentController::class, 'listBanks'])->name('banks');
            Route::get('/transactions', [App\Http\Controllers\Api\V1\PaymentController::class, 'getAllTransactions'])->name('transactions');
            Route::post('/send', [App\Http\Controllers\Api\V1\PaymentController::class, 'send'])->name('send');
            Route::post('/bank-transfer', [App\Http\Controllers\Api\V1\PaymentController::class, 'bankTransfer'])->name('bank-transfer');
            Route::post('/qr', [App\Http\Controllers\Api\V1\PaymentController::class, 'qr'])->name('qr');
            Route::get('/status/{id}', [App\Http\Controllers\Api\V1\PaymentController::class, 'status'])->name('status');
            Route::get('/transaction/{reference}', [App\Http\Controllers\Api\V1\PaymentController::class, 'getTransactionByReference'])->name('transaction.by-reference');
            Route::post('/requery-transfer', [App\Http\Controllers\Api\V1\PaymentController::class, 'requeryTransfer'])->name('requery-transfer');
            Route::post('/name-enquiry', [App\Http\Controllers\Api\V1\PaymentController::class, 'nameEnquiry'])->name('name-enquiry');
            Route::post('/internal-name-enquiry', [App\Http\Controllers\Api\V1\PaymentController::class, 'internalNameEnquiry'])->name('internal-name-enquiry');
        });
        
        // Offline transaction routes
        Route::prefix('offline')->name('offline.')->group(function () {
            Route::post('/generate', [App\Http\Controllers\Api\V1\OfflineController::class, 'generate'])->name('generate');
            Route::post('/sync', [App\Http\Controllers\Api\V1\OfflineController::class, 'sync'])->name('sync');
            Route::get('/vouchers', [App\Http\Controllers\Api\V1\OfflineController::class, 'vouchers'])->name('vouchers');
        });
        
        // User role management
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/me', [App\Http\Controllers\Api\V1\UserRoleController::class, 'getUserRoles'])->name('me');
            Route::get('/user/{userId}', [App\Http\Controllers\Api\V1\UserRoleController::class, 'getUserRoles'])->name('user');
            Route::post('/assign/{userId}', [App\Http\Controllers\Api\V1\UserRoleController::class, 'assignRole'])->name('assign');
            Route::post('/upgrade/{userId}', [App\Http\Controllers\Api\V1\UserRoleController::class, 'upgradeTier'])->name('upgrade');
        });
        
        // Fee calculation
        Route::prefix('fees')->name('fees.')->group(function () {
            Route::post('/calculate', [App\Http\Controllers\Api\V1\FeeController::class, 'calculate'])->name('calculate');
        });
        
        // Merchant routes
        Route::prefix('merchants')->name('merchants.')->group(function () {
            Route::post('/apply', [App\Http\Controllers\Api\V1\MerchantController::class, 'apply'])->name('apply');
            Route::get('/dashboard', [App\Http\Controllers\Api\V1\MerchantController::class, 'dashboard'])->name('dashboard');
        });
        
        // Agent routes
        Route::prefix('agents')->name('agents.')->group(function () {
            Route::post('/apply', [App\Http\Controllers\Api\V1\AgentController::class, 'apply'])->name('apply');
            Route::get('/dashboard', [App\Http\Controllers\Api\V1\AgentController::class, 'dashboard'])->name('dashboard');
            Route::post('/cash-in', [App\Http\Controllers\Api\V1\AgentController::class, 'cashIn'])->name('cash-in');
            Route::post('/cash-out', [App\Http\Controllers\Api\V1\AgentController::class, 'cashOut'])->name('cash-out');
        });
        
        // Admin routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/users', [App\Http\Controllers\Api\V1\AdminController::class, 'users'])->name('users');
            Route::get('/kyc-submissions', [App\Http\Controllers\Api\V1\AdminController::class, 'kycSubmissions'])->name('kyc-submissions');
            Route::post('/kyc/{id}/review', [App\Http\Controllers\Api\V1\AdminController::class, 'reviewKyc'])->name('review-kyc');
            Route::get('/transactions', [App\Http\Controllers\Api\V1\AdminController::class, 'transactions'])->name('transactions');
            Route::get('/statistics', [App\Http\Controllers\Api\V1\AdminController::class, 'statistics'])->name('statistics');
            Route::get('/account-info', [App\Http\Controllers\Api\V1\WalletController::class, 'getAccountInfo'])->name('account-info');
        });
    });

    // Webhook routes (no auth, but signature verification)
    Route::prefix('webhooks')->name('webhooks.')->group(function () {
        Route::post('/bellbank', [App\Http\Controllers\Api\V1\WebhookController::class, 'bellbank'])->name('bellbank');
    });
});

