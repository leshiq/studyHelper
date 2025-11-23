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
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // File management
    Route::resource('files', FileManagementController::class);
    Route::post('files/{file}/grant-access', [FileManagementController::class, 'grantAccess'])->name('files.grant-access');
    Route::delete('files/{file}/students/{student}', [FileManagementController::class, 'revokeAccess'])->name('files.revoke-access');
    
    // Student management
    Route::resource('students', StudentManagementController::class);
    
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
        Route::get('/general', [\App\Http\Controllers\Superuser\SettingsController::class, 'general'])->name('general');
        Route::post('/general', [\App\Http\Controllers\Superuser\SettingsController::class, 'updateGeneral'])->name('general.update');
        Route::get('/appearance', [\App\Http\Controllers\Superuser\SettingsController::class, 'appearance'])->name('appearance');
        Route::post('/appearance', [\App\Http\Controllers\Superuser\SettingsController::class, 'updateAppearance'])->name('appearance.update');
    });
});
