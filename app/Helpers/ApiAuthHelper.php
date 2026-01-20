<?php

namespace App\Helpers;

use App\Models\ApiKey;

class ApiAuthHelper
{
    /**
     * Validate API key from request.
     */
    public static function validateApiKey($request): array
    {
        // Get API key from header or query parameter
        $apiKey = $request->header('X-API-Key') ?: $request->query('api_key');

        // Check if API key is provided
        if (!$apiKey) {
            return [
                'success' => false,
                'message' => 'API key is required. Please provide X-API-Key header or api_key query parameter.',
                'error_code' => 'MISSING_API_KEY',
                'status_code' => 401
            ];
        }

        // Find API key in database
        $keyRecord = ApiKey::findByKey($apiKey);

        // Check if API key exists
        if (!$keyRecord) {
            return [
                'success' => false,
                'message' => 'Invalid API key.',
                'error_code' => 'INVALID_API_KEY',
                'status_code' => 401
            ];
        }

        // Check if API key is valid
        if (!$keyRecord->isValid()) {
            return [
                'success' => false,
                'message' => 'API key is not active or has expired.',
                'error_code' => 'INACTIVE_API_KEY',
                'status_code' => 401
            ];
        }

        // Mark API key as used
        $keyRecord->markAsUsed();

        return [
            'success' => true,
            'message' => 'API key validated successfully.',
            'api_key' => $keyRecord,
            'status_code' => 200
        ];
    }

    /**
     * Simple function to check if request is authenticated.
     */
    public static function checkAuth($request): bool
    {
        $validation = self::validateApiKey($request);
        return $validation['success'];
    }
}