<?php

namespace App\Services;

use App\Models\Audit\AuditLog;
use App\Models\Audit\ErrorLog;

class AuditService
{
    /**
     * Log audit entry
     */
    public function logAudit(array $data): AuditLog
    {
        return AuditLog::create([
            'TRANSACTION_ID' => $data['transaction_id'] ?? null,
            'COMPONENT_NAME' => $data['component_name'] ?? 'CourseDetailsExtMEA',
            'DESCRIPTION' => $data['description'] ?? '',
            'AUDIT_TYPE' => $data['audit_type'] ?? 'INFO',
            'SOURCE_TIMESTAMP' => $data['source_timestamp'] ?? now(),
            'MESSAGE_UID' => $data['message_uid'] ?? uniqid('MSG_', true),
            'APP_SERVER_ID' => $data['app_server_id'] ?? gethostname(),
            'ENVIRONMENT' => $data['environment'] ?? config('app.env'),
        ]);
    }

    /**
     * Log error entry
     */
    public function logError(array $data): ErrorLog
    {
        return ErrorLog::create([
            'TRANSACTION_ID' => $data['transaction_id'] ?? null,
            'COMPONENT_NAME' => $data['component_name'] ?? 'CourseDetailsExtMEA',
            'DESCRIPTION' => $data['description'] ?? '',
            'EXCEPTION' => $data['exception'] ?? '',
            'CRITICALITY' => $data['criticality'] ?? 'Medium',
            'CATEGORY' => $data['category'] ?? 'TechnicalError',
            'MESSAGE_UID' => $data['message_uid'] ?? uniqid('MSG_', true),
            'APP_SERVER_ID' => $data['app_server_id'] ?? gethostname(),
            'ENVIRONMENT' => $data['environment'] ?? config('app.env'),
            'SOURCE_TIMESTAMP' => $data['source_timestamp'] ?? now(),
        ]);
    }

    /**
     * Get audit logs by transaction ID
     */
    public function getAuditByTransaction(string $transactionId)
    {
        return AuditLog::byTransactionId($transactionId)->get();
    }

    /**
     * Get error logs by transaction ID
     */
    public function getErrorsByTransaction(string $transactionId)
    {
        return ErrorLog::byTransactionId($transactionId)->get();
    }
}