#!/bin/bash
chown -R www-data:www-data /var/www/html

if [ ! -f ".env" ]; then
    echo ".env file missing. Copying from .env.example..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo ".env created."
    fi
fi

if [ ! -d "vendor" ]; then
    echo "Vendor folder missing. Installing PHP dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs
fi

if [ ! -d "node_modules" ]; then
    echo "node_modules folder missing. Installing JS dependencies..."
    npm install
fi

php artisan key:generate
php artisan migrate --force
php artisan db:seed --force 

npm run dev &

echo "Starting Apache..."
exec apache2-foreground