<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\MediaLibraryController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

// Public Blog Routes
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/search', [BlogController::class, 'search'])->name('search');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
    Route::get('/create', [BlogController::class, 'create'])->name('create')->middleware('auth');
    Route::post('/', [BlogController::class, 'store'])->name('store')->middleware('auth');
    Route::get('/{post}/edit', [BlogController::class, 'edit'])->name('edit')->middleware('auth');
    Route::put('/{post}', [BlogController::class, 'update'])->name('update')->middleware('auth');
    Route::delete('/{post}', [BlogController::class, 'destroy'])->name('destroy')->middleware('auth');
});

// Public Pages Routes
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');

// Public News Routes
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
});

// Public Guides Routes
Route::prefix('guides')->name('guides.')->group(function () {
    Route::get('/', [GuideController::class, 'index'])->name('index');
    Route::get('/{slug}', [GuideController::class, 'show'])->name('show');
});

// Public FAQs Routes
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// Admin Content Management Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Content Dashboard
    Route::get('/content', [ContentController::class, 'dashboard'])->name('content.dashboard');
    Route::get('/content/search', [ContentController::class, 'search'])->name('content.search');
    Route::get('/content/media', [ContentController::class, 'mediaLibrary'])->name('content.media');
    Route::get('/content/seo', [ContentController::class, 'seoTools'])->name('content.seo');

    // Blog Management
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/posts', [BlogPostController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [BlogPostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [BlogPostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}', [BlogPostController::class, 'show'])->name('posts.show');
        Route::get('/posts/{post}/edit', [BlogPostController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [BlogPostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [BlogPostController::class, 'destroy'])->name('posts.destroy');
        Route::post('/posts/{post}/duplicate', [BlogPostController::class, 'duplicate'])->name('posts.duplicate');
        Route::post('/posts/{post}/restore', [BlogPostController::class, 'restore'])->name('posts.restore');

        Route::get('/categories', [BlogCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [BlogCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [BlogCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}', [BlogCategoryController::class, 'show'])->name('categories.show');
        Route::get('/categories/{category}/edit', [BlogCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [BlogCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [BlogCategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Pages Management
    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::get('/create', [PageController::class, 'create'])->name('create');
        Route::post('/', [PageController::class, 'store'])->name('store');
        Route::get('/{page}', [PageController::class, 'show'])->name('show');
        Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
        Route::put('/{page}', [PageController::class, 'update'])->name('update');
        Route::delete('/{page}', [PageController::class, 'destroy'])->name('destroy');
        Route::post('/{page}/duplicate', [PageController::class, 'duplicate'])->name('duplicate');
    });

    // News Management
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'adminIndex'])->name('index');
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{news}', [NewsController::class, 'adminShow'])->name('show');
        Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{news}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
    });

    // Guides Management
    Route::prefix('guides')->name('guides.')->group(function () {
        Route::get('/', [GuideController::class, 'adminIndex'])->name('index');
        Route::get('/create', [GuideController::class, 'create'])->name('create');
        Route::post('/', [GuideController::class, 'store'])->name('store');
        Route::get('/{guide}', [GuideController::class, 'adminShow'])->name('show');
        Route::get('/{guide}/edit', [GuideController::class, 'edit'])->name('edit');
        Route::put('/{guide}', [GuideController::class, 'update'])->name('update');
        Route::delete('/{guide}', [GuideController::class, 'destroy'])->name('destroy');
    });

    // FAQs Management
    Route::prefix('faqs')->name('faqs.')->group(function () {
        Route::get('/', [FaqController::class, 'adminIndex'])->name('index');
        Route::get('/create', [FaqController::class, 'create'])->name('create');
        Route::post('/', [FaqController::class, 'store'])->name('store');
        Route::get('/{faq}', [FaqController::class, 'show'])->name('show');
        Route::get('/{faq}/edit', [FaqController::class, 'edit'])->name('edit');
        Route::put('/{faq}', [FaqController::class, 'update'])->name('update');
        Route::delete('/{faq}', [FaqController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [FaqController::class, 'reorder'])->name('reorder');
    });

    // Media Library Management
    Route::prefix('media')->name('media.')->group(function () {
        Route::get('/', [MediaLibraryController::class, 'index'])->name('index');
        Route::post('/upload', [MediaLibraryController::class, 'upload'])->name('upload');
        Route::get('/{mediaFile}', [MediaLibraryController::class, 'show'])->name('show');
        Route::get('/{mediaFile}/edit', [MediaLibraryController::class, 'edit'])->name('edit');
        Route::put('/{mediaFile}', [MediaLibraryController::class, 'update'])->name('update');
        Route::delete('/{mediaFile}', [MediaLibraryController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [MediaLibraryController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/{mediaFile}/download', [MediaLibraryController::class, 'download'])->name('download');
    });

    // Menu Management
    Route::prefix('menus')->name('menus.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('index');
        Route::get('/create', [MenuController::class, 'create'])->name('create');
        Route::post('/', [MenuController::class, 'store'])->name('store');
        Route::get('/{menu}', [MenuController::class, 'show'])->name('show');
        Route::get('/{menu}/edit', [MenuController::class, 'edit'])->name('edit');
        Route::put('/{menu}', [MenuController::class, 'update'])->name('update');
        Route::delete('/{menu}', [MenuController::class, 'destroy'])->name('destroy');
        Route::get('/{menu}/builder', [MenuController::class, 'builder'])->name('builder');
        Route::post('/{menu}/items', [MenuController::class, 'addItem'])->name('items.add');
        Route::put('/items/{menuItem}', [MenuController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{menuItem}', [MenuController::class, 'deleteItem'])->name('items.delete');
        Route::post('/{menu}/reorder', [MenuController::class, 'reorderItems'])->name('reorder');
    });

    // Widget Management
    Route::prefix('widgets')->name('widgets.')->group(function () {
        Route::get('/', [WidgetController::class, 'index'])->name('index');
        Route::get('/create', [WidgetController::class, 'create'])->name('create');
        Route::post('/', [WidgetController::class, 'store'])->name('store');
        Route::get('/{widget}', [WidgetController::class, 'show'])->name('show');
        Route::get('/{widget}/edit', [WidgetController::class, 'edit'])->name('edit');
        Route::put('/{widget}', [WidgetController::class, 'update'])->name('update');
        Route::delete('/{widget}', [WidgetController::class, 'destroy'])->name('destroy');
        Route::post('/{widget}/duplicate', [WidgetController::class, 'duplicate'])->name('duplicate');
        Route::post('/reorder', [WidgetController::class, 'reorder'])->name('reorder');
        Route::post('/{widget}/toggle', [WidgetController::class, 'toggleStatus'])->name('toggle');
        Route::get('/{widget}/preview', [WidgetController::class, 'preview'])->name('preview');
    });
});
