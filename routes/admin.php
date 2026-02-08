<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminAgentController;
use App\Http\Controllers\Admin\AdminPropertyController;
use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Content\BlogController;
use App\Http\Controllers\Content\PageController;
use App\Http\Controllers\Content\NewsController;
use App\Http\Controllers\Content\GuideController;
use App\Http\Controllers\Content\FaqController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MediaLibraryController;
use App\Http\Controllers\WidgetController;
use App\Http\Controllers\Financial\FinancialController;
use App\Http\Controllers\Reports\ReportController;
// use App\Http\Controllers\Admin\Settings\SettingController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\System\SystemController;
use App\Http\Controllers\PerformanceController;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
    });
    
    // Agents Management
    Route::prefix('agents')->name('agents.')->group(function () {
        Route::get('/', [AdminAgentController::class, 'index'])->name('index');
        Route::get('/create', [AdminAgentController::class, 'create'])->name('create');
        Route::post('/', [AdminAgentController::class, 'store'])->name('store');
        Route::get('/{agent}', [AdminAgentController::class, 'show'])->name('show');
        Route::get('/{agent}/edit', [AdminAgentController::class, 'edit'])->name('edit');
        Route::put('/{agent}', [AdminAgentController::class, 'update'])->name('update');
        Route::delete('/{agent}', [AdminAgentController::class, 'destroy'])->name('destroy');
    });
    
    // Properties Management
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/', [AdminPropertyController::class, 'index'])->name('index');
        Route::get('/create', [AdminPropertyController::class, 'create'])->name('create');
        Route::post('/', [AdminPropertyController::class, 'store'])->name('store');
        Route::get('/{property}', [AdminPropertyController::class, 'show'])->name('show');
        Route::get('/{property}/edit', [AdminPropertyController::class, 'edit'])->name('edit');
        Route::put('/{property}', [AdminPropertyController::class, 'update'])->name('update');
        Route::delete('/{property}', [AdminPropertyController::class, 'destroy'])->name('destroy');
    });
    
    // Companies Management
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [AdminCompanyController::class, 'index'])->name('index');
        Route::get('/create', [AdminCompanyController::class, 'create'])->name('create');
        Route::post('/', [AdminCompanyController::class, 'store'])->name('store');
        Route::get('/{company}', [AdminCompanyController::class, 'show'])->name('show');
        Route::get('/{company}/edit', [AdminCompanyController::class, 'edit'])->name('edit');
        Route::put('/{company}', [AdminCompanyController::class, 'update'])->name('update');
        Route::delete('/{company}', [AdminCompanyController::class, 'destroy'])->name('destroy');
    });
    
    // Content Management
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/dashboard', [ContentController::class, 'dashboard'])->name('dashboard');
        
        // Blog Posts
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/posts', [BlogController::class, 'index'])->name('posts.index');
            Route::get('/posts/create', [BlogController::class, 'create'])->name('posts.create');
            Route::post('/posts', [BlogController::class, 'store'])->name('posts.store');
            Route::get('/posts/{post}', [BlogController::class, 'show'])->name('posts.show');
            Route::get('/posts/{post}/edit', [BlogController::class, 'edit'])->name('posts.edit');
            Route::put('/posts/{post}', [BlogController::class, 'update'])->name('posts.update');
            Route::delete('/posts/{post}', [BlogController::class, 'destroy'])->name('posts.destroy');
        });
        
        // Pages
        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('index');
            Route::get('/create', [PageController::class, 'create'])->name('create');
            Route::post('/', [PageController::class, 'store'])->name('store');
            Route::get('/{page}', [PageController::class, 'show'])->name('show');
            Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
            Route::put('/{page}', [PageController::class, 'update'])->name('update');
            Route::delete('/{page}', [PageController::class, 'destroy'])->name('destroy');
        });
        
        // News
        Route::prefix('news')->name('news.')->group(function () {
            Route::get('/', [NewsController::class, 'index'])->name('index');
            Route::get('/create', [NewsController::class, 'create'])->name('create');
            Route::post('/', [NewsController::class, 'store'])->name('store');
            Route::get('/{news}', [NewsController::class, 'show'])->name('show');
            Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
            Route::put('/{news}', [NewsController::class, 'update'])->name('update');
            Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
        });
        
        // Guides
        Route::prefix('guides')->name('guides.')->group(function () {
            Route::get('/', [GuideController::class, 'index'])->name('index');
            Route::get('/create', [GuideController::class, 'create'])->name('create');
            Route::post('/', [GuideController::class, 'store'])->name('store');
            Route::get('/{guide}', [GuideController::class, 'show'])->name('show');
            Route::get('/{guide}/edit', [GuideController::class, 'edit'])->name('edit');
            Route::put('/{guide}', [GuideController::class, 'update'])->name('update');
            Route::delete('/{guide}', [GuideController::class, 'destroy'])->name('destroy');
        });
        
        // FAQs
        Route::prefix('faqs')->name('faqs.')->group(function () {
            Route::get('/', [FaqController::class, 'index'])->name('index');
            Route::get('/create', [FaqController::class, 'create'])->name('create');
            Route::post('/', [FaqController::class, 'store'])->name('store');
            Route::get('/{faq}', [FaqController::class, 'show'])->name('show');
            Route::get('/{faq}/edit', [FaqController::class, 'edit'])->name('edit');
            Route::put('/{faq}', [FaqController::class, 'update'])->name('update');
            Route::delete('/{faq}', [FaqController::class, 'destroy'])->name('destroy');
        });
        
        // Media
        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [MediaLibraryController::class, 'index'])->name('index');
            Route::post('/upload', [MediaLibraryController::class, 'upload'])->name('upload');
            Route::get('/{media}/preview', [MediaLibraryController::class, 'preview'])->name('preview');
            Route::delete('/{media}', [MediaLibraryController::class, 'destroy'])->name('destroy');
        });
        
        // Widgets
        Route::prefix('widgets')->name('widgets.')->group(function () {
            Route::get('/', [WidgetController::class, 'index'])->name('index');
            Route::get('/create', [WidgetController::class, 'create'])->name('create');
            Route::post('/', [WidgetController::class, 'store'])->name('store');
            Route::get('/{widget}', [WidgetController::class, 'show'])->name('show');
            Route::get('/{widget}/edit', [WidgetController::class, 'edit'])->name('edit');
            Route::put('/{widget}', [WidgetController::class, 'update'])->name('update');
            Route::delete('/{widget}', [WidgetController::class, 'destroy'])->name('destroy');
        });
    });
    
    // Menus
    Route::prefix('menus')->name('menus.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::get('/create', [MenuController::class, 'create'])->name('create');
        Route::post('/', [MenuController::class, 'store'])->name('store');
        Route::get('/{menu}', [MenuController::class, 'show'])->name('show');
        Route::get('/{menu}/edit', [MenuController::class, 'edit'])->name('edit');
        Route::put('/{menu}', [MenuController::class, 'update'])->name('update');
        Route::delete('/{menu}', [MenuController::class, 'destroy'])->name('destroy');
        
        // Menu Builder Routes
        Route::get('/{menu}/builder', [MenuController::class, 'builder'])->name('builder');
        Route::post('/{menu}/add-item', [MenuController::class, 'addItem'])->name('add_item');
        Route::delete('/menu-items/{menuItem}', [MenuController::class, 'deleteItem'])->name('delete_item');
        Route::put('/menu-items/{menuItem}', [MenuController::class, 'updateItem'])->name('update_item');
        Route::post('/{menu}/reorder-items', [MenuController::class, 'reorderItems'])->name('reorder_items');
    });
    
    // Financial Management
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/dashboard', [FinancialController::class, 'dashboard'])->name('dashboard');
        Route::get('/transactions', [FinancialController::class, 'transactions'])->name('transactions');
        Route::get('/invoices', [FinancialController::class, 'invoices'])->name('invoices');
        Route::get('/payments', [FinancialController::class, 'payments'])->name('payments');
        Route::get('/expenses', [FinancialController::class, 'expenses'])->name('expenses');
        Route::get('/reports', [FinancialController::class, 'reports'])->name('reports');
        Route::get('/analytics', [FinancialController::class, 'analytics'])->name('analytics');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial.index');
        Route::get('/property-performance', [ReportController::class, 'propertyPerformance'])->name('property.performance');
        Route::get('/agent-performance', [ReportController::class, 'agentPerformance'])->name('agent.performance');
        Route::get('/client-analytics', [ReportController::class, 'clientAnalytics'])->name('client.analytics');
        Route::get('/cash-flow', [ReportController::class, 'cashFlow'])->name('cash.flow');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/custom', [ReportController::class, 'custom'])->name('custom');
        Route::get('/scheduled', [ReportController::class, 'scheduled'])->name('scheduled');
    });
    
    // Settings - Temporarily disabled
    /*
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/general', [SettingController::class, 'general'])->name('general');
        Route::get('/seo', [SettingController::class, 'seo'])->name('seo');
        Route::get('/security', [SettingController::class, 'security'])->name('security');
        Route::get('/email', [SettingController::class, 'email'])->name('email');
        Route::get('/payment', [SettingController::class, 'payment'])->name('payment');
        Route::get('/backup', [SettingController::class, 'backup'])->name('backup');
        Route::get('/maintenance', [SettingController::class, 'maintenance'])->name('maintenance');
    });
    */
    
    // SEO Management
    Route::prefix('seo')->name('seo.')->group(function () {
        Route::get('/', [SeoController::class, 'index'])->name('index');
        Route::get('/analyze', [SeoController::class, 'analyze'])->name('analyze');
        Route::get('/keywords', [SeoController::class, 'keywords'])->name('keywords');
        Route::get('/meta-tags', [SeoController::class, 'metaTags'])->name('meta.tags');
        Route::get('/sitemap', [SeoController::class, 'sitemap'])->name('sitemap');
        Route::get('/robots', [SeoController::class, 'robots'])->name('robots');
    });
    
    // Performance Management
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [PerformanceController::class, 'index'])->name('dashboard');
        Route::get('/cache', [PerformanceController::class, 'cache'])->name('cache');
        Route::post('/clear-cache', [PerformanceController::class, 'flushCache'])->name('clear_cache');
        Route::get('/database', [PerformanceController::class, 'database'])->name('database');
        Route::get('/queries', [PerformanceController::class, 'queries'])->name('queries');
        Route::get('/system', [PerformanceController::class, 'system'])->name('system');
        Route::get('/recommendations', [PerformanceController::class, 'recommendations'])->name('recommendations');
        Route::get('/realtime', [PerformanceController::class, 'realtime'])->name('realtime');
    });

    // System Management
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/dashboard', [SystemController::class, 'dashboard'])->name('dashboard');
        Route::get('/logs', [SystemController::class, 'logs'])->name('logs');
        Route::get('/cache', [SystemController::class, 'cache'])->name('cache');
        Route::get('/queue', [SystemController::class, 'queue'])->name('queue');
        Route::get('/storage', [SystemController::class, 'storage'])->name('storage');
        Route::get('/database', [SystemController::class, 'database'])->name('database');
        Route::get('/monitoring', [SystemController::class, 'monitoring'])->name('monitoring');
        Route::get('/security', [SystemController::class, 'security'])->name('security');
        Route::get('/backup', [SystemController::class, 'backup'])->name('backup');
        Route::post('/backup/create', [SystemController::class, 'createBackup'])->name('backup.create');
        Route::get('/backup/download/{filename}', [SystemController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{filename}', [SystemController::class, 'deleteBackup'])->name('backup.destroy');
        Route::get('/updates', [SystemController::class, 'updates'])->name('updates');
    });
});
