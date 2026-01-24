<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmartPropertyController;
use App\Http\Controllers\IotDeviceController;
use App\Http\Controllers\SmartHomeAutomationController;
use App\Http\Controllers\EnergyMonitoringController;
use App\Http\Controllers\SmartSecurityController;
use App\Http\Controllers\ClimateControlController;
use App\Http\Controllers\SmartLockController;
use App\Http\Controllers\WaterManagementController;
use App\Http\Controllers\AirQualityMonitorController;
use App\Http\Controllers\SmartLightingController;
use App\Http\Controllers\IotAlertController;
use App\Http\Controllers\PropertySensorController;

/*
|--------------------------------------------------------------------------
| IoT Smart Property System Routes
|--------------------------------------------------------------------------
|
| Routes for managing smart properties and IoT devices
|
*/

Route::prefix('iot')->name('iot.')->middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Smart Properties Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('smart-property')->name('smart-property.')->group(function () {
        Route::get('/dashboard', [SmartPropertyController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [SmartPropertyController::class, 'index'])->name('index');
        Route::get('/create', [SmartPropertyController::class, 'create'])->name('create');
        Route::post('/', [SmartPropertyController::class, 'store'])->name('store');
        Route::get('/{smartProperty}', [SmartPropertyController::class, 'show'])->name('show');
        Route::get('/{smartProperty}/edit', [SmartPropertyController::class, 'edit'])->name('edit');
        Route::put('/{smartProperty}', [SmartPropertyController::class, 'update'])->name('update');
        Route::delete('/{smartProperty}', [SmartPropertyController::class, 'destroy'])->name('destroy');
        
        // Control and monitoring
        Route::post('/{smartProperty}/control', [SmartPropertyController::class, 'controlProperty'])->name('control');
        Route::get('/{smartProperty}/realtime-data', [SmartPropertyController::class, 'getRealTimeData'])->name('realtime-data');
        Route::get('/{smartProperty}/analytics', [SmartPropertyController::class, 'analytics'])->name('analytics');
        Route::get('/{smartProperty}/export', [SmartPropertyController::class, 'exportData'])->name('export');
    });

    /*
    |--------------------------------------------------------------------------
    | IoT Devices Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('iot-device')->name('iot-device.')->group(function () {
        Route::get('/dashboard', [IotDeviceController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [IotDeviceController::class, 'index'])->name('index');
        Route::get('/create', [IotDeviceController::class, 'create'])->name('create');
        Route::post('/', [IotDeviceController::class, 'store'])->name('store');
        Route::get('/{device}', [IotDeviceController::class, 'show'])->name('show');
        Route::get('/{device}/edit', [IotDeviceController::class, 'edit'])->name('edit');
        Route::put('/{device}', [IotDeviceController::class, 'update'])->name('update');
        Route::delete('/{device}', [IotDeviceController::class, 'destroy'])->name('destroy');
        
        // Device control and monitoring
        Route::post('/{device}/control', [IotDeviceController::class, 'controlDevice'])->name('control');
        Route::get('/{device}/data', [IotDeviceController::class, 'getDeviceData'])->name('data');
        Route::post('/{device}/firmware-update', [IotDeviceController::class, 'updateFirmware'])->name('firmware-update');
        Route::post('/{device}/restart', [IotDeviceController::class, 'restartDevice'])->name('restart');
        Route::get('/{device}/diagnostics', [IotDeviceController::class, 'diagnostics'])->name('diagnostics');
    });

    /*
    |--------------------------------------------------------------------------
    | Smart Home Automation Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('smart-automation')->name('smart-automation.')->group(function () {
        Route::get('/dashboard', [SmartHomeAutomationController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [SmartHomeAutomationController::class, 'index'])->name('index');
        Route::get('/create', [SmartHomeAutomationController::class, 'create'])->name('create');
        Route::post('/', [SmartHomeAutomationController::class, 'store'])->name('store');
        Route::get('/{automation}', [SmartHomeAutomationController::class, 'show'])->name('show');
        Route::get('/{automation}/edit', [SmartHomeAutomationController::class, 'edit'])->name('edit');
        Route::put('/{automation}', [SmartHomeAutomationController::class, 'update'])->name('update');
        Route::delete('/{automation}', [SmartHomeAutomationController::class, 'destroy'])->name('destroy');
        
        // Automation control
        Route::post('/{automation}/execute', [SmartHomeAutomationController::class, 'executeAutomation'])->name('execute');
        Route::post('/{automation}/toggle', [SmartHomeAutomationController::class, 'toggleAutomation'])->name('toggle');
        Route::post('/{automation}/test', [SmartHomeAutomationController::class, 'testAutomation'])->name('test');
        Route::get('/{automation}/logs', [SmartHomeAutomationController::class, 'getExecutionLogs'])->name('logs');
    });

    /*
    |--------------------------------------------------------------------------
    | Energy Monitoring Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('energy-monitoring')->name('energy-monitoring.')->group(function () {
        Route::get('/dashboard', [EnergyMonitoringController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [EnergyMonitoringController::class, 'index'])->name('index');
        Route::get('/create', [EnergyMonitoringController::class, 'create'])->name('create');
        Route::post('/', [EnergyMonitoringController::class, 'store'])->name('store');
        Route::get('/{monitoring}', [EnergyMonitoringController::class, 'show'])->name('show');
        Route::get('/{monitoring}/edit', [EnergyMonitoringController::class, 'edit'])->name('edit');
        Route::put('/{monitoring}', [EnergyMonitoringController::class, 'update'])->name('update');
        Route::delete('/{monitoring}', [EnergyMonitoringController::class, 'destroy'])->name('destroy');
        
        // Energy monitoring features
        Route::get('/{monitoring}/realtime-data', [EnergyMonitoringController::class, 'getRealTimeData'])->name('realtime-data');
        Route::post('/{monitoring}/report', [EnergyMonitoringController::class, 'generateReport'])->name('report');
        Route::post('/{monitoring}/optimize', [EnergyMonitoringController::class, 'optimizeUsage'])->name('optimize');
        Route::post('/{monitoring}/thresholds', [EnergyMonitoringController::class, 'setAlertThresholds'])->name('thresholds');
    });

    /*
    |--------------------------------------------------------------------------
    | Smart Security Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('smart-security')->name('smart-security.')->group(function () {
        Route::get('/dashboard', [SmartSecurityController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [SmartSecurityController::class, 'index'])->name('index');
        Route::get('/create', [SmartSecurityController::class, 'create'])->name('create');
        Route::post('/', [SmartSecurityController::class, 'store'])->name('store');
        Route::get('/{security}', [SmartSecurityController::class, 'show'])->name('show');
        Route::get('/{security}/edit', [SmartSecurityController::class, 'edit'])->name('edit');
        Route::put('/{security}', [SmartSecurityController::class, 'update'])->name('update');
        Route::delete('/{security}', [SmartSecurityController::class, 'destroy'])->name('destroy');
        
        // Security control
        Route::post('/{security}/arm', [SmartSecurityController::class, 'armSystem'])->name('arm');
        Route::post('/{security}/disarm', [SmartSecurityController::class, 'disarmSystem'])->name('disarm');
        Route::get('/{security}/status', [SmartSecurityController::class, 'getSystemStatus'])->name('status');
        Route::post('/{security}/alert', [SmartSecurityController::class, 'triggerAlert'])->name('alert');
        Route::get('/{security}/logs', [SmartSecurityController::class, 'getSecurityLogs'])->name('logs');
    });

    /*
    |--------------------------------------------------------------------------
    | Climate Control Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('climate-control')->name('climate-control.')->group(function () {
        Route::get('/dashboard', [ClimateControlController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [ClimateControlController::class, 'index'])->name('index');
        Route::get('/create', [ClimateControlController::class, 'create'])->name('create');
        Route::post('/', [ClimateControlController::class, 'store'])->name('store');
        Route::get('/{climate}', [ClimateControlController::class, 'show'])->name('show');
        Route::get('/{climate}/edit', [ClimateControlController::class, 'edit'])->name('edit');
        Route::put('/{climate}', [ClimateControlController::class, 'update'])->name('update');
        Route::delete('/{climate}', [ClimateControlController::class, 'destroy'])->name('destroy');
        
        // Climate control features
        Route::post('/{climate}/temperature', [ClimateControlController::class, 'setTemperature'])->name('temperature');
        Route::post('/{climate}/humidity', [ClimateControlController::class, 'setHumidity'])->name('humidity');
        Route::post('/{climate}/mode', [ClimateControlController::class, 'toggleMode'])->name('mode');
        Route::get('/{climate}/realtime-data', [ClimateControlController::class, 'getRealTimeData'])->name('realtime-data');
        Route::post('/{climate}/report', [ClimateControlController::class, 'generateReport'])->name('report');
    });

    /*
    |--------------------------------------------------------------------------
    | Smart Lock Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('smart-lock')->name('smart-lock.')->group(function () {
        Route::get('/dashboard', [SmartLockController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [SmartLockController::class, 'index'])->name('index');
        Route::get('/create', [SmartLockController::class, 'create'])->name('create');
        Route::post('/', [SmartLockController::class, 'store'])->name('store');
        Route::get('/{lock}', [SmartLockController::class, 'show'])->name('show');
        Route::get('/{lock}/edit', [SmartLockController::class, 'edit'])->name('edit');
        Route::put('/{lock}', [SmartLockController::class, 'update'])->name('update');
        Route::delete('/{lock}', [SmartLockController::class, 'destroy'])->name('destroy');
        
        // Lock control
        Route::post('/{lock}/lock', [SmartLockController::class, 'lockDoor'])->name('lock');
        Route::post('/{lock}/unlock', [SmartLockController::class, 'unlockDoor'])->name('unlock');
        Route::post('/{lock}/grant-access', [SmartLockController::class, 'grantAccess'])->name('grant-access');
        Route::post('/{lock}/revoke-access', [SmartLockController::class, 'revokeAccess'])->name('revoke-access');
        Route::get('/{lock}/status', [SmartLockController::class, 'getLockStatus'])->name('status');
        Route::get('/{lock}/logs', [SmartLockController::class, 'getAccessLogs'])->name('logs');
    });

    /*
    |--------------------------------------------------------------------------
    | Water Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('water-management')->name('water-management.')->group(function () {
        Route::get('/dashboard', [WaterManagementController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [WaterManagementController::class, 'index'])->name('index');
        Route::get('/create', [WaterManagementController::class, 'create'])->name('create');
        Route::post('/', [WaterManagementController::class, 'store'])->name('store');
        Route::get('/{water}', [WaterManagementController::class, 'show'])->name('show');
        Route::get('/{water}/edit', [WaterManagementController::class, 'edit'])->name('edit');
        Route::put('/{water}', [WaterManagementController::class, 'update'])->name('update');
        Route::delete('/{water}', [WaterManagementController::class, 'destroy'])->name('destroy');
        
        // Water management features
        Route::get('/{water}/realtime-data', [WaterManagementController::class, 'getRealTimeData'])->name('realtime-data');
        Route::post('/{water}/report', [WaterManagementController::class, 'generateReport'])->name('report');
        Route::post('/{water}/optimize', [WaterManagementController::class, 'optimizeUsage'])->name('optimize');
    });

    /*
    |--------------------------------------------------------------------------
    | Air Quality Monitor Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('air-quality')->name('air-quality.')->group(function () {
        Route::get('/dashboard', [AirQualityMonitorController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [AirQualityMonitorController::class, 'index'])->name('index');
        Route::get('/create', [AirQualityMonitorController::class, 'create'])->name('create');
        Route::post('/', [AirQualityMonitorController::class, 'store'])->name('store');
        Route::get('/{airQuality}', [AirQualityMonitorController::class, 'show'])->name('show');
        Route::get('/{airQuality}/edit', [AirQualityMonitorController::class, 'edit'])->name('edit');
        Route::put('/{airQuality}', [AirQualityMonitorController::class, 'update'])->name('update');
        Route::delete('/{airQuality}', [AirQualityMonitorController::class, 'destroy'])->name('destroy');
        
        // Air quality monitoring
        Route::get('/{airQuality}/realtime-data', [AirQualityMonitorController::class, 'getRealTimeData'])->name('realtime-data');
        Route::post('/{airQuality}/report', [AirQualityMonitorController::class, 'generateReport'])->name('report');
        Route::post('/{airQuality}/alert', [AirQualityMonitorController::class, 'triggerAlert'])->name('alert');
    });

    /*
    |--------------------------------------------------------------------------
    | Smart Lighting Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('smart-lighting')->name('smart-lighting.')->group(function () {
        Route::get('/dashboard', [SmartLightingController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [SmartLightingController::class, 'index'])->name('index');
        Route::get('/create', [SmartLightingController::class, 'create'])->name('create');
        Route::post('/', [SmartLightingController::class, 'store'])->name('store');
        Route::get('/{lighting}', [SmartLightingController::class, 'show'])->name('show');
        Route::get('/{lighting}/edit', [SmartLightingController::class, 'edit'])->name('edit');
        Route::put('/{lighting}', [SmartLightingController::class, 'update'])->name('update');
        Route::delete('/{lighting}', [SmartLightingController::class, 'destroy'])->name('destroy');
        
        // Lighting control
        Route::post('/{lighting}/control', [SmartLightingController::class, 'controlLighting'])->name('control');
        Route::post('/{lighting}/scene', [SmartLightingController::class, 'setScene'])->name('scene');
        Route::post('/{lighting}/schedule', [SmartLightingController::class, 'setSchedule'])->name('schedule');
        Route::get('/{lighting}/realtime-data', [SmartLightingController::class, 'getRealTimeData'])->name('realtime-data');
    });

    /*
    |--------------------------------------------------------------------------
    | IoT Alerts Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('iot-alerts')->name('iot-alerts.')->group(function () {
        Route::get('/dashboard', [IotAlertController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [IotAlertController::class, 'index'])->name('index');
        Route::get('/create', [IotAlertController::class, 'create'])->name('create');
        Route::post('/', [IotAlertController::class, 'store'])->name('store');
        Route::get('/{alert}', [IotAlertController::class, 'show'])->name('show');
        Route::get('/{alert}/edit', [IotAlertController::class, 'edit'])->name('edit');
        Route::put('/{alert}', [IotAlertController::class, 'update'])->name('update');
        Route::delete('/{alert}', [IotAlertController::class, 'destroy'])->name('destroy');
        
        // Alert management
        Route::post('/{alert}/resolve', [IotAlertController::class, 'resolveAlert'])->name('resolve');
        Route::post('/{alert}/escalate', [IotAlertController::class, 'escalateAlert'])->name('escalate');
        Route::post('/{alert}/notify', [IotAlertController::class, 'sendNotification'])->name('notify');
    });

    /*
    |--------------------------------------------------------------------------
    | Property Sensors Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('property-sensors')->name('property-sensors.')->group(function () {
        Route::get('/dashboard', [PropertySensorController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [PropertySensorController::class, 'index'])->name('index');
        Route::get('/create', [PropertySensorController::class, 'create'])->name('create');
        Route::post('/', [PropertySensorController::class, 'store'])->name('store');
        Route::get('/{sensor}', [PropertySensorController::class, 'show'])->name('show');
        Route::get('/{sensor}/edit', [PropertySensorController::class, 'edit'])->name('edit');
        Route::put('/{sensor}', [PropertySensorController::class, 'update'])->name('update');
        Route::delete('/{sensor}', [PropertySensorController::class, 'destroy'])->name('destroy');
        
        // Sensor data and calibration
        Route::get('/{sensor}/data', [PropertySensorController::class, 'getSensorData'])->name('data');
        Route::post('/{sensor}/calibrate', [PropertySensorController::class, 'calibrateSensor'])->name('calibrate');
        Route::get('/{sensor}/logs', [PropertySensorController::class, 'getSensorLogs'])->name('logs');
    });

});
