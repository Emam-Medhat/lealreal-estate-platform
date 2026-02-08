<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Modules\ModuleController;

/*
|--------------------------------------------------------------------------
| Module Routes
|--------------------------------------------------------------------------
|
| Routes for the modular system management
|
*/

Route::prefix('modules')->name('modules.')->group(function () {
    // Module Dashboard
    Route::get('/', [ModuleController::class, 'dashboard'])->name('dashboard');
    
    // API Routes for Module Management
    Route::prefix('api')->group(function () {
        Route::get('/', [ModuleController::class, 'getModules'])->name('list');
        Route::get('/{moduleKey}', [ModuleController::class, 'getModuleDetails'])->name('details');
        Route::post('/{moduleKey}/toggle', [ModuleController::class, 'toggleModule'])->name('toggle');
    });
    
    // Individual Module Routes
    Route::get('/{moduleKey}', function($moduleKey) {
        return view("modules.{$moduleKey}.index");
    })->name('show');
    
    Route::get('/{moduleKey}/configure', function($moduleKey) {
        return view("modules.{$moduleKey}.configure");
    })->name('configure');
    
    Route::get('/{moduleKey}/settings', function($moduleKey) {
        return view("modules.{$moduleKey}.settings");
    })->name('settings');
    
    Route::get('/{moduleKey}/statistics', function($moduleKey) {
        return view("modules.{$moduleKey}.statistics");
    })->name('statistics');
});

// Module-specific routes will be loaded dynamically
Route::get('/modules/{moduleKey}/routes', function($moduleKey) {
    // This endpoint will return the routes for a specific module
    return response()->json([
        'routes' => getModuleRoutes($moduleKey)
    ]);
});

function getModuleRoutes($moduleKey) {
    $routes = [
        'core' => [
            'properties.index' => '/properties',
            'properties.create' => '/properties/create',
            'properties.show' => '/properties/{id}',
            'properties.edit' => '/properties/{id}/edit',
            'users.index' => '/users',
            'agents.index' => '/agents',
            'companies.index' => '/companies',
            'leads.index' => '/leads',
            'investments.index' => '/investments'
        ],
        'global_services' => [
            'currency.index' => '/currency',
            'currency.converter' => '/currency/converter',
            'language.index' => '/language',
            'language.translations' => '/language/translations',
            'gamification.index' => '/gamification',
            'gamification.achievements' => '/gamification/achievements',
            'blockchain.index' => '/blockchain',
            'blockchain.contracts' => '/blockchain/contracts',
            'ai.dashboard' => '/ai',
            'ai.descriptions' => '/ai/descriptions',
            'enterprise.dashboard' => '/enterprise'
        ],
        'advanced_features' => [
            'iot.index' => '/iot',
            'iot.devices' => '/iot/devices',
            'security.index' => '/security',
            'security.alerts' => '/security/alerts',
            'analytics.index' => '/analytics',
            'automation.index' => '/automation'
        ],
        'communication' => [
            'messaging.index' => '/messages',
            'notifications.index' => '/notifications',
            'video_calls.index' => '/video-calls',
            'forums.index' => '/forums',
            'support.index' => '/support'
        ],
        'marketplace' => [
            'marketplace.index' => '/marketplace',
            'auctions.index' => '/auctions',
            'services.index' => '/services',
            'reviews.index' => '/reviews',
            'deals.index' => '/deals'
        ],
        'admin_tools' => [
            'admin.index' => '/admin',
            'settings.index' => '/settings',
            'logs.index' => '/logs',
            'backups.index' => '/backups',
            'maintenance.index' => '/maintenance'
        ]
    ];

    return $routes[$moduleKey] ?? [];
}
