<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\V2\ErrorLogV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ErrorLogControllerV2 extends Controller
{
    /**
     * Receive error log from OSB
     */
    public function store(Request $request)
    {
        try {
            // Validate API key
            if ($request->header('X-API-Key') !== env('API_KEY_V2', 'cdmea_b5b76664f2cb972124cdc4bb45e3c092')) {
                return response()->json(['error' => 'Invalid API Key'], 401);
            }

            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required|string',
                'message_uid' => 'required|string',
                'component_name' => 'required|string',
                'bus_key_name' => 'nullable|string',
                'bus_key_value' => 'nullable|string',
                'error_message' => 'required|string',
                'error_details' => 'nullable|string',
                'source_timestamp' => 'nullable|date',
                'app_server_id' => 'nullable|string',
                'environment' => 'nullable|string',
                'raw_payload' => 'nullable|json'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $errorLog = ErrorLogV2::create($request->all());

            return response()->json([
                'success' => true,
                'id' => $errorLog->id
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to store error log',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get error logs
     */
    public function index(Request $request)
    {
        try {
            $query = ErrorLogV2::query();
            
            if ($request->has('transaction_id')) {
                $query->where('transaction_id', $request->transaction_id);
            }
            
            $logs = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 50));
            
            return response()->json($logs);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}