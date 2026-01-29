<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\BiometricAuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Analytics\AnalyticsController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SystemErrorLogController;
// use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\SettingsController;
// use App\Http\Controllers\KYCController;
// use App\Http\Controllers\WalletController;
// use App\Http\Controllers\ReferralController;
// use App\Http\Controllers\Admin\UserController as AdminUserController;
// use App\Http\Controllers\Admin\KYCController as AdminKYCController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Simple test route
Route::get('/test-properties', function () {
    $propertyTypes = \App\Models\PropertyType::select('id', 'name', 'slug')
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view('properties.simple_index', ['propertyTypes' => $propertyTypes]);
})->name('test.properties');

// ===================================================================
// NOTE: Most route files are now registered in bootstrap/app.php
// to avoid double loading and ensure proper middleware application.
// Only specialized routes that need different configuration remain here.
// ===================================================================

// Registered in bootstrap/app.php:
// - properties.php, optimized_properties.php, taxes.php
// - maintenance.php, reports.php, agents.php, companies.php
// - leads.php, analytics.php, financial.php, rentals.php
// - insurance.php, documents.php, inspections.php
// - appraisals.php, warranties.php

// Public test route (no auth required)
Route::get('/public-test', function () {
    return 'Public route works! User: ' . (auth()->check() ? auth()->user()->name : 'Not logged in');
});

// Test SEO route
Route::get('/test-seo', function () {
    return 'SEO route test works!';
})->middleware(['auth', 'admin']);

// Direct SEO test route
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/seo-test', function () {
        return 'Direct SEO route works!';
    })->name('seo.test');
    
    // SEO Management Routes (moved from content.php)
    Route::prefix('seo')->name('seo.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SeoController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\SeoController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\SeoController::class, 'store'])->name('store');
        Route::get('/{seoMeta}', [\App\Http\Controllers\SeoController::class, 'show'])->name('show');
        Route::get('/{seoMeta}/edit', [\App\Http\Controllers\SeoController::class, 'edit'])->name('edit');
        Route::put('/{seoMeta}', [\App\Http\Controllers\SeoController::class, 'update'])->name('update');
        Route::delete('/{seoMeta}', [\App\Http\Controllers\SeoController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-update', [\App\Http\Controllers\SeoController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/generate-sitemap', [\App\Http\Controllers\SeoController::class, 'generateSitemap'])->name('generate-sitemap');
        Route::get('/analyze', [\App\Http\Controllers\SeoController::class, 'analyzeSeo'])->name('analyze');
    });
});

// Include content routes
require __DIR__ . '/content.php';

// Include API routes
// Test Notifications Route
Route::get('/test-notifications', function () {
    return view('test-notifications');
})->middleware('auth')->name('test.notifications');

// Clear Cache Route (for testing)
Route::get('/clear-cache', function () {
    \Cache::flush();
    return 'Cache cleared!';
});

require __DIR__.'/api_notifications.php';

// Agent Reviews
Route::post('/agent-reviews', function (\Illuminate\Http\Request $request) {
    try {
        $review = \App\Models\AgentReview::create([
            'agent_id' => $request->agent_id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,
            'reviewer_name' => $request->reviewer_name,
            'reviewer_email' => $request->reviewer_email,
            'status' => 'pending', // Requires admin approval
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error submitting review: ' . $e->getMessage()
        ], 500);
    }
})->name('agent.reviews.store');

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public Routes
Route::get('/agents', [App\Http\Controllers\AgentController::class, 'directory'])->name('agents.directory');
Route::get('/about', [App\Http\Controllers\AboutController::class, 'index'])->name('about');
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    
    // Admin Login Route
    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login.post');

    // Registration Routes
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    // Password Reset Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    // Email Verification Routes
    Route::get('/verify-email', [VerifyEmailController::class, 'show'])->name('verification.notice');
    Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])->name('verification.send');
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])->name('verification.verify')->middleware('signed');

    // Two-Factor Authentication Routes
    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
    Route::get('/two-factor/backup', [TwoFactorController::class, 'showBackupForm'])->name('two-factor.backup');
    Route::post('/two-factor/backup', [TwoFactorController::class, 'verifyBackup'])->name('two-factor.backup.verify');

    // Social Authentication Routes
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

// Authenticated Routes
Route::middleware(['auth', 'trackactivity', 'banned', '2fa'])->group(function () {
    // Logout Routes
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::post('/logout-all', [LogoutController::class, 'logoutAll'])->name('logout.all');

    // Two-Factor Authentication Setup
    Route::get('/two-factor/setup', [TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');

    // Biometric Authentication Routes
    Route::get('/biometric/register', [BiometricAuthController::class, 'showRegistrationForm'])->name('biometric.register');
    Route::post('/biometric/register', [BiometricAuthController::class, 'register'])->name('biometric.register.post');
    Route::get('/biometric/authenticate', [BiometricAuthController::class, 'showAuthenticationForm'])->name('biometric.authenticate');
    Route::post('/biometric/authenticate', [BiometricAuthController::class, 'authenticate'])->name('biometric.authenticate.post');
    Route::delete('/biometric/{id}', [BiometricAuthController::class, 'revoke'])->name('biometric.revoke');
    Route::get('/biometric', [BiometricAuthController::class, 'index'])->name('biometric.index');

    // Social Account Management
    Route::get('/social-accounts', [SocialAuthController::class, 'index'])->name('social.accounts');
    Route::post('/social-accounts/{provider}/unlink', [SocialAuthController::class, 'unlink'])->name('social.unlink');

    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::get('/dashboard/settings', [DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::put('/dashboard/settings', [DashboardController::class, 'updateSettings'])->name('dashboard.settings.update');

    // Analytics Routes
    Route::get('/analytics/dashboard', [AnalyticsController::class, 'dashboard'])->name('analytics.dashboard');

    // Test route for debugging
    Route::get('/debug-favorites', function() {
        return 'Debug: Favorites system is working!';
    });

    // Notification API routes
    Route::put('/notifications/{notification}/read', [App\Http\Controllers\UserController::class, 'markNotificationRead'])->name('notifications.read');
    Route::put('/notifications/read-all', [App\Http\Controllers\UserController::class, 'markAllNotificationsRead'])->name('notifications.read.all');
    Route::get('/notifications/count', [App\Http\Controllers\UserController::class, 'getNotificationCount'])->name('notifications.count');

    // User Profile Management
// Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
// Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
// Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
// Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
// Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

    // User Settings
// Route::get('/settings', [SettingsController::class, 'show'])->name('settings.show');
// Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
// Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
// Route::put('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');

    // KYC Verification
// Route::get('/kyc', [KYCController::class, 'show'])->name('kyc.show');
// Route::post('/kyc', [KYCController::class, 'submit'])->name('kyc.submit');
// Route::get('/kyc/status', [KYCController::class, 'status'])->name('kyc.status');

    // Wallet Management
// Route::get('/wallet', [WalletController::class, 'show'])->name('wallet.show');
// Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
// Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');
// Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');

    // Developers Management
    Route::prefix('developer')->name('developer.')->group(function () {
        // Developer Management
        Route::get('/', [App\Http\Controllers\Developer\DeveloperController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Developer\DeveloperController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Developer\DeveloperController::class, 'store'])->name('store');
        Route::get('/{developer}', [App\Http\Controllers\Developer\DeveloperController::class, 'show'])->name('show');
        Route::get('/{developer}/edit', [App\Http\Controllers\Developer\DeveloperController::class, 'edit'])->name('edit');
        Route::put('/{developer}', [App\Http\Controllers\Developer\DeveloperController::class, 'update'])->name('update');
        Route::delete('/{developer}', [App\Http\Controllers\Developer\DeveloperController::class, 'destroy'])->name('destroy');
        Route::post('/{developer}/toggle-status', [App\Http\Controllers\Developer\DeveloperController::class, 'toggleStatus'])->name('toggleStatus');
        Route::post('/{developer}/toggle-verification', [App\Http\Controllers\Developer\DeveloperController::class, 'toggleVerification'])->name('toggleVerification');
        Route::get('/stats', [App\Http\Controllers\Developer\DeveloperController::class, 'getStats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\Developer\DeveloperController::class, 'export'])->name('export');

        // Developer Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperProfileController::class, 'show'])->name('show');
            Route::get('/edit', [App\Http\Controllers\Developer\DeveloperProfileController::class, 'edit'])->name('edit');
            Route::put('/', [App\Http\Controllers\Developer\DeveloperProfileController::class, 'update'])->name('update');
            Route::post('/logo', [App\Http\Controllers\Developer\DeveloperProfileController::class, 'updateLogo'])->name('updateLogo');
            Route::post('/cover', [App\Http\Controllers\Developer\DeveloperProfileController::class, 'updateCoverImage'])->name('updateCoverImage');
            Route::delete('/logo', [App\Http\Controllers\Developer\DeveloperProfileController::class, 'deleteLogo'])->name('deleteLogo');
            Route::delete('/cover', [App\Http\Controllers\Developer\DeveloperProfileController::class, 'deleteCoverImage'])->name('deleteCoverImage');
            Route::get('/completion', [App\Http\Controllers\Developer\DeveloperProfileController::class, 'getProfileCompletion'])->name('completion');
        });

        // Developer Dashboard
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/stats', [App\Http\Controllers\Developer\DeveloperDashboardController::class, 'getQuickStats'])->name('stats');
            Route::get('/activities', [App\Http\Controllers\Developer\DeveloperDashboardController::class, 'getRecentActivities'])->name('activities');
            Route::get('/project-progress', [App\Http\Controllers\Developer\DeveloperDashboardController::class, 'getProjectProgress'])->name('projectProgress');
            Route::get('/upcoming-deadlines', [App\Http\Controllers\Developer\DeveloperDashboardController::class, 'getUpcomingDeadlines'])->name('upcomingDeadlines');
            Route::get('/financial-overview', [App\Http\Controllers\Developer\DeveloperDashboardController::class, 'getFinancialOverview'])->name('financialOverview');
            Route::get('/unit-sales', [App\Http\Controllers\Developer\DeveloperDashboardController::class, 'getUnitSalesStats'])->name('unitSales');
            Route::get('/construction-updates', [App\Http\Controllers\Developer\DeveloperDashboardController::class, 'getConstructionUpdates'])->name('constructionUpdates');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperDashboardController::class, 'exportDashboardData'])->name('export');
        });

        // Projects Management
        Route::prefix('projects')->name('projects.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'store'])->name('store');
            Route::get('/{project}', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'show'])->name('show');
            Route::get('/{project}/edit', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'edit'])->name('edit');
            Route::put('/{project}', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'update'])->name('update');
            Route::delete('/{project}', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'destroy'])->name('destroy');
            Route::post('/{project}/toggle-status', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'toggleStatus'])->name('toggleStatus');
            Route::post('/{project}/update-progress', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'updateProgress'])->name('updateProgress');
            Route::get('/{project}/stats', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'getProjectStats'])->name('stats');
            Route::get('/{project}/timeline', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'getProjectTimeline'])->name('timeline');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperProjectController::class, 'exportProjects'])->name('export');
        });

        // Project Phases Management
        Route::prefix('phases')->name('phases.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperProjectPhaseController::class, 'index'])->name('index');
            Route::post('/add', [App\Http\Controllers\Developer\DeveloperProjectPhaseController::class, 'addPhase'])->name('add');
            Route::get('/{phase}', [App\Http\Controllers\Developer\DeveloperProjectPhaseController::class, 'show'])->name('show');
            Route::get('/{phase}/edit', [App\Http\Controllers\Developer\DeveloperProjectPhaseController::class, 'edit'])->name('edit');
            Route::put('/{phase}', [App\Http\Controllers\Developer\DeveloperProjectPhaseController::class, 'update'])->name('update');
            Route::delete('/{phase}', [App\Http\Controllers\Developer\DeveloperProjectPhaseController::class, 'destroy'])->name('destroy');
            Route::post('/{phase}/toggle-status', [App\Http\Controllers\Developer\DeveloperProjectPhaseController::class, 'toggleStatus'])->name('toggleStatus');
            Route::post('/{phase}/update-progress', [App\Http\Controllers\Developer\DeveloperProjectPhaseController::class, 'updateProgress'])->name('updateProgress');
            Route::get('/{phase}/timeline', [App\Http\Controllers\Developer\DeveloperProjectPhaseController::class, 'getPhaseTimeline'])->name('timeline');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperProjectPhaseController::class, 'exportPhases'])->name('export');
        });

        // Project Units Management
        Route::prefix('units')->name('units.')->group(function () {
            Route::get('/{project}', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'index'])->name('index');
            Route::get('/create/{project}', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'create'])->name('create');
            Route::post('/add/{project}', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'store'])->name('store');
            Route::get('/{project}/{unit}', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'show'])->name('show');
            Route::get('/{project}/{unit}/edit', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'edit'])->name('edit');
            Route::put('/{project}/{unit}', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'update'])->name('update');
            Route::delete('/{project}/{unit}', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'destroy'])->name('destroy');
            Route::post('/{project}/{unit}/toggle-status', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{project}/{unit}/update-price', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'updatePrice'])->name('updatePrice');
            Route::get('/{project}/stats', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'getUnitStats'])->name('stats');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperProjectUnitController::class, 'exportUnits'])->name('export');
        });

        // Certifications Management
        Route::prefix('certifications')->name('certifications.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'store'])->name('store');
            Route::get('/{certification}', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'show'])->name('show');
            Route::get('/{certification}/edit', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'edit'])->name('edit');
            Route::put('/{certification}', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'update'])->name('update');
            Route::delete('/{certification}', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'destroy'])->name('destroy');
            Route::post('/{certification}/toggle-status', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'updateStatus'])->name('updateStatus');
            Route::get('/{certification}/download', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'downloadDocument'])->name('download');
            Route::get('/expiring-soon', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'getExpiringSoon'])->name('expiringSoon');
            Route::get('/expired', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'getExpired'])->name('expired');
            Route::get('/stats', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'getCertificationStats'])->name('stats');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperCertificationController::class, 'exportCertifications'])->name('export');
        });

        // Portfolio Management
        Route::prefix('portfolios')->name('portfolios.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'store'])->name('store');
            Route::get('/{portfolio}', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'show'])->name('show');
            Route::get('/{portfolio}/edit', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'edit'])->name('edit');
            Route::put('/{portfolio}', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'update'])->name('update');
            Route::delete('/{portfolio}', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'destroy'])->name('destroy');
            Route::post('/{portfolio}/toggle-featured', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'toggleFeatured'])->name('toggleFeatured');
            Route::post('/{portfolio}/update-status', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'updateStatus'])->name('updateStatus');
            Route::get('/stats', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'getPortfolioStats'])->name('stats');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperPortfolioController::class, 'exportPortfolios'])->name('export');
        });

        // BIM Models Management
        Route::prefix('bim')->name('bim.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperBimController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Developer\DeveloperBimController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Developer\DeveloperBimController::class, 'store'])->name('store');
            Route::get('/{bim}', [App\Http\Controllers\Developer\DeveloperBimController::class, 'show'])->name('show');
            Route::get('/{bim}/edit', [App\Http\Controllers\Developer\DeveloperBimController::class, 'edit'])->name('edit');
            Route::put('/{bim}', [App\Http\Controllers\Developer\DeveloperBimController::class, 'update'])->name('update');
            Route::delete('/{bim}', [App\Http\Controllers\Developer\DeveloperBimController::class, 'destroy'])->name('destroy');
            Route::post('/{bim}/toggle-status', [App\Http\Controllers\Developer\DeveloperBimController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{bim}/clash-detection', [App\Http\Controllers\Developer\DeveloperBimController::class, 'runClashDetection'])->name('clashDetection');
            Route::post('/{bim}/quantities', [App\Http\Controllers\Developer\DeveloperBimController::class, 'generateQuantities'])->name('generateQuantities');
            Route::get('/{bim}/download', [App\Http\Controllers\Developer\DeveloperBimController::class, 'downloadModel'])->name('download');
            Route::get('/stats', [App\Http\Controllers\Developer\DeveloperBimController::class, 'getBimStats'])->name('stats');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperBimController::class, 'exportBimModels'])->name('export');
        });

        // Construction Updates Management
        Route::prefix('construction-updates')->name('construction-updates.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'store'])->name('store');
            Route::get('/{update}', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'show'])->name('show');
            Route::get('/{update}/edit', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'edit'])->name('edit');
            Route::put('/{update}', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'update'])->name('update');
            Route::delete('/{update}', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'destroy'])->name('destroy');
            Route::get('/{project}/updates', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'getProjectUpdates'])->name('projectUpdates');
            Route::get('/{project}/timeline', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'getProgressTimeline'])->name('timeline');
            Route::get('/stats', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'getUpdateStats'])->name('stats');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperConstructionUpdateController::class, 'exportUpdates'])->name('export');
        });

        // Permits Management
        Route::prefix('permits')->name('permits.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'store'])->name('store');
            Route::get('/{permit}', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'show'])->name('show');
            Route::get('/{permit}/edit', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'edit'])->name('edit');
            Route::put('/{permit}', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'update'])->name('update');
            Route::delete('/{permit}', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'destroy'])->name('destroy');
            Route::post('/{permit}/toggle-status', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'updateStatus'])->name('updateStatus');
            Route::get('/expiring-soon', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'getExpiringSoon'])->name('expiringSoon');
            Route::get('/expired', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'getExpired'])->name('expired');
            Route::get('/{project}/permits', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'getProjectPermits'])->name('projectPermits');
            Route::get('/stats', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'getPermitStats'])->name('stats');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperPermitController::class, 'exportPermits'])->name('export');
        });

        // Contractors Management
        Route::prefix('contractors')->name('contractors.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'store'])->name('store');
            Route::get('/{contractor}', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'show'])->name('show');
            Route::get('/{contractor}/edit', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'edit'])->name('edit');
            Route::put('/{contractor}', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'update'])->name('update');
            Route::delete('/{contractor}', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'destroy'])->name('destroy');
            Route::post('/{contractor}/toggle-status', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{contractor}/rating', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'updateRating'])->name('updateRating');
            Route::get('/available', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'getAvailableContractors'])->name('available');
            Route::get('/stats', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'getContractorStats'])->name('stats');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperContractorController::class, 'exportContractors'])->name('export');
        });

        // Milestones Management
        Route::prefix('milestones')->name('milestones.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'store'])->name('store');
            Route::get('/{milestone}', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'show'])->name('show');
            Route::get('/{milestone}/edit', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'edit'])->name('edit');
            Route::put('/{milestone}', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'update'])->name('update');
            Route::delete('/{milestone}', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'destroy'])->name('destroy');
            Route::post('/{milestone}/toggle-status', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{milestone}/progress', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'updateProgress'])->name('updateProgress');
            Route::post('/{milestone}/complete', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'completeMilestone'])->name('complete');
            Route::get('/upcoming', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'getUpcomingMilestones'])->name('upcoming');
            Route::get('/overdue', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'getOverdueMilestones'])->name('overdue');
            Route::get('/{project}/milestones', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'getProjectMilestones'])->name('projectMilestones');
            Route::get('/stats', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'getMilestoneStats'])->name('stats');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperMilestoneController::class, 'exportMilestones'])->name('export');
        });

        // Financing Management
        Route::prefix('financings')->name('financings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'store'])->name('store');
            Route::get('/{financing}', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'show'])->name('show');
            Route::get('/{financing}/edit', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'edit'])->name('edit');
            Route::put('/{financing}', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'update'])->name('update');
            Route::delete('/{financing}', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'destroy'])->name('destroy');
            Route::post('/{financing}/toggle-status', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'updateStatus'])->name('updateStatus');
            Route::post('/{financing}/payment-schedule', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'calculatePaymentSchedule'])->name('paymentSchedule');
            Route::get('/{project}/financings', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'getProjectFinancings'])->name('projectFinancings');
            Route::get('/stats', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'getFinancingStats'])->name('stats');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperFinancingController::class, 'exportFinancings'])->name('export');
        });

        // Metaverse Management
        Route::prefix('metaverses')->name('metaverses.')->group(function () {
            Route::get('/', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'store'])->name('store');
            Route::get('/{metaverse}', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'show'])->name('show');
            Route::get('/{metaverse}/edit', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'edit'])->name('edit');
            Route::put('/{metaverse}', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'update'])->name('update');
            Route::delete('/{metaverse}', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'destroy'])->name('destroy');
            Route::post('/{metaverse}/publish', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'publish'])->name('publish');
            Route::post('/{metaverse}/access-link', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'generateAccessLink'])->name('accessLink');
            Route::get('/{metaverse}/usage-stats', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'getUsageStats'])->name('usageStats');
            Route::get('/{project}/metaverses', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'getProjectMetaverses'])->name('projectMetaverses');
            Route::get('/stats', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'getMetaverseStats'])->name('stats');
            Route::get('/export', [App\Http\Controllers\Developer\DeveloperMetaverseController::class, 'exportMetaverses'])->name('export');
        });
    });

    // Reports System Routes
    Route::middleware('auth')->prefix('reports')->name('reports.')->group(function () {
        // Main Reports
        Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/dashboard', [App\Http\Controllers\ReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [App\Http\Controllers\ReportController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\ReportController::class, 'store'])->name('store');
        Route::get('/{report}', [App\Http\Controllers\ReportController::class, 'show'])->name('show');
        Route::get('/{report}/edit', [App\Http\Controllers\ReportController::class, 'edit'])->name('edit');
        Route::put('/{report}', [App\Http\Controllers\ReportController::class, 'update'])->name('update');
        Route::delete('/{report}', [App\Http\Controllers\ReportController::class, 'destroy'])->name('destroy');
        Route::post('/{report}/regenerate', [App\Http\Controllers\ReportController::class, 'regenerate'])->name('regenerate');
        Route::get('/{report}/download', [App\Http\Controllers\ReportController::class, 'download'])->name('download');
        Route::get('/{report}/preview', [App\Http\Controllers\ReportController::class, 'preview'])->name('preview');
        Route::get('/analytics', [App\Http\Controllers\ReportController::class, 'analytics'])->name('analytics');

        // Sales Reports
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/', [App\Http\Controllers\Reports\SalesReportController::class, 'index'])->name('index');
            Route::get('/dashboard', [App\Http\Controllers\Reports\SalesReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/create', [App\Http\Controllers\Reports\SalesReportController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Reports\SalesReportController::class, 'store'])->name('store');
            Route::get('/{report}', [App\Http\Controllers\Reports\SalesReportController::class, 'show'])->name('show');
            Route::get('/{report}/analytics', [App\Http\Controllers\Reports\SalesReportController::class, 'analytics'])->name('analytics');
            Route::get('/{report}/export', [App\Http\Controllers\Reports\SalesReportController::class, 'export'])->name('export');
            Route::get('/property/{property}', [App\Http\Controllers\Reports\SalesReportController::class, 'propertyReport'])->name('property');
        });

        // Performance Reports
        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/', [App\Http\Controllers\Reports\PerformanceReportController::class, 'index'])->name('index');
            Route::get('/dashboard', [App\Http\Controllers\Reports\PerformanceReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/create', [App\Http\Controllers\Reports\PerformanceReportController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Reports\PerformanceReportController::class, 'store'])->name('store');
            Route::get('/{report}', [App\Http\Controllers\Reports\PerformanceReportController::class, 'show'])->name('show');
            Route::get('/{report}/analytics', [App\Http\Controllers\Reports\PerformanceReportController::class, 'analytics'])->name('analytics');
            Route::get('/{report}/insights', [App\Http\Controllers\Reports\PerformanceReportController::class, 'insights'])->name('insights');
        });

        // Financial Reports
        Route::prefix('financial')->name('financial.')->group(function () {
            Route::get('/', [App\Http\Controllers\FinancialReportController::class, 'index'])->name('index');
            Route::get('/dashboard', [App\Http\Controllers\FinancialReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/create', [App\Http\Controllers\FinancialReportController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\FinancialReportController::class, 'store'])->name('store');
            Route::get('/{report}', [App\Http\Controllers\FinancialReportController::class, 'show'])->name('show');
            Route::get('/income-statement', [App\Http\Controllers\FinancialReportController::class, 'incomeStatement'])->name('income-statement');
            Route::get('/balance-sheet', [App\Http\Controllers\FinancialReportController::class, 'balanceSheet'])->name('balance-sheet');
            Route::get('/cash-flow', [App\Http\Controllers\FinancialReportController::class, 'cashFlow'])->name('cash-flow');
            Route::get('/analytics', [App\Http\Controllers\FinancialReportController::class, 'analytics'])->name('analytics');
        });

        // Market Reports
        Route::prefix('market')->name('market.')->group(function () {
            Route::get('/', [App\Http\Controllers\Reports\MarketReportController::class, 'index'])->name('index');
            Route::get('/dashboard', [App\Http\Controllers\Reports\MarketReportController::class, 'dashboard'])->name('dashboard');
            Route::get('/create', [App\Http\Controllers\Reports\MarketReportController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Reports\MarketReportController::class, 'store'])->name('store');
            Route::get('/{report}', [App\Http\Controllers\Reports\MarketReportController::class, 'show'])->name('show');
            Route::get('/trends', [App\Http\Controllers\Reports\MarketReportController::class, 'trends'])->name('trends');
            Route::get('/analysis', [App\Http\Controllers\Reports\MarketReportController::class, 'analysis'])->name('analysis');
        });

        // Custom Reports
        Route::prefix('custom')->name('custom.')->group(function () {
            Route::get('/', [App\Http\Controllers\Reports\CustomReportController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Reports\CustomReportController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Reports\CustomReportController::class, 'store'])->name('store');
            Route::get('/{report}', [App\Http\Controllers\Reports\CustomReportController::class, 'show'])->name('show');
            Route::get('/builder', [App\Http\Controllers\Reports\CustomReportController::class, 'builder'])->name('builder');
            Route::post('/preview', [App\Http\Controllers\Reports\CustomReportController::class, 'preview'])->name('preview');
            Route::get('/templates', [App\Http\Controllers\Reports\CustomReportController::class, 'templates'])->name('templates');
            Route::post('/templates', [App\Http\Controllers\Reports\CustomReportController::class, 'saveTemplate'])->name('save-template');
            Route::get('/templates/{template}', [App\Http\Controllers\Reports\CustomReportController::class, 'loadTemplate'])->name('load-template');
            Route::post('/{report}/duplicate', [App\Http\Controllers\Reports\CustomReportController::class, 'duplicate'])->name('duplicate');
            Route::get('/{report}/share', [App\Http\Controllers\Reports\CustomReportController::class, 'share'])->name('share');
            Route::post('/{report}/run', [App\Http\Controllers\Reports\CustomReportController::class, 'run'])->name('run');
        });

        // Report Templates
        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/', [App\Http\Controllers\ReportTemplateController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\ReportTemplateController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\ReportTemplateController::class, 'store'])->name('store');
            Route::get('/{template}', [App\Http\Controllers\ReportTemplateController::class, 'show'])->name('show');
            Route::get('/{template}/edit', [App\Http\Controllers\ReportTemplateController::class, 'edit'])->name('edit');
            Route::put('/{template}', [App\Http\Controllers\ReportTemplateController::class, 'update'])->name('update');
            Route::delete('/{template}', [App\Http\Controllers\ReportTemplateController::class, 'destroy'])->name('destroy');
            Route::post('/{template}/duplicate', [App\Http\Controllers\ReportTemplateController::class, 'duplicate'])->name('duplicate');
            Route::get('/{template}/preview', [App\Http\Controllers\ReportTemplateController::class, 'preview'])->name('preview');
            Route::get('/{template}/parameters', [App\Http\Controllers\ReportTemplateController::class, 'getParameters'])->name('parameters');
        });

        // Report Schedules
        Route::prefix('schedules')->name('schedules.')->group(function () {
            Route::get('/', [App\Http\Controllers\ReportScheduleController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\ReportScheduleController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\ReportScheduleController::class, 'store'])->name('store');
            Route::get('/{schedule}', [App\Http\Controllers\ReportScheduleController::class, 'show'])->name('show');
            Route::get('/{schedule}/edit', [App\Http\Controllers\ReportScheduleController::class, 'edit'])->name('edit');
            Route::put('/{schedule}', [App\Http\Controllers\ReportScheduleController::class, 'update'])->name('update');
            Route::delete('/{schedule}', [App\Http\Controllers\ReportScheduleController::class, 'destroy'])->name('destroy');
            Route::post('/{schedule}/toggle', [App\Http\Controllers\ReportScheduleController::class, 'toggle'])->name('toggle');
            Route::post('/{schedule}/run', [App\Http\Controllers\ReportScheduleController::class, 'run'])->name('run');
            Route::get('/{schedule}/history', [App\Http\Controllers\ReportScheduleController::class, 'history'])->name('history');
        });

        // Report Exports
        Route::prefix('exports')->name('exports.')->group(function () {
            Route::get('/', [App\Http\Controllers\ReportExportController::class, 'index'])->name('index');
            Route::post('/{report}', [App\Http\Controllers\ReportExportController::class, 'export'])->name('export');
            Route::get('/{export}', [App\Http\Controllers\ReportExportController::class, 'download'])->name('download');
            Route::get('/{export}/preview', [App\Http\Controllers\ReportExportController::class, 'preview'])->name('preview');
            Route::delete('/{export}', [App\Http\Controllers\ReportExportController::class, 'destroy'])->name('destroy');
            Route::post('/{export}/share', [App\Http\Controllers\ReportExportController::class, 'share'])->name('share');
        });

        // API Routes for AJAX requests
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/stats', [App\Http\Controllers\ReportController::class, 'getStats'])->name('stats');
            Route::get('/recent', [App\Http\Controllers\ReportController::class, 'getRecentReports'])->name('recent');
            Route::get('/templates/{template}/config', [App\Http\Controllers\ReportTemplateController::class, 'getConfig'])->name('template-config');
            Route::post('/preview', [App\Http\Controllers\ReportController::class, 'previewReport'])->name('preview-report');
            Route::post('/save-draft', [App\Http\Controllers\ReportController::class, 'saveDraft'])->name('save-draft');
        });
    });

    // Referral System
// Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
// Route::post('/referrals/invite', [ReferralController::class, 'invite'])->name('referrals.invite');

    // Admin Routes
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

        // User Management
        Route::get('/users', [App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users');
        Route::get('/users', [App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [App\Http\Controllers\Admin\AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\Admin\AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\AdminController::class, 'deleteUser'])->name('users.delete');
        Route::post('/users/{user}/toggle-status', [App\Http\Controllers\Admin\AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');

        // Property Management
        Route::get('/properties', [App\Http\Controllers\Admin\AdminController::class, 'properties'])->name('properties');
        Route::get('/properties', [App\Http\Controllers\Admin\AdminController::class, 'properties'])->name('properties.index');

        // Company Management
        Route::get('/companies', [App\Http\Controllers\Admin\AdminController::class, 'companies'])->name('companies');
        Route::get('/companies', [App\Http\Controllers\Admin\AdminController::class, 'companies'])->name('companies.index');
        Route::get('/companies/create', [App\Http\Controllers\Admin\AdminController::class, 'createCompany'])->name('companies.create');
        Route::post('/companies', [App\Http\Controllers\Admin\AdminController::class, 'storeCompany'])->name('companies.store');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', function () {
                return redirect()->route('admin.settings.general');
            })->name('index');
            Route::get('/general', function () {
                return redirect()->route('admin.dashboard');
            })->name('general');
            Route::get('/security', function () {
                return redirect()->route('admin.dashboard');
            })->name('security');
            Route::get('/system', function () {
                return redirect()->route('admin.dashboard');
            })->name('system');
            Route::get('/email', function () {
                return redirect()->route('admin.dashboard');
            })->name('email');
            Route::get('/payment', function () {
                return redirect()->route('admin.dashboard');
            })->name('payment');
            Route::get('/social', function () {
                return redirect()->route('admin.dashboard');
            })->name('social');
            Route::get('/seo', function () {
                return redirect()->route('admin.dashboard');
            })->name('seo');
            Route::get('/backup', function () {
                return redirect()->route('admin.dashboard');
            })->name('backup');
            Route::get('/logs', function () {
                return redirect()->route('admin.dashboard');
            })->name('logs');
        });

        Route::get('/settings', function () {
            return redirect()->route('admin.settings.general');
        })->name('settings');

        Route::get('/projects', function () {
            return redirect()->route('admin.dashboard');
        })->name('projects.index');

        Route::get('/agents', function () {
            return redirect()->route('admin.dashboard');
        })->name('agents.index');

        Route::get('/activity', [App\Http\Controllers\Admin\ActivityController::class, 'index'])->name('activity');
    });

    // User Management (Admin only)
    Route::prefix('admin/users')->name('admin.users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AdminUserController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}', [App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [App\Http\Controllers\Admin\AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [App\Http\Controllers\Admin\AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('destroy');
    });

    // Properties Management (Admin only)
    Route::prefix('admin/properties')->name('admin.properties.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminPropertyController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AdminPropertyController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\AdminPropertyController::class, 'store'])->name('store');
        Route::get('/{property}', [App\Http\Controllers\Admin\AdminPropertyController::class, 'show'])->name('show');
        Route::get('/{property}/edit', [App\Http\Controllers\Admin\AdminPropertyController::class, 'edit'])->name('edit');
        Route::put('/{property}', [App\Http\Controllers\Admin\AdminPropertyController::class, 'update'])->name('update');
        Route::delete('/{property}', [App\Http\Controllers\Admin\AdminPropertyController::class, 'destroy'])->name('destroy');
    });

    // Companies Management (Admin only)
    Route::prefix('admin/companies')->name('admin.companies.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminCompanyController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AdminCompanyController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\AdminCompanyController::class, 'store'])->name('store');
        Route::get('/{company}', [App\Http\Controllers\Admin\AdminCompanyController::class, 'show'])->name('show');
        Route::get('/{company}/edit', [App\Http\Controllers\Admin\AdminCompanyController::class, 'edit'])->name('edit');
        Route::put('/{company}', [App\Http\Controllers\Admin\AdminCompanyController::class, 'update'])->name('update');
        Route::delete('/{company}', [App\Http\Controllers\Admin\AdminCompanyController::class, 'destroy'])->name('destroy');
    });

    // System Management (Admin only)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/settings', [App\Http\Controllers\Admin\AdminSettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\Admin\AdminSettingsController::class, 'update'])->name('settings.update');
        
        Route::get('/maintenance', [App\Http\Controllers\Admin\AdminMaintenanceController::class, 'index'])->name('maintenance');
        Route::post('/maintenance/run', [App\Http\Controllers\Admin\AdminMaintenanceController::class, 'runMaintenance'])->name('maintenance.run');
        
        Route::get('/backups', [App\Http\Controllers\Admin\AdminBackupController::class, 'index'])->name('backups');
        Route::post('/backups/create', [App\Http\Controllers\Admin\AdminBackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/{backup}/download', [App\Http\Controllers\Admin\AdminBackupController::class, 'download'])->name('backups.download');
    });

// Investors Management Routes
Route::prefix('investor')->name('investor.')->middleware(['auth'])->group(function () {

    // Investor Management
    Route::get('/', [App\Http\Controllers\Investor\InvestorController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Investor\InvestorController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Investor\InvestorController::class, 'store'])->name('store');
    Route::get('/{investor}', [App\Http\Controllers\Investor\InvestorController::class, 'show'])->name('show');
    Route::get('/{investor}/edit', [App\Http\Controllers\Investor\InvestorController::class, 'edit'])->name('edit');
    Route::put('/{investor}', [App\Http\Controllers\Investor\InvestorController::class, 'update'])->name('update');
    Route::delete('/{investor}', [App\Http\Controllers\Investor\InvestorController::class, 'destroy'])->name('destroy');
    Route::put('/{investor}/status', [App\Http\Controllers\Investor\InvestorController::class, 'updateStatus'])->name('update.status');
    Route::put('/{investor}/verification', [App\Http\Controllers\Investor\InvestorController::class, 'updateVerification'])->name('update.verification');
    Route::get('/stats', [App\Http\Controllers\Investor\InvestorController::class, 'getInvestorStats'])->name('stats');
    Route::get('/export', [App\Http\Controllers\Investor\InvestorController::class, 'exportInvestors'])->name('export');

    // Investor Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestorProfileController::class, 'show'])->name('show');
        Route::get('/edit', [App\Http\Controllers\Investor\InvestorProfileController::class, 'edit'])->name('edit');
        Route::put('/', [App\Http\Controllers\Investor\InvestorProfileController::class, 'update'])->name('update');
        Route::post('/picture', [App\Http\Controllers\Investor\InvestorProfileController::class, 'updateProfilePicture'])->name('update.picture');
        Route::delete('/picture', [App\Http\Controllers\Investor\InvestorProfileController::class, 'deleteProfilePicture'])->name('delete.picture');
        Route::get('/completion', [App\Http\Controllers\Investor\InvestorProfileController::class, 'getProfileCompletion'])->name('completion');
        Route::get('/public/{investor}', [App\Http\Controllers\Investor\InvestorProfileController::class, 'getPublicProfile'])->name('public');
        Route::get('/export', [App\Http\Controllers\Investor\InvestorProfileController::class, 'exportProfile'])->name('export');
    });

    // Investor Dashboard
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/quick-stats', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'getQuickStats'])->name('quick.stats');
        Route::get('/activities', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'getRecentActivities'])->name('activities');
        Route::get('/portfolio-performance', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'getPortfolioPerformance'])->name('portfolio.performance');
        Route::get('/investment-distribution', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'getInvestmentDistribution'])->name('investment.distribution');
        Route::get('/monthly-returns', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'getMonthlyReturns'])->name('monthly.returns');
        Route::get('/top-investments', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'getTopPerformingInvestments'])->name('top.investments');
        Route::get('/upcoming-milestones', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'getUpcomingMilestones'])->name('upcoming.milestones');
        Route::get('/risk-analysis', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'getRiskAnalysis'])->name('risk.analysis');
        Route::get('/market-trends', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'getMarketTrends'])->name('market.trends');
        Route::get('/export', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'exportDashboardData'])->name('export');
    });

    // Portfolio Management
    Route::prefix('portfolio')->name('portfolio.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'store'])->name('store');
        Route::get('/{portfolio}', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'show'])->name('show');
        Route::get('/{portfolio}/edit', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'edit'])->name('edit');
        Route::put('/{portfolio}', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'update'])->name('update');
        Route::delete('/{portfolio}', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'destroy'])->name('destroy');
        Route::put('/{portfolio}/value', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'updateValue'])->name('update.value');
        Route::get('/stats', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'getPortfolioStats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'exportPortfolios'])->name('export');
    });

    // Transactions Management
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'store'])->name('store');
        Route::get('/{transaction}', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'show'])->name('show');
        Route::get('/{transaction}/edit', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'edit'])->name('edit');
        Route::put('/{transaction}', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'destroy'])->name('destroy');
        Route::put('/{transaction}/status', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'updateStatus'])->name('update.status');
        Route::get('/stats', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'getTransactionStats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'exportTransactions'])->name('export');
        Route::get('/{transaction}/download-receipt', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'downloadReceipt'])->name('download.receipt');
    });

    // ROI Calculations
    Route::prefix('roi')->name('roi.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestorRoiController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\InvestorRoiController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\InvestorRoiController::class, 'store'])->name('store');
        Route::get('/{roi}', [App\Http\Controllers\Investor\InvestorRoiController::class, 'show'])->name('show');
        Route::get('/calculate', [App\Http\Controllers\Investor\InvestorRoiController::class, 'calculate'])->name('calculate');
        Route::get('/portfolio/{portfolio}/history', [App\Http\Controllers\Investor\InvestorRoiController::class, 'getPortfolioRoiHistory'])->name('portfolio.history');
        Route::get('/projections', [App\Http\Controllers\Investor\InvestorRoiController::class, 'getRoiProjections'])->name('projections');
        Route::get('/comparison', [App\Http\Controllers\Investor\InvestorRoiController::class, 'getRoiComparison'])->name('comparison');
        Route::get('/export', [App\Http\Controllers\Investor\InvestorRoiController::class, 'exportRoiCalculations'])->name('export');
    });

    // Risk Assessment
    Route::prefix('risk')->name('risk.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestorRiskController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\InvestorRiskController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\InvestorRiskController::class, 'store'])->name('store');
        Route::get('/{risk}', [App\Http\Controllers\Investor\InvestorRiskController::class, 'show'])->name('show');
        Route::get('/{risk}/edit', [App\Http\Controllers\Investor\InvestorRiskController::class, 'edit'])->name('edit');
        Route::put('/{risk}', [App\Http\Controllers\Investor\InvestorRiskController::class, 'update'])->name('update');
        Route::delete('/{risk}', [App\Http\Controllers\Investor\InvestorRiskController::class, 'destroy'])->name('destroy');
        Route::post('/calculate', [App\Http\Controllers\Investor\InvestorRiskController::class, 'calculatePortfolioRisk'])->name('calculate');
        Route::get('/dashboard', [App\Http\Controllers\Investor\InvestorRiskController::class, 'getRiskDashboard'])->name('dashboard');
        Route::get('/portfolio/{portfolio}/performance', [App\Http\Controllers\Investor\InvestorRiskController::class, 'getPortfolioPerformance'])->name('portfolio.performance');
        Route::post('/stress-test', [App\Http\Controllers\Investor\InvestorRiskController::class, 'runStressTest'])->name('stress.test');
        Route::get('/alerts', [App\Http\Controllers\Investor\InvestorRiskController::class, 'getRiskAlerts'])->name('alerts');
        Route::get('/export', [App\Http\Controllers\Investor\InvestorRiskController::class, 'exportRiskAssessments'])->name('export');
    });

    // Investment Opportunities
    Route::prefix('opportunities')->name('opportunities.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'index'])->name('index');
        Route::get('/{opportunity}', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'show'])->name('show');
        Route::post('/{opportunity}/invest', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'invest'])->name('invest');
        Route::get('/my-investments', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'getMyInvestments'])->name('my.investments');
        Route::get('/stats', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'getOpportunityStats'])->name('stats');
        Route::get('/recommended', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'getRecommendedOpportunities'])->name('recommended');
        Route::post('/{opportunity}/watch', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'watchOpportunity'])->name('watch');
        Route::delete('/{opportunity}/unwatch', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'unwatchOpportunity'])->name('unwatch');
        Route::get('/watchlist', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'getWatchlist'])->name('watchlist');
        Route::post('/calculate-return', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'calculateInvestmentReturn'])->name('calculate.return');
        Route::post('/compare', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'compareOpportunities'])->name('compare');
        Route::get('/export', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'exportOpportunities'])->name('export');
    });

    // Investment Funds
    Route::prefix('funds')->name('funds.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestmentFundController::class, 'index'])->name('index');
        Route::get('/{fund}', [App\Http\Controllers\Investor\InvestmentFundController::class, 'show'])->name('show');
        Route::post('/{fund}/invest', [App\Http\Controllers\Investor\InvestmentFundController::class, 'invest'])->name('invest');
        Route::get('/my-investments', [App\Http\Controllers\Investor\InvestmentFundController::class, 'getMyFundInvestments'])->name('my.investments');
        Route::get('/{fund}/performance', [App\Http\Controllers\Investor\InvestmentFundController::class, 'getFundPerformance'])->name('performance');
        Route::get('/stats', [App\Http\Controllers\Investor\InvestmentFundController::class, 'getFundStats'])->name('stats');
        Route::get('/recommended', [App\Http\Controllers\Investor\InvestmentFundController::class, 'getRecommendedFunds'])->name('recommended');
        Route::post('/compare', [App\Http\Controllers\Investor\InvestmentFundController::class, 'compareFunds'])->name('compare');
        Route::post('/calculate-value', [App\Http\Controllers\Investor\InvestmentFundController::class, 'calculateInvestmentValue'])->name('calculate.value');
        Route::get('/export', [App\Http\Controllers\Investor\InvestmentFundController::class, 'exportFunds'])->name('export');
    });

    // Crowdfunding
    Route::prefix('crowdfunding')->name('crowdfunding.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'index'])->name('index');
        Route::get('/{campaign}', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'show'])->name('show');
        Route::post('/{campaign}/invest', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'invest'])->name('invest');
        Route::get('/my-investments', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'getMyCrowdfundingInvestments'])->name('my.investments');
        Route::get('/stats', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'getCampaignStats'])->name('stats');
        Route::get('/recommended', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'getRecommendedCampaigns'])->name('recommended');
        Route::post('/{campaign}/watch', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'watchCampaign'])->name('watch');
        Route::delete('/{campaign}/unwatch', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'unwatchCampaign'])->name('unwatch');
        Route::get('/{campaign}/updates', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'getCampaignUpdates'])->name('updates');
        Route::get('/{campaign}/progress', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'getCampaignProgress'])->name('progress');
        Route::post('/calculate-return', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'calculateInvestmentReturn'])->name('calculate.return');
        Route::post('/compare', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'compareCampaigns'])->name('compare');
        Route::get('/export', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'exportCampaigns'])->name('export');
    });

    // DeFi Loans
    Route::prefix('defi/loans')->name('defi.loans.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\DefiLoanController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\DefiLoanController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\DefiLoanController::class, 'store'])->name('store');
        Route::get('/{loan}', [App\Http\Controllers\Investor\DefiLoanController::class, 'show'])->name('show');
        Route::put('/{loan}/status', [App\Http\Controllers\Investor\DefiLoanController::class, 'updateStatus'])->name('update.status');
        Route::post('/{loan}/repayment', [App\Http\Controllers\Investor\DefiLoanController::class, 'recordRepayment'])->name('record.repayment');
        Route::get('/stats', [App\Http\Controllers\Investor\DefiLoanController::class, 'getLoanStats'])->name('stats');
        Route::get('/{loan}/performance', [App\Http\Controllers\Investor\DefiLoanController::class, 'getLoanPerformance'])->name('performance');
        Route::get('/export', [App\Http\Controllers\Investor\DefiLoanController::class, 'exportLoans'])->name('export');
    });

    // DeFi Staking
    Route::prefix('defi/staking')->name('defi.staking.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\DefiStakingController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\DefiStakingController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\DefiStakingController::class, 'store'])->name('store');
        Route::get('/{staking}', [App\Http\Controllers\Investor\DefiStakingController::class, 'show'])->name('show');
        Route::post('/{staking}/unstake', [App\Http\Controllers\Investor\DefiStakingController::class, 'unstake'])->name('unstake');
        Route::post('/{staking}/claim-rewards', [App\Http\Controllers\Investor\DefiStakingController::class, 'claimRewards'])->name('claim.rewards');
        Route::put('/{staking}/rewards', [App\Http\Controllers\Investor\DefiStakingController::class, 'updateRewards'])->name('update.rewards');
        Route::get('/stats', [App\Http\Controllers\Investor\DefiStakingController::class, 'getStakingStats'])->name('stats');
        Route::get('/{staking}/performance', [App\Http\Controllers\Investor\DefiStakingController::class, 'getStakingPerformance'])->name('performance');
        Route::get('/export', [App\Http\Controllers\Investor\DefiStakingController::class, 'exportStaking'])->name('export');
    });
});

});

// Payments System Routes
Route::prefix('payments')->name('payments.')->middleware(['auth'])->group(function () {

    // Payment Routes
    Route::get('/', [App\Http\Controllers\Payment\PaymentController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Payment\PaymentController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Payment\PaymentController::class, 'process'])->name('process');
    Route::get('/{payment}', [App\Http\Controllers\Payment\PaymentController::class, 'show'])->name('show');
    Route::put('/{payment}/status', [App\Http\Controllers\Payment\PaymentController::class, 'updateStatus'])->name('update.status');
    Route::post('/{payment}/refund', [App\Http\Controllers\Payment\PaymentController::class, 'refund'])->name('refund');
    Route::get('/stats', [App\Http\Controllers\Payment\PaymentController::class, 'getPaymentStats'])->name('stats');
    Route::get('/export', [App\Http\Controllers\Payment\PaymentController::class, 'exportPayments'])->name('export');

    // Payment Methods Routes
    Route::prefix('methods')->name('methods.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\PaymentMethodController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Payment\PaymentMethodController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Payment\PaymentMethodController::class, 'store'])->name('store');
        Route::get('/{paymentMethod}', [App\Http\Controllers\Payment\PaymentMethodController::class, 'show'])->name('show');
        Route::get('/{paymentMethod}/edit', [App\Http\Controllers\Payment\PaymentMethodController::class, 'edit'])->name('edit');
        Route::put('/{paymentMethod}', [App\Http\Controllers\Payment\PaymentMethodController::class, 'update'])->name('update');
        Route::delete('/{paymentMethod}', [App\Http\Controllers\Payment\PaymentMethodController::class, 'destroy'])->name('destroy');
        Route::put('/{paymentMethod}/default', [App\Http\Controllers\Payment\PaymentMethodController::class, 'setDefault'])->name('set.default');
        Route::post('/{paymentMethod}/verify', [App\Http\Controllers\Payment\PaymentMethodController::class, 'verifyPaymentMethod'])->name('verify');
        Route::get('/list', [App\Http\Controllers\Payment\PaymentMethodController::class, 'getPaymentMethods'])->name('list');
        Route::get('/stats', [App\Http\Controllers\Payment\PaymentMethodController::class, 'getPaymentMethodStats'])->name('stats');
    });

    // Payment Gateways Routes
    Route::prefix('gateways')->name('gateways.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'store'])->name('store');
        Route::get('/{gateway}', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'show'])->name('show');
        Route::get('/{gateway}/edit', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'edit'])->name('edit');
        Route::put('/{gateway}', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'update'])->name('update');
        Route::delete('/{gateway}', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'destroy'])->name('destroy');
        Route::post('/{gateway}/test', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'testConnection'])->name('test');
        Route::post('/webhook/{gateway}', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'processWebhook'])->name('webhook');
        Route::get('/active', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'getActiveGateways'])->name('active');
        Route::get('/stats', [App\Http\Controllers\Payment\PaymentGatewayController::class, 'getGatewayStats'])->name('stats');
    });

    // Transactions Routes
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\TransactionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Payment\TransactionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Payment\TransactionController::class, 'store'])->name('store');
        Route::get('/{transaction}', [App\Http\Controllers\Payment\TransactionController::class, 'show'])->name('show');
        Route::put('/{transaction}/status', [App\Http\Controllers\Payment\TransactionController::class, 'updateStatus'])->name('update.status');
        Route::post('/{transaction}/reverse', [App\Http\Controllers\Payment\TransactionController::class, 'reverse'])->name('reverse');
        Route::get('/stats', [App\Http\Controllers\Payment\TransactionController::class, 'getTransactionStats'])->name('stats');
        Route::get('/user/{user_id}', [App\Http\Controllers\Payment\TransactionController::class, 'getUserTransactions'])->name('user');
        Route::get('/export', [App\Http\Controllers\Payment\TransactionController::class, 'exportTransactions'])->name('export');
        Route::get('/search', [App\Http\Controllers\Payment\TransactionController::class, 'searchTransactions'])->name('search');
        Route::get('/reference/{reference}', [App\Http\Controllers\Payment\TransactionController::class, 'getTransactionDetails'])->name('reference');
    });

    // Invoices Routes
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Payment\InvoiceController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Payment\InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [App\Http\Controllers\Payment\InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/edit', [App\Http\Controllers\Payment\InvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [App\Http\Controllers\Payment\InvoiceController::class, 'update'])->name('update');
        Route::post('/{invoice}/send', [App\Http\Controllers\Payment\InvoiceController::class, 'send'])->name('send');
        Route::post('/{invoice}/mark-paid', [App\Http\Controllers\Payment\InvoiceController::class, 'markAsPaid'])->name('mark.paid');
        Route::post('/{invoice}/cancel', [App\Http\Controllers\Payment\InvoiceController::class, 'cancel'])->name('cancel');
        Route::get('/{invoice}/download', [App\Http\Controllers\Payment\InvoiceController::class, 'downloadPDF'])->name('download');
        Route::get('/stats', [App\Http\Controllers\Payment\InvoiceController::class, 'getInvoiceStats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\Payment\InvoiceController::class, 'exportInvoices'])->name('export');
    });

    // Receipts Routes
    Route::prefix('receipts')->name('receipts.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\ReceiptController::class, 'index'])->name('index');
        Route::get('/{receipt}', [App\Http\Controllers\Payment\ReceiptController::class, 'show'])->name('show');
        Route::post('/generate', [App\Http\Controllers\Payment\ReceiptController::class, 'generate'])->name('generate');
        Route::get('/{receipt}/download', [App\Http\Controllers\Payment\ReceiptController::class, 'downloadPDF'])->name('download');
        Route::post('/{receipt}/send', [App\Http\Controllers\Payment\ReceiptController::class, 'sendEmail'])->name('send');
        Route::post('/{receipt}/void', [App\Http\Controllers\Payment\ReceiptController::class, 'void'])->name('void');
        Route::post('/{receipt}/duplicate', [App\Http\Controllers\Payment\ReceiptController::class, 'duplicate'])->name('duplicate');
        Route::get('/stats', [App\Http\Controllers\Payment\ReceiptController::class, 'getReceiptStats'])->name('stats');
        Route::get('/user/{user_id}', [App\Http\Controllers\Payment\ReceiptController::class, 'getUserReceipts'])->name('user');
        Route::get('/export', [App\Http\Controllers\Payment\ReceiptController::class, 'exportReceipts'])->name('export');
        Route::get('/search', [App\Http\Controllers\Payment\ReceiptController::class, 'searchReceipts'])->name('search');
        Route::get('/number/{receiptNumber}', [App\Http\Controllers\Payment\ReceiptController::class, 'getReceiptDetails'])->name('number');
    });

    // Refunds Routes
    Route::prefix('refunds')->name('refunds.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\RefundController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Payment\RefundController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Payment\RefundController::class, 'store'])->name('store');
        Route::get('/{refund}', [App\Http\Controllers\Payment\RefundController::class, 'show'])->name('show');
        Route::put('/{refund}/status', [App\Http\Controllers\Payment\RefundController::class, 'updateStatus'])->name('update.status');
        Route::post('/{refund}/approve', [App\Http\Controllers\Payment\RefundController::class, 'approve'])->name('approve');
        Route::post('/{refund}/reject', [App\Http\Controllers\Payment\RefundController::class, 'reject'])->name('reject');
        Route::post('/{refund}/cancel', [App\Http\Controllers\Payment\RefundController::class, 'cancel'])->name('cancel');
        Route::get('/stats', [App\Http\Controllers\Payment\RefundController::class, 'getRefundStats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\Payment\RefundController::class, 'exportRefunds'])->name('export');
    });

    // Escrow Routes
    Route::prefix('escrow')->name('escrow.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\EscrowController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Payment\EscrowController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Payment\EscrowController::class, 'store'])->name('store');
        Route::get('/{escrow}', [App\Http\Controllers\Payment\EscrowController::class, 'show'])->name('show');
        Route::post('/{escrow}/fund', [App\Http\Controllers\Payment\EscrowController::class, 'fund'])->name('fund');
        Route::post('/{escrow}/release', [App\Http\Controllers\Payment\EscrowController::class, 'release'])->name('release');
        Route::post('/{escrow}/dispute', [App\Http\Controllers\Payment\EscrowController::class, 'createDispute'])->name('dispute');
        Route::post('/{escrow}/resolve', [App\Http\Controllers\Payment\EscrowController::class, 'resolveDispute'])->name('resolve');
        Route::get('/stats', [App\Http\Controllers\Payment\EscrowController::class, 'getEscrowStats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\Payment\EscrowController::class, 'exportEscrows'])->name('export');
    });

    // Mortgage Routes
    Route::prefix('mortgage')->name('mortgage.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\MortgageController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Payment\MortgageController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Payment\MortgageController::class, 'store'])->name('store');
        Route::get('/{application}', [App\Http\Controllers\Payment\MortgageController::class, 'show'])->name('show');
        Route::put('/{application}/status', [App\Http\Controllers\Payment\MortgageController::class, 'updateStatus'])->name('update.status');
        Route::post('/{application}/documents', [App\Http\Controllers\Payment\MortgageController::class, 'uploadDocuments'])->name('upload.documents');
        Route::get('/{application}/schedule', [App\Http\Controllers\Payment\MortgageController::class, 'calculateSchedule'])->name('schedule');
        Route::get('/stats', [App\Http\Controllers\Payment\MortgageController::class, 'getApplicationStats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\Payment\MortgageController::class, 'exportApplications'])->name('export');
    });

    // Mortgage Calculator Routes
    Route::prefix('calculator')->name('calculator.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\MortgageCalculatorController::class, 'index'])->name('index');
        Route::post('/calculate', [App\Http\Controllers\Payment\MortgageCalculatorController::class, 'calculate'])->name('calculate');
        Route::post('/compare', [App\Http\Controllers\Payment\MortgageCalculatorController::class, 'compareLoans'])->name('compare');
        Route::post('/refinance', [App\Http\Controllers\Payment\MortgageCalculatorController::class, 'refinanceCalculator'])->name('refinance');
        Route::post('/affordability', [App\Http\Controllers\Payment\MortgageCalculatorController::class, 'affordabilityAnalysis'])->name('affordability');
    });

    // Loans Routes
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\LoanController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Payment\LoanController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Payment\LoanController::class, 'store'])->name('store');
        Route::get('/{loan}', [App\Http\Controllers\Payment\LoanController::class, 'show'])->name('show');
        Route::put('/{loan}/status', [App\Http\Controllers\Payment\LoanController::class, 'updateStatus'])->name('update.status');
        Route::post('/{loan}/disburse', [App\Http\Controllers\Payment\LoanController::class, 'disburse'])->name('disburse');
        Route::get('/{loan}/schedule', [App\Http\Controllers\Payment\LoanController::class, 'calculateSchedule'])->name('schedule');
        Route::get('/stats', [App\Http\Controllers\Payment\LoanController::class, 'getLoanStats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\Payment\LoanController::class, 'exportLoans'])->name('export');
    });

    // Wallet Routes
    Route::prefix('wallets')->name('wallets.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\WalletController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Payment\WalletController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Payment\WalletController::class, 'store'])->name('store');
        Route::get('/{wallet}', [App\Http\Controllers\Payment\WalletController::class, 'show'])->name('show');
        Route::get('/{wallet}/edit', [App\Http\Controllers\Payment\WalletController::class, 'edit'])->name('edit');
        Route::put('/{wallet}', [App\Http\Controllers\Payment\WalletController::class, 'update'])->name('update');
        Route::delete('/{wallet}', [App\Http\Controllers\Payment\WalletController::class, 'destroy'])->name('destroy');
        Route::get('/{wallet}/balance', [App\Http\Controllers\Payment\WalletController::class, 'getBalance'])->name('balance');
        Route::get('/{wallet}/transactions', [App\Http\Controllers\Payment\WalletController::class, 'getTransactionHistory'])->name('transactions');
        Route::post('/{wallet}/send', [App\Http\Controllers\Payment\WalletController::class, 'sendTransaction'])->name('send');
        Route::get('/stats', [App\Http\Controllers\Payment\WalletController::class, 'getWalletStats'])->name('stats');
        Route::put('/{wallet}/default', [App\Http\Controllers\Payment\WalletController::class, 'setDefault'])->name('set.default');
        Route::get('/{walletId}/balance', [App\Http\Controllers\Payment\WalletController::class, 'getWalletBalance'])->name('balance.id');
    });

    // Crypto Payments Routes
    Route::prefix('crypto')->name('crypto.')->group(function () {
        Route::get('/', [App\Http\Controllers\Payment\CryptoPaymentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Payment\CryptoPaymentController::class, 'create'])->name('create');
        Route::post('/process', [App\Http\Controllers\Payment\CryptoPaymentController::class, 'processPayment'])->name('process');
        Route::get('/{transaction}', [App\Http\Controllers\Payment\CryptoPaymentController::class, 'show'])->name('show');
        Route::get('/status/{transactionHash}', [App\Http\Controllers\Payment\CryptoPaymentController::class, 'getTransactionStatus'])->name('status');
        Route::post('/receive', [App\Http\Controllers\Payment\CryptoPaymentController::class, 'receivePayment'])->name('receive');
        Route::get('/rates', [App\Http\Controllers\Payment\CryptoPaymentController::class, 'getExchangeRates'])->name('rates');
        Route::get('/{walletId}/balance', [App\Http\Controllers\Payment\CryptoPaymentController::class, 'getWalletBalance'])->name('wallet.balance');
        Route::get('/history', [App\Http\Controllers\Payment\CryptoPaymentController::class, 'getTransactionHistory'])->name('history');
        Route::get('/stats', [App\Http\Controllers\Payment\CryptoPaymentController::class, 'getCryptoStats'])->name('stats');
    });
});
Route::prefix('investors')->name('investors.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Investor\InvestorController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Investor\InvestorController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Investor\InvestorController::class, 'store'])->name('store');
    Route::get('/{investor}', [App\Http\Controllers\Investor\InvestorController::class, 'show'])->name('show');
    Route::get('/{investor}/edit', [App\Http\Controllers\Investor\InvestorController::class, 'edit'])->name('edit');
    Route::put('/{investor}', [App\Http\Controllers\Investor\InvestorController::class, 'update'])->name('update');
    Route::put('/{investor}/status', [App\Http\Controllers\Investor\InvestorController::class, 'updateStatus'])->name('status');
    Route::put('/{investor}/verify', [App\Http\Controllers\Investor\InvestorController::class, 'verifyInvestor'])->name('verify');
    Route::get('/stats', [App\Http\Controllers\Investor\InvestorController::class, 'getInvestorStats'])->name('stats');
    Route::get('/export', [App\Http\Controllers\Investor\InvestorController::class, 'exportInvestors'])->name('export');
    Route::get('/{investor}/performance', [App\Http\Controllers\Investor\InvestorController::class, 'getInvestorPerformance'])->name('performance');
    Route::put('/{investor}/risk-profile', [App\Http\Controllers\Investor\InvestorController::class, 'updateRiskProfile'])->name('risk-profile');
    Route::get('/{investor}/recommendations', [App\Http\Controllers\Investor\InvestorController::class, 'getInvestmentRecommendations'])->name('recommendations');

    // Investor Dashboard
    Route::get('/{investor}/dashboard', [App\Http\Controllers\Investor\InvestorDashboardController::class, 'index'])->name('dashboard');

    // Investor Portfolio
    Route::prefix('/{investor}/portfolio')->name('portfolio.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'store'])->name('store');
        Route::get('/{investment}', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'show'])->name('show');
        Route::get('/{investment}/edit', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'edit'])->name('edit');
        Route::put('/{investment}', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'update'])->name('update');
        Route::delete('/{investment}', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'destroy'])->name('destroy');
        Route::get('/export', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'export'])->name('export');
        Route::get('/stats', [App\Http\Controllers\Investor\InvestorPortfolioController::class, 'getPortfolioStats'])->name('stats');
    });

    // Investor Transactions
    Route::prefix('/{investor}/transactions')->name('transactions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'store'])->name('store');
        Route::get('/{transaction}', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'show'])->name('show');
        Route::get('/{transaction}/edit', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'edit'])->name('edit');
        Route::put('/{transaction}', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'update'])->name('update');
        Route::delete('/{transaction}', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'delete'])->name('delete');
        Route::post('/{transaction}/cancel', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'cancel'])->name('cancel');
        Route::get('/{transaction}/receipt', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'generateReceipt'])->name('receipt');
        Route::get('/export', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'export'])->name('export');
        Route::get('/stats', [App\Http\Controllers\Investor\InvestorTransactionController::class, 'getTransactionStats'])->name('stats');
    });

    // Investor ROI Analysis
    Route::prefix('/{investor}/roi')->name('roi.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestorRoiController::class, 'index'])->name('index');
        Route::get('/analysis', [App\Http\Controllers\Investor\InvestorRoiController::class, 'getROIAnalysis'])->name('analysis');
        Route::get('/export', [App\Http\Controllers\Investor\InvestorRoiController::class, 'exportROIReport'])->name('export');
        Route::post('/generate', [App\Http\Controllers\Investor\InvestorRoiController::class, 'generateDetailedReport'])->name('generate');
        Route::get('/charts', [App\Http\Controllers\Investor\InvestorRoiController::class, 'getROICharts'])->name('charts');
    });

    // Investor Risk Assessment
    Route::prefix('/{investor}/risk')->name('risk.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestorRiskController::class, 'index'])->name('index');
        Route::post('/assess', [App\Http\Controllers\Investor\InvestorRiskController::class, 'performRiskAssessment'])->name('assess');
        Route::get('/analysis', [App\Http\Controllers\Investor\InvestorRiskController::class, 'getRiskAnalysis'])->name('analysis');
        Route::get('/report', [App\Http\Controllers\Investor\InvestorRiskController::class, 'generateRiskReport'])->name('report');
        Route::get('/export', [App\Http\Controllers\Investor\InvestorRiskController::class, 'exportRiskAssessment'])->name('export');
    });

    // Investment Opportunities
    Route::prefix('/opportunities')->name('opportunities.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'store'])->name('store');
        Route::get('/{opportunity}', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'show'])->name('show');
        Route::get('/{opportunity}/edit', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'edit'])->name('edit');
        Route::put('/{opportunity}', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'update'])->name('update');
        Route::delete('/{opportunity}', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'delete'])->name('delete');
        Route::get('/recommended', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'getRecommendedOpportunities'])->name('recommended');
        Route::post('/{opportunity}/invest', [App\Http\Controllers\Investor\InvestmentOpportunityController::class, 'invest'])->name('invest');
    });

    // Investment Funds
    Route::prefix('/funds')->name('funds.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestmentFundController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\InvestmentFundController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\InvestmentFundController::class, 'store'])->name('store');
        Route::get('/{fund}', [App\Http\Controllers\Investor\InvestmentFundController::class, 'show'])->name('show');
        Route::get('/{fund}/edit', [App\Http\Controllers\Investor\InvestmentFundController::class, 'edit'])->name('edit');
        Route::put('/{fund}', [App\Http\Controllers\Investor\InvestmentFundController::class, 'update'])->name('update');
        Route::delete('/{fund}', [App\Http\Controllers\Investor\InvestmentFundController::class, 'delete'])->name('delete');
        Route::post('/{fund}/invest', [App\Http\Controllers\Investor\InvestmentFundController::class, 'invest'])->name('invest');
        Route::get('/{fund}/performance', [App\Http\Controllers\Investor\InvestmentFundController::class, 'getFundPerformance'])->name('performance');
        Route::get('/export', [App\Http\Controllers\Investor\InvestmentFundController::class, 'export'])->name('export');
    });

    // Investment Crowdfunding
    Route::prefix('/crowdfunding')->name('crowdfunding.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'store'])->name('store');
        Route::get('/{campaign}', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'show'])->name('show');
        Route::post('/{campaign}/invest', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'invest'])->name('invest');
        Route::get('/{campaign}/progress', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'getCampaignProgress'])->name('progress');
        Route::get('/export', [App\Http\Controllers\Investor\InvestmentCrowdfundingController::class, 'export'])->name('export');
    });

    // DeFi Loans
    Route::prefix('/defi/loans')->name('defi.loans.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\DefiLoanController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\DefiLoanController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\DefiLoanController::class, 'store'])->name('store');
        Route::get('/{loan}', [App\Http\Controllers\Investor\DefiLoanController::class, 'show'])->name('show');
        Route::put('/{loan}/approve', [App\Http\Controllers\Investor\DefiLoanController::class, 'approveLoan'])->name('approve');
        Route::put('/{loan}/reject', [App\Http\Controllers\Investor\DefiLoanController::class, 'rejectLoan'])->name('reject');
        Route::post('/{loan}/repay', [App\Http\Controllers\Investor\DefiLoanController::class, 'repayLoan'])->name('repay');
        Route::get('/{loan}/schedule', [App\Http\Controllers\Investor\DefiLoanController::class, 'getRepaymentSchedule'])->name('schedule');
        Route::get('/export', [App\Http\Controllers\Investor\DefiLoanController::class, 'export'])->name('export');
    });

    // DeFi Staking
    Route::prefix('/defi/staking')->name('defi.staking.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\DefiStakingController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Investor\DefiStakingController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Investor\DefiStakingController::class, 'store'])->name('store');
        Route::get('/{stake}', [App\Http\Controllers\Investor\DefiStakingController::class, 'show'])->name('show');
        Route::post('/{stake}/unstake', [App\Http\Controllers\Investor\DefiStakingController::class, 'unstake'])->name('unstake');
        Route::get('/{stake}/rewards', [App\Http\Controllers\Investor\DefiStakingController::class, 'getStakingRewards'])->name('rewards');
        Route::get('/stats', [App\Http\Controllers\Investor\DefiStakingController::class, 'getStakingStats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\Investor\DefiStakingController::class, 'export'])->name('export');
    });

    // Investor Profile
    Route::prefix('/{investor}/profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\Investor\InvestorProfileController::class, 'index'])->name('index');
        Route::get('/edit', [App\Http\Controllers\Investor\InvestorProfileController::class, 'edit'])->name('edit');
        Route::put('/', [App\Http\Controllers\Investor\InvestorProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [App\Http\Controllers\Investor\InvestorProfileController::class, 'updateAvatar'])->name('avatar');
        Route::post('/preferences', [App\Http\Controllers\Investor\InvestorProfileController::class, 'updatePreferences'])->name('preferences');
        Route::get('/settings', [App\Http\Controllers\Investor\InvestorProfileController::class, 'getSettings'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\Investor\InvestorProfileController::class, 'updateSettings'])->name('settings.update');
    });
});

// Subscriptions Management Routes
Route::prefix('subscriptions')->name('subscriptions.')->middleware(['auth'])->group(function () {
    // Subscription Management
    Route::get('/', [App\Http\Controllers\Subscription\SubscriptionController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Subscription\SubscriptionController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Subscription\SubscriptionController::class, 'store'])->name('store');
    Route::get('/{subscription}', [App\Http\Controllers\Subscription\SubscriptionController::class, 'show'])->name('show');
    Route::get('/{subscription}/edit', [App\Http\Controllers\Subscription\SubscriptionController::class, 'edit'])->name('edit');
    Route::put('/{subscription}', [App\Http\Controllers\Subscription\SubscriptionController::class, 'update'])->name('update');
    Route::delete('/{subscription}', [App\Http\Controllers\Subscription\SubscriptionController::class, 'destroy'])->name('destroy');
    Route::get('/{subscription}/payment', [App\Http\Controllers\Subscription\SubscriptionController::class, 'payment'])->name('payment');
    Route::post('/{subscription}/process-payment', [App\Http\Controllers\Subscription\SubscriptionController::class, 'processPayment'])->name('process-payment');
    Route::post('/{subscription}/renew', [App\Http\Controllers\Subscription\SubscriptionController::class, 'renew'])->name('renew');
    Route::get('/{subscription}/usage', [App\Http\Controllers\Subscription\SubscriptionController::class, 'usage'])->name('usage');
    Route::get('/{subscription}/invoices', [App\Http\Controllers\Subscription\SubscriptionController::class, 'invoices'])->name('invoices');
    Route::get('/stats', [App\Http\Controllers\Subscription\SubscriptionController::class, 'getSubscriptionStats'])->name('stats');

    // Subscription Plans
    Route::prefix('/plans')->name('plans.')->group(function () {
        Route::get('/', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'store'])->name('store');
        Route::get('/{plan}', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'show'])->name('show');
        Route::get('/{plan}/edit', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'edit'])->name('edit');
        Route::put('/{plan}', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'update'])->name('update');
        Route::delete('/{plan}', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'destroy'])->name('destroy');
        Route::post('/{plan}/duplicate', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'duplicate'])->name('duplicate');
        Route::post('/{plan}/toggle-status', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/stats', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'getPlanStats'])->name('stats');
        Route::get('/compare', [App\Http\Controllers\Subscription\SubscriptionPlanController::class, 'comparePlans'])->name('compare');
    });

    // Subscription Features
    Route::prefix('/features')->name('features.')->group(function () {
        Route::get('/', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'store'])->name('store');
        Route::get('/{feature}', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'show'])->name('show');
        Route::get('/{feature}/edit', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'edit'])->name('edit');
        Route::put('/{feature}', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'update'])->name('update');
        Route::delete('/{feature}', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'destroy'])->name('destroy');
        Route::post('/{feature}/toggle-status', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/by-category', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'getFeaturesByCategory'])->name('by-category');
        Route::get('/search', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'search'])->name('search');
        Route::post('/bulk-update', [App\Http\Controllers\Subscription\SubscriptionFeatureController::class, 'bulkUpdate'])->name('bulk-update');
    });

    // Subscription Upgrades
    Route::prefix('/upgrades')->name('upgrades.')->group(function () {
        Route::get('/', [App\Http\Controllers\Subscription\SubscriptionUpgradeController::class, 'index'])->name('index');
        Route::get('/{subscription}/create', [App\Http\Controllers\Subscription\SubscriptionUpgradeController::class, 'create'])->name('create');
        Route::post('/{subscription}', [App\Http\Controllers\Subscription\SubscriptionUpgradeController::class, 'store'])->name('store');
        Route::get('/{upgrade}', [App\Http\Controllers\Subscription\SubscriptionUpgradeController::class, 'show'])->name('show');
        Route::post('/{upgrade}/confirm', [App\Http\Controllers\Subscription\SubscriptionUpgradeController::class, 'confirmUpgrade'])->name('confirm');
        Route::post('/{upgrade}/cancel', [App\Http\Controllers\Subscription\SubscriptionUpgradeController::class, 'cancelUpgrade'])->name('cancel');
        Route::get('/{subscription}/options', [App\Http\Controllers\Subscription\SubscriptionUpgradeController::class, 'getUpgradeOptions'])->name('options');
        Route::get('/history', [App\Http\Controllers\Subscription\SubscriptionUpgradeController::class, 'getUpgradeHistory'])->name('history');
    });

    // Subscription Cancellations
    Route::prefix('/cancellations')->name('cancellations.')->group(function () {
        Route::get('/', [App\Http\Controllers\Subscription\SubscriptionCancellationController::class, 'index'])->name('index');
        Route::get('/{subscription}/create', [App\Http\Controllers\Subscription\SubscriptionCancellationController::class, 'create'])->name('create');
        Route::post('/{subscription}', [App\Http\Controllers\Subscription\SubscriptionCancellationController::class, 'store'])->name('store');
        Route::get('/{cancellation}', [App\Http\Controllers\Subscription\SubscriptionCancellationController::class, 'show'])->name('show');
        Route::post('/{cancellation}/refund', [App\Http\Controllers\Subscription\SubscriptionCancellationController::class, 'processRefund'])->name('refund');
        Route::post('/{cancellation}/reactivate', [App\Http\Controllers\Subscription\SubscriptionCancellationController::class, 'reactivate'])->name('reactivate');
        Route::get('/stats', [App\Http\Controllers\Subscription\SubscriptionCancellationController::class, 'getCancellationStats'])->name('stats');
    });

    // Subscription Renewals
    Route::prefix('/renewals')->name('renewals.')->group(function () {
        Route::get('/', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'index'])->name('index');
        Route::get('/{subscription}/create', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'create'])->name('create');
        Route::post('/{subscription}', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'store'])->name('store');
        Route::get('/{renewal}', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'show'])->name('show');
        Route::post('/{renewal}/process', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'processRenewal'])->name('process');
        Route::post('/{renewal}/cancel', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'cancelRenewal'])->name('cancel');
        Route::post('/{subscription}/enable-auto', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'enableAutoRenewal'])->name('enable-auto');
        Route::post('/{subscription}/disable-auto', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'disableAutoRenewal'])->name('disable-auto');
        Route::get('/upcoming', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'getUpcomingRenewals'])->name('upcoming');
        Route::post('/process-auto', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'processAutoRenewals'])->name('process-auto');
        Route::get('/stats', [App\Http\Controllers\Subscription\SubscriptionRenewalController::class, 'getRenewalStats'])->name('stats');
    });

    // Subscription Usage
    Route::prefix('/usage')->name('usage.')->group(function () {
        Route::get('/{subscription}', [App\Http\Controllers\Subscription\SubscriptionUsageController::class, 'index'])->name('index');
        Route::get('/{usage}', [App\Http\Controllers\Subscription\SubscriptionUsageController::class, 'show'])->name('show');
        Route::post('/{subscription}/track', [App\Http\Controllers\Subscription\SubscriptionUsageController::class, 'trackUsage'])->name('track');
        Route::get('/{subscription}/report', [App\Http\Controllers\Subscription\SubscriptionUsageController::class, 'getUsageReport'])->name('report');
        Route::get('/{subscription}/limits', [App\Http\Controllers\Subscription\SubscriptionUsageController::class, 'getUsageLimits'])->name('limits');
        Route::post('/{subscription}/reset', [App\Http\Controllers\Subscription\SubscriptionUsageController::class, 'resetUsage'])->name('reset');
        Route::get('/{subscription}/export', [App\Http\Controllers\Subscription\SubscriptionUsageController::class, 'exportUsage'])->name('export');
    });

    // Subscription Invoices
    Route::prefix('/invoices')->name('invoices.')->group(function () {
        Route::get('/', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'index'])->name('index');
        Route::get('/{invoice}', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'show'])->name('show');
        Route::get('/{subscription}/create', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'create'])->name('create');
        Route::post('/{subscription}', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}/edit', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'edit'])->name('edit');
        Route::put('/{invoice}', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'update'])->name('update');
        Route::delete('/{invoice}', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'destroy'])->name('destroy');
        Route::post('/{invoice}/pay', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'pay'])->name('pay');
        Route::get('/{invoice}/download', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'download'])->name('download');
        Route::post('/{invoice}/send-email', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'sendEmail'])->name('send-email');
        Route::post('/{invoice}/mark-paid', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'markAsPaid'])->name('mark-paid');
        Route::post('/{invoice}/void', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'void'])->name('void');
        Route::get('/stats', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'getInvoiceStats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\Subscription\SubscriptionInvoiceController::class, 'exportInvoices'])->name('export');
    });

    // Billing Management
    Route::prefix('/billing')->name('billing.')->group(function () {
        Route::get('/', [App\Http\Controllers\Subscription\BillingController::class, 'index'])->name('index');
        Route::get('/payment-methods', [App\Http\Controllers\Subscription\BillingController::class, 'paymentMethods'])->name('payment-methods');
        Route::post('/payment-methods', [App\Http\Controllers\Subscription\BillingController::class, 'addPaymentMethod'])->name('add-payment-method');
        Route::delete('/payment-methods/{method}', [App\Http\Controllers\Subscription\BillingController::class, 'removePaymentMethod'])->name('remove-payment-method');
        Route::post('/payment-methods/{method}/set-default', [App\Http\Controllers\Subscription\BillingController::class, 'setDefaultPaymentMethod'])->name('set-default-payment-method');
        Route::get('/history', [App\Http\Controllers\Subscription\BillingController::class, 'billingHistory'])->name('history');
        Route::get('/upcoming', [App\Http\Controllers\Subscription\BillingController::class, 'upcomingInvoices'])->name('upcoming');
        Route::get('/settings', [App\Http\Controllers\Subscription\BillingController::class, 'billingSettings'])->name('settings');
        Route::put('/settings', [App\Http\Controllers\Subscription\BillingController::class, 'updateBillingSettings'])->name('update-settings');
        Route::get('/invoices/{invoice}/download', [App\Http\Controllers\Subscription\BillingController::class, 'downloadInvoice'])->name('download-invoice');
        Route::get('/payments', [App\Http\Controllers\Subscription\BillingController::class, 'paymentHistory'])->name('payments');
        Route::get('/tax', [App\Http\Controllers\Subscription\BillingController::class, 'taxInfo'])->name('tax');
        Route::put('/tax', [App\Http\Controllers\Subscription\BillingController::class, 'updateTaxInfo'])->name('update-tax');
        Route::get('/export', [App\Http\Controllers\Subscription\BillingController::class, 'exportBillingData'])->name('export');
    });
});

// Messaging System Routes
Route::middleware(['auth'])->prefix('messages')->name('messages.')->group(function () {
    // Messages & Conversations
    Route::get('/', [App\Http\Controllers\MessageController::class, 'index'])->name('inbox');
    Route::get('/conversation/{id}', [App\Http\Controllers\MessageController::class, 'show'])->name('conversation');
    Route::post('/send', [App\Http\Controllers\MessageController::class, 'send'])->name('send');
    Route::post('/upload', [App\Http\Controllers\MessageController::class, 'upload'])->name('upload');
    Route::post('/conversations', [App\Http\Controllers\MessageController::class, 'createConversation'])->name('create-conversation');
    Route::delete('/conversation/{id}', [App\Http\Controllers\MessageController::class, 'deleteConversation'])->name('delete-conversation');

    // Chat System
    Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat');
    Route::get('/chat/room/{id}', [App\Http\Controllers\ChatController::class, 'showRoom'])->name('chat.room');
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/rooms', [App\Http\Controllers\ChatRoomController::class, 'create'])->name('chat.create-room');
    Route::post('/chat/room/{id}/join', [App\Http\Controllers\ChatController::class, 'joinRoom'])->name('chat.join-room');
    Route::post('/chat/room/{id}/leave', [App\Http\Controllers\ChatController::class, 'leaveRoom'])->name('chat.leave-room');

    // Appointments
    Route::get('/appointments', [App\Http\Controllers\AppointmentController::class, 'index'])->name('appointments');
    Route::get('/appointments/create', [App\Http\Controllers\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [App\Http\Controllers\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{id}/edit', [App\Http\Controllers\AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{id}', [App\Http\Controllers\AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{id}', [App\Http\Controllers\AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::post('/appointments/{id}/confirm', [App\Http\Controllers\AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('/appointments/{id}/cancel', [App\Http\Controllers\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{id}/reschedule', [App\Http\Controllers\AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::post('/appointments/{id}/reminder', [App\Http\Controllers\AppointmentController::class, 'sendReminder'])->name('appointments.reminder');

    // Calendar
    Route::get('/calendar', [App\Http\Controllers\AppointmentController::class, 'calendar'])->name('calendar');
    Route::get('/calendar/export', [App\Http\Controllers\AppointmentController::class, 'export'])->name('calendar.export');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/clear-all', [App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'delete'])->name('notifications.delete');
    Route::post('/notifications/settings', [App\Http\Controllers\NotificationController::class, 'updateSettings'])->name('notifications.settings');

    // Video & Voice Calls
    Route::get('/video-call/{conversation_id}', [App\Http\Controllers\VideoCallController::class, 'start'])->name('video-call');
    Route::get('/video-call/appointment/{appointment_id}', [App\Http\Controllers\VideoCallController::class, 'startAppointment'])->name('video-call.appointment');
    Route::post('/video-call/{id}/join', [App\Http\Controllers\VideoCallController::class, 'join'])->name('video-call.join');
    Route::post('/video-call/{id}/end', [App\Http\Controllers\VideoCallController::class, 'end'])->name('video-call.end');

    Route::get('/voice-call/{conversation_id}', [App\Http\Controllers\VoiceCallController::class, 'start'])->name('voice-call');
    Route::get('/voice-call/appointment/{appointment_id}', [App\Http\Controllers\VoiceCallController::class, 'startAppointment'])->name('voice-call.appointment');
    Route::post('/voice-call/{id}/join', [App\Http\Controllers\VoiceCallController::class, 'join'])->name('voice-call.join');
    Route::post('/voice-call/{id}/end', [App\Http\Controllers\VoiceCallController::class, 'end'])->name('voice-call.end');

    // Email & SMS
    Route::post('/email/send', [App\Http\Controllers\EmailController::class, 'send'])->name('email.send');
    Route::post('/sms/send', [App\Http\Controllers\SmsController::class, 'send'])->name('sms.send');

    // WhatsApp & Telegram
    Route::post('/whatsapp/send', [App\Http\Controllers\WhatsappController::class, 'send'])->name('whatsapp.send');
    Route::post('/telegram/webhook', [App\Http\Controllers\TelegramBotController::class, 'webhook'])->name('telegram.webhook');

    // Auctions System Routes
    Route::prefix('auctions')->group(function () {
        // Auction Management
        Route::get('/', [App\Http\Controllers\AuctionController::class, 'index'])->name('auctions.index');
        Route::get('/create', [App\Http\Controllers\AuctionController::class, 'create'])->name('auctions.create');
        Route::post('/', [App\Http\Controllers\AuctionController::class, 'store'])->name('auctions.store');
        Route::get('/{id}', [App\Http\Controllers\AuctionController::class, 'show'])->name('auctions.show');
        Route::get('/{id}/edit', [App\Http\Controllers\AuctionController::class, 'edit'])->name('auctions.edit');
        Route::put('/{id}', [App\Http\Controllers\AuctionController::class, 'update'])->name('auctions.update');
        Route::post('/{id}/start', [App\Http\Controllers\AuctionController::class, 'start'])->name('auctions.start');
        Route::post('/{id}/end', [App\Http\Controllers\AuctionController::class, 'end'])->name('auctions.end');
        Route::post('/{id}/cancel', [App\Http\Controllers\AuctionController::class, 'cancel'])->name('auctions.cancel');
        Route::get('/my-auctions', [App\Http\Controllers\AuctionController::class, 'myAuctions'])->name('auctions.my');

        // Bidding
        Route::post('/{id}/bid', [App\Http\Controllers\AuctionBidController::class, 'placeBid'])->name('auctions.bid');
        Route::post('/bids/{id}/retract', [App\Http\Controllers\AuctionBidController::class, 'retractBid'])->name('auctions.bids.retract');
        Route::get('/my-bids', [App\Http\Controllers\AuctionBidController::class, 'myBids'])->name('auctions.my-bids');
        Route::get('/{id}/bid-history', [App\Http\Controllers\AuctionBidController::class, 'bidHistory'])->name('auctions.bid-history');
        Route::get('/{id}/highest-bid', [App\Http\Controllers\AuctionBidController::class, 'getHighestBid'])->name('auctions.highest-bid');
        Route::get('/{id}/bid-stats', [App\Http\Controllers\AuctionBidController::class, 'getBidStats'])->name('auctions.bid-stats');

        // Participants
        Route::post('/{id}/join', [App\Http\Controllers\AuctionParticipantController::class, 'join'])->name('auctions.join');
        Route::post('/{id}/leave', [App\Http\Controllers\AuctionParticipantController::class, 'leave'])->name('auctions.leave');
        Route::get('/{id}/participants', [App\Http\Controllers\AuctionParticipantController::class, 'index'])->name('auctions.participants');
        Route::post('/{auctionId}/participants/{participantId}/remove', [App\Http\Controllers\AuctionParticipantController::class, 'removeParticipant'])->name('auctions.participants.remove');
        Route::post('/{auctionId}/participants/{participantId}/approve', [App\Http\Controllers\AuctionParticipantController::class, 'approveParticipant'])->name('auctions.participants.approve');
        Route::post('/{auctionId}/participants/{participantId}/reject', [App\Http\Controllers\AuctionParticipantController::class, 'rejectParticipant'])->name('auctions.participants.reject');
        Route::get('/my-participations', [App\Http\Controllers\AuctionParticipantController::class, 'myParticipations'])->name('auctions.my-participations');
        Route::get('/{id}/participant-stats', [App\Http\Controllers\AuctionParticipantController::class, 'getParticipantStats'])->name('auctions.participant-stats');

        // Results
        Route::get('/results', [App\Http\Controllers\AuctionResultController::class, 'index'])->name('auctions.results');
        Route::get('/results/{id}', [App\Http\Controllers\AuctionResultController::class, 'show'])->name('auctions.results.show');
        Route::post('/{id}/result', [App\Http\Controllers\AuctionResultController::class, 'create'])->name('auctions.result.create');
        Route::post('/results/{id}/confirm-winner', [App\Http\Controllers\AuctionResultController::class, 'confirmWinner'])->name('auctions.results.confirm-winner');
        Route::post('/results/{id}/reject-winner', [App\Http\Controllers\AuctionResultController::class, 'rejectWinner'])->name('auctions.results.reject-winner');
        Route::get('/my-results', [App\Http\Controllers\AuctionResultController::class, 'myResults'])->name('auctions.my-results');
        Route::get('/results/{id}/download', [App\Http\Controllers\AuctionResultController::class, 'downloadReport'])->name('auctions.results.download');
        Route::get('/results/stats', [App\Http\Controllers\AuctionResultController::class, 'getStats'])->name('auctions.results.stats');
    });

    // Offers Routes
    Route::prefix('offers')->group(function () {
        Route::get('/', [App\Http\Controllers\OfferController::class, 'index'])->name('offers.index');
        Route::get('/create/{propertyId}', [App\Http\Controllers\OfferController::class, 'create'])->name('offers.create');
        Route::post('/', [App\Http\Controllers\OfferController::class, 'store'])->name('offers.store');
        Route::get('/{id}', [App\Http\Controllers\OfferController::class, 'show'])->name('offers.show');
        Route::post('/{id}/accept', [App\Http\Controllers\OfferController::class, 'accept'])->name('offers.accept');
        Route::post('/{id}/reject', [App\Http\Controllers\OfferController::class, 'reject'])->name('offers.reject');
        Route::post('/{id}/withdraw', [App\Http\Controllers\OfferController::class, 'withdraw'])->name('offers.withdraw');
        Route::get('/{id}/negotiation', [App\Http\Controllers\OfferController::class, 'negotiation'])->name('offers.negotiation');
        Route::put('/{id}/status', [App\Http\Controllers\OfferController::class, 'updateStatus'])->name('offers.update-status');
        Route::get('/stats', [App\Http\Controllers\OfferController::class, 'getStats'])->name('offers.stats');

        // Counter Offers
        Route::post('/{offerId}/counter-offer', [App\Http\Controllers\CounterOfferController::class, 'store'])->name('offers.counter-offer');
        Route::post('/counter-offers/{id}/accept', [App\Http\Controllers\CounterOfferController::class, 'accept'])->name('counter-offers.accept');
        Route::post('/counter-offers/{id}/reject', [App\Http\Controllers\CounterOfferController::class, 'reject'])->name('counter-offers.reject');
        Route::post('/counter-offers/{id}/withdraw', [App\Http\Controllers\CounterOfferController::class, 'withdraw'])->name('counter-offers.withdraw');
        Route::get('/{offerId}/counter-offers', [App\Http\Controllers\CounterOfferController::class, 'index'])->name('offers.counter-offers');
        Route::get('/counter-offers/{id}', [App\Http\Controllers\CounterOfferController::class, 'show'])->name('counter-offers.show');
        Route::get('/{offerId}/counter-offer-history', [App\Http\Controllers\CounterOfferController::class, 'getHistory'])->name('offers.counter-offer-history');
        Route::get('/counter-offers/stats', [App\Http\Controllers\CounterOfferController::class, 'getStats'])->name('counter-offers.stats');
    });

    // Negotiations Routes
    Route::prefix('negotiations')->group(function () {
        Route::get('/', [App\Http\Controllers\NegotiationController::class, 'index'])->name('negotiations.index');
        Route::post('/offers/{offerId}/start', [App\Http\Controllers\NegotiationController::class, 'start'])->name('negotiations.start');
        Route::get('/{id}', [App\Http\Controllers\NegotiationController::class, 'show'])->name('negotiations.show');
        Route::post('/{id}/message', [App\Http\Controllers\NegotiationController::class, 'sendMessage'])->name('negotiations.message');
        Route::post('/{id}/propose-terms', [App\Http\Controllers\NegotiationController::class, 'proposeTerms'])->name('negotiations.propose-terms');
        Route::post('/{id}/proposals/{proposalId}/accept', [App\Http\Controllers\NegotiationController::class, 'acceptProposal'])->name('negotiations.accept-proposal');
        Route::post('/{id}/proposals/{proposalId}/reject', [App\Http\Controllers\NegotiationController::class, 'rejectProposal'])->name('negotiations.reject-proposal');
        Route::post('/{id}/pause', [App\Http\Controllers\NegotiationController::class, 'pause'])->name('negotiations.pause');
        Route::post('/{id}/resume', [App\Http\Controllers\NegotiationController::class, 'resume'])->name('negotiations.resume');
        Route::post('/{id}/terminate', [App\Http\Controllers\NegotiationController::class, 'terminate'])->name('negotiations.terminate');
    });

    // Contracts Routes
    Route::prefix('contracts')->group(function () {
        Route::get('/', [App\Http\Controllers\ContractController::class, 'index'])->name('contracts.index');
        Route::post('/', [App\Http\Controllers\ContractController::class, 'create'])->name('contracts.create');
        Route::get('/{id}', [App\Http\Controllers\ContractController::class, 'show'])->name('contracts.show');
        Route::put('/{id}', [App\Http\Controllers\ContractController::class, 'update'])->name('contracts.update');
        Route::post('/{id}/sign', [App\Http\Controllers\ContractController::class, 'sign'])->name('contracts.sign');
        Route::post('/{id}/amend', [App\Http\Controllers\ContractController::class, 'amend'])->name('contracts.amend');
        Route::post('/{id}/amendments/{amendmentId}/accept', [App\Http\Controllers\ContractController::class, 'acceptAmendment'])->name('contracts.accept-amendment');
        Route::post('/{id}/amendments/{amendmentId}/reject', [App\Http\Controllers\ContractController::class, 'rejectAmendment'])->name('contracts.reject-amendment');
        Route::post('/{id}/terminate', [App\Http\Controllers\ContractController::class, 'terminate'])->name('contracts.terminate');
        Route::post('/{id}/complete', [App\Http\Controllers\ContractController::class, 'complete'])->name('contracts.complete');
        Route::get('/{id}/download', [App\Http\Controllers\ContractController::class, 'download'])->name('contracts.download');

        // Signatures
        Route::post('/{contractId}/signatures', [App\Http\Controllers\ContractSignatureController::class, 'store'])->name('contracts.signatures.store');
        Route::get('/signatures/{id}/verify', [App\Http\Controllers\ContractSignatureController::class, 'verify'])->name('contracts.signatures.verify');
        Route::get('/signatures/{id}/download', [App\Http\Controllers\ContractSignatureController::class, 'download'])->name('contracts.signatures.download');
        Route::post('/signatures/{id}/revoke', [App\Http\Controllers\ContractSignatureController::class, 'revoke'])->name('contracts.signatures.revoke');
        Route::get('/{contractId}/signatures/history', [App\Http\Controllers\ContractSignatureController::class, 'history'])->name('contracts.signatures.history');
        Route::get('/signatures/{id}/audit-trail', [App\Http\Controllers\ContractSignatureController::class, 'auditTrail'])->name('contracts.signatures.audit-trail');
    });
});

// Reviews System Routes (Module 13)
Route::middleware('auth')->prefix('reviews')->group(function () {
    Route::get('/', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/create/{type}/{id}', [App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/{review}', [App\Http\Controllers\ReviewController::class, 'show'])->name('reviews.show');
    Route::get('/{review}/edit', [App\Http\Controllers\ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/{review}/helpful', [App\Http\Controllers\ReviewController::class, 'helpful'])->name('reviews.helpful');
    Route::post('/{review}/not-helpful', [App\Http\Controllers\ReviewController::class, 'notHelpful'])->name('reviews.notHelpful');
    Route::post('/{review}/report', [App\Http\Controllers\ReviewController::class, 'report'])->name('reviews.report');
    Route::post('/{review}/approve', [App\Http\Controllers\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/{review}/reject', [App\Http\Controllers\ReviewController::class, 'reject'])->name('reviews.reject');
    Route::get('/search', [App\Http\Controllers\ReviewController::class, 'search'])->name('reviews.search');
    Route::get('/my', [App\Http\Controllers\ReviewController::class, 'myReviews'])->name('reviews.my');
});

// Testimonials Routes
Route::middleware('auth')->prefix('testimonials')->group(function () {
    Route::get('/', [App\Http\Controllers\TestimonialController::class, 'index'])->name('testimonials.index');
    Route::get('/create', [App\Http\Controllers\TestimonialController::class, 'create'])->name('testimonials.create');
    Route::post('/', [App\Http\Controllers\TestimonialController::class, 'store'])->name('testimonials.store');
    Route::get('/{testimonial}', [App\Http\Controllers\TestimonialController::class, 'show'])->name('testimonials.show');
    Route::get('/{testimonial}/edit', [App\Http\Controllers\TestimonialController::class, 'edit'])->name('testimonials.edit');
    Route::put('/{testimonial}', [App\Http\Controllers\TestimonialController::class, 'update'])->name('testimonials.update');
    Route::delete('/{testimonial}', [App\Http\Controllers\TestimonialController::class, 'destroy'])->name('testimonials.destroy');
    Route::post('/{testimonial}/approve', [App\Http\Controllers\TestimonialController::class, 'approve'])->name('testimonials.approve');
    Route::post('/{testimonial}/reject', [App\Http\Controllers\TestimonialController::class, 'reject'])->name('testimonials.reject');
    Route::post('/{testimonial}/feature', [App\Http\Controllers\TestimonialController::class, 'feature'])->name('testimonials.feature');
    Route::get('/my', [App\Http\Controllers\TestimonialController::class, 'myTestimonials'])->name('testimonials.my');
    Route::get('/search', [App\Http\Controllers\TestimonialController::class, 'search'])->name('testimonials.search');
    Route::get('/featured', [App\Http\Controllers\TestimonialController::class, 'getFeatured'])->name('testimonials.featured');
    Route::get('/by-type/{type}', [App\Http\Controllers\TestimonialController::class, 'getByProjectType'])->name('testimonials.by-type');
});

// Complaints Routes
Route::middleware('auth')->prefix('complaints')->group(function () {
    Route::get('/', [App\Http\Controllers\ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/create', [App\Http\Controllers\ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/', [App\Http\Controllers\ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/{complaint}', [App\Http\Controllers\ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/{complaint}/edit', [App\Http\Controllers\ComplaintController::class, 'edit'])->name('complaints.edit');
    Route::put('/{complaint}', [App\Http\Controllers\ComplaintController::class, 'update'])->name('complaints.update');
    Route::delete('/{complaint}', [App\Http\Controllers\ComplaintController::class, 'destroy'])->name('complaints.destroy');
    Route::post('/{complaint}/assign', [App\Http\Controllers\ComplaintController::class, 'assign'])->name('complaints.assign');
    Route::post('/{complaint}/resolve', [App\Http\Controllers\ComplaintController::class, 'resolve'])->name('complaints.resolve');
    Route::post('/{complaint}/escalate', [App\Http\Controllers\ComplaintController::class, 'escalate'])->name('complaints.escalate');
    Route::post('/{complaint}/close', [App\Http\Controllers\ComplaintController::class, 'close'])->name('complaints.close');
    Route::get('/my', [App\Http\Controllers\ComplaintController::class, 'myComplaints'])->name('complaints.my');
    Route::get('/search', [App\Http\Controllers\ComplaintController::class, 'search'])->name('complaints.search');
});

// Surveys Routes
Route::middleware('auth')->prefix('surveys')->group(function () {
    Route::get('/', [App\Http\Controllers\SurveyController::class, 'index'])->name('surveys.index');
    Route::get('/create', [App\Http\Controllers\SurveyController::class, 'create'])->name('surveys.create');
    Route::post('/', [App\Http\Controllers\SurveyController::class, 'store'])->name('surveys.store');
    Route::get('/{survey}', [App\Http\Controllers\SurveyController::class, 'show'])->name('surveys.show');
    Route::get('/{survey}/edit', [App\Http\Controllers\SurveyController::class, 'edit'])->name('surveys.edit');
    Route::put('/{survey}', [App\Http\Controllers\SurveyController::class, 'update'])->name('surveys.update');
    Route::delete('/{survey}', [App\Http\Controllers\SurveyController::class, 'destroy'])->name('surveys.destroy');
    Route::post('/{survey}/publish', [App\Http\Controllers\SurveyController::class, 'publish'])->name('surveys.publish');
    Route::post('/{survey}/close', [App\Http\Controllers\SurveyController::class, 'close'])->name('surveys.close');
    Route::get('/{survey}/participate', [App\Http\Controllers\SurveyController::class, 'participate'])->name('surveys.participate');
    Route::post('/{survey}/complete', [App\Http\Controllers\SurveyController::class, 'complete'])->name('surveys.complete');
    Route::get('/{survey}/results', [App\Http\Controllers\SurveyController::class, 'results'])->name('surveys.results');
    Route::get('/my', [App\Http\Controllers\SurveyController::class, 'mySurveys'])->name('surveys.my');
    Route::get('/search', [App\Http\Controllers\SurveyController::class, 'search'])->name('surveys.search');
});

// Advertising System Routes (Module 14)
Route::prefix('ads')->name('ads.')->middleware(['auth'])->group(function () {
    // Advertisement Routes
    Route::get('/', [App\Http\Controllers\AdvertisementController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\AdvertisementController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\AdvertisementController::class, 'store'])->name('store');
    Route::get('/{advertisement}', [App\Http\Controllers\AdvertisementController::class, 'show'])->name('show');
    Route::get('/{advertisement}/edit', [App\Http\Controllers\AdvertisementController::class, 'edit'])->name('edit');
    Route::put('/{advertisement}', [App\Http\Controllers\AdvertisementController::class, 'update'])->name('update');
    Route::delete('/{advertisement}', [App\Http\Controllers\AdvertisementController::class, 'destroy'])->name('destroy');
    Route::post('/{advertisement}/pause', [App\Http\Controllers\AdvertisementController::class, 'pause'])->name('pause');
    Route::post('/{advertisement}/resume', [App\Http\Controllers\AdvertisementController::class, 'resume'])->name('resume');
    Route::post('/{advertisement}/duplicate', [App\Http\Controllers\AdvertisementController::class, 'duplicate'])->name('duplicate');
    Route::post('/{advertisement}/approve', [App\Http\Controllers\AdvertisementController::class, 'approve'])->name('approve');
    Route::post('/{advertisement}/reject', [App\Http\Controllers\AdvertisementController::class, 'reject'])->name('reject');
    Route::get('/{advertisement}/preview', [App\Http\Controllers\AdvertisementController::class, 'preview'])->name('preview');
    Route::post('/{advertisement}/track-click', [App\Http\Controllers\AdvertisementController::class, 'trackClick'])->name('track-click');
    Route::post('/{advertisement}/track-impression', [App\Http\Controllers\AdvertisementController::class, 'trackImpression'])->name('track-impression');

    // Campaign Routes
    Route::prefix('campaigns')->name('campaigns.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdCampaignController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdCampaignController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\AdCampaignController::class, 'store'])->name('store');
        Route::get('/{campaign}', [App\Http\Controllers\AdCampaignController::class, 'show'])->name('show');
        Route::get('/{campaign}/edit', [App\Http\Controllers\AdCampaignController::class, 'edit'])->name('edit');
        Route::put('/{campaign}', [App\Http\Controllers\AdCampaignController::class, 'update'])->name('update');
        Route::delete('/{campaign}', [App\Http\Controllers\AdCampaignController::class, 'destroy'])->name('destroy');
        Route::post('/{campaign}/launch', [App\Http\Controllers\AdCampaignController::class, 'launch'])->name('launch');
        Route::post('/{campaign}/pause', [App\Http\Controllers\AdCampaignController::class, 'pause'])->name('pause');
        Route::post('/{campaign}/resume', [App\Http\Controllers\AdCampaignController::class, 'resume'])->name('resume');
        Route::post('/{campaign}/duplicate', [App\Http\Controllers\AdCampaignController::class, 'duplicate'])->name('duplicate');
        Route::post('/{campaign}/complete', [App\Http\Controllers\AdCampaignController::class, 'complete'])->name('complete');
        Route::get('/{campaign}/analytics', [App\Http\Controllers\AdCampaignController::class, 'analytics'])->name('analytics');
        Route::get('/{campaign}/performance', [App\Http\Controllers\AdCampaignController::class, 'performance'])->name('performance');
    });

    // Placement Routes
    Route::prefix('placements')->name('placements.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdPlacementController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdPlacementController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\AdPlacementController::class, 'store'])->name('store');
        Route::get('/{placement}', [App\Http\Controllers\AdPlacementController::class, 'show'])->name('show');
        Route::get('/{placement}/edit', [App\Http\Controllers\AdPlacementController::class, 'edit'])->name('edit');
        Route::put('/{placement}', [App\Http\Controllers\AdPlacementController::class, 'update'])->name('update');
        Route::delete('/{placement}', [App\Http\Controllers\AdPlacementController::class, 'destroy'])->name('destroy');
        Route::post('/{placement}/activate', [App\Http\Controllers\AdPlacementController::class, 'activate'])->name('activate');
        Route::post('/{placement}/deactivate', [App\Http\Controllers\AdPlacementController::class, 'deactivate'])->name('deactivate');
        Route::get('/{placement}/eligible-ads', [App\Http\Controllers\AdPlacementController::class, 'getEligibleAds'])->name('eligible-ads');
        Route::get('/{placement}/analytics', [App\Http\Controllers\AdPlacementController::class, 'analytics'])->name('analytics');
        Route::post('/{placement}/update-pricing', [App\Http\Controllers\AdPlacementController::class, 'updatePricing'])->name('update-pricing');
    });

    // Budget Routes
    Route::prefix('budgets')->name('budgets.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdBudgetController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdBudgetController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\AdBudgetController::class, 'store'])->name('store');
        Route::get('/{budget}', [App\Http\Controllers\AdBudgetController::class, 'show'])->name('show');
        Route::get('/{budget}/edit', [App\Http\Controllers\AdBudgetController::class, 'edit'])->name('edit');
        Route::put('/{budget}', [App\Http\Controllers\AdBudgetController::class, 'update'])->name('update');
        Route::delete('/{budget}', [App\Http\Controllers\AdBudgetController::class, 'destroy'])->name('destroy');
        Route::post('/{budget}/pause', [App\Http\Controllers\AdBudgetController::class, 'pause'])->name('pause');
        Route::post('/{budget}/resume', [App\Http\Controllers\AdBudgetController::class, 'resume'])->name('resume');
        Route::post('/{budget}/add-funds', [App\Http\Controllers\AdBudgetController::class, 'addFunds'])->name('add-funds');
        Route::post('/{budget}/adjust-daily', [App\Http\Controllers\AdBudgetController::class, 'adjustDailyBudget'])->name('adjust-daily');
        Route::post('/{budget}/set-limit', [App\Http\Controllers\AdBudgetController::class, 'setSpendingLimit'])->name('set-limit');
        Route::get('/{budget}/report', [App\Http\Controllers\AdBudgetController::class, 'getSpendingReport'])->name('report');
        Route::get('/{budget}/optimization', [App\Http\Controllers\AdBudgetController::class, 'getOptimizationSuggestions'])->name('optimization');
    });

    // Targeting Routes
    Route::prefix('targeting')->name('targeting.')->group(function () {
        Route::get('/', [App\Http\Controllers\AdTargetingController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\AdTargetingController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\AdTargetingController::class, 'store'])->name('store');
        Route::get('/{targeting}', [App\Http\Controllers\AdTargetingController::class, 'show'])->name('show');
        Route::get('/{targeting}/edit', [App\Http\Controllers\AdTargetingController::class, 'edit'])->name('edit');
        Route::put('/{targeting}', [App\Http\Controllers\AdTargetingController::class, 'update'])->name('update');
        Route::delete('/{targeting}', [App\Http\Controllers\AdTargetingController::class, 'destroy'])->name('destroy');
        Route::post('/{targeting}/duplicate', [App\Http\Controllers\AdTargetingController::class, 'duplicate'])->name('duplicate');
        Route::get('/{targeting}/preview', [App\Http\Controllers\AdTargetingController::class, 'audiencePreview'])->name('preview');
        Route::get('/{targeting}/optimization', [App\Http\Controllers\AdTargetingController::class, 'getOptimizationSuggestions'])->name('optimization');
        Route::post('/{targeting}/apply-template', [App\Http\Controllers\AdTargetingController::class, 'applyTemplate'])->name('apply-template');
        Route::get('/templates', [App\Http\Controllers\AdTargetingController::class, 'getTemplates'])->name('templates');
    });

    // Analytics Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdAnalyticsController::class, 'dashboard'])->name('dashboard');
        Route::get('/overview', [App\Http\Controllers\AdAnalyticsController::class, 'platformOverview'])->name('overview');
        Route::get('/advertisers', [App\Http\Controllers\AdAnalyticsController::class, 'advertiserAnalytics'])->name('advertisers');
        Route::get('/placements', [App\Http\Controllers\AdAnalyticsController::class, 'placementAnalytics'])->name('placements');
        Route::get('/revenue', [App\Http\Controllers\AdAnalyticsController::class, 'revenueAnalytics'])->name('revenue');
        Route::get('/performance', [App\Http\Controllers\AdAnalyticsController::class, 'performanceAnalytics'])->name('performance');
        Route::get('/realtime', [App\Http\Controllers\AdAnalyticsController::class, 'realtimeAnalytics'])->name('realtime');
        Route::get('/comparative', [App\Http\Controllers\AdAnalyticsController::class, 'comparativeAnalytics'])->name('comparative');
        Route::get('/export/{type}', [App\Http\Controllers\AdAnalyticsController::class, 'exportData'])->name('export');
    });

    // Promoted Listings Routes
    Route::prefix('promoted-listings')->name('promoted-listings.')->group(function () {
        Route::get('/', [App\Http\Controllers\PromotedListingController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\PromotedListingController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\PromotedListingController::class, 'store'])->name('store');
        Route::get('/{promotedListing}', [App\Http\Controllers\PromotedListingController::class, 'show'])->name('show');
        Route::get('/{promotedListing}/edit', [App\Http\Controllers\PromotedListingController::class, 'edit'])->name('edit');
        Route::put('/{promotedListing}', [App\Http\Controllers\PromotedListingController::class, 'update'])->name('update');
        Route::delete('/{promotedListing}', [App\Http\Controllers\PromotedListingController::class, 'destroy'])->name('destroy');
        Route::post('/{promotedListing}/pause', [App\Http\Controllers\PromotedListingController::class, 'pause'])->name('pause');
        Route::post('/{promotedListing}/resume', [App\Http\Controllers\PromotedListingController::class, 'resume'])->name('resume');
        Route::post('/{promotedListing}/extend', [App\Http\Controllers\PromotedListingController::class, 'extend'])->name('extend');
        Route::post('/{promotedListing}/upgrade', [App\Http\Controllers\PromotedListingController::class, 'upgrade'])->name('upgrade');
        Route::get('/{promotedListing}/analytics', [App\Http\Controllers\PromotedListingController::class, 'analytics'])->name('analytics');
        Route::post('/{promotedListing}/track-view', [App\Http\Controllers\PromotedListingController::class, 'trackView'])->name('track-view');
        Route::post('/{promotedListing}/track-inquiry', [App\Http\Controllers\PromotedListingController::class, 'trackInquiry'])->name('track-inquiry');
    });

    // Banner Ads Routes
    Route::prefix('banner-ads')->name('banner-ads.')->group(function () {
        Route::get('/', [App\Http\Controllers\BannerAdController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\BannerAdController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\BannerAdController::class, 'store'])->name('store');
        Route::get('/{bannerAd}', [App\Http\Controllers\BannerAdController::class, 'show'])->name('show');
        Route::get('/{bannerAd}/edit', [App\Http\Controllers\BannerAdController::class, 'edit'])->name('edit');
        Route::put('/{bannerAd}', [App\Http\Controllers\BannerAdController::class, 'update'])->name('update');
        Route::delete('/{bannerAd}', [App\Http\Controllers\BannerAdController::class, 'destroy'])->name('destroy');
        Route::get('/{bannerAd}/preview', [App\Http\Controllers\BannerAdController::class, 'preview'])->name('preview');
        Route::get('/{bannerAd}/code', [App\Http\Controllers\BannerAdController::class, 'getBannerCode'])->name('get-code');
        Route::get('/{bannerAd}/click', [App\Http\Controllers\BannerAdController::class, 'trackClick'])->name('click');
        Route::post('/{bannerAd}/impression', [App\Http\Controllers\BannerAdController::class, 'trackImpression'])->name('impression');
        Route::get('/sizes', [App\Http\Controllers\BannerAdController::class, 'getBannerSizes'])->name('sizes');
    });

    // Video Ads Routes
    Route::prefix('video-ads')->name('video-ads.')->group(function () {
        Route::get('/', [App\Http\Controllers\VideoAdController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\VideoAdController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\VideoAdController::class, 'store'])->name('store');
        Route::get('/{videoAd}', [App\Http\Controllers\VideoAdController::class, 'show'])->name('show');
        Route::get('/{videoAd}/edit', [App\Http\Controllers\VideoAdController::class, 'edit'])->name('edit');
        Route::put('/{videoAd}', [App\Http\Controllers\VideoAdController::class, 'update'])->name('update');
        Route::delete('/{videoAd}', [App\Http\Controllers\VideoAdController::class, 'destroy'])->name('destroy');
        Route::get('/{videoAd}/preview', [App\Http\Controllers\VideoAdController::class, 'preview'])->name('preview');
        Route::get('/{videoAd}/code', [App\Http\Controllers\VideoAdController::class, 'getVideoCode'])->name('get-code');
        Route::post('/{videoAd}/track-play', [App\Http\Controllers\VideoAdController::class, 'trackVideoPlay'])->name('track-play');
        Route::post('/{videoAd}/track-completion', [App\Http\Controllers\VideoAdController::class, 'trackVideoCompletion'])->name('track-completion');
        Route::post('/{videoAd}/track-engagement', [App\Http\Controllers\VideoAdController::class, 'trackVideoEngagement'])->name('track-engagement');
    });

    // Public Routes (no auth required for tracking)
    Route::get('/featured-listings', [App\Http\Controllers\PromotedListingController::class, 'featuredListings'])->name('featured-listings');
    Route::get('/spotlight-listings', [App\Http\Controllers\PromotedListingController::class, 'spotlightListings'])->name('spotlight-listings');
});

// Tax System Routes
Route::middleware(['auth'])->prefix('taxes')->name('taxes.')->group(function () {
    // Main Tax Routes
    Route::get('/', [App\Http\Controllers\TaxController::class, 'index'])->name('index');
    Route::get('/analytics', [App\Http\Controllers\TaxController::class, 'analytics'])->name('analytics');
    Route::get('/list', [App\Http\Controllers\TaxController::class, 'list'])->name('list');
    Route::get('/{tax}', [App\Http\Controllers\TaxController::class, 'show'])->name('show');
    Route::get('/{tax}/report', [App\Http\Controllers\TaxController::class, 'generateReport'])->name('report');
    Route::get('/stats', [App\Http\Controllers\TaxController::class, 'getStats'])->name('stats');

    // Property Tax Routes
    Route::prefix('property')->name('property.')->group(function () {
        Route::get('/', [App\Http\Controllers\PropertyTaxController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\PropertyTaxController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\PropertyTaxController::class, 'store'])->name('store');
        Route::get('/{propertyTax}', [App\Http\Controllers\PropertyTaxController::class, 'show'])->name('show');
        Route::get('/{propertyTax}/edit', [App\Http\Controllers\PropertyTaxController::class, 'edit'])->name('edit');
        Route::put('/{propertyTax}', [App\Http\Controllers\PropertyTaxController::class, 'update'])->name('update');
        Route::delete('/{propertyTax}', [App\Http\Controllers\PropertyTaxController::class, 'destroy'])->name('destroy');
    });

    // Tax Calculator Routes
    Route::prefix('calculator')->name('calculator.')->group(function () {
        Route::get('/', [App\Http\Controllers\TaxCalculatorController::class, 'index'])->name('index');
        Route::post('/calculate', [App\Http\Controllers\TaxCalculatorController::class, 'calculate'])->name('calculate');
        Route::get('/property', [App\Http\Controllers\TaxCalculatorController::class, 'propertyTaxCalculator'])->name('property');
        Route::get('/capital-gains', [App\Http\Controllers\TaxCalculatorController::class, 'capitalGainsCalculator'])->name('capital-gains');
        Route::get('/vat', [App\Http\Controllers\TaxCalculatorController::class, 'vatCalculator'])->name('vat');
    });

    // Tax Filing Routes
    Route::prefix('filing')->name('filing.')->group(function () {
        Route::get('/', [App\Http\Controllers\TaxFilingController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\TaxFilingController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\TaxFilingController::class, 'store'])->name('store');
        Route::get('/{taxFiling}', [App\Http\Controllers\TaxFilingController::class, 'show'])->name('show');
        Route::get('/{taxFiling}/edit', [App\Http\Controllers\TaxFilingController::class, 'edit'])->name('edit');
        Route::put('/{taxFiling}', [App\Http\Controllers\TaxFilingController::class, 'update'])->name('update');
        Route::post('/{taxFiling}/submit', [App\Http\Controllers\TaxFilingController::class, 'submit'])->name('submit');
        Route::post('/{taxFiling}/approve', [App\Http\Controllers\TaxFilingController::class, 'approve'])->name('approve');
        Route::post('/{taxFiling}/reject', [App\Http\Controllers\TaxFilingController::class, 'reject'])->name('reject');
        Route::get('/{taxFiling}/attachment/{attachmentId}', [App\Http\Controllers\TaxFilingController::class, 'downloadAttachment'])->name('attachment.download');
    });

    // Tax Payment Routes
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [App\Http\Controllers\TaxPaymentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\TaxPaymentController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\TaxPaymentController::class, 'store'])->name('store');
        Route::get('/{taxPayment}', [App\Http\Controllers\TaxPaymentController::class, 'show'])->name('show');
        Route::get('/{taxPayment}/edit', [App\Http\Controllers\TaxPaymentController::class, 'edit'])->name('edit');
        Route::put('/{taxPayment}', [App\Http\Controllers\TaxPaymentController::class, 'update'])->name('update');
        Route::post('/{taxPayment}/process', [App\Http\Controllers\TaxPaymentController::class, 'process'])->name('process');
        Route::post('/{taxPayment}/complete', [App\Http\Controllers\TaxPaymentController::class, 'complete'])->name('complete');
        Route::post('/{taxPayment}/cancel', [App\Http\Controllers\TaxPaymentController::class, 'cancel'])->name('cancel');
        Route::get('/{taxPayment}/receipt', [App\Http\Controllers\TaxPaymentController::class, 'receipt'])->name('receipt');
        Route::get('/{taxPayment}/generate-receipt', [App\Http\Controllers\TaxPaymentController::class, 'generateReceipt'])->name('generate-receipt');
    });

    // Tax Exemption Routes
    Route::prefix('exemptions')->name('exemptions.')->group(function () {
        Route::get('/', [App\Http\Controllers\TaxExemptionController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\TaxExemptionController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\TaxExemptionController::class, 'store'])->name('store');
        Route::get('/{taxExemption}', [App\Http\Controllers\TaxExemptionController::class, 'show'])->name('show');
        Route::post('/{taxExemption}/approve', [App\Http\Controllers\TaxExemptionController::class, 'approve'])->name('approve');
        Route::post('/{taxExemption}/reject', [App\Http\Controllers\TaxExemptionController::class, 'reject'])->name('reject');
    });

    // Tax Assessment Routes
    Route::prefix('assessments')->name('assessments.')->group(function () {
        Route::get('/', [App\Http\Controllers\TaxAssessmentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\TaxAssessmentController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\TaxAssessmentController::class, 'store'])->name('store');
        Route::get('/{taxAssessment}', [App\Http\Controllers\TaxAssessmentController::class, 'show'])->name('show');
    });

    // Tax Document Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/', [App\Http\Controllers\TaxDocumentController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\TaxDocumentController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\TaxDocumentController::class, 'store'])->name('store');
        Route::get('/{taxDocument}', [App\Http\Controllers\TaxDocumentController::class, 'show'])->name('show');
        Route::get('/{taxDocument}/download', [App\Http\Controllers\TaxDocumentController::class, 'download'])->name('download');
        Route::delete('/{taxDocument}', [App\Http\Controllers\TaxDocumentController::class, 'destroy'])->name('destroy');
    });

    // Tax Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\TaxReportController::class, 'index'])->name('index');
        Route::get('/collection', [App\Http\Controllers\TaxReportController::class, 'collectionReport'])->name('collection');
        Route::get('/outstanding', [App\Http\Controllers\TaxReportController::class, 'outstandingReport'])->name('outstanding');
        Route::get('/exemptions', [App\Http\Controllers\TaxReportController::class, 'exemptionReport'])->name('exemptions');
        Route::get('/analytics', [App\Http\Controllers\TaxReportController::class, 'analytics'])->name('analytics');
        Route::get('/export', [App\Http\Controllers\TaxReportController::class, 'export'])->name('export');
    });

    // Capital Gains Tax Routes
    Route::prefix('capital-gains')->name('capital-gains.')->group(function () {
        Route::get('/', [App\Http\Controllers\CapitalGainsTaxController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\CapitalGainsTaxController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\CapitalGainsTaxController::class, 'store'])->name('store');
        Route::get('/{capitalGainsTax}', [App\Http\Controllers\CapitalGainsTaxController::class, 'show'])->name('show');
        Route::post('/calculate', [App\Http\Controllers\CapitalGainsTaxController::class, 'calculate'])->name('calculate');
    });

    // VAT Routes
    Route::prefix('vat')->name('vat.')->group(function () {
        Route::get('/', [App\Http\Controllers\VatController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\VatController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\VatController::class, 'store'])->name('store');
        Route::get('/{vatRecord}', [App\Http\Controllers\VatController::class, 'show'])->name('show');
        Route::get('/calculator', [App\Http\Controllers\VatController::class, 'calculator'])->name('calculator');
        Route::post('/calculate', [App\Http\Controllers\VatController::class, 'calculate'])->name('calculate');
        Route::post('/{vatRecord}/submit', [App\Http\Controllers\VatController::class, 'submit'])->name('submit');
        Route::post('/{vatRecord}/pay', [App\Http\Controllers\VatController::class, 'pay'])->name('pay');
    });
});

// Route Map Page - Display all routes (Admin only)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/routes', [App\Http\Controllers\RouteController::class, 'index'])->name('routes.index');
    Route::get('/routes/export', [App\Http\Controllers\RouteController::class, 'export'])->name('routes.export');
    Route::get('/routes/details/{routeName}', [App\Http\Controllers\RouteController::class, 'getRouteDetails'])->name('routes.details');
    Route::get('/routes/test/{routeName}', [App\Http\Controllers\RouteController::class, 'testRoute'])->name('routes.test');
});

// Analytics Module Routes - Alternative Routes
Route::prefix('analytics-alt')->name('analytics-alt.')->middleware(['auth'])->group(function () {
    // Main Analytics Dashboard
    Route::get('/', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('dashboard');
    Route::get('/overview', [App\Http\Controllers\AnalyticsController::class, 'overview'])->name('overview');
    Route::get('/real-time', [App\Http\Controllers\AnalyticsController::class, 'realTime'])->name('real-time');
    Route::post('/track-event', [App\Http\Controllers\AnalyticsController::class, 'trackEvent'])->name('track-event');
    Route::get('/metrics', [App\Http\Controllers\AnalyticsController::class, 'getMetrics'])->name('metrics');
    Route::get('/export', [App\Http\Controllers\AnalyticsController::class, 'export'])->name('export');

    // Big Data Analytics
    Route::prefix('bigdata')->name('bigdata.')->group(function () {
        Route::get('/', [App\Http\Controllers\BigDataController::class, 'index'])->name('index');
        Route::post('/process', [App\Http\Controllers\BigDataController::class, 'processData'])->name('process');
        Route::get('/aggregate', [App\Http\Controllers\BigDataController::class, 'aggregateData'])->name('aggregate');
        Route::get('/stream', [App\Http\Controllers\BigDataController::class, 'realTimeStream'])->name('stream');
        Route::get('/insights', [App\Http\Controllers\BigDataController::class, 'dataInsights'])->name('insights');
        Route::get('/export', [App\Http\Controllers\BigDataController::class, 'exportBigData'])->name('export');
    });

    // Predictive Analytics
    Route::prefix('predictions')->name('predictions.')->group(function () {
        Route::get('/', [App\Http\Controllers\PredictiveAnalyticsController::class, 'index'])->name('index');
        Route::post('/generate', [App\Http\Controllers\PredictiveAnalyticsController::class, 'generatePrediction'])->name('generate');
        Route::get('/revenue', [App\Http\Controllers\PredictiveAnalyticsController::class, 'revenueForecast'])->name('revenue');
        Route::get('/traffic', [App\Http\Controllers\PredictiveAnalyticsController::class, 'trafficPrediction'])->name('traffic');
        Route::get('/conversion', [App\Http\Controllers\PredictiveAnalyticsController::class, 'conversionPrediction'])->name('conversion');
        Route::get('/churn', [App\Http\Controllers\PredictiveAnalyticsController::class, 'churnPrediction'])->name('churn');
        Route::get('/accuracy', [App\Http\Controllers\PredictiveAnalyticsController::class, 'modelAccuracy'])->name('accuracy');
        Route::get('/insights', [App\Http\Controllers\PredictiveAnalyticsController::class, 'predictionInsights'])->name('insights');
        Route::get('/export', [App\Http\Controllers\PredictiveAnalyticsController::class, 'exportPredictions'])->name('export');
    });

    // Market Analytics
    Route::prefix('market')->name('market.')->group(function () {
        Route::get('/', [App\Http\Controllers\MarketAnalyticsController::class, 'index'])->name('index');
        Route::get('/overview', [App\Http\Controllers\MarketAnalyticsController::class, 'marketOverview'])->name('overview');
        Route::get('/trends', [App\Http\Controllers\MarketAnalyticsController::class, 'marketTrends'])->name('trends');
        Route::get('/competitors', [App\Http\Controllers\MarketAnalyticsController::class, 'competitorAnalysis'])->name('competitors');
        Route::get('/pricing', [App\Http\Controllers\MarketAnalyticsController::class, 'priceAnalysis'])->name('pricing');
        Route::get('/segments', [App\Http\Controllers\MarketAnalyticsController::class, 'marketSegmentation'])->name('segments');
        Route::get('/opportunities', [App\Http\Controllers\MarketAnalyticsController::class, 'opportunityAnalysis'])->name('opportunities');
        Route::get('/threats', [App\Http\Controllers\MarketAnalyticsController::class, 'threatAnalysis'])->name('threats');
        Route::get('/report', [App\Http\Controllers\MarketAnalyticsController::class, 'generateReport'])->name('report');
    });

    // User Behavior Analytics
    Route::prefix('behavior')->name('behavior.')->group(function () {
        Route::get('/', [App\Http\Controllers\UserBehaviorController::class, 'index'])->name('index');
        Route::get('/patterns', [App\Http\Controllers\UserBehaviorController::class, 'behaviorPatterns'])->name('patterns');
        Route::get('/segments', [App\Http\Controllers\UserBehaviorController::class, 'segmentAnalysis'])->name('segments');
        Route::get('/retention', [App\Http\Controllers\UserBehaviorController::class, 'retentionAnalysis'])->name('retention');
        Route::get('/real-time', [App\Http\Controllers\UserBehaviorController::class, 'realTimeBehavior'])->name('real-time');
        Route::get('/journey', [App\Http\Controllers\UserBehaviorController::class, 'userJourney'])->name('journey');
        Route::get('/funnel', [App\Http\Controllers\UserBehaviorController::class, 'behaviorFunnel'])->name('funnel');
        Route::get('/export', [App\Http\Controllers\UserBehaviorController::class, 'exportBehaviorData'])->name('export');
    });

    // Heatmap Analytics
    Route::prefix('heatmap')->name('heatmap.')->group(function () {
        Route::get('/', [App\Http\Controllers\HeatmapController::class, 'index'])->name('index');
        Route::post('/generate', [App\Http\Controllers\HeatmapController::class, 'generateHeatmap'])->name('generate');
        Route::get('/{heatmap}', [App\Http\Controllers\HeatmapController::class, 'show'])->name('show');
        Route::get('/click', [App\Http\Controllers\HeatmapController::class, 'clickHeatmap'])->name('click');
        Route::get('/movement', [App\Http\Controllers\HeatmapController::class, 'movementHeatmap'])->name('movement');
        Route::get('/scroll', [App\Http\Controllers\HeatmapController::class, 'scrollHeatmap'])->name('scroll');
        Route::get('/attention', [App\Http\Controllers\HeatmapController::class, 'attentionHeatmap'])->name('attention');
        Route::get('/compare', [App\Http\Controllers\HeatmapController::class, 'compareHeatmaps'])->name('compare');
        Route::get('/analytics', [App\Http\Controllers\HeatmapController::class, 'heatmapAnalytics'])->name('analytics');
        Route::get('/export', [App\Http\Controllers\HeatmapController::class, 'exportHeatmap'])->name('export');
        Route::get('/realtime', [App\Http\Controllers\HeatmapController::class, 'getHeatmap'])->name('realtime');
    });

    // Funnel Analysis
    Route::prefix('funnel')->name('funnel.')->group(function () {
        Route::get('/', [App\Http\Controllers\FunnelAnalysisController::class, 'index'])->name('index');
        Route::post('/create', [App\Http\Controllers\FunnelAnalysisController::class, 'createFunnel'])->name('create');
        Route::get('/{funnel}', [App\Http\Controllers\FunnelAnalysisController::class, 'analyze'])->name('analyze');
        Route::get('/conversion', [App\Http\Controllers\FunnelAnalysisController::class, 'conversionFunnel'])->name('conversion');
        Route::get('/journey', [App\Http\Controllers\FunnelAnalysisController::class, 'userJourneyFunnel'])->name('journey');
        Route::get('/comparison', [App\Http\Controllers\FunnelAnalysisController::class, 'funnelComparison'])->name('comparison');
        Route::get('/optimization', [App\Http\Controllers\FunnelAnalysisController::class, 'funnelOptimization'])->name('optimization');
        Route::get('/realtime', [App\Http\Controllers\FunnelAnalysisController::class, 'realTimeFunnel'])->name('real-time');
        Route::get('/report', [App\Http\Controllers\FunnelAnalysisController::class, 'exportFunnelReport'])->name('report');
    });

    // Cohort Analysis
    Route::prefix('cohort')->name('cohort.')->group(function () {
        Route::get('/', [App\Http\Controllers\CohortAnalysisController::class, 'index'])->name('index');
        Route::post('/create', [App\Http\Controllers\CohortAnalysisController::class, 'createCohort'])->name('create');
        Route::get('/{cohort}', [App\Http\Controllers\CohortAnalysisController::class, 'analyzeCohort'])->name('analyze');
        Route::get('/retention', [App\Http\Controllers\CohortAnalysisController::class, 'retentionAnalysis'])->name('retention');
        Route::get('/revenue', [App\Http\Controllers\CohortAnalysisController::class, 'revenueAnalysis'])->name('revenue');
        Route::get('/comparison', [App\Http\Controllers\CohortAnalysisController::class, 'cohortComparison'])->name('comparison');
        Route::get('/export', [App\Http\Controllers\CohortAnalysisController::class, 'exportCohortData'])->name('export');
    });

    // AI Insights
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/', [App\Http\Controllers\AiInsightsController::class, 'index'])->name('index');
        Route::post('/generate', [App\Http\Controllers\AiInsightsController::class, 'generateInsights'])->name('generate');
        Route::get('/recommendations', [App\Http\Controllers\AiInsightsController::class, 'getRecommendations'])->name('recommendations');
        Route::get('/predictions', [App\Http\Controllers\AiInsightsController::class, 'getPredictions'])->name('predictions');
        Route::get('/anomalies', [App\Http\Controllers\AiInsightsController::class, 'detectAnomalies'])->name('anomalies');
        Route::get('/optimization', [App\Http\Controllers\AiInsightsController::class, 'optimizationSuggestions'])->name('optimization');
        Route::get('/export', [App\Http\Controllers\AiInsightsController::class, 'exportInsights'])->name('export');
    });

    // Sentiment Analysis
    Route::prefix('sentiment')->name('sentiment.')->group(function () {
        Route::get('/', [App\Http\Controllers\SentimentAnalysisController::class, 'index'])->name('index');
        Route::post('/analyze', [App\Http\Controllers\SentimentAnalysisController::class, 'analyzeSentiment'])->name('analyze');
        Route::get('/reviews', [App\Http\Controllers\SentimentAnalysisController::class, 'reviewSentiment'])->name('reviews');
        Route::get('/social', [App\Http\Controllers\SentimentAnalysisController::class, 'socialMediaSentiment'])->name('social');
        Route::get('/trends', [App\Http\Controllers\SentimentAnalysisController::class, 'sentimentTrends'])->name('trends');
        Route::get('/comparison', [App\Http\Controllers\SentimentAnalysisController::class, 'sentimentComparison'])->name('comparison');
        Route::get('/export', [App\Http\Controllers\SentimentAnalysisController::class, 'exportSentimentData'])->name('export');
    });

    // Trend Analysis
    Route::prefix('trends')->name('trends.')->group(function () {
        Route::get('/', [App\Http\Controllers\TrendAnalysisController::class, 'index'])->name('index');
        Route::post('/analyze', [App\Http\Controllers\TrendAnalysisController::class, 'analyzeTrends'])->name('analyze');
        Route::get('/market', [App\Http\Controllers\TrendAnalysisController::class, 'marketTrends'])->name('market');
        Route::get('/user', [App\Http\Controllers\TrendAnalysisController::class, 'userTrends'])->name('user');
        Route::get('/property', [App\Http\Controllers\TrendAnalysisController::class, 'propertyTrends'])->name('property');
        Route::get('/seasonal', [App\Http\Controllers\TrendAnalysisController::class, 'seasonalTrends'])->name('seasonal');
        Route::get('/forecast', [App\Http\Controllers\TrendAnalysisController::class, 'trendForecast'])->name('forecast');
        Route::get('/export', [App\Http\Controllers\TrendAnalysisController::class, 'exportTrendData'])->name('export');
    });

    // Competitive Analysis
    Route::prefix('competitive')->name('competitive.')->group(function () {
        Route::get('/', [App\Http\Controllers\CompetitiveAnalysisController::class, 'index'])->name('index');
        Route::post('/analyze', [App\Http\Controllers\CompetitiveAnalysisController::class, 'analyzeCompetition'])->name('analyze');
        Route::get('/market', [App\Http\Controllers\CompetitiveAnalysisController::class, 'marketAnalysis'])->name('market');
        Route::get('/competitors', [App\Http\Controllers\CompetitiveAnalysisController::class, 'competitorAnalysis'])->name('competitors');
        Route::get('/pricing', [App\Http\Controllers\CompetitiveAnalysisController::class, 'pricingAnalysis'])->name('pricing');
        Route::get('/features', [App\Http\Controllers\CompetitiveAnalysisController::class, 'featureComparison'])->name('features');
        Route::get('/opportunities', [App\Http\Controllers\CompetitiveAnalysisController::class, 'opportunityAnalysis'])->name('opportunities');
        Route::get('/threats', [App\Http\Controllers\CompetitiveAnalysisController::class, 'threatAnalysis'])->name('threats');
        Route::get('/report', [App\Http\Controllers\CompetitiveAnalysisController::class, 'generateReport'])->name('report');
    });

    // API Routes for AJAX requests
    Route::prefix('api')->name('api.')->group(function () {
        Route::post('/track-event', [App\Http\Controllers\AnalyticsController::class, 'trackEvent'])->name('track-event');
        Route::get('/real-time', [App\Http\Controllers\AnalyticsController::class, 'realTime'])->name('real-time');
        Route::get('/metrics', [App\Http\Controllers\AnalyticsController::class, 'getMetrics'])->name('metrics');
        Route::get('/overview', [App\Http\Controllers\AnalyticsController::class, 'overview'])->name('overview');
        Route::get('/behavior/patterns', [App\Http\Controllers\UserBehaviorController::class, 'behaviorPatterns'])->name('patterns');
        Route::get('/behavior/segments', [App\Http\Controllers\UserBehaviorController::class, 'segmentAnalysis'])->name('segments');
        Route::get('/behavior/retention', [App\Http\Controllers\UserBehaviorController::class, 'retentionAnalysis'])->name('retention');
        Route::get('/behavior/real-time', [App\Http\Controllers\UserBehaviorController::class, 'realTimeBehavior'])->name('behavior.real-time');
        Route::get('/predictions/accuracy', [App\Http\Controllers\PredictiveAnalyticsController::class, 'modelAccuracy'])->name('accuracy');
        Route::get('/ai-insights', [App\Http\Controllers\AiInsightsController::class, 'generateInsights'])->name('insights');
        Route::get('/predictions', [App\Http\Controllers\PredictiveAnalyticsController::class, 'generatePredictiveInsights'])->name('predictions');
        Route::get('/market/trends', [App\Http\Controllers\MarketAnalyticsController::class, 'marketOverview'])->name('trends');
        Route::get('/market/competitors', [App\Http\Controllers\MarketAnalyticsController::class, 'competitorAnalysis'])->name('competitors');
        Route::get('/market/pricing', [App\Http\Controllers\MarketAnalyticsController::class, 'priceAnalysis'])->name('pricing');
        Route::get('/market/segments', [App\Http\Controllers\MarketAnalyticsController::class, 'marketSegmentation'])->name('market.segments');
        Route::get('/market/opportunities', [App\Http\Controllers\MarketAnalyticsController::class, 'opportunityAnalysis'])->name('opportunities');
        Route::get('/heatmaps/realtime', [App\Http\Controllers\HeatmapController::class, 'getHeatmap'])->name('heatmap-realtime');
        Route::post('/heatmaps/generate', [App\Http\Controllers\HeatmapController::class, 'generateHeatmap'])->name('generate-heatmap');
        Route::get('/heatmaps/{heatmap}', [App\Http\Controllers\HeatmapController::class, 'show'])->name('show-heatmap');
        Route::post('/heatmaps/compare', [App\Http\Controllers\HeatmapController::class, 'compareHeatmaps'])->name('compare-heatmaps');
        Route::get('/heatmaps/export', [App\Http\Controllers\HeatmapController::class, 'exportHeatmap'])->name('export-heatmap');
    });
});

// Request Monitoring Routes
Route::prefix('requests')->name('requests.')->group(function () {
    Route::get('/', [RequestController::class, 'index'])->name('index')->middleware('auth');
    Route::get('/get', [RequestController::class, 'getRequests'])->name('get')->middleware('auth');
    Route::get('/stats', [RequestController::class, 'getStats'])->name('stats')->middleware('auth');
    Route::get('/{request}', [RequestController::class, 'show'])->name('show')->middleware('auth');
    Route::post('/export', [RequestController::class, 'export'])->name('export')->middleware('auth');
    Route::post('/clear-old', [RequestController::class, 'clearOld'])->name('clear-old')->middleware('auth');
});

// System Error Monitoring Routes
Route::prefix('admin/errors')->name('admin.errors.')->middleware('auth')->group(function () {
    Route::get('/', [SystemErrorLogController::class, 'index'])->name('index');
    Route::get('/show/{id}', [SystemErrorLogController::class, 'show'])->name('show');
    Route::post('/resolve/{id}', [SystemErrorLogController::class, 'resolve'])->name('resolve');
    Route::post('/clear', [SystemErrorLogController::class, 'clear'])->name('clear');
    Route::get('/scan', [SystemErrorLogController::class, 'scanRoutes'])->name('scan');
});
