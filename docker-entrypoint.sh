#!/bin/bash
php artisan migrate --force
npm run dev &
exec /usr/local/bin/start-container "$@"