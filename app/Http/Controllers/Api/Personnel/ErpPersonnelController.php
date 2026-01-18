<?php

namespace App\Http\Controllers\Api\Personnel;

use App\Http\Controllers\Api\BaseController;
use App\Models\Personnel\ErpPersonnel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ErpPersonnelController extends BaseController
{
    /**
     * Get all erp personnel.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            
            $personnel = ErpPersonnel::with([
                'personnel',
                'erpPerson',
                'accessCards',
                'crafts',
                'competencies',
                'skills',
                'organisations',
                'locations'
            ])->paginate($perPage);

            return $this->sendResponse($personnel, 'ERP Personnel retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving ERP personnel: ' . $e->getMessage());
        }
    }

    /**
     * Get single erp personnel record.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $personnel = ErpPersonnel::with([
                'personnel',
                'erpPerson',
                'accessCards',
                'crafts',
                'competencies',
                'skills',
                'organisations',
                'locations'
            ])->find($id);

            if (is_null($personnel)) {
                return $this->sendError('ERP Personnel not found.');
            }

            return $this->sendResponse($personnel, 'ERP Personnel retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving ERP personnel: ' . $e->getMessage());
        }
    }

    /**
     * Update erp personnel record.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $personnel = ErpPersonnel::find($id);
            if (is_null($personnel)) {
                return $this->sendError('ERP Personnel not found.');
            }

            $validator = Validator::make($request->all(), [
                'administration_indicator' => 'nullable|boolean',
                'deemed_start_date_time' => 'nullable|date',
                'finish_date' => 'nullable|date',
                'start_date' => 'nullable|date',
                'key_person_indicator' => 'nullable|boolean',
                'overtime_eligible_indicator' => 'nullable|boolean',
                'transfer_benefits_payable_indicator' => 'nullable|boolean',
                'payment_method' => 'nullable|string|max:100',
                'responsibility' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator);
            }

            $personnel->update($request->all());

            return $this->sendResponse($personnel->load(['erpPerson']), 'ERP Personnel updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error updating ERP personnel: ' . $e->getMessage());
        }
    }

    /**
     * Get personnel by mRID.
     *
     * @param string $mrid
     * @return JsonResponse
     */
    public function findByMrid($mrid): JsonResponse
    {
        try {
            $personnel = ErpPersonnel::whereHas('erpPerson', function ($query) use ($mrid) {
                $query->where('mrid', $mrid);
            })->with(['personnel', 'erpPerson'])->first();

            if (is_null($personnel)) {
                return $this->sendError('ERP Personnel not found with mRID: ' . $mrid);
            }

            return $this->sendResponse($personnel, 'ERP Personnel retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving ERP personnel: ' . $e->getMessage());
        }
    }
}