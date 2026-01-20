<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Simple API Authentication ===\n\n";

$client = new GuzzleHttp\Client();
$baseUrl = 'http://localhost:8000';

echo "1. Testing health endpoint (public):\n";
try {
    $response = $client->get($baseUrl . '/health');
    echo "   ✓ Status: " . $response->getStatusCode() . "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n2. Testing API docs (public):\n";
try {
    $response = $client->get($baseUrl . '/api/docs');
    echo "   ✓ Status: " . $response->getStatusCode() . "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n3. Testing validate key (public):\n";
try {
    $response = $client->get($baseUrl . '/api/validate-key', [
        'query' => ['api_key' => 'test-api-key-123']
    ]);
    $data = json_decode($response->getBody(), true);
    echo "   ✓ Status: " . $response->getStatusCode() . "\n";
    echo "   ✓ Valid: " . ($data['data']['is_valid'] ? 'Yes' : 'No') . "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n4. Testing protected endpoint WITHOUT key:\n";
try {
    $response = $client->get($baseUrl . '/api/personnel', ['http_errors' => false]);
    $data = json_decode($response->getBody(), true);
    echo "   Status: " . $response->getStatusCode() . " (should be 401)\n";
    echo "   Message: " . ($data['message'] ?? 'N/A') . "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n5. Testing protected endpoint WITH valid key:\n";
try {
    $response = $client->get($baseUrl . '/api/personnel', [
        'headers' => ['X-API-Key' => 'test-api-key-123'],
        'http_errors' => false
    ]);
    $data = json_decode($response->getBody(), true);
    echo "   Status: " . $response->getStatusCode() . " (should be 200)\n";
    echo "   Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";