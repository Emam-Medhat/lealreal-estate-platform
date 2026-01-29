<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\IdentityVerificationController;
use App\Http\Controllers\UserWalletController;
use App\Http\Controllers\UserPreferenceController;

/*
|--------------------------------------------------------------------------
| User Management Routes
|--------------------------------------------------------------------------
|
| Routes for user management, profiles, KYC, and wallet functionality
|
*/

// Home route (fallback)
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Inventory route (fallback) - add admin middleware
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/inventory', [App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // User Dashboard
    Route::get('/user/dashboard', [App\Http\Controllers\UserDashboardController::class, 'index'])->name('user.dashboard');
    
    // Profile Management
    Route::prefix('profile')->name('user.profile')->group(function () {
        Route::get('/', [UserProfileController::class, 'show'])->name('');
        Route::get('/edit', [UserProfileController::class, 'edit'])->name('.edit');
        Route::put('/', [UserProfileController::class, 'update'])->name('.update');
        Route::post('/avatar', [UserProfileController::class, 'uploadAvatar'])->name('.avatar.upload');
        Route::delete('/avatar', [UserProfileController::class, 'deleteAvatar'])->name('.avatar.delete');
        Route::get('/completion', [UserProfileController::class, 'completion'])->name('.completion');
        Route::get('/public/{user}', [UserProfileController::class, 'publicProfile'])->name('.public');
    });
    
    // KYC Verification
    Route::prefix('kyc')->group(function () {
        Route::get('/', [IdentityVerificationController::class, 'index'])->name('kyc.index');
        Route::get('/create', [IdentityVerificationController::class, 'create'])->name('kyc.create');
        Route::post('/', [IdentityVerificationController::class, 'store'])->name('kyc.store');
        Route::get('/status', [IdentityVerificationController::class, 'status'])->name('kyc.status');
        Route::get('/documents/{document}', [IdentityVerificationController::class, 'downloadDocument'])->name('kyc.document.download');
        Route::post('/upgrade', [IdentityVerificationController::class, 'upgrade'])->name('kyc.upgrade');
        Route::post('/resubmit', [IdentityVerificationController::class, 'resubmit'])->name('kyc.resubmit');
    });
    
    // Wallet Management
    Route::prefix('wallet')->group(function () {
        Route::get('/', [UserWalletController::class, 'index'])->name('wallet.index');
        Route::get('/balance', [UserWalletController::class, 'getBalance'])->name('wallet.balance');
        Route::get('/transactions', [UserWalletController::class, 'getTransactions'])->name('wallet.transactions');
        Route::get('/transactions/{transaction}', [UserWalletController::class, 'getTransaction'])->name('wallet.transaction.show');
        Route::post('/deposit', [UserWalletController::class, 'deposit'])->name('wallet.deposit');
        Route::post('/withdraw', [UserWalletController::class, 'withdraw'])->name('wallet.withdraw');
        Route::get('/deposit/methods', [UserWalletController::class, 'show'])->name('wallet.deposit.methods');
        Route::get('/withdraw/methods', [UserWalletController::class, 'show'])->name('wallet.withdraw.methods');
        Route::post('/transfer', [UserWalletController::class, 'transfer'])->name('wallet.transfer');
        Route::get('/transfer/verify', [UserWalletController::class, 'show'])->name('wallet.transfer.verify');
        Route::post('/transfer/confirm', [UserWalletController::class, 'transfer'])->name('wallet.transfer.confirm');
        Route::get('/statement', [UserWalletController::class, 'getTransactions'])->name('wallet.statement');
        Route::get('/statistics', [UserWalletController::class, 'getStats'])->name('wallet.statistics');
        Route::get('/export', [UserWalletController::class, 'exportTransactions'])->name('wallet.export');
    });
    
    // User Settings
    Route::prefix('settings')->group(function () {
        Route::get('/', [UserPreferenceController::class, 'index'])->name('settings.index');
        Route::put('/profile', [UserPreferenceController::class, 'update'])->name('settings.profile.update');
        Route::put('/notifications', [UserPreferenceController::class, 'updateNotificationSettings'])->name('settings.notifications.update');
        Route::put('/privacy', [UserPreferenceController::class, 'updatePrivacySettings'])->name('settings.privacy.update');
        Route::put('/security', [UserPreferenceController::class, 'update'])->name('settings.security.update');
        Route::put('/preferences', [UserPreferenceController::class, 'update'])->name('settings.preferences.update');
        Route::get('/export', [UserPreferenceController::class, 'exportPreferences'])->name('settings.export');
        Route::delete('/account', [UserController::class, 'deleteAccount'])->name('settings.account.delete');
        Route::post('/deactivate', [UserController::class, 'deactivateAccount'])->name('settings.account.deactivate');
    });
    
    // User Activity
    Route::prefix('activity')->group(function () {
        Route::get('/', [UserController::class, 'activity'])->name('user.activity');
        Route::get('/log', [UserController::class, 'activityLog'])->name('user.activity.log');
        Route::get('/analytics', [UserController::class, 'activityAnalytics'])->name('user.activity.analytics');
    });
    
    // User Reports (Moved/Handled by routes/reports.php)
    /*
    Route::prefix('reports')->group(function () {
        Route::get('/', [UserController::class, 'reports'])->name('user.reports');
        Route::post('/generate', [UserController::class, 'generateReport'])->name('user.reports.generate');
        Route::get('/{report}', [App\Http\Controllers\Reports\SalesReportController::class, 'show'])->name('user.reports.download');
        Route::get('/{report}/preview', [UserController::class, 'previewReport'])->name('user.reports.preview');
    });
    */
    
    // User Favorites
    Route::prefix('favorites')->group(function () {
        Route::get('/', [App\Http\Controllers\PropertyFavoriteController::class, 'index'])->name('user.favorites');
        Route::post('/{property}', [App\Http\Controllers\PropertyFavoriteController::class, 'add'])->name('user.favorites.add');
        Route::delete('/{property}', [App\Http\Controllers\PropertyFavoriteController::class, 'remove'])->name('user.favorites.remove');
        Route::get('/export', [App\Http\Controllers\PropertyFavoriteController::class, 'export'])->name('user.favorites.export');
    });
    
    // User Comparisons
    Route::prefix('comparisons')->group(function () {
        Route::get('/', [UserController::class, 'comparisons'])->name('user.comparisons');
        Route::post('/', [UserController::class, 'createComparison'])->name('user.comparisons.create');
        Route::get('/{comparison}', [UserController::class, 'showComparison'])->name('user.comparisons.show');
        Route::put('/{comparison}', [UserController::class, 'updateComparison'])->name('user.comparisons.update');
        Route::delete('/{comparison}', [UserController::class, 'deleteComparison'])->name('user.comparisons.delete');
        Route::get('/export/{comparison}', [UserController::class, 'exportComparison'])->name('user.comparisons.export');
    });
    
    // User Sessions
    Route::prefix('sessions')->group(function () {
        Route::get('/', [UserController::class, 'sessions'])->name('user.sessions');
        Route::delete('/{session}', [UserController::class, 'revokeSession'])->name('user.sessions.revoke');
        Route::delete('/all', [UserController::class, 'revokeAllSessions'])->name('user.sessions.revoke.all');
        Route::get('/active', [UserController::class, 'activeSessions'])->name('user.sessions.active');
    });
    
    // User Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [UserController::class, 'notifications'])->name('user.notifications');
        Route::get('/unread', [UserController::class, 'unreadNotifications'])->name('user.notifications.unread');
        Route::put('/{notification}/read', [UserController::class, 'markNotificationRead'])->name('user.notifications.read');
        Route::put('/read-all', [UserController::class, 'markAllNotificationsRead'])->name('user.notifications.read.all');
        Route::delete('/{notification}', [UserController::class, 'deleteNotification'])->name('user.notifications.delete');
        Route::post('/settings', [UserController::class, 'updateNotificationSettings'])->name('user.notifications.settings');
    });
    
    // User Search History
    Route::prefix('search')->group(function () {
        Route::get('/history', [UserController::class, 'searchHistory'])->name('user.search.history');
        Route::post('/save', [UserController::class, 'saveSearch'])->name('user.search.save');
        Route::get('/saved', [UserController::class, 'savedSearches'])->name('user.search.saved');
        Route::delete('/{search}', [UserController::class, 'deleteSavedSearch'])->name('user.search.delete');
        Route::post('/{search}/alert', [UserController::class, 'createSearchAlert'])->name('user.search.alert');
        Route::get('/alerts', [UserController::class, 'searchAlerts'])->name('user.search.alerts');
        Route::delete('/alert/{alert}', [UserController::class, 'deleteSearchAlert'])->name('user.search.alert.delete');
    });
});

// API Routes for User Management
Route::prefix('api/user')->middleware(['auth:api', 'throttle:60,1'])->group(function () {
    
    // Profile API
    Route::get('/profile', [UserController::class, 'apiProfile'])->name('api.user.profile');
    Route::put('/profile', [UserController::class, 'apiUpdateProfile'])->name('api.user.profile.update');
    Route::post('/avatar', [UserController::class, 'apiUploadAvatar'])->name('api.user.avatar.upload');
    
    // KYC API
    Route::get('/kyc', [IdentityVerificationController::class, 'apiStatus'])->name('api.user.kyc.status');
    Route::post('/kyc', [IdentityVerificationController::class, 'apiStore'])->name('api.user.kyc.store');
    Route::post('/kyc/documents', [IdentityVerificationController::class, 'apiUploadDocuments'])->name('api.user.kyc.documents.upload');
    
    // Wallet API
    Route::get('/wallet', [UserWalletController::class, 'getBalance'])->name('api.user.wallet.balance');
    Route::get('/wallet/transactions', [UserWalletController::class, 'getTransactions'])->name('api.user.wallet.transactions');
    Route::post('/wallet/transfer', [UserWalletController::class, 'transfer'])->name('api.user.wallet.transfer');
    
    // Activity API
    Route::get('/activity', [UserController::class, 'apiActivity'])->name('api.user.activity');
    Route::post('/activity/log', [UserController::class, 'apiLogActivity'])->name('api.user.activity.log');
    
    // Settings API
    Route::get('/settings', [UserPreferenceController::class, 'index'])->name('api.user.settings');
    Route::put('/settings', [UserPreferenceController::class, 'update'])->name('api.user.settings.update');
    
    // Notifications API
    Route::get('/notifications', [UserController::class, 'apiNotifications'])->name('api.user.notifications');
    Route::put('/notifications/{notification}/read', [UserController::class, 'apiMarkNotificationRead'])->name('api.user.notifications.read');
    Route::put('/notifications/read-all', [UserController::class, 'apiMarkAllNotificationsRead'])->name('api.user.notifications.read.all');
});
