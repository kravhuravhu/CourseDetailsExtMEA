<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== Checking Route Configuration ===\n\n";

// Check which routes are registered
$routes = Route::getRoutes();

echo "Total routes registered: " . count($routes) . "\n\n";

echo "Registered routes:\n";
foreach ($routes as $route) {
    echo "  " . $route->methods()[0] . " " . $route->uri() . "\n";
    echo "    -> " . ($route->getActionName() ?? 'Closure') . "\n";
}

// Test if we can access the API
echo "\n=== Testing Direct Access ===\n";

// Create a simple request to test
$request = Illuminate\Http\Request::create('/api/health', 'GET');
$response = $app->handle($request);

echo "API Health Check Status: " . $response->getStatusCode() . "\n";
echo "Response: " . $response->getContent() . "\n";

// Test database connection
echo "\n=== Testing Database ===\n";
try {
    DB::connection()->getPdo();
    echo "✓ Database connection: OK\n";
    echo "Database: " . DB::connection()->getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";