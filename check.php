<?php
require 'vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$taxa = \App\Models\Taxon::pluck('slug', 'name')->toArray();
echo json_encode($taxa, JSON_PRETTY_PRINT);
