<?php
$output = shell_exec('php artisan migrate:fresh --seed --force 2>&1');
file_put_contents('migrate_debug.txt', $output);
echo "migration done";
