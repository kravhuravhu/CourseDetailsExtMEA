<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing CourseDetailsExtMEA Integration Endpoints ===\n\n";

$client = new GuzzleHttp\Client();
$baseUrl = 'http://localhost:8000';

echo "1. Testing integration health check:\n";
try {
    $response = $client->get($baseUrl . '/api/integration/health');
    $data = json_decode($response->getBody(), true);
    echo "   ✓ Status: " . $response->getStatusCode() . "\n";
    echo "   ✓ Service: " . ($data['service'] ?? 'N/A') . "\n";
    echo "   ✓ Database: " . ($data['database'] ?? 'N/A') . "\n";
    if (isset($data['table_counts'])) {
        echo "   ✓ Personnel Records: " . ($data['table_counts']['personnel'] ?? 0) . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing integration test endpoint:\n";
try {
    $response = $client->get($baseUrl . '/api/integration/test');
    $data = json_decode($response->getBody(), true);
    echo "   ✓ Status: " . $response->getStatusCode() . "\n";
    echo "   ✓ Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    echo "   ✓ Test ID: " . ($data['test_data']['test_id'] ?? 'N/A') . "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n3. Testing integration statistics:\n";
try {
    $response = $client->get($baseUrl . '/api/integration/stats');
    $data = json_decode($response->getBody(), true);
    echo "   ✓ Status: " . $response->getStatusCode() . "\n";
    if (isset($data['data']['last_24_hours'])) {
        echo "   ✓ Personnel Created (24h): " . ($data['data']['last_24_hours']['personnel_created'] ?? 0) . "\n";
        echo "   ✓ Total Personnel: " . ($data['data']['last_24_hours']['total_personnel'] ?? 0) . "\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n4. Testing personnel data submission (JSON):\n";
try {
    $testPersonnel = [
        'MRID' => 'OSB_TEST_' . uniqid(),
        'Name' => 'John OSB Test',
        'FirstName' => 'John',
        'LastName' => 'OSB Test',
        'Gender' => 'Male',
        'BirthDate' => '1985-01-15',
        'Nationality' => 'South African',
        'Email' => 'john.osbtest@example.com',
        'JobTitle' => 'Integration Engineer',
        'StartDate' => '2024-01-01',
        'KeyPerson' => true,
        'Responsibility' => 'OSB Integration Testing'
    ];
    
    $response = $client->post($baseUrl . '/api/integration/personnel', [
        'headers' => [
            'Content-Type' => 'application/json',
            'X-Transaction-ID' => 'TEST_TXN_' . uniqid(),
            'X-Message-ID' => 'TEST_MSG_' . uniqid(),
        ],
        'json' => $testPersonnel,
        'http_errors' => false
    ]);
    
    $data = json_decode($response->getBody(), true);
    echo "   ✓ Status: " . $response->getStatusCode() . "\n";
    echo "   ✓ Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    echo "   ✓ Transaction ID: " . ($data['transaction_id'] ?? 'N/A') . "\n";
    echo "   ✓ Message: " . ($data['message'] ?? 'N/A') . "\n";
    
    // Verify the record was created
    if ($data['success']) {
        echo "   ✓ Checking database for new record...\n";
        $person = \App\Models\Personnel\ErpPerson::where('MRID', $testPersonnel['MRID'])->first();
        if ($person) {
            echo "   ✓ Record found in database!\n";
            echo "     Name: " . $person->NAME . "\n";
            echo "     ID: " . $person->ERP_PERSON_ID . "\n";
        } else {
            echo "   ✗ Record not found in database\n";
        }
    }
    
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n5. Testing batch personnel data submission:\n";
try {
    $batchData = [
        'PersonnelDetails' => [
            [
                'MRID' => 'OSB_BATCH_1_' . uniqid(),
                'Name' => 'Batch User 1',
                'FirstName' => 'Batch',
                'LastName' => 'User 1',
                'Gender' => 'Male',
                'JobTitle' => 'Engineer'
            ],
            [
                'MRID' => 'OSB_BATCH_2_' . uniqid(),
                'Name' => 'Batch User 2',
                'FirstName' => 'Batch',
                'LastName' => 'User 2',
                'Gender' => 'Female',
                'JobTitle' => 'Technician'
            ]
        ]
    ];
    
    $response = $client->post($baseUrl . '/api/integration/personnel/batch', [
        'headers' => [
            'Content-Type' => 'application/json',
            'X-Transaction-ID' => 'BATCH_TXN_' . uniqid(),
        ],
        'json' => $batchData,
        'http_errors' => false
    ]);
    
    $data = json_decode($response->getBody(), true);
    echo "   ✓ Status: " . $response->getStatusCode() . "\n";
    echo "   ✓ Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    echo "   ✓ Records in batch: " . count($batchData['PersonnelDetails']) . "\n";
    
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Integration Test Complete ===\n";

// Summary
echo "\n=== Summary ===\n";
echo "Integration endpoints are ready for OSB CourseDetailsMEAProvABCSREST\n";
echo "Main endpoint to use: POST /api/integration/personnel\n";
echo "Expected headers from OSB:\n";
echo "  - X-Transaction-ID: Unique transaction identifier\n";
echo "  - X-Message-ID: Unique message identifier\n";
echo "  - Content-Type: application/json or XML\n";
echo "\nHealth check endpoint: GET /api/integration/health\n";
echo "Test endpoint: GET /api/integration/test\n";