<?php

namespace App\Http\Controllers\Api\Audit;

use App\Http\Controllers\Api\BaseController;
use App\Models\Audit\AuditLog;
use App\Models\Audit\ErrorLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class AuditLogController extends BaseController
{
    /**
     * Display audit logs.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = AuditLog::query();

            // Filters
            if ($request->has('transaction_id')) {
                $query->byTransactionId($request->get('transaction_id'));
            }

            if ($request->has('audit_type')) {
                $query->byType($request->get('audit_type'));
            }

            if ($request->has('component_name')) {
                $query->where('COMPONENT_NAME', 'LIKE', "%{$request->get('component_name')}%");
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->betweenDates($request->get('start_date'), $request->get('end_date'));
            }

            // Order by latest first
            $query->orderBy('CREATED_AT', 'desc');

            // Pagination
            $perPage = $request->get('per_page', 50);
            $logs = $query->paginate($perPage);

            return $this->sendResponse([
                'audit_logs' => $logs->items(),
                'pagination' => [
                    'total' => $logs->total(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                ]
            ], 'Audit logs retrieved successfully.');

        } catch (Exception $e) {
            return $this->sendError('Error retrieving audit logs.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display error logs.
     */
    public function errorLogs(Request $request): JsonResponse
    {
        try {
            $query = ErrorLog::query();

            // Filters
            if ($request->has('transaction_id')) {
                $query->byTransactionId($request->get('transaction_id'));
            }

            if ($request->has('criticality')) {
                $query->byCriticality($request->get('criticality'));
            }

            if ($request->has('category')) {
                $query->byCategory($request->get('category'));
            }

            if ($request->has('component_name')) {
                $query->where('COMPONENT_NAME', 'LIKE', "%{$request->get('component_name')}%");
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->betweenDates($request->get('start_date'), $request->get('end_date'));
            }

            // Order by latest first
            $query->orderBy('CREATED_AT', 'desc');

            // Pagination
            $perPage = $request->get('per_page', 50);
            $logs = $query->paginate($perPage);

            return $this->sendResponse([
                'error_logs' => $logs->items(),
                'pagination' => [
                    'total' => $logs->total(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                ]
            ], 'Error logs retrieved successfully.');

        } catch (Exception $e) {
            return $this->sendError('Error retrieving error logs.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get audit summary.
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'today'); // today, week, month, year

            switch ($period) {
                case 'week':
                    $startDate = now()->subWeek();
                    break;
                case 'month':
                    $startDate = now()->subMonth();
                    break;
                case 'year':
                    $startDate = now()->subYear();
                    break;
                default:
                    $startDate = now()->startOfDay();
            }

            $auditCount = AuditLog::where('CREATED_AT', '>=', $startDate)->count();
            $errorCount = ErrorLog::where('CREATED_AT', '>=', $startDate)->count();

            $auditByType = AuditLog::where('CREATED_AT', '>=', $startDate)
                ->selectRaw('AUDIT_TYPE, count(*) as count')
                ->groupBy('AUDIT_TYPE')
                ->get();

            $errorsByCriticality = ErrorLog::where('CREATED_AT', '>=', $startDate)
                ->selectRaw('CRITICALITY, count(*) as count')
                ->groupBy('CRITICALITY')
                ->get();

            return $this->sendResponse([
                'period' => $period,
                'start_date' => $startDate,
                'audit_count' => $auditCount,
                'error_count' => $errorCount,
                'audit_by_type' => $auditByType,
                'errors_by_criticality' => $errorsByCriticality,
            ], 'Audit summary retrieved successfully.');

        } catch (Exception $e) {
            return $this->sendError('Error retrieving audit summary.', ['error' => $e->getMessage()], 500);
        }
    }
}