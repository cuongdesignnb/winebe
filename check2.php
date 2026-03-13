<?php
require 'vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$prods = \App\Models\Product::pluck('slug')->take(10)->toArray();
echo json_encode($prods, JSON_PRETTY_PRINT);
