<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing CourseDetailsExtMEA API ===\n\n";

// Load API key from .env
$apiKey = env('API_KEY', 'default-test-key'); // fallback if not set

// Test endpoints
$baseUrl = 'http://localhost:8000/api';
$tests = [
    ['GET', '/health', 'Health Check'],
    ['GET', '/docs', 'API Documentation'],
    ['GET', '/personnel', 'List Personnel'],
    ['GET', '/organisations', 'List Organisations'],
    ['GET', '/locations', 'List Locations'],
    ['GET', '/vehicles', 'List Vehicles'],
    ['GET', '/audit/logs', 'Audit Logs'],
];

$client = new GuzzleHttp\Client();

foreach ($tests as $test) {
    list($method, $endpoint, $description) = $test;
    $url = $baseUrl . $endpoint;
    
    echo "Testing: {$description}\n";
    echo "Endpoint: {$method} {$endpoint}\n";
    
    try {
        $response = $client->request($method, $url, [
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'X-API-Key'    => $apiKey,
            ],
            'http_errors' => false,
        ]);
        
        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        
        if ($statusCode >= 200 && $statusCode < 300) {
            echo "✓ Status: {$statusCode}\n";
            if (isset($body['success'])) {
                echo "  Success: " . ($body['success'] ? 'Yes' : 'No') . "\n";
                echo "  Message: " . ($body['message'] ?? 'N/A') . "\n";
            }
            if (isset($body['data']) && is_array($body['data'])) {
                $count = count($body['data']) ?? (isset($body['data']['pagination']['total']) ? $body['data']['pagination']['total'] : 'N/A');
                echo "  Data Count: {$count}\n";
            }
        } else {
            echo "✗ Status: {$statusCode}\n";
            echo "  Error: " . ($body['message'] ?? 'N/A') . "\n";
        }
        
    } catch (Exception $e) {
        echo "✗ Failed: " . $e->getMessage() . "\n";
    }
    
    echo str_repeat('-', 50) . "\n";
}

echo "\n=== API Test Complete ===\n";

// Test database connection through Models
echo "\n=== Testing Database through Models ===\n";

use App\Models\Personnel\ErpPerson;
use App\Models\Organisation\ErpOrganisation;
use App\Models\Location\Location;
use App\Models\Vehicle\Vehicle;

$models = [
    'ErpPerson'       => ErpPerson::class,
    'ErpOrganisation' => ErpOrganisation::class,
    'Location'        => Location::class,
    'Vehicle'         => Vehicle::class,
];

foreach ($models as $name => $model) {
    try {
        $count = $model::count();
        $sample = $model::first();
        echo "✓ {$name}: {$count} records\n";
        if ($sample) {
            echo "  Sample MRID: " . ($sample->MRID ?? 'N/A') . "\n";
            echo "  Sample Name: " . ($sample->NAME ?? 'N/A') . "\n";
        }
    } catch (Exception $e) {
        echo "✗ {$name}: Error - " . $e->getMessage() . "\n";
    }
}

echo "\n=== All Tests Complete ===\n";