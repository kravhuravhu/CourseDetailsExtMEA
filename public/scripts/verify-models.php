<?php

require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Verifying Models and Database Connection ===\n\n";

// Test database connection
try {
    DB::connection()->getPdo();
    echo "✓ Database connection: OK\n";
} catch (\Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test model loading
$models = [
    'Personnel' => \App\Models\Personnel\Personnel::class,
    'ErpPersonnel' => \App\Models\Personnel\ErpPersonnel::class,
    'ErpPerson' => \App\Models\Personnel\ErpPerson::class,
    'ErpOrganisation' => \App\Models\Organisation\ErpOrganisation::class,
    'Location' => \App\Models\Location\Location::class,
    'Vehicle' => \App\Models\Vehicle\Vehicle::class,
    'AuditLog' => \App\Models\Audit\AuditLog::class,
    'ErrorLog' => \App\Models\Audit\ErrorLog::class,
];

foreach ($models as $name => $modelClass) {
    try {
        $count = $modelClass::count();
        echo "✓ {$name} model: OK (Records: {$count})\n";
    } catch (\Exception $e) {
        echo "✗ {$name} model failed: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Database Tables Check ===\n";
$tables = DB::select('SHOW TABLES');
echo "Total tables: " . count($tables) . "\n";

foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    $count = DB::table($tableName)->count();
    echo "  - {$tableName}: {$count} records\n";
}

echo "\n=== Verification Complete ===\n";