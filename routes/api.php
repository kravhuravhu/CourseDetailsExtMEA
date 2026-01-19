<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Personnel\ErpPersonController;
use App\Http\Controllers\Api\Personnel\SkillController;
use App\Http\Controllers\Api\Organisation\ErpOrganisationController;
use App\Http\Controllers\Api\Location\LocationController;
use App\Http\Controllers\Api\Vehicle\VehicleController;
use App\Http\Controllers\Api\Audit\AuditLogController;

Route::middleware(['api'])->group(function () {
    
    // Health check endpoint
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'service' => 'CourseDetailsExtMEA API',
            'version' => '1.0.0',
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
                    'POST /api/personnel/{id}/link-organisation' => 'Link organisation',
                    'POST /api/personnel/{id}/unlink-organisation' => 'Unlink organisation',
                ],
                'organisations' => [
                    'GET /api/organisations' => 'List all organisations',
                    'POST /api/organisations' => 'Create new organisation',
                    'GET /api/organisations/{id}' => 'Get organisation details',
                    'PUT /api/organisations/{id}' => 'Update organisation',
                    'DELETE /api/organisations/{id}' => 'Delete organisation',
                    'GET /api/organisations/{id}/personnel' => 'Get organisation personnel',
                    'GET /api/organisations/{id}/locations' => 'Get organisation locations',
                ],
                'locations' => [
                    'GET /api/locations' => 'List all locations',
                    'POST /api/locations' => 'Create new location',
                    'GET /api/locations/{id}' => 'Get location details',
                    'PUT /api/locations/{id}' => 'Update location',
                    'DELETE /api/locations/{id}' => 'Delete location',
                    'GET /api/locations/{id}/address' => 'Get location address',
                    'GET /api/locations/{id}/personnel' => 'Get location personnel',
                ],
                'vehicles' => [
                    'GET /api/vehicles' => 'List all vehicles',
                    'POST /api/vehicles' => 'Create new vehicle',
                    'GET /api/vehicles/{id}' => 'Get vehicle details',
                    'PUT /api/vehicles/{id}' => 'Update vehicle',
                    'DELETE /api/vehicles/{id}' => 'Delete vehicle',
                    'PUT /api/vehicles/{id}/odometer' => 'Update vehicle odometer',
                ],
                'audit' => [
                    'GET /api/audit/logs' => 'Get audit logs',
                    'GET /api/audit/errors' => 'Get error logs',
                    'GET /api/audit/summary' => 'Get audit summary',
                ],
                'skills' => [
                    'GET /api/personnel/{personnelId}/skills' => 'Get personnel skills',
                    'POST /api/personnel/{personnelId}/skills' => 'Add skill',
                    'DELETE /api/personnel/{personnelId}/skills/{skillId}' => 'Remove skill',
                ],
            ],
            'authentication' => 'API Key required in X-API-Key header',
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
        Route::post('/{id}/link-organisation', [ErpPersonController::class, 'linkOrganisation']);
        Route::post('/{id}/unlink-organisation', [ErpPersonController::class, 'unlinkOrganisation']);
        
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
        Route::get('/{id}/personnel', [ErpOrganisationController::class, 'getPersonnel']);
        Route::get('/{id}/locations', [ErpOrganisationController::class, 'getLocations']);
    });

    // Location Routes
    Route::prefix('locations')->group(function () {
        Route::get('/', [LocationController::class, 'index']);
        Route::post('/', [LocationController::class, 'store']);
        Route::get('/{id}', [LocationController::class, 'show']);
        Route::put('/{id}', [LocationController::class, 'update']);
        Route::delete('/{id}', [LocationController::class, 'destroy']);
        Route::get('/{id}/address', [LocationController::class, 'getAddress']);
        Route::get('/{id}/personnel', [LocationController::class, 'getPersonnel']);
    });

    // Vehicle Routes
    Route::prefix('vehicles')->group(function () {
        Route::get('/', [VehicleController::class, 'index']);
        Route::post('/', [VehicleController::class, 'store']);
        Route::get('/{id}', [VehicleController::class, 'show']);
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::delete('/{id}', [VehicleController::class, 'destroy']);
        Route::put('/{id}/odometer', [VehicleController::class, 'updateOdometer']);
    });

    // Audit Routes
    Route::prefix('audit')->group(function () {
        Route::get('/logs', [AuditLogController::class, 'index']);
        Route::get('/errors', [AuditLogController::class, 'errorLogs']);
        Route::get('/summary', [AuditLogController::class, 'summary']);
    });
});