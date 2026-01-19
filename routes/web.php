<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Personnel\ErpPersonController;
use App\Http\Controllers\Api\Personnel\SkillController;
use App\Http\Controllers\Api\Organisation\ErpOrganisationController;
use App\Http\Controllers\Api\Location\LocationController;
use App\Http\Controllers\Api\Vehicle\VehicleController;
use App\Http\Controllers\Api\Audit\AuditLogController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'service' => 'CourseDetailsExtMEA API',
            'version' => '1.0.0',
            'database' => DB::connection()->getDatabaseName(),
        ]);
    });

    // API Documentation
    Route::get('/docs', function () {
        return response()->json([
            'api' => 'CourseDetailsExtMEA REST API',
            'version' => '1.0.0',
            'endpoints' => [
                'personnel' => [
                    'GET /api/personnel' => 'List all personnel',
                    'POST /api/personnel' => 'Create new personnel',
                    'GET /api/personnel/{id}' => 'Get personnel details',
                    'PUT /api/personnel/{id}' => 'Update personnel',
                    'DELETE /api/personnel/{id}' => 'Delete personnel',
                    'GET /api/personnel/mrid/{mrid}' => 'Get personnel by MRID',
                    'GET /api/personnel/{id}/full' => 'Get personnel full details',
                ],
                'organisations' => [
                    'GET /api/organisations' => 'List all organisations',
                    'POST /api/organisations' => 'Create new organisation',
                    'GET /api/organisations/{id}' => 'Get organisation details',
                    'PUT /api/organisations/{id}' => 'Update organisation',
                    'DELETE /api/organisations/{id}' => 'Delete organisation',
                ],
                'locations' => [
                    'GET /api/locations' => 'List all locations',
                    'POST /api/locations' => 'Create new location',
                    'GET /api/locations/{id}' => 'Get location details',
                    'PUT /api/locations/{id}' => 'Update location',
                    'DELETE /api/locations/{id}' => 'Delete location',
                ],
                'vehicles' => [
                    'GET /api/vehicles' => 'List all vehicles',
                    'POST /api/vehicles' => 'Create new vehicle',
                    'GET /api/vehicles/{id}' => 'Get vehicle details',
                    'PUT /api/vehicles/{id}' => 'Update vehicle',
                    'DELETE /api/vehicles/{id}' => 'Delete vehicle',
                ],
                'audit' => [
                    'GET /api/audit/logs' => 'Get audit logs',
                    'GET /api/audit/errors' => 'Get error logs',
                ],
            ],
        ]);
    });

    // Personnel Routes
    Route::prefix('personnel')->group(function () {
        Route::get('/', [ErpPersonController::class, 'index']);
        Route::post('/', [ErpPersonController::class, 'store']);
        Route::get('/{id}', [ErpPersonController::class, 'show']);
        Route::put('/{id}', [ErpPersonController::class, 'update']);
        Route::delete('/{id}', [ErpPersonController::class, 'destroy']);
        Route::get('/mrid/{mrid}', [ErpPersonController::class, 'showByMrid']);
        Route::get('/{id}/full', [ErpPersonController::class, 'getFullDetails']);
        
        // Skills sub-routes
        Route::get('/{personnelId}/skills', [SkillController::class, 'index']);
        Route::post('/{personnelId}/skills', [SkillController::class, 'store']);
        Route::delete('/{personnelId}/skills/{skillId}', [SkillController::class, 'destroy']);
    });

    // Organisation Routes
    Route::prefix('organisations')->group(function () {
        Route::get('/', [ErpOrganisationController::class, 'index']);
        Route::post('/', [ErpOrganisationController::class, 'store']);
        Route::get('/{id}', [ErpOrganisationController::class, 'show']);
        Route::put('/{id}', [ErpOrganisationController::class, 'update']);
        Route::delete('/{id}', [ErpOrganisationController::class, 'destroy']);
    });

    // Location Routes
    Route::prefix('locations')->group(function () {
        Route::get('/', [LocationController::class, 'index']);
        Route::post('/', [LocationController::class, 'store']);
        Route::get('/{id}', [LocationController::class, 'show']);
        Route::put('/{id}', [LocationController::class, 'update']);
        Route::delete('/{id}', [LocationController::class, 'destroy']);
    });

    // Vehicle Routes
    Route::prefix('vehicles')->group(function () {
        Route::get('/', [VehicleController::class, 'index']);
        Route::post('/', [VehicleController::class, 'store']);
        Route::get('/{id}', [VehicleController::class, 'show']);
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::delete('/{id}', [VehicleController::class, 'destroy']);
    });

    // Audit Routes
    Route::prefix('audit')->group(function () {
        Route::get('/logs', [AuditLogController::class, 'index']);
        Route::get('/errors', [AuditLogController::class, 'errorLogs']);
    });
});