<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function log_msg($msg) {
    echo $msg . "\n";
    file_put_contents('db_reset.log', $msg . "\n", FILE_APPEND);
}

unlink('db_reset.log');

try {
    log_msg("Wiping database...");
    \Illuminate\Support\Facades\Artisan::call('db:wipe', ['--force' => true]);
    log_msg("Database wiped.");

    log_msg("Running migrations...");
    $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    log_msg("Migrations finished with exit code: " . $exitCode);
    log_msg("Output:\n" . \Illuminate\Support\Facades\Artisan::output());

    log_msg("Running seeders...");
    $seedCode = \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    log_msg("Seeders finished with exit code: " . $seedCode);
    log_msg("Output:\n" . \Illuminate\Support\Facades\Artisan::output());

} catch (\Exception $e) {
    log_msg("ERROR: " . $e->getMessage());
    log_msg($e->getTraceAsString());
}
