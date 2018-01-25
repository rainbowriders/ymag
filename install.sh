#!/usr/bin/env bash

touch database/database.sqlite && echo "Database created";

cp .env.example .env && echo ".env file copied"

composer install --no-scripts

composer update -v && echo "Composer packages updated"

php artisan migrate