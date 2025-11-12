<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\Admin\FileManagementController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\InvitationController;
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

// Student routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/download/{file}', [FileDownloadController::class, 'download'])->name('file.download');
    Route::get('/watch/{file}', [VideoStreamController::class, 'watch'])->name('video.watch');
    Route::get('/stream/{file}', [VideoStreamController::class, 'stream'])->name('video.stream');
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
