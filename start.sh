#!/bin/bash

# 1. Run database migrations
php artisan migrate --force

# 2. Start the Apache server in the foreground
apache2-foreground
