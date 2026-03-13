call php artisan db:wipe > wipe.log 2>&1
call php artisan migrate > migrate.log 2>&1
call php artisan db:seed > seed.log 2>&1
