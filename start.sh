#!/bin/bash
php artisan config:clear &
php artisan cache:clear &
 php artisan serve &
 php artisan queue:work
