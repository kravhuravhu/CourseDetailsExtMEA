<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/health', function () {
    return response()->json(['status' => 'healthy', 'timestamp' => now()]);
});

// Integration Routes
Route::prefix('integration')->group(function () {
    // Receive course details
    Route::post('/course-details/adhoc', [\App\Http\Controllers\Api\Integration\IntegrationController::class, 'receiveCourseDetailsAdHoc']);
    Route::post('/course-details/takeon', [\App\Http\Controllers\Api\Integration\IntegrationController::class, 'receiveCourseDetailsTakeOn']);
    
    // Integration status and logs
    Route::get('/status', [\App\Http\Controllers\Api\Integration\IntegrationController::class, 'getStatus']);
    Route::get('/logs', [\App\Http\Controllers\Api\Integration\IntegrationController::class, 'getLogs']);
    Route::post('/logs/{id}/retry', [\App\Http\Controllers\Api\Integration\IntegrationController::class, 'retryMessage']);
});

// API routes with auth (using Sanctum)
Route::middleware(['auth:sanctum'])->group(function () {
    // Personnel routes
    Route::prefix('personnel')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Personnel\PersonnelController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Personnel\PersonnelController::class, 'store']);
        Route::get('/search', [\App\Http\Controllers\Api\Personnel\PersonnelController::class, 'search']);
        Route::get('/statistics', [\App\Http\Controllers\Api\Personnel\PersonnelController::class, 'statistics']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Personnel\PersonnelController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Personnel\PersonnelController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\Personnel\PersonnelController::class, 'destroy']);
        
        // ERP Personnel specific
        Route::get('/erp/{id}', [\App\Http\Controllers\Api\Personnel\ErpPersonnelController::class, 'show']);
        Route::put('/erp/{id}', [\App\Http\Controllers\Api\Personnel\ErpPersonnelController::class, 'update']);
        Route::get('/mrid/{mrid}', [\App\Http\Controllers\Api\Personnel\ErpPersonnelController::class, 'findByMrid']);
    });

    // Organisations routes
    Route::prefix('organisations')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Organisations\OrganisationController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Organisations\OrganisationController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Organisations\OrganisationController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Organisations\OrganisationController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\Organisations\OrganisationController::class, 'destroy']);
    });

    // Locations routes
    Route::prefix('locations')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Locations\LocationController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Locations\LocationController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Locations\LocationController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Locations\LocationController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\Locations\LocationController::class, 'destroy']);
    });

    // Vehicles routes
    Route::prefix('vehicles')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Vehicles\VehicleController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\Vehicles\VehicleController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Vehicles\VehicleController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\Api\Vehicles\VehicleController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\Vehicles\VehicleController::class, 'destroy']);
    });

    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});