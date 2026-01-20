<?php

namespace App\Http\Controllers\Api\Integration;

use App\Http\Controllers\Api\BaseController;
use App\Models\Personnel\Personnel;
use App\Models\Personnel\ErpPersonnel;
use App\Models\Personnel\ErpPerson;
use App\Models\Organisation\ErpOrganisation;
use App\Models\Location\Location;
use App\Models\Vehicle\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class IntegrationController extends BaseController
{
    /**
     * Receive personnel data from OSB integration
     * This endpoint will be called by CourseDetailsMEAProvABCSREST
     */
    public function receivePersonnelData(Request $request): JsonResponse
    {
        DB::beginTransaction();
        
        try {
            $this->logAudit('INTEGRATION', 'Received personnel data from OSB');
            
            // Get transaction ID from headers for audit trail
            $transactionId = $request->header('X-Transaction-ID', uniqid('OSB_', true));
            $messageId = $request->header('X-Message-ID', uniqid('MSG_', true));
            
            // Log the raw request for debugging
            $personnel_data_ctype = $request->header('Content-Type') ?? 'unknown';
            Log::info('OSB Integration - Personnel Data Received', [
                'transaction_id' => $transactionId,
                'message_id' => $messageId,
                'headers' => $request->headers->all(),
                'payload_type' => $personnel_data_ctype,
            ]);
            
            // Get data based on content type
            if ($request->isJson()) {
                $data = $request->json()->all();
                $this->processJsonPersonnelData($data, $transactionId);
            } else {
                // Assume XML for SOAP/OSB integration
                $xmlData = $request->getContent();
                $this->processXmlPersonnelData($xmlData, $transactionId);
            }
            
            DB::commit();
            
            // Return success response in format expected by OSB
            return response()->json([
                'success' => true,
                'transaction_id' => $transactionId,
                'message_id' => $messageId,
                'timestamp' => now()->toISOString(),
                'message' => 'Personnel data processed successfully',
                'status' => 'PROCESSED'
            ], 201);
            
        } catch (Exception $e) {
            DB::rollBack();
            
            $this->logError('Failed to process personnel data from OSB', $e, 'High');
            
            // Return error response in format expected by OSB
            return response()->json([
                'success' => false,
                'transaction_id' => $transactionId ?? uniqid('ERR_', true),
                'timestamp' => now()->toISOString(),
                'error_code' => 'PROCESSING_ERROR',
                'error_message' => $e->getMessage(),
                'error_details' => 'Failed to process personnel data',
                'status' => 'ERROR'
            ], 500);
        }
    }
    
    /**
     * Process JSON personnel data
     */
    private function processJsonPersonnelData(array $data, string $transactionId): void
    {
        Log::info('Processing JSON personnel data', [
            'transaction_id' => $transactionId,
            'data_structure' => array_keys($data)
        ]);
        
        // if this is a batch or single record
        if (isset($data['PersonnelDetails']) && is_array($data['PersonnelDetails'])) {
            // Batch processing
            foreach ($data['PersonnelDetails'] as $personnelData) {
                $this->createOrUpdatePersonnel($personnelData, $transactionId);
            }
        } else {
            // Single record processing
            $this->createOrUpdatePersonnel($data, $transactionId);
        }
    }
    
    /**
     * Process XML personnel data (for SOAP/OSB)
     */
    private function processXmlPersonnelData(string $xmlData, string $transactionId): void
    {
        Log::info('Processing XML personnel data', [
            'transaction_id' => $transactionId,
            'xml_length' => strlen($xmlData)
        ]);
        
        try {
            // Convert XML to array for processing
            $xml = simplexml_load_string($xmlData);
            $json = json_encode($xml);
            $data = json_decode($json, true);
            
            // Process the data
            $this->processJsonPersonnelData($data, $transactionId);
            
        } catch (Exception $e) {
            throw new Exception('Failed to parse XML data: ' . $e->getMessage());
        }
    }
    
    /**
     * Create or update personnel record
     */
    private function createOrUpdatePersonnel(array $data, string $transactionId): void
    {
        try {
            $mrid = $data['MRID'] ?? $data['mrid'] ?? null;
            
            if (!$mrid) {
                throw new Exception('MRID is required for personnel record');
            }
            
            Log::info('Processing personnel record', [
                'transaction_id' => $transactionId,
                'mrid' => $mrid,
                'operation' => 'CREATE_OR_UPDATE'
            ]);
            
            $existingPerson = ErpPerson::byMrid($mrid)->first();
            
            if ($existingPerson) {
                // Update existing record
                $this->updatePersonnel($existingPerson, $data, $transactionId);
            } else {
                // Create new record
                $this->createPersonnel($data, $transactionId);
            }
            
        } catch (Exception $e) {
            Log::error('Failed to process personnel record', [
                'transaction_id' => $transactionId,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Create new personnel record
     */
    private function createPersonnel(array $data, string $transactionId): void
    {
        // Create personnel record
        $personnel = Personnel::create([]);
        
        // Create ERP personnel record
        $erpPersonnel = ErpPersonnel::create([
            'PERSONNEL_ID' => $personnel->PERSONNEL_ID,
            'JOB_TITLE' => $data['jobTitle'] ?? $data['JobTitle'] ?? null,
            'START_DATE' => $data['startDate'] ?? $data['StartDate'] ?? null,
            'FINISH_DATE' => $data['finishDate'] ?? $data['FinishDate'] ?? null,
            'KEY_PERSON_INDICATOR' => $data['keyPerson'] ?? $data['KeyPerson'] ?? false,
            'RESPONSIBILITY' => $data['responsibility'] ?? $data['Responsibility'] ?? null,
        ]);
        
        // Create ERP person record
        ErpPerson::create([
            'ERP_PERSONNEL_ID' => $erpPersonnel->ERP_PERSONNEL_ID,
            'MRID' => $data['MRID'] ?? $data['mrid'],
            'NAME' => $data['Name'] ?? $data['name'] ?? 'Unknown',
            'FIRST_NAME' => $data['firstName'] ?? $data['FirstName'] ?? null,
            'LAST_NAME' => $data['lastName'] ?? $data['LastName'] ?? null,
            'GENDER' => $data['gender'] ?? $data['Gender'] ?? null,
            'BIRTH_DATE_TIME' => $data['birthDate'] ?? $data['BirthDate'] ?? null,
            'NATIONALITY' => $data['nationality'] ?? $data['Nationality'] ?? null,
            'CATEGORY' => $data['category'] ?? $data['Category'] ?? null,
            'EMAIL' => $data['email'] ?? $data['Email'] ?? null,
        ]);
        
        Log::info('Created new personnel record', [
            'transaction_id' => $transactionId,
            'personnel_id' => $personnel->PERSONNEL_ID,
            'mrid' => $data['MRID'] ?? $data['mrid']
        ]);
    }
    
    /**
     * Update existing personnel record
     */
    private function updatePersonnel(ErpPerson $person, array $data, string $transactionId): void
    {
        // Update ERP person record
        $person->update([
            'NAME' => $data['Name'] ?? $data['name'] ?? $person->NAME,
            'FIRST_NAME' => $data['firstName'] ?? $data['FirstName'] ?? $person->FIRST_NAME,
            'LAST_NAME' => $data['lastName'] ?? $data['LastName'] ?? $person->LAST_NAME,
            'GENDER' => $data['gender'] ?? $data['Gender'] ?? $person->GENDER,
            'BIRTH_DATE_TIME' => $data['birthDate'] ?? $data['BirthDate'] ?? $person->BIRTH_DATE_TIME,
            'NATIONALITY' => $data['nationality'] ?? $data['Nationality'] ?? $person->NATIONALITY,
            'CATEGORY' => $data['category'] ?? $data['Category'] ?? $person->CATEGORY,
            'EMAIL' => $data['email'] ?? $data['Email'] ?? $person->EMAIL,
        ]);
        
        // Update ERP personnel if data provided
        if (isset($data['jobTitle']) || isset($data['JobTitle'])) {
            $person->erpPersonnel->update([
                'JOB_TITLE' => $data['jobTitle'] ?? $data['JobTitle'] ?? $person->erpPersonnel->JOB_TITLE,
                'START_DATE' => $data['startDate'] ?? $data['StartDate'] ?? $person->erpPersonnel->START_DATE,
                'FINISH_DATE' => $data['finishDate'] ?? $data['FinishDate'] ?? $person->erpPersonnel->FINISH_DATE,
                'KEY_PERSON_INDICATOR' => $data['keyPerson'] ?? $data['KeyPerson'] ?? $person->erpPersonnel->KEY_PERSON_INDICATOR,
            ]);
        }
        
        Log::info('Updated existing personnel record', [
            'transaction_id' => $transactionId,
            'personnel_id' => $person->ERP_PERSON_ID,
            'mrid' => $person->MRID
        ]);
    }
    
    /**
     * Health check endpoint for OSB integration
     */
    public function integrationHealth(Request $request): JsonResponse
    {
        try {
            $dbStatus = DB::connection()->getPdo() ? 'CONNECTED' : 'DISCONNECTED';
            $tableCounts = [
                'personnel' => Personnel::count(),
                'erp_persons' => ErpPerson::count(),
                'organisations' => ErpOrganisation::count(),
                'locations' => Location::count(),
                'vehicles' => Vehicle::count(),
            ];
            
            return response()->json([
                'status' => 'HEALTHY',
                'service' => 'CourseDetailsExtMEA Integration API',
                'timestamp' => now()->toISOString(),
                'database' => $dbStatus,
                'table_counts' => $tableCounts,
                'version' => '1.0.0',
                'environment' => config('app.env'),
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'UNHEALTHY',
                'service' => 'CourseDetailsExtMEA Integration API',
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage(),
                'database' => 'DISCONNECTED',
            ], 500);
        }
    }
    
    /**
     * Test endpoint for OSB to verify connectivity
     */
    public function integrationTest(Request $request): JsonResponse
    {
        $testData = [
            'test_id' => uniqid('TEST_', true),
            'timestamp' => now()->toISOString(),
            'headers_received' => array_keys($request->headers->all()),
            'method' => $request->method(),
            'client_ip' => $request->ip(),
            'server_info' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            ]
        ];
        
        $this->logAudit('INTEGRATION_TEST', 'OSB integration test received');
        
        return response()->json([
            'success' => true,
            'message' => 'Integration test successful',
            'test_data' => $testData,
            'response_timestamp' => now()->toISOString(),
        ]);
    }
    
    /**
     * Get integration statistics
     */
    public function integrationStats(Request $request): JsonResponse
    {
        // statistics for the last 24 hours
        $since = now()->subDay();
        
        $stats = [
            'last_24_hours' => [
                'personnel_created' => Personnel::where('CREATED_AT', '>=', $since)->count(),
                'personnel_updated' => ErpPerson::where('UPDATED_AT', '>=', $since)
                    ->where('CREATED_AT', '<', $since)
                    ->count(),
                'total_personnel' => Personnel::count(),
                'total_organisations' => ErpOrganisation::count(),
                'total_locations' => Location::count(),
            ],
            'system' => [
                'database_tables' => 31,
                'api_keys_active' => \App\Models\ApiKey::where('is_active', true)->count(),
                'audit_logs' => \App\Models\Audit\AuditLog::count(),
                'error_logs' => \App\Models\Audit\ErrorLog::count(),
            ]
        ];
        
        return $this->sendResponse($stats, 'Integration statistics retrieved');
    }
}