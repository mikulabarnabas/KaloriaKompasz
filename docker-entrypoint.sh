#!/bin/bash

if [ ! -f ".env" ]; then
    echo ".env file missing. Copying from .env.example..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo ".env created."
    fi
fi

echo "Waiting for database..."
until timeout 1s bash -c ":> /dev/tcp/mysql/3306" 2>/dev/null; do
  sleep 1
done

if [ ! -d "vendor" ]; then
    echo "Vendor folder missing. Installing PHP dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# 4. Check JS dependencies
if [ ! -d "node_modules" ]; then
    echo "node_modules folder missing. Installing JS dependencies..."
    npm install
fi

php artisan key:generate --skip
php artisan migrate --force
php artisan db:seed --force 

npm run dev &

exec /usr/local/bin/start-container "$@"