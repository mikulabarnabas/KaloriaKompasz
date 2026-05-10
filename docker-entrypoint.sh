#!/bin/bash
set -e

<<<<<<< HEAD
export PORT=${PORT:-8080}
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf
=======
if [ ! -f ".env" ]; then
    echo ".env file missing. Copying from .env.example..."
    cp .env.example .env
    echo ".env created."
fi
>>>>>>> dev

echo "Running migrations..."
php artisan migrate --force

if [ ! -z "$APP_KEY" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

<<<<<<< HEAD
echo "Starting Apache on port $PORT..."
=======
echo "Linking storage..."
php artisan storage:link --force

echo "Starting Apache..."
>>>>>>> dev
exec apache2-foreground