<?php
$output = [];
$return_var = 0;
exec('php artisan migrate:status 2>&1', $output, $return_var);
echo "Status output:\n";
echo implode("\n", $output);
echo "\nReturn var: $return_var\n";

exec('php artisan migrate 2>&1', $output2, $return_var2);
echo "\nMigrate output:\n";
echo implode("\n", $output2);
echo "\nReturn var: $return_var2\n";
