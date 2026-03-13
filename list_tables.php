<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
$out = [];
foreach ($tables as $t) {
    $out[] = current((array) $t);
}

file_put_contents('tables_out.txt', implode("\n", $out));
echo "Done writing out tables.";
