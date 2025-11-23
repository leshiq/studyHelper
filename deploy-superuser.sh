#!/bin/bash

echo "Deploying superuser feature to production..."

# Upload files to server
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude 'storage' --exclude '.git' --exclude '.env' \
  ./ andreik@91.99.27.52:/var/www/studyhelper.iforlive.com/

# SSH into server and run commands
ssh andreik@91.99.27.52 << 'ENDSSH'
cd /var/www/studyhelper.iforlive.com

# Run migration
php artisan migrate --force

# Run seeder
php artisan db:seed --class=SuperAdminSeeder --force

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment complete!"
ENDSSH

echo "Done!"
