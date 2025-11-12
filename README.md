# Study Helper

A Laravel-based web application for managing and distributing educational video content to students with controlled access and download tracking.

## Features

- **Student Authentication**: Secure login system for students to access their learning materials
- **Role-Based Access Control**: Admin and student roles with different permissions
- **Self-Registration**: One-click invitation links for easy student onboarding (expires in 24 hours)
- **Video Streaming**: Watch lessons online with HTML5 player (keyboard shortcuts, progress saving)
- **File Management**: Upload and manage video lesson files (admin only)
- **Access Control**: Grant time-limited or permanent access to specific students
- **Download Tracking**: Monitor who downloads files and when
- **Download Limits**: Set maximum download counts per student per file
- **Admin Dashboard**: Manage students, files, access permissions, and invitations
- **Bootstrap UI**: Modern, responsive interface using Bootstrap 5
- **Video Compression**: FFmpeg integration for efficient video storage

## Technology Stack

- **Framework**: Laravel 12.x
- **Database**: PostgreSQL
- **Frontend**: Bootstrap 5.3
- **PHP**: 8.2+

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- PostgreSQL
- Web server (Apache/Nginx)

### Setup Steps

1. **Clone and Install Dependencies**
   ```bash
   cd /path/to/studyHelper
   composer install
   ```

2. **Configure Environment**
   
   Copy `.env.example` to `.env` and configure your database:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=studyhelper
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

3. **Create PostgreSQL Database**
   ```bash
   createdb studyhelper
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```

6. **Create Storage Directory**
   ```bash
   mkdir -p storage/app/uploads/lessons
   php artisan storage:link
   ```

7. **Start Development Server**
   ```bash
   php artisan serve
   ```

   Visit: http://localhost:8000

## Usage

### Uploading Video Files

Since there's no file upload interface (as per requirements), you'll upload files directly to the server:

1. **Upload via FTP/SSH** to: `storage/app/uploads/lessons/`
2. **Note the filename and path**
3. **Get file size** (in bytes):
   ```bash
   stat -f%z storage/app/uploads/lessons/your-video.mp4  # macOS
   # or
   stat -c%s storage/app/uploads/lessons/your-video.mp4  # Linux
   ```

### Creating File Records

1. Log in to the admin panel
2. Navigate to **Files** → **Add New File**
3. Fill in the details:
   - **Title**: Display name for students
   - **Description**: Brief description of the lesson
   - **Filename**: Actual filename (e.g., `lesson-01.mp4`)
   - **File Path**: Relative path from `storage/app/` (e.g., `uploads/lessons/lesson-01.mp4`)
   - **File Size**: Size in bytes
   - **MIME Type**: Usually `video/mp4`
   - **Max Downloads**: Optional limit per student
   - **Active**: Check to make visible to students

### Managing Student Access

1. Go to **Files** → Select a file
2. In the **Student Access** section:
   - Select a student from the dropdown
   - Optionally set an expiration date
   - Click **Grant Access**

To revoke access:
- Click the X button next to the student's name

### Creating Students

1. Navigate to **Students** → **Add New Student**
2. Fill in:
   - Full Name
   - Email Address (used for login)
   - Password
   - Active status

## Database Schema

### Students Table
- `id`: Primary key
- `name`: Student's full name
- `email`: Login email (unique)
- `password`: Hashed password
- `is_active`: Account status
- `remember_token`: For "remember me" functionality
- `timestamps`: Created/updated dates

### Downloadable Files Table
- `id`: Primary key
- `title`: Display title
- `description`: Optional description
- `filename`: Actual filename
- `file_path`: Path from storage/app/
- `file_size`: Size in bytes
- `mime_type`: File type
- `max_downloads`: Per-student download limit (nullable)
- `is_active`: Visibility status
- `timestamps`: Created/updated dates

### File Accesses Table (Pivot)
- `id`: Primary key
- `student_id`: Foreign key to students
- `downloadable_file_id`: Foreign key to files
- `expires_at`: Optional expiration date
- `timestamps`: Created/updated dates

### Download Logs Table
- `id`: Primary key
- `student_id`: Foreign key to students
- `downloadable_file_id`: Foreign key to files
- `ip_address`: Download IP address
- `user_agent`: Browser/device information
- `timestamps`: Created/updated dates

## Security Features

- **Password Hashing**: Bcrypt hashing for passwords
- **CSRF Protection**: All forms protected with CSRF tokens
- **Authentication Middleware**: Routes protected by auth middleware
- **Active Status Check**: Inactive students cannot login
- **Access Expiration**: Time-based access control
- **Download Limits**: Prevent excessive downloads

## Future Enhancements

This platform can be expanded with:

- File upload interface
- Student progress tracking
- Quiz/assessment system
- Discussion forums
- Video streaming (instead of downloads)
- Email notifications
- Bulk student import
- Analytics dashboard
- Mobile app
- Payment integration
- Course organization
- Certificate generation

## File Structure

```
studyHelper/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/
│   │       │   └── LoginController.php
│   │       ├── Admin/
│   │       │   ├── FileManagementController.php
│   │       │   └── StudentManagementController.php
│   │       ├── StudentDashboardController.php
│   │       └── FileDownloadController.php
│   └── Models/
│       ├── Student.php
│       ├── DownloadableFile.php
│       ├── FileAccess.php
│       └── DownloadLog.php
├── database/
│   └── migrations/
│       ├── *_create_students_table.php
│       ├── *_create_downloadable_files_table.php
│       ├── *_create_file_accesses_table.php
│       └── *_create_download_logs_table.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── auth/
│       │   └── login.blade.php
│       ├── dashboard.blade.php
│       └── admin/
│           ├── files/
│           │   ├── index.blade.php
│           │   ├── create.blade.php
│           │   ├── edit.blade.php
│           │   └── show.blade.php
│           └── students/
│               ├── index.blade.php
│               ├── create.blade.php
│               ├── edit.blade.php
│               └── show.blade.php
├── routes/
│   └── web.php
└── storage/
    └── app/
        └── uploads/
            └── lessons/  (create this directory)
```

## Common Commands

```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Create a new student (via tinker)
php artisan tinker
>>> \App\Models\Student::create(['name' => 'John Doe', 'email' => 'john@example.com', 'password' => bcrypt('password'), 'is_active' => true])

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run in production
php artisan optimize
```

## Troubleshooting

### Database Connection Issues
- Verify PostgreSQL is running
- Check `.env` database credentials
- Ensure database exists: `createdb studyhelper`

### File Download Issues
- Check file path in database matches actual location
- Verify file permissions: `chmod -R 755 storage/app/uploads`
- Ensure storage directory exists

### Login Issues
- Verify student `is_active` is set to `true`
- Check password is properly hashed
- Clear sessions: `php artisan session:clear`

## License

This is a custom educational platform. Modify as needed for your teaching requirements.

## Support

For issues or questions about this platform, please refer to the Laravel documentation at https://laravel.com/docs

## Changelog

### Version 1.1.0 (November 12, 2025)

**New Features**

#### Video Streaming
- HTML5 video player with native controls
- Watch lessons online without downloading
- Keyboard shortcuts (Space, F, Arrow keys)
- Automatic progress saving (resume where you left off)
- Seek/skip functionality
- Volume control
- Fullscreen support
- HTTP range request support for efficient streaming

#### Self-Registration System
- One-click invitation link generation
- Temporary registration links (24-hour expiration)
- Auto-expire when used
- Student self-service registration
- Beautiful gradient registration page
- Admin tracking of invitation usage
- Copy-to-clipboard functionality

**Technical Improvements**
- VideoStream service for chunked video delivery
- StudentInvitation model with token management
- Guest layout for public pages
- Enhanced navigation with Invitations menu

**Database Schema**
- `student_invitations` table (token, expires_at, used_at, created_by, student_id)

**User Experience**
- Dashboard now shows "Watch Online" as primary action
- Download still available as secondary option
- Streamlined student onboarding process
- No manual account creation needed

---

### Version 1.0.0 (November 12, 2025)

**Initial Release**

#### Features
- Complete authentication system with role-based access control (Admin/Student)
- Student management (CRUD operations for admins)
- Downloadable file management system
- File access control with time-based expiration
- Download tracking and logging (IP, user agent, timestamp)
- Download limits per student per file
- Bootstrap 5 responsive UI
- PostgreSQL database integration

#### Technical Details
- Laravel 12.x framework
- PHP 8.3 compatibility
- PostgreSQL 16 support
- Nginx + PHP-FPM deployment
- SSL/TLS certificate (Let's Encrypt)
- Production-ready optimization (config, route, view caching)

#### Database Schema
- `students` table with admin flag
- `downloadable_files` table with metadata
- `file_accesses` pivot table with expiration
- `download_logs` audit table

#### Security
- Bcrypt password hashing
- CSRF protection on all forms
- Admin middleware for protected routes
- Active status checking
- Session-based authentication

#### Deployment
- Automated deployment script (`server-deploy.sh`)
- Database migration system
- Environment configuration
- File permission management
- SSL certificate automation

#### Video Processing
- FFmpeg video compression (CRF 23)
- Audio noise reduction filters
- Optimized for screen recordings
- 85-90% size reduction achieved

#### Known Limitations
- No file upload UI (files uploaded via SFTP/SSH)
- No video streaming (download only)
- Admin panel requires manual file registration
- Single language support (English)

---

### Development History

**Phase 1: Core Development**
- Set up Laravel 12 project structure
- Created models (Student, DownloadableFile, FileAccess, DownloadLog)
- Built authentication system with custom Student model
- Developed admin controllers (FileManagement, StudentManagement)
- Created Bootstrap 5 UI with responsive layouts

**Phase 2: Deployment**
- Configured production server (Ubuntu 24.04)
- Installed PostgreSQL 16 and PHP 8.3 extensions
- Set up Nginx virtual host
- Installed SSL certificate via Certbot
- Deployed via rsync with automated script

**Phase 3: Security & Access Control**
- Added `is_admin` column for role separation
- Created AdminMiddleware for route protection
- Updated navigation to hide admin menu from students
- Applied middleware to admin routes
- Tested access control scenarios

**Phase 4: Optimization**
- Video compression with FFmpeg
- Audio noise reduction
- Laravel cache optimization (config, routes, views)
- File size reduction (2.2GB → 192MB)

---

### Upgrade Notes

When upgrading from development to production:
1. Run migrations: `php artisan migrate --force`
2. Clear caches: `php artisan optimize:clear`
3. Rebuild caches: `php artisan optimize`
4. Set first admin: Update `is_admin = true` for initial user
5. Upload video files to `storage/app/uploads/lessons/`
6. Register files via admin panel

---

### Credits

- **Framework**: Laravel 12.x by Taylor Otwell
- **UI**: Bootstrap 5.3
- **Database**: PostgreSQL 16
- **Video Processing**: FFmpeg
- **SSL**: Let's Encrypt / Certbot

