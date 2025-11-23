<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\CredentialChangeController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\FileManagementController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Superuser\DashboardController as SuperuserDashboardController;
use App\Http\Controllers\Superuser\UserManagementController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes (public)
Route::get('/register/{token}', [RegisterController::class, 'showRegistrationForm'])->name('register.show');
Route::post('/register/{token}', [RegisterController::class, 'register'])->name('register.submit');

// Credential change routes (authenticated users who must change credentials)
Route::middleware('auth')->group(function () {
    Route::get('/change-credentials', [CredentialChangeController::class, 'show'])->name('credentials.show');
    Route::put('/change-credentials', [CredentialChangeController::class, 'update'])->name('credentials.update');
});

// Student routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/download/{file}', [FileDownloadController::class, 'download'])->name('file.download');
    Route::get('/watch/{file}', [VideoStreamController::class, 'watch'])->name('video.watch');
    Route::get('/stream/{file}', [VideoStreamController::class, 'stream'])->name('video.stream');
    
    // Profile settings
    Route::get('/profile/settings', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/settings', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar.upload');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    
    // Student course routes
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Student\CourseController::class, 'index'])->name('index');
        Route::get('/{course}', [\App\Http\Controllers\Student\CourseController::class, 'show'])->name('show');
        Route::post('/{course}/request', [\App\Http\Controllers\Student\CourseController::class, 'request'])->name('request');
        Route::delete('/{course}/cancel-request', [\App\Http\Controllers\Student\CourseController::class, 'cancelRequest'])->name('cancel-request');
        
        // Course chat
        Route::get('/{course}/chat', [\App\Http\Controllers\CourseChatController::class, 'index'])->name('chat.index');
        Route::post('/{course}/chat', [\App\Http\Controllers\CourseChatController::class, 'store'])->name('chat.store');
    });
});

// Teacher routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'teacher'])->group(function () {
    Route::resource('courses', \App\Http\Controllers\Teacher\CourseController::class);
    
    Route::prefix('courses/{course}')->group(function () {
        // Lesson management
        Route::post('lessons', [\App\Http\Controllers\Teacher\CourseLessonController::class, 'store'])->name('courses.lessons.store');
        Route::put('lessons/{lesson}', [\App\Http\Controllers\Teacher\CourseLessonController::class, 'update'])->name('courses.lessons.update');
        Route::delete('lessons/{lesson}', [\App\Http\Controllers\Teacher\CourseLessonController::class, 'destroy'])->name('courses.lessons.destroy');
        
        // Enrollment management
        Route::post('enrollments/{enrollment}/approve', [\App\Http\Controllers\Teacher\EnrollmentController::class, 'approve'])->name('courses.enrollments.approve');
        Route::post('enrollments/{enrollment}/reject', [\App\Http\Controllers\Teacher\EnrollmentController::class, 'reject'])->name('courses.enrollments.reject');
        Route::post('enrollments/enroll', [\App\Http\Controllers\Teacher\EnrollmentController::class, 'enroll'])->name('courses.enrollments.enroll');
        Route::delete('students/{student}', [\App\Http\Controllers\Teacher\EnrollmentController::class, 'remove'])->name('courses.students.remove');
    });
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // File management
    Route::resource('files', FileManagementController::class);
    Route::post('files/{file}/grant-access', [FileManagementController::class, 'grantAccess'])->name('files.grant-access');
    Route::delete('files/{file}/students/{student}', [FileManagementController::class, 'revokeAccess'])->name('files.revoke-access');
    
    // Student management
    Route::resource('students', StudentManagementController::class);
    Route::post('students/{student}/grant-access', [StudentManagementController::class, 'grantAccess'])->name('students.grant-access');
    Route::delete('students/{student}/files/{file}', [StudentManagementController::class, 'revokeAccess'])->name('students.revoke-access');
    
    // Invitation management
    Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
});

// Superuser routes
Route::prefix('superuser')->name('superuser.')->middleware(['auth', 'superuser'])->group(function () {
    Route::get('/dashboard', [SuperuserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/about', [\App\Http\Controllers\Superuser\SettingsController::class, 'about'])->name('about');
    
    // Settings & Testing
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Superuser\SettingsController::class, 'index'])->name('index');
        Route::get('/email-test', [\App\Http\Controllers\Superuser\SettingsController::class, 'emailTest'])->name('email-test');
        Route::post('/email-test/send', [\App\Http\Controllers\Superuser\SettingsController::class, 'sendTestEmail'])->name('email-test.send');
        Route::get('/websocket-test', [\App\Http\Controllers\Superuser\SettingsController::class, 'websocketTest'])->name('websocket-test');
        Route::post('/websocket-test/broadcast', [\App\Http\Controllers\Superuser\SettingsController::class, 'broadcastTestMessage'])->name('websocket-test.broadcast');
        Route::get('/general', [\App\Http\Controllers\Superuser\SettingsController::class, 'general'])->name('general');
        Route::post('/general', [\App\Http\Controllers\Superuser\SettingsController::class, 'updateGeneral'])->name('general.update');
        Route::get('/appearance', [\App\Http\Controllers\Superuser\SettingsController::class, 'appearance'])->name('appearance');
        Route::post('/appearance', [\App\Http\Controllers\Superuser\SettingsController::class, 'updateAppearance'])->name('appearance.update');
    });
});
