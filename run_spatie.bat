@echo off
php artisan optimize:clear
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
