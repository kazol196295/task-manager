#!/usr/bin/bash

# 1. Install PHP dependencies
composer install --no-interaction --optimize-autoloader --no-dev

# 2. Install Node dependencies and build assets (Tailwind/Alpine)
npm ci && npm run build

# 3. Run database migrations safely
php artisan migrate --force

# 4. Start the Laravel server on the port Render provides
exec php artisan serve --host 0.0.0.0 --port $PORT
