@extends('layouts.sidebar')

@section('title', 'About - Superuser Panel')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                <i class="bi bi-info-circle"></i> About Study Helper
            </h1>
            <p class="text-muted">Portal information and version details</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Version Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-tag"></i> Version Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Current Version:</strong>
                        </div>
                        <div class="col-md-6">
                            <span class="badge bg-primary fs-6">v1.3.1</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Release Date:</strong>
                        </div>
                        <div class="col-md-8">
                            November 23, 2025
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Environment:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge bg-success">{{ config('app.env') }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Debug Mode:</strong>
                        </div>
                        <div class="col-md-8">
                            @if(config('app.debug'))
                            <span class="badge bg-warning text-dark">Enabled</span>
                            @else
                            <span class="badge bg-success">Disabled</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-server"></i> System Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Laravel Version:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ app()->version() }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>PHP Version:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ PHP_VERSION }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Database:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ config('database.default') }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Timezone:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ config('app.timezone') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-stars"></i> Key Features</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>User Management:</strong> Three-tier authentication system (Superuser, Admin, Student)
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>File Management:</strong> Secure file uploads and downloads with access control
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>Student Invitations:</strong> Email-based student onboarding system
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>Download Tracking:</strong> Comprehensive logging of all file downloads
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>Portal Customization:</strong> Logo, backgrounds, and color scheme customization
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>User Avatars:</strong> Multi-size avatar processing with automatic optimization
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>Email System:</strong> Built-in email testing and configuration
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>Security Features:</strong> Credential change enforcement, password policies
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>Course Management:</strong> Create courses, organize lessons, enroll students
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>Real-time Chat:</strong> WebSocket-powered course discussions with Laravel Reverb
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <strong>Video Streaming:</strong> Context-aware video player with course navigation
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Stats Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Total Users:</span>
                            <span class="badge bg-primary">{{ \App\Models\Student::count() }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Active Users:</span>
                            <span class="badge bg-success">{{ \App\Models\Student::where('is_active', true)->count() }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Total Courses:</span>
                            <span class="badge bg-info">{{ \App\Models\Course::count() }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Total Files:</span>
                            <span class="badge bg-secondary">{{ \App\Models\DownloadableFile::count() }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Total Downloads:</span>
                            <span class="badge bg-warning text-dark">{{ \App\Models\DownloadLog::count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technology Stack Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-stack"></i> Technology Stack</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-arrow-right-circle"></i> Laravel Framework</li>
                        <li class="mb-2"><i class="bi bi-arrow-right-circle"></i> Laravel Reverb (WebSocket)</li>
                        <li class="mb-2"><i class="bi bi-arrow-right-circle"></i> PostgreSQL Database</li>
                        <li class="mb-2"><i class="bi bi-arrow-right-circle"></i> Bootstrap 5.3</li>
                        <li class="mb-2"><i class="bi bi-arrow-right-circle"></i> Pusher.js 8.2.0</li>
                        <li class="mb-2"><i class="bi bi-arrow-right-circle"></i> Nginx Web Server</li>
                        <li class="mb-2"><i class="bi bi-arrow-right-circle"></i> GD Image Library</li>
                        <li class="mb-2"><i class="bi bi-arrow-right-circle"></i> Intervention Image</li>
                        <li><i class="bi bi-arrow-right-circle"></i> Let's Encrypt SSL</li>
                    </ul>
                </div>
            </div>

            <!-- Support Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-life-preserver"></i> Support</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Documentation:</strong></p>
                    <p class="text-muted small">See DEPLOYMENT.md for setup instructions</p>
                    
                    <p class="mb-2 mt-3"><strong>Server:</strong></p>
                    <p class="text-muted small">Ubuntu 24.04 LTS</p>
                    
                    <p class="mb-2 mt-3"><strong>SSL Certificate:</strong></p>
                    <p class="text-muted small">Automatically renewed via Let's Encrypt</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
