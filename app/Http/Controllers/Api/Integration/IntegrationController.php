<?php

namespace App\Http\Controllers\Api\Integration;

use App\Http\Controllers\Api\BaseController;
use App\Models\Integration\IntegrationLog;
use App\Services\Integration\CourseDetailsProcessor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class IntegrationController extends BaseController
{
    protected $processor;

    public function __construct(CourseDetailsProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Receive course details from integration (AdHoc).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function receiveCourseDetailsAdHoc(Request $request): JsonResponse
    {
        return $this->processCourseDetails($request, 'adhoc');
    }

    /**
     * Receive course details from integration (TakeOn).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function receiveCourseDetailsTakeOn(Request $request): JsonResponse
    {
        return $this->processCourseDetails($request, 'takeon');
    }

    /**
     * Process course details from integration.
     *
     * @param Request $request
     * @param string $messageType
     * @return JsonResponse
     */
    private function processCourseDetails(Request $request, string $messageType): JsonResponse
    {
        try {
            // Log the incoming request
            $log = IntegrationLog::create([
                'message_type' => $messageType,
                'message_id' => $request->header('X-Message-ID', uniqid('msg_', true)),
                'status' => 'received',
                'source' => $request->header('X-Source', 'unknown'),
                'payload' => json_encode($request->all()),
            ]);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'header.message_id' => 'required|string',
                'header.noun' => 'required|string|in:PersonnelDetails',
                'header.verb' => 'required|string|in:publish',
                'header.source' => 'required|string',
                'payload.personnel' => 'required|array',
                'payload.organisations' => 'nullable|array',
                'payload.locations' => 'nullable|array',
                'payload.vehicle_asset_information' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                $log->update([
                    'status' => 'error',
                    'error_message' => 'Validation failed: ' . json_encode($validator->errors()->toArray()),
                ]);

                return $this->sendError(
                    'Validation failed.',
                    $validator->errors()->toArray(),
                    400
                );
            }

            // Process the data
            $log->update(['status' => 'processing']);

            $result = $this->processor->process($request->all(), $messageType);

            if ($result['success']) {
                $log->update([
                    'status' => 'success',
                    'processed_data' => json_encode($result['data']),
                    'processed_at' => now(),
                ]);

                return $this->sendResponse([
                    'message_id' => $request->input('header.message_id'),
                    'status' => 'success',
                    'processed_records' => $result['data']['processed_records'] ?? 0,
                    'message' => 'Course details processed successfully.'
                ], 'Course details processed successfully.', 201);
            } else {
                $log->update([
                    'status' => 'error',
                    'error_message' => $result['error'],
                ]);

                return $this->sendError(
                    'Processing failed: ' . $result['error'],
                    $result['errors'] ?? [],
                    422
                );
            }
        } catch (\Exception $e) {
            Log::error('Integration processing error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            if (isset($log)) {
                $log->update([
                    'status' => 'error',
                    'error_message' => 'Server error: ' . $e->getMessage(),
                ]);
            }

            return $this->sendError(
                'Server error processing course details.',
                ['error' => $e->getMessage()],
                500
            );
        }
    }

    /**
     * Get integration status.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStatus(Request $request): JsonResponse
    {
        try {
            $status = [
                'system_status' => 'operational',
                'last_message_received' => IntegrationLog::latest()->first()->created_at ?? null,
                'total_messages_received' => IntegrationLog::count(),
                'successful_messages' => IntegrationLog::where('status', 'success')->count(),
                'failed_messages' => IntegrationLog::where('status', 'error')->count(),
                'pending_messages' => IntegrationLog::where('status', 'processing')->count(),
                'recent_messages' => IntegrationLog::orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(['message_type', 'status', 'source', 'created_at'])
            ];

            return $this->sendResponse($status, 'Integration status retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving integration status: ' . $e->getMessage());
        }
    }

    /**
     * Get integration logs.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLogs(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'nullable|string|in:received,processing,success,error',
                'message_type' => 'nullable|string|in:adhoc,takeon',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return $this->sendValidationError($validator);
            }

            $query = IntegrationLog::query();

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('message_type')) {
                $query->where('message_type', $request->input('message_type'));
            }

            if ($request->has('start_date')) {
                $query->where('created_at', '>=', $request->input('start_date'));
            }

            if ($request->has('end_date')) {
                $query->where('created_at', '<=', $request->input('end_date') . ' 23:59:59');
            }

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('message_id', 'like', "%{$search}%")
                      ->orWhere('source', 'like', "%{$search}%")
                      ->orWhere('error_message', 'like', "%{$search}%");
                });
            }

            $perPage = $request->input('per_page', 20);
            $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return $this->sendResponse($logs, 'Integration logs retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving integration logs: ' . $e->getMessage());
        }
    }

    /**
     * Retry failed integration message.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function retryMessage($id): JsonResponse
    {
        try {
            $log = IntegrationLog::find($id);

            if (is_null($log)) {
                return $this->sendError('Integration log not found.');
            }

            if ($log->status !== 'error') {
                return $this->sendError('Only failed messages can be retried.');
            }

            // Parse the payload and retry processing
            $payload = json_decode($log->payload, true);
            
            $log->update([
                'status' => 'processing',
                'retry_count' => $log->retry_count + 1,
                'error_message' => null,
            ]);

            // Process the data again
            $result = $this->processor->process($payload, $log->message_type);

            if ($result['success']) {
                $log->update([
                    'status' => 'success',
                    'processed_data' => json_encode($result['data']),
                    'processed_at' => now(),
                ]);

                return $this->sendResponse([
                    'message' => 'Message retried successfully.',
                    'new_status' => 'success'
                ], 'Message retried successfully.');
            } else {
                $log->update([
                    'status' => 'error',
                    'error_message' => $result['error'],
                ]);

                return $this->sendError('Retry failed: ' . $result['error']);
            }
        } catch (\Exception $e) {
            return $this->sendError('Error retrying message: ' . $e->getMessage());
        }
    }
}