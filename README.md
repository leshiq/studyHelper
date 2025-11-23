# Study Helper

A Laravel-based web application for managing and distributing educational video content to students with controlled access and download tracking.

> **Development Note:** This project is being developed with the assistance of GitHub Copilot under the guidance and direction of the project owner.

## Features

- **Student Authentication**: Secure login system for students to access their learning materials
- **Role-Based Access Control**: Admin and student roles with different permissions
- **Self-Registration**: One-click invitation links for easy student onboarding (expires in 24 hours)
- **Course Management**: Organize lessons into courses, enroll students, track progress
- **Real-time Chat**: WebSocket-powered course discussions using Laravel Reverb
- **Video Streaming**: Watch lessons online with HTML5 player (keyboard shortcuts, progress saving)
- **Context-Aware Navigation**: Smart back buttons that return to course context
- **File Management**: Upload and manage video lesson files (admin only)
- **Access Control**: Grant time-limited or permanent access to specific students
- **Download Tracking**: Monitor who downloads files and when
- **Download Limits**: Set maximum download counts per student per file
- **Admin Dashboard**: Manage students, files, courses, access permissions, and invitations
- **Bootstrap UI**: Modern, responsive interface using Bootstrap 5
- **Video Compression**: FFmpeg integration for efficient video storage

## Technology Stack

- **Framework**: Laravel 12.x
- **Database**: PostgreSQL
- **WebSocket**: Laravel Reverb 1.6.1
- **Frontend**: Bootstrap 5.3, Pusher.js 8.2.0
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

### Version 1.3.0 (November 23, 2025)

**Course Management System**
- Complete course creation and management for teachers
- Course lesson organization - attach videos to courses
- Student enrollment system - teachers can add students to courses
- Course detail pages for both teachers and students
- Lesson progress tracking within courses
- Teacher course dashboard with enrollment management

**Real-time Course Chat**
- WebSocket-powered chat rooms for course discussions
- Laravel Reverb WebSocket server integration
- Pusher.js client library for real-time messaging
- Chat interface for both teachers and students
- Message persistence with PostgreSQL
- Broadcasting infrastructure with public channels
- Nginx WebSocket proxy configuration

**Video Player Enhancements**
- Context-aware back button in video watch page
- Smart navigation - returns to course when watching course lesson
- Returns to dashboard when watching standalone file
- Improved video streaming with course integration

**WebSocket Infrastructure**
- Laravel Reverb 1.6.1 installation and configuration
- Systemd service for production WebSocket server
- Nginx proxy for wss:// connections at /app endpoint
- Environment configuration (REVERB_APP_ID, REVERB_APP_KEY, REVERB_APP_SECRET)
- WebSocket testing page with installation validation
- Real-time connection status monitoring

**Database Schema Updates**
- `courses` table (teacher_id, title, description, active status)
- `course_student` pivot table for enrollments
- `course_lessons` table (course_id, file_id associations)
- `course_messages` table (course chat history)
- Foreign key relationships and indexes

**Technical Improvements**
- CourseChatMessage broadcast event with ShouldBroadcastNow
- CourseChatController REST API for messages
- Public channel broadcasting (no authentication overhead)
- Reverb installation check on WebSocket testing page
- Comprehensive console logging for debugging

**UI/UX Enhancements**
- Modern chat interface with avatars and timestamps
- Real-time message updates without page refresh
- Course enrollment badges and status indicators
- Lesson count display on course cards
- Teacher and student role-specific views

**Production Deployment**
- Reverb systemd service configuration
- Nginx WebSocket proxy setup
- SSL/TLS support for wss:// connections
- Environment variable management
- Service restart and monitoring

---

### Version 1.2.1 (November 23, 2025)

**File Management Improvements**
- Direct storage directory scanning - files read from `storage/app/uploads/lessons/` automatically
- Auto-detection of file metadata (size, MIME type, modification date)
- "Save to DB" button for files not yet registered in database
- Pre-filled file information in save modal
- Visual status indicators (Active/Inactive/Not Saved badges)
- File type icons based on MIME type
- Delete operation now only removes DB record, warns physical file remains

**Student File Assignment**
- Direct file assignment from student detail page
- "Grant Access" modal with file selection dropdown
- Optional expiration date for file access
- Revoke access functionality with one-click removal
- Available files list (active files not yet assigned)
- Improved student show page layout

**CSS & Theme Fixes**
- Fixed dark theme background breaking on scroll in video watch page
- Added aggressive background enforcement for `.main-content` and `.content-wrapper`
- Portal appearance settings now only apply in light mode (prevents white background in dark mode)
- Improved CSS specificity with `!important` rules for dark mode consistency

**Technical Changes**
- `FileManagementController::index()` now uses `scandir()` to read storage directory
- `FileManagementController::store()` validation updated (file_size and mime_type required)
- `StudentManagementController` added `grantAccess()` and `revokeAccess()` methods
- New routes: `admin.students.grant-access`, `admin.students.revoke-access`
- Enhanced CSS modularization with improved dark mode support

---

### Version 1.2.0 (November 23, 2025)

**New Features**

#### User Interface Redesign
- Modern sidebar navigation with collapsible menu (250px/70px toggle)
- Persistent sidebar state with localStorage
- Top bar with user info, settings, and logout
- Improved responsive layout
- Bootstrap Icons integration

#### CSS Modularization
- Extracted inline styles to 5 modular CSS files:
  - `base.css` - Shared base styles
  - `sidebar.css` - Sidebar navigation
  - `topbar.css` - Top bar with user menu
  - `main-content.css` - Content area layout
  - `guest.css` - Guest pages (login, register)

#### Profile Settings System
- Tabbed profile settings interface
- Personal information editing (name, email)
- Security tab for password changes
- Avatar upload with multi-size processing
- Display preferences with light/dark/auto themes

#### System Settings Hub (Superuser)
- Centralized settings dashboard with card layout
- General settings page with feature toggles
- Email testing functionality with detailed logging
- Password change feature toggle (admin-controlled)

#### Portal Appearance Customization
- Logo upload and management
- Login page background (image/gradient/color with fallback chain)
- Pages background customization
- Sidebar color/gradient customization
- Live preview and tips section

#### Avatar System
- Multi-size avatar processing (original, large 400x, medium 200x, small 64x)
- Automatic WebP conversion for optimized sizes
- GD Library and ImageMagick integration
- Intervention Image 3.11 for image processing
- Avatar display in topbar, sidebar, and user lists
- Fallback to colored initial circles

#### Theme System
- Light/dark mode toggle
- Auto mode with system preference detection
- Automatic theme switching on system change
- Comprehensive dark mode support for all components
- Bootstrap 5.3 theme integration

#### About Page
- System information and version tracking
- Technology stack details
- Quick statistics (users, files, downloads)
- Support and documentation links

**Technical Improvements**
- Image processing with jpegoptim, optipng, pngquant, webp, gifsicle
- Setting model with key-value storage
- Portal assets directory structure
- Avatar storage with size-based subdirectories
- Dynamic CSS overrides for portal customization

**Database Schema Updates**
- `settings` table for portal configuration
- Avatar fields in students table (avatar_original, avatar_large, avatar_medium, avatar_small)
- `theme_preference` column for user theme settings

**User Experience Enhancements**
- Avatar thumbnails in all user lists
- Consistent avatar display pattern across UI
- Smooth transitions and hover effects
- Mobile-responsive sidebar toggle
- Better dark mode readability

**Deployment**
- Updated DEPLOYMENT.md with image processing requirements
- Server packages: php8.3-gd, php8.3-imagick, imagemagick, optimization tools
- Portal assets and avatar directories with proper permissions

---

### Version 1.1.1 (November 22, 2025)

**Bug Fixes**
- Fixed login page showing navigation bar for authenticated users
- Added automatic redirect to dashboard when logged-in users access login page
- Modernized clipboard API in invitation management (replaced deprecated `document.execCommand`)
- Fixed JavaScript syntax errors in invitation link copying functionality
- Improved event handling with data attributes instead of inline onclick handlers

**UI Improvements**
- Login page now uses guest layout (no navigation bar)
- Sticky footer implementation across all layouts using flexbox
- Better error handling for clipboard operations with fallback support
- Enhanced copy-to-clipboard feedback with visual confirmation

**Technical Changes**
- Refactored `LoginController::showLoginForm()` to check authentication state
- Updated `auth/login.blade.php` to extend `layouts.guest` instead of `layouts.app`
- Implemented event delegation for invitation copy buttons
- Added CSS flexbox layout for footer positioning in both app and guest layouts

---

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

