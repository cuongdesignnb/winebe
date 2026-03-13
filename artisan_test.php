<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Running migrate...\n";
    $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "Exit code: " . $exitCode . "\n";
    echo "Output:\n" . \Illuminate\Support\Facades\Artisan::output();
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
