<?php

namespace App\Http\Controllers\Api\Organisation;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Organisation\CreateErpOrganisationRequest;
use App\Models\Organisation\ErpOrganisation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use App\Models\ApiKey;

class ErpOrganisationController extends BaseController
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
     * Display a listing of ERP Organisations.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ErpOrganisation::query();

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
                $validRelations = ['customerData', 'supplierData', 'electronicAddresses', 'telephoneNumbers', 'personnel', 'locations'];
                $with = array_intersect($with, $validRelations);
            }

            if (!empty($with)) {
                $query->with($with);
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $organisations = $query->paginate($perPage);

            $this->logAudit('LIST', 'Retrieved list of ERP Organisations');

            return $this->sendResponse([
                'organisations' => $organisations->items(),
                'pagination' => [
                    'total' => $organisations->total(),
                    'per_page' => $organisations->perPage(),
                    'current_page' => $organisations->currentPage(),
                    'last_page' => $organisations->lastPage(),
                ]
            ], 'ERP Organisations retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve ERP Organisations', $e);
            return $this->sendError('Error retrieving ERP Organisations.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created ERP Organisation.
     */
    public function store(CreateErpOrganisationRequest $request): JsonResponse
    {
        try {
            $organisation = ErpOrganisation::create($request->validated());

            // Create customer data if provided
            if ($request->has('customer_data')) {
                $customerData = $request->get('customer_data');
                $organisation->customerData()->create($customerData);
            }

            // Create supplier data if provided
            if ($request->has('supplier_data')) {
                $supplierData = $request->get('supplier_data');
                $organisation->supplierData()->create($supplierData);
            }

            $this->logAudit('CREATE', "Created ERP Organisation with MRID: {$organisation->MRID}");

            return $this->sendResponse($organisation->load(['customerData', 'supplierData']), 
                'ERP Organisation created successfully.', 201);

        } catch (Exception $e) {
            $this->logError('Failed to create ERP Organisation', $e, 'High');
            return $this->sendError('Error creating ERP Organisation.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified ERP Organisation.
     */
    public function show($id): JsonResponse
    {
        try {
            $organisation = ErpOrganisation::with([
                'customerData',
                'supplierData',
                'electronicAddresses',
                'telephoneNumbers',
                'personnel',
                'locations'
            ])->find($id);

            if (!$organisation) {
                return $this->sendError('ERP Organisation not found.', [], 404);
            }

            $this->logAudit('VIEW', "Viewed ERP Organisation with ID: {$id}");

            return $this->sendResponse($organisation, 'ERP Organisation retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve ERP Organisation', $e);
            return $this->sendError('Error retrieving ERP Organisation.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified ERP Organisation.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $organisation = ErpOrganisation::find($id);

            if (!$organisation) {
                return $this->sendError('ERP Organisation not found.', [], 404);
            }

            $request->validate([
                'NAME' => 'sometimes|required|string|max:200',
                'CATEGORY' => 'nullable|string|max:100',
                'CODE' => 'nullable|string|max:100',
                'COMPANY_REGISTRATION_NO' => 'nullable|string|max:100',
                'IS_COST_CENTER' => 'nullable|boolean',
                'IS_PROFIT_CENTER' => 'nullable|boolean',
            ]);

            $organisation->update($request->all());

            // Update customer data if provided
            if ($request->has('customer_data')) {
                $customerData = $request->get('customer_data');
                if ($organisation->customerData) {
                    $organisation->customerData()->update($customerData);
                } else {
                    $organisation->customerData()->create($customerData);
                }
            }

            $this->logAudit('UPDATE', "Updated ERP Organisation with ID: {$id}");

            return $this->sendResponse($organisation->fresh(), 'ERP Organisation updated successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to update ERP Organisation', $e, 'High');
            return $this->sendError('Error updating ERP Organisation.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified ERP Organisation.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $organisation = ErpOrganisation::find($id);

            if (!$organisation) {
                return $this->sendError('ERP Organisation not found.', [], 404);
            }

            $organisation->delete();

            $this->logAudit('DELETE', "Deleted ERP Organisation with ID: {$id}");

            return $this->sendResponse([], 'ERP Organisation deleted successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to delete ERP Organisation', $e, 'High');
            return $this->sendError('Error deleting ERP Organisation.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get organisation personnel.
     */
    public function getPersonnel($id): JsonResponse
    {
        try {
            $organisation = ErpOrganisation::with('personnel.erpPersonnel.erpPerson')->find($id);

            if (!$organisation) {
                return $this->sendError('ERP Organisation not found.', [], 404);
            }

            $personnel = $organisation->personnel->map(function ($person) {
                return [
                    'personnel_id' => $person->PERSONNEL_ID,
                    'person' => $person->erpPersonnel->erpPerson ?? null,
                    'relationship_type' => $person->pivot->RELATIONSHIP_TYPE ?? null,
                ];
            });

            return $this->sendResponse($personnel, 'Organisation personnel retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve organisation personnel', $e);
            return $this->sendError('Error retrieving organisation personnel.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get organisation locations.
     */
    public function getLocations($id): JsonResponse
    {
        try {
            $organisation = ErpOrganisation::with('locations')->find($id);

            if (!$organisation) {
                return $this->sendError('ERP Organisation not found.', [], 404);
            }

            return $this->sendResponse($organisation->locations, 'Organisation locations retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve organisation locations', $e);
            return $this->sendError('Error retrieving organisation locations.', ['error' => $e->getMessage()], 500);
        }
    }
}