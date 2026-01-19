<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\AuditService;

class BaseController extends Controller
{
    protected $auditService;

    public function __construct()
    {
        $this->auditService = new AuditService();
    }

    /**
     * Success response method.
     */
    protected function sendResponse($data, $message = '', $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * Error response method.
     */
    protected function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * Log audit entry for API calls.
     */
    protected function logAudit($action, $description, $data = null)
    {
        $this->auditService->logAudit([
            'transaction_id' => request()->header('X-Transaction-ID') ?? uniqid('TXN_', true),
            'component_name' => 'CourseDetailsExtMEA-API',
            'description' => "API {$action}: {$description}",
            'audit_type' => 'API_CALL',
            'source_timestamp' => now(),
        ]);
    }

    /**
     * Log error for API calls.
     */
    protected function logError($description, $exception = null, $criticality = 'Medium')
    {
        $this->auditService->logError([
            'transaction_id' => request()->header('X-Transaction-ID') ?? uniqid('TXN_', true),
            'component_name' => 'CourseDetailsExtMEA-API',
            'description' => $description,
            'exception' => $exception ? $exception->getMessage() : null,
            'criticality' => $criticality,
            'category' => 'APIError',
            'source_timestamp' => now(),
        ]);
    }
}