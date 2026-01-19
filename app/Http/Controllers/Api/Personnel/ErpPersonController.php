<?php

namespace App\Http\Controllers\Api\Personnel;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Personnel\CreateErpPersonRequest;
use App\Http\Requests\Api\Personnel\UpdateErpPersonRequest;
use App\Models\Personnel\Personnel;
use App\Models\Personnel\ErpPersonnel;
use App\Models\Personnel\ErpPerson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ErpPersonController extends BaseController
{
    /**
     * Display a listing of ERP Persons.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ErpPerson::query();

            // Search filters
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->searchByName($search);
            }

            if ($request->has('mrid')) {
                $query->byMrid($request->get('mrid'));
            }

            if ($request->has('category')) {
                $query->where('CATEGORY', $request->get('category'));
            }

            // Pagination
            $perPage = $request->get('per_page', 20);
            $persons = $query->with('erpPersonnel')->paginate($perPage);

            $this->logAudit('LIST', 'Retrieved list of ERP Persons');

            return $this->sendResponse([
                'persons' => $persons->items(),
                'pagination' => [
                    'total' => $persons->total(),
                    'per_page' => $persons->perPage(),
                    'current_page' => $persons->currentPage(),
                    'last_page' => $persons->lastPage(),
                ]
            ], 'ERP Persons retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve ERP Persons', $e);
            return $this->sendError('Error retrieving ERP Persons.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created ERP Person.
     */
    public function store(CreateErpPersonRequest $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            // Create personnel record
            $personnel = Personnel::create([]);
            
            // Create ERP personnel record
            $erpPersonnel = ErpPersonnel::create([
                'PERSONNEL_ID' => $personnel->PERSONNEL_ID,
                'JOB_TITLE' => $request->get('job_title'),
                'START_DATE' => $request->get('start_date'),
                'KEY_PERSON_INDICATOR' => $request->get('key_person_indicator', false),
            ]);
            
            // Create ERP person record
            $erpPerson = ErpPerson::create(array_merge(
                $request->validated(),
                ['ERP_PERSONNEL_ID' => $erpPersonnel->ERP_PERSONNEL_ID]
            ));

            DB::commit();

            $this->logAudit('CREATE', "Created ERP Person with MRID: {$erpPerson->MRID}");

            return $this->sendResponse([
                'person' => $erpPerson->load('erpPersonnel'),
                'personnel_id' => $personnel->PERSONNEL_ID,
            ], 'ERP Person created successfully.', 201);

        } catch (Exception $e) {
            DB::rollBack();
            $this->logError('Failed to create ERP Person', $e, 'High');
            return $this->sendError('Error creating ERP Person.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified ERP Person.
     */
    public function show($id): JsonResponse
    {
        try {
            $person = ErpPerson::with(['erpPersonnel.personnel'])->find($id);

            if (!$person) {
                return $this->sendError('ERP Person not found.', [], 404);
            }

            $this->logAudit('VIEW', "Viewed ERP Person with ID: {$id}");

            return $this->sendResponse([
                'person' => $person,
                'related_data' => [
                    'organisations' => $person->erpPersonnel->personnel->organisations ?? [],
                    'locations' => $person->erpPersonnel->personnel->locations ?? [],
                    'skills' => $person->erpPersonnel->skills ?? [],
                    'competencies' => $person->erpPersonnel->competencies ?? [],
                ]
            ], 'ERP Person retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve ERP Person', $e);
            return $this->sendError('Error retrieving ERP Person.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display ERP Person by MRID.
     */
    public function showByMrid($mrid): JsonResponse
    {
        try {
            $person = ErpPerson::with(['erpPersonnel.personnel'])->byMrid($mrid)->first();

            if (!$person) {
                return $this->sendError('ERP Person not found.', [], 404);
            }

            $this->logAudit('VIEW', "Viewed ERP Person with MRID: {$mrid}");

            return $this->sendResponse($person, 'ERP Person retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve ERP Person by MRID', $e);
            return $this->sendError('Error retrieving ERP Person.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified ERP Person.
     */
    public function update(UpdateErpPersonRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $person = ErpPerson::find($id);

            if (!$person) {
                return $this->sendError('ERP Person not found.', [], 404);
            }

            // Update ERP Person
            $person->update($request->validated());

            // Update ERP Personnel if provided
            if ($request->has('job_title') || $request->has('start_date')) {
                $person->erpPersonnel->update($request->only(['job_title', 'start_date', 'key_person_indicator']));
            }

            DB::commit();

            $this->logAudit('UPDATE', "Updated ERP Person with ID: {$id}");

            return $this->sendResponse($person->fresh(), 'ERP Person updated successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            $this->logError('Failed to update ERP Person', $e, 'High');
            return $this->sendError('Error updating ERP Person.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified ERP Person.
     */
    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $person = ErpPerson::find($id);

            if (!$person) {
                return $this->sendError('ERP Person not found.', [], 404);
            }

            $personnelId = $person->erpPersonnel->PERSONNEL_ID;
            
            // Delete will cascade through relationships due to foreign key constraints
            $person->erpPersonnel->personnel->delete();

            DB::commit();

            $this->logAudit('DELETE', "Deleted ERP Person with ID: {$id}");

            return $this->sendResponse([], 'ERP Person deleted successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            $this->logError('Failed to delete ERP Person', $e, 'High');
            return $this->sendError('Error deleting ERP Person.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get ERP Person with full details.
     */
    public function getFullDetails($id): JsonResponse
    {
        try {
            $person = ErpPerson::with([
                'erpPersonnel.personnel.organisations',
                'erpPersonnel.personnel.locations',
                'erpPersonnel.skills',
                'erpPersonnel.competencies',
                'erpPersonnel.accessCards.accessControlAreas',
                'erpPersonnel.employeeBenefits',
            ])->find($id);

            if (!$person) {
                return $this->sendError('ERP Person not found.', [], 404);
            }

            $this->logAudit('VIEW', "Viewed full details of ERP Person with ID: {$id}");

            return $this->sendResponse($person, 'ERP Person full details retrieved successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to retrieve ERP Person full details', $e);
            return $this->sendError('Error retrieving ERP Person details.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Link organisation to personnel.
     */
    public function linkOrganisation(Request $request, $personId): JsonResponse
    {
        try {
            $request->validate([
                'organisation_id' => 'required|exists:ERP_ORGANISATION,ERP_ORGANISATION_ID',
                'relationship_type' => 'required|string|max:100',
            ]);

            $person = Personnel::find($personId);
            
            if (!$person) {
                return $this->sendError('Personnel not found.', [], 404);
            }

            $person->organisations()->attach($request->organisation_id, [
                'RELATIONSHIP_TYPE' => $request->relationship_type
            ]);

            $this->logAudit('LINK', "Linked organisation to personnel ID: {$personId}");

            return $this->sendResponse([], 'Organisation linked successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to link organisation', $e);
            return $this->sendError('Error linking organisation.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Unlink organisation from personnel.
     */
    public function unlinkOrganisation(Request $request, $personId): JsonResponse
    {
        try {
            $request->validate([
                'organisation_id' => 'required|exists:ERP_ORGANISATION,ERP_ORGANISATION_ID',
            ]);

            $person = Personnel::find($personId);
            
            if (!$person) {
                return $this->sendError('Personnel not found.', [], 404);
            }

            $person->organisations()->detach($request->organisation_id);

            $this->logAudit('UNLINK', "Unlinked organisation from personnel ID: {$personId}");

            return $this->sendResponse([], 'Organisation unlinked successfully.');

        } catch (Exception $e) {
            $this->logError('Failed to unlink organisation', $e);
            return $this->sendError('Error unlinking organisation.', ['error' => $e->getMessage()], 500);
        }
    }
}