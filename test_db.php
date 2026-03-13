<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \App\Models\Menu::count();
echo "Menu Count: " . $count . "\n";
$menu = \App\Models\Menu::where('code', 'header')->first();
if ($menu) {
    echo "Found menu!\n";
    print_r($menu->toArray());
} else {
    echo "Menu 'header' not found in DB!\n";
}
