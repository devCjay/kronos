<?php

use App\Http\Controllers\User\KycController;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Controllers\User\Auth\RegisterController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\DepositController;

use App\Http\Controllers\User\ReferralController;
use App\Http\Controllers\User\Trading\TradingBotController;
use App\Http\Controllers\User\TransactionController;
use App\Http\Controllers\User\WithdrawalController;
use Illuminate\Support\Facades\Route;


// Only Authenticated Users can access these routes
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'registerValidate'])->name('register-validate');
    Route::post('/email-verification', [RegisterController::class, 'emailVerification'])->name('email-verification');
    Route::post('/resend-verification', [RegisterController::class, 'resendVerification'])->name('resend-verification');
    Route::post('/register-cancel', [RegisterController::class, 'registerCancel'])->name('register-cancel');
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'loginValidate'])->name('login.validate');
    Route::get('/login/{provider}', [LoginController::class, 'redirectToSocial'])->name('login.social');
    Route::get('/login/{provider}/callback', [LoginController::class, 'handleSocialCallback'])->name('login.social.callback');

    // Forgot Password
    Route::get('/forgot-password', [LoginController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [LoginController::class, 'sendResetCode'])->name('forgot-password.send');
    Route::get('/forgot-password/otp', [LoginController::class, 'resetOtp'])->name('forgot-password.otp');
    Route::post('/forgot-password/otp', [LoginController::class, 'validateResetOtp'])->name('forgot-password.otp.validate');
    Route::get('/reset-password', [LoginController::class, 'resetPasswordForm'])->name('reset-password');
    Route::post('/reset-password', [LoginController::class, 'updatePassword'])->name('reset-password.update');
});

// OTP routes — user is already authenticated but not yet OTP-verified
Route::middleware(['auth'])->prefix('login')->name('login.')->group(function () {
    Route::get('/verify/otp', [LoginController::class, 'otp'])->name('otp');
    Route::post('/verify/otp', [LoginController::class, 'validateOtp'])->name('otp.validate');
    Route::post('/verify/resend-otp', [LoginController::class, 'resendOtp'])->name('resend-otp');
});


// Only Authenticated users can access these routes
Route::prefix('user')->middleware(['auth', 'otp.verified', 'user.status', 'user.kyc'])->group(function () {
    // logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->withoutMiddleware(['otp.verified', 'user.status', 'user.kyc']);
    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/notification-mark-as-read', [DashboardController::class, 'notificationMarkAsRead'])->name('notification-mark-as-read');
    Route::post('/onboarding', [DashboardController::class, 'onboarding'])->name('onboarding')->middleware('sandbox');
    // KYC
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc');
    Route::post('/kyc', [KycController::class, 'submitKyc'])->name('kyc.submit')->middleware('sandbox');


    // Deposits
    Route::prefix('deposits')->name('deposits.')->group(function () {
        Route::get('/', [DepositController::class, 'index'])->name('index');
        Route::get('/approved', [DepositController::class, 'byScope'])->name('approved');
        Route::get('/pending', [DepositController::class, 'byScope'])->name('pending');
        Route::get('/failed', [DepositController::class, 'byScope'])->name('failed');
        Route::get('/new', [DepositController::class, 'newDeposit'])->name('new');
        Route::get("/view/{transaction_reference}", [DepositController::class, 'viewDeposit'])->name('view');
        Route::get('receipt/{transaction_reference}', [DepositController::class, 'downloadReceipt'])->name('receipt');
        Route::post('/generate-wallet', [DepositController::class, 'generateWallet'])->name('generate-wallet');
    });

    // Trading Bots
    Route::prefix('trading-bots')->name('trading-bots.')->group(function () {
        Route::get('/', [TradingBotController::class, 'index'])->name('index');
        Route::get('/activations', [TradingBotController::class, 'activations'])->name('activations');
        Route::get('/logs', [TradingBotController::class, 'logs'])->name('logs');
        Route::get('daily-summary', [TradingBotController::class, 'dailySummary'])->name('daily-summary');
        Route::post('/activate', [TradingBotController::class, 'activate'])->name('activate');
    });

    // Transactions
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions');

    // referrals
    Route::get('referrals', [ReferralController::class, 'index'])->name('referrals');

    // Withdrawal
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [WithdrawalController::class, 'index'])->name('index');
        Route::get('/approved', [WithdrawalController::class, 'byScope'])->name('approved');
        Route::get('/pending', [WithdrawalController::class, 'byScope'])->name('pending');
        Route::get('/failed', [WithdrawalController::class, 'byScope'])->name('failed');
        Route::get('/partial', [WithdrawalController::class, 'byScope'])->name('partial');
        Route::get('/new', [WithdrawalController::class, 'newWithdrawal'])->name('new');
        Route::post('/new', [WithdrawalController::class, 'newWithdrawalValidate'])->name('new-validate');
        Route::get('/view/{transaction_reference}', [WithdrawalController::class, 'viewWithdrawal'])->name('view');
        Route::get('/get-rate', [WithdrawalController::class, 'getRate'])->name('get-rate');
    });

    // Account Settings
    Route::prefix('account')->name('account.')->middleware('sandbox')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\User\AccountController::class, 'profile'])->name('profile');
        Route::post('/profile', [\App\Http\Controllers\User\AccountController::class, 'profileUpdate'])->name('profile.update');
        Route::get('/security', [\App\Http\Controllers\User\AccountController::class, 'security'])->name('security');
        Route::post('/password', [\App\Http\Controllers\User\AccountController::class, 'passwordUpdate'])->name('password.update');
        Route::post('/sessions/logout-other', [\App\Http\Controllers\User\AccountController::class, 'logoutOtherDevices'])->name('sessions.logout-other');
    });
});
