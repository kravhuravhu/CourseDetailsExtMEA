<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;

class SimpleApiAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Get API key from header or query parameter
        $apiKey = $request->header('X-API-Key') ?: $request->query('api_key');

        // Check if API key is provided
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required. Please provide X-API-Key header or api_key query parameter.',
                'error_code' => 'MISSING_API_KEY'
            ], 401);
        }

        // Find API key in database
        $keyRecord = ApiKey::findByKey($apiKey);

        // Check if API key exists
        if (!$keyRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key.',
                'error_code' => 'INVALID_API_KEY'
            ], 401);
        }

        // Check if API key is valid
        if (!$keyRecord->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'API key is not active or has expired.',
                'error_code' => 'INACTIVE_API_KEY'
            ], 401);
        }

        // Mark API key as used
        $keyRecord->markAsUsed();

        // Add API key info to request for logging
        $request->merge([
            'api_key_id' => $keyRecord->id,
            'api_key_name' => $keyRecord->name,
        ]);

        return $next($request);
    }
}