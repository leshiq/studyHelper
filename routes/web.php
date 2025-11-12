<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\Admin\FileManagementController;
use App\Http\Controllers\Admin\StudentManagementController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Student routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/download/{file}', [FileDownloadController::class, 'download'])->name('file.download');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // File management
    Route::resource('files', FileManagementController::class);
    Route::post('files/{file}/grant-access', [FileManagementController::class, 'grantAccess'])->name('files.grant-access');
    Route::delete('files/{file}/students/{student}', [FileManagementController::class, 'revokeAccess'])->name('files.revoke-access');
    
    // Student management
    Route::resource('students', StudentManagementController::class);
});
