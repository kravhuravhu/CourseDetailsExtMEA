<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\ApiKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class SimpleApiKeyController extends BaseController
{
    /**
     * Generate a new API key.
     */
    public function generateKey(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            $apiKey = ApiKey::create([
                'name' => $request->name,
                'key' => ApiKey::generateKey(),
                'description' => $request->description,
                'is_active' => true,
            ]);

            $this->logAudit('CREATE', "Generated new API key: {$apiKey->name}");

            return $this->sendResponse([
                'key' => $apiKey->key,
                'name' => $apiKey->name,
                'created_at' => $apiKey->created_at,
            ], 'API key generated successfully.', 201);

        } catch (Exception $e) {
            $this->logError('Failed to generate API key', $e);
            return $this->sendError('Error generating API key.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Validate an API key.
     */
    public function validateKey(Request $request): JsonResponse
    {
        try {
            $apiKey = $request->header('X-API-Key') ?: $request->query('api_key');

            if (!$apiKey) {
                return $this->sendError('API key is required.', [], 400);
            }

            $keyRecord = ApiKey::findByKey($apiKey);

            if (!$keyRecord) {
                return $this->sendError('Invalid API key.', [], 401);
            }

            $isValid = $keyRecord->isValid();

            if ($isValid) {
                $keyRecord->markAsUsed();
            }

            return $this->sendResponse([
                'is_valid' => $isValid,
                'key_name' => $keyRecord->name,
                'is_active' => $keyRecord->is_active,
                'expires_at' => $keyRecord->expires_at,
                'last_used_at' => $keyRecord->last_used_at,
            ], $isValid ? 'API key is valid.' : 'API key is invalid.');

        } catch (Exception $e) {
            return $this->sendError('Error validating API key.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all API keys (simple view).
     */
    public function listKeys(Request $request): JsonResponse
    {
        try {
            $keys = ApiKey::all()->map(function ($key) {
                return [
                    'id' => $key->id,
                    'name' => $key->name,
                    'key_preview' => substr($key->key, 0, 8) . '...' . substr($key->key, -4),
                    'is_active' => $key->is_active,
                    'last_used_at' => $key->last_used_at,
                    'created_at' => $key->created_at,
                ];
            });

            return $this->sendResponse($keys, 'API keys retrieved successfully.');

        } catch (Exception $e) {
            return $this->sendError('Error retrieving API keys.', ['error' => $e->getMessage()], 500);
        }
    }
}