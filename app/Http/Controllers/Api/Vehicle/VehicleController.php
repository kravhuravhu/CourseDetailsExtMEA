<?php

namespace App\Http\Controllers\Api\Vehicle;

use App\Http\Controllers\Api\BaseController;
use App\Models\Vehicle\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Models\ApiKey;

class VehicleController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->checkApiKey();
    }
    
    /**
     * Check API key authentication.
     */
    private function checkApiKey(): void
    {
        $apiKey = request()->header('X-API-Key') ?: request()->query('api_key');
        
        if (!$apiKey) {
            abort(401, 'API key is required. Please provide X-API-Key header or api_key query parameter.');
        }
        
        $keyRecord = ApiKey::findByKey($apiKey);
        
        if (!$keyRecord) {
            abort(401, 'Invalid API key.');
        }
        
        if (!$keyRecord->isValid()) {
            abort(401, 'API key is not active or has expired.');
        }
        
        // Mark as used
        $keyRecord->markAsUsed();
        
        // Store in request for logging
        request()->merge([
            'api_key_id' => $keyRecord->id,
            'api_key_name' => $keyRecord->name,
        ]);
        
        $this->apiKeyId = $keyRecord->id;
        $this->apiKeyName = $keyRecord->name;
    }
    
    /**
     * Display a listing of Vehicles.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Vehicle::query();

            // Search filters
            if ($request->has('search')) {
                $query->search($request->get('search'));
            }

            if ($request->has('mrid')) {
                $query->byMrid($request->get('mrid'));
            }

            if ($request->has('category')) {
                $query->where('CATEGORY', $request->get('category'));
            }

            if ($request->has('vehicle_make')) {
                $query->where('VEHICLE_MAKE', 'LIKE', "%{$request->get('vehicle_make')}%");
            }

            if ($request->has('year_from')) {
                $query->where('YEAR', '>=', $request->get('year_from'));
            }

            if ($request->has('year_to')) {
                $query->where('YEAR', '<=', $request->get('year_to'));
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $vehicles = $query->paginate($perPage);

            $this->logAudit('LIST', 'Retrieved list of Vehicles');

            return $this->sendResponse([
                'vehicles' => $vehicles->items(),
                'pagination' => [
                    'total' => $vehicles->total(),
                    'per_page' => $vehicles->perPage(),
                    'current_page' => $vehicles->currentPage(),
                    'last_page' => $vehicles->lastPage(),
                ]
            ], 'Vehicles retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve Vehicles', $e);
            return $this->sendError('Error retrieving Vehicles.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created Vehicle.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'MRID' => 'required|string|max:100|unique:VEHICLE,MRID',
                'NAME' => 'required|string|max:200',
                'VEHICLE_MAKE' => 'required|string|max:100',
                'VEHICLE_MODEL' => 'required|string|max:100',
                'YEAR' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'CATEGORY' => 'nullable|string|max:100',
                'FUEL_TYPE' => 'nullable|string|max:50',
                'SERIAL_NUMBER' => 'nullable|string|max:100',
                'ODOMETER_READING' => 'nullable|numeric|min:0',
            ]);

            $vehicle = Vehicle::create($request->all());

            $this->logAudit('CREATE', "Created Vehicle with MRID: {$vehicle->MRID}");

            return $this->sendResponse($vehicle, 'Vehicle created successfully.', 201);

        } catch (Exception $e) {
            $this->logError('Failed to create Vehicle', $e, 'High');
            return $this->sendError('Error creating Vehicle.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified Vehicle.
     */
    public function show($id): JsonResponse
    {
        try {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return $this->sendError('Vehicle not found.', [], 404);
            }

            $this->logAudit('VIEW', "Viewed Vehicle with ID: {$id}");

            return $this->sendResponse([
                'vehicle' => $vehicle,
                'display_name' => $vehicle->display_name,
            ], 'Vehicle retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve Vehicle', $e);
            return $this->sendError('Error retrieving Vehicle.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified Vehicle.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return $this->sendError('Vehicle not found.', [], 404);
            }

            $request->validate([
                'NAME' => 'sometimes|required|string|max:200',
                'VEHICLE_MAKE' => 'sometimes|required|string|max:100',
                'VEHICLE_MODEL' => 'sometimes|required|string|max:100',
                'YEAR' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
                'ODOMETER_READING' => 'nullable|numeric|min:0',
                'FUEL_TYPE' => 'nullable|string|max:50',
            ]);

            $vehicle->update($request->all());

            $this->logAudit('UPDATE', "Updated Vehicle with ID: {$id}");

            return $this->sendResponse($vehicle->fresh(), 'Vehicle updated successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to update Vehicle', $e, 'High');
            return $this->sendError('Error updating Vehicle.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified Vehicle.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return $this->sendError('Vehicle not found.', [], 404);
            }

            $vehicle->delete();

            $this->logAudit('DELETE', "Deleted Vehicle with ID: {$id}");

            return $this->sendResponse([], 'Vehicle deleted successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to delete Vehicle', $e, 'High');
            return $this->sendError('Error deleting Vehicle.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update vehicle odometer.
     */
    public function updateOdometer(Request $request, $id): JsonResponse
    {
        try {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return $this->sendError('Vehicle not found.', [], 404);
            }

            $request->validate([
                'odometer_reading' => 'required|numeric|min:' . $vehicle->ODOMETER_READING,
                'odometer_unit' => 'nullable|string|max:20',
            ]);

            $vehicle->update([
                'ODOMETER_READING' => $request->odometer_reading,
                'ODOMETER_UNIT' => $request->odometer_unit ?? $vehicle->ODOMETER_UNIT,
            ]);

            $this->logAudit('UPDATE', "Updated odometer for Vehicle ID: {$id} to {$request->odometer_reading}");

            return $this->sendResponse($vehicle, 'Vehicle odometer updated successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to update vehicle odometer', $e);
            return $this->sendError('Error updating vehicle odometer.', ['error' => $e->getMessage()], 500);
        }
    }
}