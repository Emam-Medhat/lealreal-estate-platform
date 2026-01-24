<?php

use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Reports\PerformanceReportController;
use App\Http\Controllers\Reports\MarketReportController;
use App\Http\Controllers\Reports\CustomReportController;
use Illuminate\Support\Facades\Route;

// Reports Dashboard
Route::get('/reports/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');

// Reports Management
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/create', [ReportController::class, 'create'])->name('create');
    Route::post('/', [ReportController::class, 'store'])->name('store');
    Route::get('/{report}', [ReportController::class, 'show'])->name('show');
    Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('edit');
    Route::put('/{report}', [ReportController::class, 'update'])->name('update');
    Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
    Route::post('/{report}/regenerate', [ReportController::class, 'regenerate'])->name('regenerate');
    Route::post('/{report}/export', [ReportController::class, 'export'])->name('export');
    Route::get('/exports/{export}/download', [ReportController::class, 'download'])->name('export.download');
    Route::post('/schedule', [ReportController::class, 'schedule'])->name('schedule');
});

// Sales Reports
Route::prefix('reports/sales')->name('reports.sales.')->group(function () {
    Route::get('/', [SalesReportController::class, 'index'])->name('index');
    Route::get('/create', [SalesReportController::class, 'create'])->name('create');
    Route::post('/', [SalesReportController::class, 'store'])->name('store');
    Route::get('/{report}', [SalesReportController::class, 'show'])->name('show');
    Route::get('/data', [SalesReportController::class, 'getSalesData'])->name('data');
});

// Performance Reports
Route::prefix('reports/performance')->name('reports.performance.')->group(function () {
    Route::get('/', [PerformanceReportController::class, 'index'])->name('index');
    Route::get('/create', [PerformanceReportController::class, 'create'])->name('create');
    Route::post('/', [PerformanceReportController::class, 'store'])->name('store');
    Route::get('/{report}', [PerformanceReportController::class, 'show'])->name('show');
    Route::get('/data', [PerformanceReportController::class, 'getPerformanceData'])->name('data');
});

// Market Reports
Route::prefix('reports/market')->name('reports.market.')->group(function () {
    Route::get('/', [MarketReportController::class, 'index'])->name('index');
    Route::get('/create', [MarketReportController::class, 'create'])->name('create');
    Route::post('/', [MarketReportController::class, 'store'])->name('store');
    Route::get('/{report}', [MarketReportController::class, 'show'])->name('show');
    Route::get('/data', [MarketReportController::class, 'getMarketData'])->name('data');
});

// Custom Reports
Route::prefix('reports/custom')->name('reports.custom.')->group(function () {
    Route::get('/', [CustomReportController::class, 'index'])->name('index');
    Route::get('/create', [CustomReportController::class, 'create'])->name('create');
    Route::post('/', [CustomReportController::class, 'store'])->name('store');
    Route::get('/{report}', [CustomReportController::class, 'show'])->name('show');
    Route::get('/{customReport}/edit', [CustomReportController::class, 'edit'])->name('edit');
    Route::put('/{customReport}', [CustomReportController::class, 'update'])->name('update');
    Route::delete('/{customReport}', [CustomReportController::class, 'destroy'])->name('destroy');
    Route::post('/{customReport}/duplicate', [CustomReportController::class, 'duplicate'])->name('duplicate');
    Route::post('/{customReport}/run', [CustomReportController::class, 'runReport'])->name('run');
    Route::get('/data', [CustomReportController::class, 'getReportData'])->name('data');
});

// Report Schedules
Route::prefix('reports/schedules')->name('reports.schedules.')->group(function () {
    Route::get('/', function () {
        return view('reports.schedules.index');
    })->name('index');
    Route::get('/create', function () {
        return view('reports.schedules.create');
    })->name('create');
});
