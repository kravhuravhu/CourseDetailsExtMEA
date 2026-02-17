<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\V2\PersonnelDataV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PersonnelDataControllerV2 extends Controller
{
    /**
     * GET endpoint - Retrieve personnel data
     */
    public function index(Request $request)
    {
        try {
            $query = PersonnelDataV2::query();
            
            if ($request->has('mrid')) {
                $query->where('mrid', $request->mrid);
            }
            
            $data = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 50));
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET single record
     */
    public function show($id)
    {
        try {
            $personnel = PersonnelDataV2::find($id);
            
            if (!$personnel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $personnel
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST endpoint - Receive personnel data from OSB
     * (OSB sends this after transformation)
     */
    public function store(Request $request)
    {
        try {
            // Validate API key
            if ($request->header('X-API-Key') !== env('API_KEY_V2', 'cdmea_b5b76664f2cb972124cdc4bb45e3c092')) {
                return response()->json(['error' => 'Invalid API Key'], 401);
            }

            $validator = Validator::make($request->all(), [
                'mrid' => 'required|string',
                'first_name' => 'nullable|string',
                'initials' => 'nullable|string',
                'last_name' => 'nullable|string',
                'nickname' => 'nullable|string',
                'skill_description' => 'nullable|string',
                'skill_status' => 'nullable|string',
                'to_document_roles_mrid' => 'nullable|string',
                'certified_date' => 'nullable|date',
                'source_system' => 'nullable|string',
                'message_id' => 'nullable|string',
                'original_event_datetime' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Add raw payload for reference
            $data = $request->all();
            $data['raw_payload'] = $request->all();
            
            $personnel = PersonnelDataV2::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Personnel data stored successfully',
                'data' => [
                    'id' => $personnel->id,
                    'mrid' => $personnel->mrid
                ]
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to store personnel data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}