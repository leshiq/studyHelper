# Quick Setup Guide

## Initial Setup

1. **Create PostgreSQL Database**
   ```bash
   # Login to PostgreSQL
   sudo -u postgres psql
   
   # Create database
   CREATE DATABASE studyhelper;
   
   # Create user (optional)
   CREATE USER studyhelper_user WITH PASSWORD 'your_secure_password';
   GRANT ALL PRIVILEGES ON DATABASE studyhelper TO studyhelper_user;
   ```

2. **Configure .env**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=studyhelper
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

3. **Run Migrations**
   ```bash
   php artisan migrate
   ```

4. **Create Upload Directory**
   ```bash
   mkdir -p storage/app/uploads/lessons
   chmod -R 775 storage/app/uploads
   ```

## Creating Your First Admin User

Use Laravel Tinker to create an admin account:

```bash
php artisan tinker
```

Then in tinker:
```php
\App\Models\Student::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
    'is_active' => true
]);
```

Exit tinker: `exit`

## Adding Your First Video

1. **Upload video to server**
   ```bash
   # Example: Upload via SCP
   scp lesson-01.mp4 user@yourserver:/path/to/studyHelper/storage/app/uploads/lessons/
   
   # Get file size
   stat -c%s storage/app/uploads/lessons/lesson-01.mp4
   ```

2. **Login to the system**
   - Visit: http://your-domain.com/login
   - Email: admin@example.com
   - Password: password123

3. **Add file record**
   - Go to Files → Add New File
   - Fill in:
     - Title: "Lesson 1: Introduction"
     - Description: "Introduction to the course"
     - Filename: "lesson-01.mp4"
     - File Path: "uploads/lessons/lesson-01.mp4"
     - File Size: [paste size from stat command]
     - MIME Type: "video/mp4"
     - Active: ✓

4. **Create a student**
   - Go to Students → Add New Student
   - Fill in name, email, password
   - Mark as Active

5. **Grant access**
   - Go to Files → View your file
   - In "Student Access" section
   - Select student and click "Grant Access"

## Production Deployment

### Web Server Configuration

**Nginx Example:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/studyHelper/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Optimization Commands

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Environment Settings

Update `.env` for production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Set a strong app key
php artisan key:generate
```

## Maintenance Tips

### Backup Database
```bash
pg_dump studyhelper > backup_$(date +%Y%m%d).sql
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Monitor Logs
```bash
tail -f storage/logs/laravel.log
```

### Database Queries

**List all students:**
```sql
SELECT * FROM students;
```

**Check file access:**
```sql
SELECT s.name, df.title, fa.expires_at 
FROM file_accesses fa
JOIN students s ON fa.student_id = s.id
JOIN downloadable_files df ON fa.downloadable_file_id = df.id;
```

**Download statistics:**
```sql
SELECT df.title, COUNT(dl.id) as downloads
FROM downloadable_files df
LEFT JOIN download_logs dl ON df.id = dl.downloadable_file_id
GROUP BY df.id, df.title;
```

## Troubleshooting

### "SQLSTATE[08006] Connection refused"
- PostgreSQL is not running: `sudo systemctl start postgresql`
- Wrong credentials in `.env`

### "Permission denied" errors
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### "File not found" on download
- Check file exists: `ls -la storage/app/uploads/lessons/`
- Verify `file_path` in database matches actual location
- Check permissions: `chmod 644 storage/app/uploads/lessons/*`

### Students can't login
- Verify `is_active = true` in database
- Check password: recreate using `bcrypt('password')`
- Clear sessions: `php artisan session:clear`
