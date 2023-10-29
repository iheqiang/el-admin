#!/usr/bin/env bash

cd ./laravel-tests
export DISPLAY=:99.0
sudo chmod -R 777  storage
sudo chmod -R 777  bootstrap/cache
php artisan serve --port=8001 > /dev/null 2>&1 &

