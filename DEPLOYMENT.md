# Manual Deployment Guide
# For: studyhelper.iforlive.com (91.99.27.52)

## Step-by-Step Manual Deployment

### 1. Install PostgreSQL and PHP Extensions

```bash
ssh andreik@91.99.27.52

sudo apt update
sudo apt install -y postgresql postgresql-contrib \
  php8.3-pgsql php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath \
  php8.3-imagick imagemagick \
  jpegoptim optipng pngquant webp gifsicle

sudo systemctl enable postgresql
sudo systemctl start postgresql
```

### 2. Create PostgreSQL Database

```bash
sudo -u postgres psql

-- In PostgreSQL prompt:
CREATE DATABASE studyhelper;
CREATE USER studyhelper_user WITH PASSWORD 'StudyHelper2025!Secure';
GRANT ALL PRIVILEGES ON DATABASE studyhelper TO studyhelper_user;
\q
```

### 3. Upload Project Files

From your local machine:

```bash
cd /home/andrei/Work/Dev/Projects/studyHelper

# Upload files (excluding vendor, node_modules)
rsync -avz --progress \
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
  ./ andreik@91.99.27.52:/var/www/studyhelper.iforlive.com/
```

### 4. Configure Laravel on Server

```bash
ssh andreik@91.99.27.52
cd /var/www/studyhelper.iforlive.com

# Copy and configure .env
cp .env.example .env
nano .env
```

Update `.env` with these values:

```env
APP_NAME="Study Helper"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://studyhelper.iforlive.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=studyhelper
DB_USERNAME=studyhelper_user
DB_PASSWORD=StudyHelper2025!Secure
```

Continue setup:

```bash
# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate app key
php artisan key:generate

# Create storage directories
mkdir -p storage/app/uploads/lessons
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p public/portal-assets/{logos,backgrounds}
mkdir -p public/avatars/{original,large,medium,small}

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache public/portal-assets public/avatars
sudo chmod -R 775 storage bootstrap/cache public/portal-assets public/avatars

# Run migrations
php artisan migrate --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/studyhelper.iforlive.com
```

Paste this configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name studyhelper.iforlive.com;
    root /var/www/studyhelper.iforlive.com/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Increase client max body size for video files
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
```

Enable the site:

```bash
# Enable site
sudo ln -sf /etc/nginx/sites-available/studyhelper.iforlive.com /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### 6. Install SSL Certificate

```bash
# Install certbot if needed
sudo apt install -y certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d studyhelper.iforlive.com
```

Follow the prompts:
- Enter your email address
- Agree to terms
- Choose to redirect HTTP to HTTPS (option 2)

### 7. Create Admin User

```bash
cd /var/www/studyhelper.iforlive.com
php artisan tinker
```

In tinker:

```php
\App\Models\Student::create([
    'name' => 'Admin User',
    'email' => 'admin@studyhelper.com',
    'password' => bcrypt('YourSecurePassword123!'),
    'is_active' => true
]);
```

Type `exit` to leave tinker.

### 8. Upload Video Files

```bash
# From your local machine
scp /path/to/your/lesson-01.mp4 andreik@91.99.27.52:/var/www/studyhelper.iforlive.com/storage/app/uploads/lessons/

# Set permissions on server
ssh andreik@91.99.27.52
sudo chown www-data:www-data /var/www/studyhelper.iforlive.com/storage/app/uploads/lessons/*
sudo chmod 644 /var/www/studyhelper.iforlive.com/storage/app/uploads/lessons/*
```

### 9. Test the Application

Visit: https://studyhelper.iforlive.com

Login with:
- Email: admin@studyhelper.com
- Password: YourSecurePassword123!

## Maintenance Commands

### Update Application

```bash
cd /var/www/studyhelper.iforlive.com

# Pull changes or rsync from local
git pull origin main
# OR
# rsync from local machine

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and rebuild cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl reload nginx
sudo systemctl reload php8.3-fpm
```

### Check Logs

```bash
# Laravel logs
tail -f /var/www/studyhelper.iforlive.com/storage/logs/laravel.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.3-fpm.log
```

### Backup Database

```bash
sudo -u postgres pg_dump studyhelper > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restore Database

```bash
sudo -u postgres psql studyhelper < backup_file.sql
```

## Troubleshooting

### Permission Issues

```bash
cd /var/www/studyhelper.iforlive.com
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Clear All Caches

```bash
php artisan optimize:clear
```

### 500 Error

Check logs:
```bash
tail -100 storage/logs/laravel.log
```

Common fixes:
```bash
php artisan config:clear
php artisan cache:clear
sudo chmod -R 775 storage
```

### Database Connection Issues

Test connection:
```bash
psql -h 127.0.0.1 -U studyhelper_user -d studyhelper
```

Check `.env` credentials match PostgreSQL user/database.

## Security Checklist

- [x] APP_DEBUG=false in production
- [x] Strong database password
- [x] SSL certificate installed
- [x] File upload directory secured
- [x] Storage directory permissions set correctly
- [x] .env file not publicly accessible
- [x] Composer production mode (--no-dev)

## Important Paths

- Application: `/var/www/studyhelper.iforlive.com`
- Public web root: `/var/www/studyhelper.iforlive.com/public`
- Storage: `/var/www/studyhelper.iforlive.com/storage`
- Video uploads: `/var/www/studyhelper.iforlive.com/storage/app/uploads/lessons`
- Logs: `/var/www/studyhelper.iforlive.com/storage/logs`
- Nginx config: `/etc/nginx/sites-available/studyhelper.iforlive.com`

---

## Image Processing Requirements

**Server Packages (Installed November 23, 2025):**

```bash
# PHP Extensions
php8.3-gd          # GD Library (basic image processing)
php8.3-imagick     # ImageMagick extension (advanced processing)

# Image Optimization Tools
imagemagick        # Core ImageMagick library
jpegoptim         # JPEG optimization
optipng           # PNG optimization
pngquant          # PNG compression
webp              # WebP format support
gifsicle          # GIF optimization
```

**Laravel Package (Composer):**
```bash
composer require intervention/image
```

**Avatar Processing:**
- **Original**: Full uploaded image (preserved for re-processing)
- **Large**: 400x400px (profile pages, detailed views)
- **Medium**: 200x200px (general use, cards)  
- **Small**: 64x64px (topbar, sidebar, thumbnails)
- **Formats**: WebP + JPEG/PNG fallback for browser compatibility
- **Auto-optimization**: Applied on upload to reduce file sizes

**Additional Upload Directories:**
- Portal assets: `/var/www/studyhelper.iforlive.com/public/portal-assets/{logos,backgrounds}`
- User avatars: `/var/www/studyhelper.iforlive.com/public/avatars/{original,large,medium,small}`

---

**Last Updated**: November 23, 2025
