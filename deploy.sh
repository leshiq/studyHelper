#!/bin/bash

# Study Helper Deployment Script
# Target: andreik@91.99.27.52:/var/www/studyhelper.iforlive.com

set -e

SERVER="andreik@91.99.27.52"
DEPLOY_PATH="/var/www/studyhelper.iforlive.com"
DOMAIN="studyhelper.iforlive.com"

echo "===================================="
echo "Study Helper Deployment Script"
echo "===================================="

# Step 1: Install PostgreSQL and required PHP extensions
echo ""
echo "Step 1: Installing PostgreSQL and PHP extensions..."
ssh $SERVER << 'ENDSSH'
sudo apt update
sudo apt install -y postgresql postgresql-contrib php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath
sudo systemctl enable postgresql
sudo systemctl start postgresql
ENDSSH

echo "✓ PostgreSQL and extensions installed"

# Step 2: Create PostgreSQL database and user
echo ""
echo "Step 2: Creating PostgreSQL database..."
ssh $SERVER << 'ENDSSH'
sudo -u postgres psql << 'EOF'
CREATE DATABASE studyhelper;
CREATE USER studyhelper_user WITH PASSWORD 'StudyHelper2025!Secure';
GRANT ALL PRIVILEGES ON DATABASE studyhelper TO studyhelper_user;
\q
EOF
ENDSSH

echo "✓ Database created"

# Step 3: Sync project files (excluding vendor, node_modules, .env)
echo ""
echo "Step 3: Uploading project files..."
rsync -avz --delete \
  --exclude '.git' \
  --exclude 'node_modules' \
  --exclude 'vendor' \
  --exclude '.env' \
  --exclude 'storage/app/*' \
  --exclude 'storage/framework/cache/*' \
  --exclude 'storage/framework/sessions/*' \
  --exclude 'storage/framework/views/*' \
  --exclude 'storage/logs/*' \
  --exclude 'bootstrap/cache/*' \
  ./ $SERVER:$DEPLOY_PATH/

echo "✓ Files uploaded"

# Step 4: Set up environment and install dependencies
echo ""
echo "Step 4: Setting up Laravel environment..."
ssh $SERVER << ENDSSH
cd $DEPLOY_PATH

# Copy .env.example to .env
cp .env.example .env

# Update .env with production settings
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
sed -i "s|APP_URL=.*|APP_URL=https://$DOMAIN|" .env
sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=pgsql/' .env
sed -i 's/# DB_HOST=127.0.0.1/DB_HOST=127.0.0.1/' .env
sed -i 's/# DB_PORT=3306/DB_PORT=5432/' .env
sed -i 's/# DB_DATABASE=laravel/DB_DATABASE=studyhelper/' .env
sed -i 's/# DB_USERNAME=root/DB_USERNAME=studyhelper_user/' .env
sed -i 's/# DB_PASSWORD=/DB_PASSWORD=StudyHelper2025!Secure/' .env

# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate app key
php artisan key:generate

# Create required directories
mkdir -p storage/app/uploads/lessons
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Run migrations
php artisan migrate --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

ENDSSH

echo "✓ Laravel configured"

# Step 5: Configure Nginx
echo ""
echo "Step 5: Configuring Nginx..."
ssh $SERVER << 'ENDSSH'
sudo tee /etc/nginx/sites-available/studyhelper.iforlive.com > /dev/null << 'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name studyhelper.iforlive.com;
    root /var/www/studyhelper.iforlive.com/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Increase client max body size for video uploads
    client_max_body_size 1G;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF

# Enable site
sudo ln -sf /etc/nginx/sites-available/studyhelper.iforlive.com /etc/nginx/sites-enabled/

# Test nginx config
sudo nginx -t

# Reload nginx
sudo systemctl reload nginx

ENDSSH

echo "✓ Nginx configured"

# Step 6: Install SSL certificate with Certbot
echo ""
echo "Step 6: Installing SSL certificate..."
ssh $SERVER << ENDSSH
# Install certbot if not present
if ! command -v certbot &> /dev/null; then
    sudo apt install -y certbot python3-certbot-nginx
fi

# Get SSL certificate
sudo certbot --nginx -d studyhelper.iforlive.com --non-interactive --agree-tos --email andreik@example.com --redirect

ENDSSH

echo "✓ SSL certificate installed"

echo ""
echo "===================================="
echo "✓ Deployment Complete!"
echo "===================================="
echo ""
echo "Your application is now live at:"
echo "https://studyhelper.iforlive.com"
echo ""
echo "Next steps:"
echo "1. Create an admin user:"
echo "   ssh $SERVER"
echo "   cd $DEPLOY_PATH"
echo "   php artisan tinker"
echo "   >>> \\App\\Models\\Student::create(['name' => 'Admin', 'email' => 'admin@studyhelper.com', 'password' => bcrypt('YourSecurePassword'), 'is_active' => true]);"
echo ""
echo "2. Upload video files to:"
echo "   $DEPLOY_PATH/storage/app/uploads/lessons/"
echo ""
echo "Database credentials:"
echo "  Database: studyhelper"
echo "  Username: studyhelper_user"
echo "  Password: StudyHelper2025!Secure"
echo ""
