<?php

namespace App\Http\Controllers\Api\Location;

use App\Http\Controllers\Api\BaseController;
use App\Models\Location\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Models\ApiKey;

class LocationController extends BaseController
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
     * Display a listing of Locations.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Location::query();

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

            // Include related data
            $with = [];
            if ($request->has('with')) {
                $with = explode(',', $request->get('with'));
                $validRelations = ['postalAddress', 'streetAddress', 'personnel', 'organisations'];
                $with = array_intersect($with, $validRelations);
            }

            if (!empty($with)) {
                $query->with($with);
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $locations = $query->paginate($perPage);

            $this->logAudit('LIST', 'Retrieved list of Locations');

            return $this->sendResponse([
                'locations' => $locations->items(),
                'pagination' => [
                    'total' => $locations->total(),
                    'per_page' => $locations->perPage(),
                    'current_page' => $locations->currentPage(),
                    'last_page' => $locations->lastPage(),
                ]
            ], 'Locations retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve Locations', $e);
            return $this->sendError('Error retrieving Locations.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created Location.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'MRID' => 'required|string|max:100|unique:LOCATION,MRID',
                'NAME' => 'required|string|max:200',
                'CATEGORY' => 'nullable|string|max:100',
                'CODE' => 'nullable|string|max:100',
                'GEO_INFO_REFERENCE' => 'nullable|string|max:500',
                'IS_POLYGON' => 'nullable|boolean',
            ]);

            $location = Location::create($request->all());

            // Create postal address if provided
            if ($request->has('postal_address')) {
                $postalData = $request->get('postal_address');
                $location->postalAddress()->create($postalData);
            }

            // Create street address if provided
            if ($request->has('street_address')) {
                $streetData = $request->get('street_address');
                $location->streetAddress()->create($streetData);
            }

            $this->logAudit('CREATE', "Created Location with MRID: {$location->MRID}");

            return $this->sendResponse($location->load(['postalAddress', 'streetAddress']), 
                'Location created successfully.', 201);

        } catch (Exception $e) {
            $this->logError('Failed to create Location', $e, 'High');
            return $this->sendError('Error creating Location.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified Location.
     */
    public function show($id): JsonResponse
    {
        try {
            $location = Location::with([
                'postalAddress',
                'streetAddress',
                'personnel.erpPersonnel.erpPerson',
                'organisations'
            ])->find($id);

            if (!$location) {
                return $this->sendError('Location not found.', [], 404);
            }

            $this->logAudit('VIEW', "Viewed Location with ID: {$id}");

            return $this->sendResponse($location, 'Location retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve Location', $e);
            return $this->sendError('Error retrieving Location.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified Location.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $location = Location::find($id);

            if (!$location) {
                return $this->sendError('Location not found.', [], 404);
            }

            $request->validate([
                'NAME' => 'sometimes|required|string|max:200',
                'CATEGORY' => 'nullable|string|max:100',
                'CODE' => 'nullable|string|max:100',
                'GEO_INFO_REFERENCE' => 'nullable|string|max:500',
                'IS_POLYGON' => 'nullable|boolean',
            ]);

            $location->update($request->all());

            // Update postal address if provided
            if ($request->has('postal_address')) {
                $postalData = $request->get('postal_address');
                if ($location->postalAddress) {
                    $location->postalAddress()->update($postalData);
                } else {
                    $location->postalAddress()->create($postalData);
                }
            }

            $this->logAudit('UPDATE', "Updated Location with ID: {$id}");

            return $this->sendResponse($location->fresh(), 'Location updated successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to update Location', $e, 'High');
            return $this->sendError('Error updating Location.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified Location.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $location = Location::find($id);

            if (!$location) {
                return $this->sendError('Location not found.', [], 404);
            }

            $location->delete();

            $this->logAudit('DELETE', "Deleted Location with ID: {$id}");

            return $this->sendResponse([], 'Location deleted successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to delete Location', $e, 'High');
            return $this->sendError('Error deleting Location.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get full address for location.
     */
    public function getAddress($id): JsonResponse
    {
        try {
            $location = Location::with(['postalAddress', 'streetAddress'])->find($id);

            if (!$location) {
                return $this->sendError('Location not found.', [], 404);
            }

            $address = [
                'full_address' => $location->full_address,
                'postal_address' => $location->postalAddress,
                'street_address' => $location->streetAddress,
            ];

            return $this->sendResponse($address, 'Location address retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve location address', $e);
            return $this->sendError('Error retrieving location address.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get personnel at location.
     */
    public function getPersonnel($id): JsonResponse
    {
        try {
            $location = Location::with('personnel.erpPersonnel.erpPerson')->find($id);

            if (!$location) {
                return $this->sendError('Location not found.', [], 404);
            }

            $personnel = $location->personnel->map(function ($person) {
                return [
                    'personnel_id' => $person->PERSONNEL_ID,
                    'person' => $person->erpPersonnel->erpPerson ?? null,
                    'relationship_type' => $person->pivot->RELATIONSHIP_TYPE ?? null,
                ];
            });

            return $this->sendResponse($personnel, 'Location personnel retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve location personnel', $e);
            return $this->sendError('Error retrieving location personnel.', ['error' => $e->getMessage()], 500);
        }
    }
}