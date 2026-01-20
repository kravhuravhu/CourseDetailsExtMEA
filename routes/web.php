<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Personnel\ErpPersonController;
use App\Http\Controllers\Api\Personnel\SkillController;
use App\Http\Controllers\Api\Organisation\ErpOrganisationController;
use App\Http\Controllers\Api\Location\LocationController;
use App\Http\Controllers\Api\Vehicle\VehicleController;
use App\Http\Controllers\Api\Audit\AuditLogController;
use App\Http\Controllers\Api\SimpleApiKeyController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api-auth-test', function () {
    return view('simple-auth');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'service' => 'CourseDetailsExtMEA API',
        'version' => '1.0.0',
        'note' => 'For API access, use /api/* endpoints with X-API-Key header',
        'default_test_key' => 'test-api-key-123',
    ]);
});

// PUBLIC API ROUTES (No auth)
Route::prefix('api')->group(function () {
    // API Documentation
    Route::get('/docs', function () {
        return response()->json([
            'api' => 'CourseDetailsExtMEA REST API',
            'version' => '1.0.0',
            'authentication' => 'API Key required in X-API-Key header or api_key query parameter',
            'default_test_key' => 'test-api-key-123',
            'endpoints' => [
                'personnel' => [
                    'GET /api/personnel' => 'List all personnel',
                    'POST /api/personnel' => 'Create new personnel',
                    'GET /api/personnel/{id}' => 'Get personnel details',
                    'PUT /api/personnel/{id}' => 'Update personnel',
                    'DELETE /api/personnel/{id}' => 'Delete personnel',
                ],
                'organisations' => 'GET /api/organisations',
                'locations' => 'GET /api/locations',
                'vehicles' => 'GET /api/vehicles',
                'audit' => 'GET /api/audit/logs',
                'api_keys' => [
                    'POST /api/generate-key' => 'Generate new API key',
                    'GET /api/validate-key' => 'Validate API key',
                    'GET /api/list-keys' => 'List all API keys',
                ],
            ],
        ]);
    });
    
    // API Key Management (Public - anyone can generate/validate keys)
    Route::post('/generate-key', [SimpleApiKeyController::class, 'generateKey']);
    Route::get('/validate-key', [SimpleApiKeyController::class, 'validateKey']);
    Route::get('/list-keys', [SimpleApiKeyController::class, 'listKeys']);
});

// PROTECTED API ROUTES (Auth)
Route::prefix('api')->group(function () {
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