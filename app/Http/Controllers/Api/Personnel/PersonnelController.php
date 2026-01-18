<?php

namespace App\Http\Controllers\Api\Personnel;

use App\Http\Controllers\Api\BaseController;
use App\Models\Personnel\Personnel;
use App\Models\Personnel\ErpPersonnel;
use App\Models\Personnel\ErpPerson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PersonnelController extends BaseController
{
    /**
     * Get all personnel with pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            
            $personnel = Personnel::with([
                'erpPersonnel.erpPerson',
                'erpPersonnel.accessCards',
                'erpPersonnel.crafts',
                'erpPersonnel.competencies',
                'erpPersonnel.skills',
                'erpPersonnel.organisations',
                'erpPersonnel.locations'
            ])->paginate($perPage);

            return $this->sendResponse($personnel, 'Personnel retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving personnel: ' . $e->getMessage());
        }
    }

    /**
     * Get single personnel record.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $personnel = Personnel::with([
                'erpPersonnel.erpPerson',
                'erpPersonnel.accessCards',
                'erpPersonnel.crafts',
                'erpPersonnel.competencies',
                'erpPersonnel.skills',
                'erpPersonnel.organisations',
                'erpPersonnel.locations'
            ])->find($id);

            if (is_null($personnel)) {
                return $this->sendError('Personnel not found.');
            }

            return $this->sendResponse($personnel, 'Personnel retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving personnel: ' . $e->getMessage());
        }
    }

    /**
     * Create new personnel record.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Validate the request
            $validator = Validator::make($request->all(), [
                'personnel_data.administration_indicator' => 'nullable|boolean',
                'personnel_data.deemed_start_date_time' => 'nullable|date',
                'personnel_data.finish_date' => 'nullable|date',
                'personnel_data.start_date' => 'nullable|date',
                'personnel_data.key_person_indicator' => 'nullable|boolean',
                'personnel_data.overtime_eligible_indicator' => 'nullable|boolean',
                'personnel_data.transfer_benefits_payable_indicator' => 'nullable|boolean',
                
                'person_data.mrid' => 'required|string|max:255',
                'person_data.first_name' => 'nullable|string|max:100',
                'person_data.last_name' => 'nullable|string|max:100',
                'person_data.birth_date_time' => 'nullable|date',
                'person_data.gender' => 'nullable|string|max:50',
                'person_data.nationality' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return $this->sendValidationError($validator);
            }

            // Create Personnel
            $personnel = Personnel::create();

            // Create ErpPersonnel
            $erpPersonnelData = $request->input('personnel_data', []);
            $erpPersonnelData['personnel_id'] = $personnel->id;
            $erpPersonnel = ErpPersonnel::create($erpPersonnelData);

            // Create ErpPerson
            $personData = $request->input('person_data', []);
            $personData['erp_personnel_id'] = $erpPersonnel->id;
            $erpPerson = ErpPerson::create($personData);

            DB::commit();

            $responseData = [
                'personnel' => $personnel,
                'erp_personnel' => $erpPersonnel,
                'erp_person' => $erpPerson
            ];

            return $this->sendResponse($responseData, 'Personnel created successfully.', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error creating personnel: ' . $e->getMessage());
        }
    }

    /**
     * Update personnel record.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $personnel = Personnel::find($id);
            if (is_null($personnel)) {
                return $this->sendError('Personnel not found.');
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'personnel_data.administration_indicator' => 'nullable|boolean',
                'personnel_data.deemed_start_date_time' => 'nullable|date',
                'personnel_data.finish_date' => 'nullable|date',
                'personnel_data.start_date' => 'nullable|date',
                
                'person_data.first_name' => 'nullable|string|max:100',
                'person_data.last_name' => 'nullable|string|max:100',
                'person_data.birth_date_time' => 'nullable|date',
                'person_data.gender' => 'nullable|string|max:50',
                'person_data.nationality' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                DB::rollBack();
                return $this->sendValidationError($validator);
            }

            // Update ErpPersonnel if exists
            if ($personnel->erpPersonnel && $request->has('personnel_data')) {
                $personnel->erpPersonnel->update($request->input('personnel_data', []));
            }

            // Update ErpPerson if exists
            if ($personnel->erpPersonnel && $personnel->erpPersonnel->erpPerson && $request->has('person_data')) {
                $personnel->erpPersonnel->erpPerson->update($request->input('person_data', []));
            }

            DB::commit();

            return $this->sendResponse($personnel->load(['erpPersonnel.erpPerson']), 'Personnel updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error updating personnel: ' . $e->getMessage());
        }
    }

    /**
     * Delete personnel record.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $personnel = Personnel::find($id);
            if (is_null($personnel)) {
                return $this->sendError('Personnel not found.');
            }

            $personnel->delete();

            DB::commit();

            return $this->sendResponse([], 'Personnel deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error deleting personnel: ' . $e->getMessage());
        }
    }

    /**
     * Search personnel by name, mrid, or other criteria.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
                'field' => 'nullable|string|in:mrid,first_name,last_name,email,phone'
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator);
            }

            $query = $request->input('query');
            $field = $request->input('field', 'name');

            $personnel = Personnel::whereHas('erpPersonnel.erpPerson', function ($q) use ($query, $field) {
                if ($field === 'mrid') {
                    $q->where('mrid', 'like', "%{$query}%");
                } elseif ($field === 'first_name') {
                    $q->where('first_name', 'like', "%{$query}%");
                } elseif ($field === 'last_name') {
                    $q->where('last_name', 'like', "%{$query}%");
                } else {
                    $q->where(function ($q2) use ($query) {
                        $q2->where('first_name', 'like', "%{$query}%")
                           ->orWhere('last_name', 'like', "%{$query}%")
                           ->orWhere('mrid', 'like', "%{$query}%");
                    });
                }
            })->with(['erpPersonnel.erpPerson'])
              ->paginate($request->input('per_page', 15));

            return $this->sendResponse($personnel, 'Search results retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error searching personnel: ' . $e->getMessage());
        }
    }

    /**
     * Get personnel statistics.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalPersonnel = Personnel::count();
            $activePersonnel = Personnel::whereHas('erpPersonnel', function ($q) {
                $q->whereNull('finish_date')
                  ->orWhere('finish_date', '>', now());
            })->count();

            $recentPersonnel = Personnel::where('created_at', '>=', now()->subDays(30))->count();

            $statistics = [
                'total_personnel' => $totalPersonnel,
                'active_personnel' => $activePersonnel,
                'recent_personnel' => $recentPersonnel,
                'inactive_personnel' => $totalPersonnel - $activePersonnel,
            ];

            return $this->sendResponse($statistics, 'Personnel statistics retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving statistics: ' . $e->getMessage());
        }
    }
}