@extends('layouts.sidebar')

@section('title', 'System Settings')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">
        <i class="bi bi-gear-fill"></i> System Settings & Testing
    </h2>

    <div class="row g-4">
        <!-- General Settings -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-toggles fs-3"></i>
                        </div>
                        <h5 class="card-title mb-0">General Settings</h5>
                    </div>
                    <p class="card-text text-muted">
                        Configure general application settings and feature toggles.
                    </p>
                    <a href="{{ route('superuser.settings.general') }}" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Open
                    </a>
                </div>
            </div>
        </div>

        <!-- Portal Appearance -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-palette fs-3"></i>
                        </div>
                        <h5 class="card-title mb-0">Portal Appearance</h5>
                    </div>
                    <p class="card-text text-muted">
                        Customize logo, backgrounds, colors, and overall portal branding.
                    </p>
                    <a href="{{ route('superuser.settings.appearance') }}" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Open
                    </a>
                </div>
            </div>
        </div>

        <!-- Email Testing -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-envelope-check fs-3"></i>
                        </div>
                        <h5 class="card-title mb-0">Email Testing</h5>
                    </div>
                    <p class="card-text text-muted">
                        Test email delivery and configuration. Send test emails to verify SMTP settings.
                    </p>
                    <a href="{{ route('superuser.settings.email-test') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Open
                    </a>
                </div>
            </div>
        </div>

        <!-- WebSocket Testing -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-diagram-3 fs-3"></i>
                        </div>
                        <h5 class="card-title mb-0">WebSocket Testing</h5>
                    </div>
                    <p class="card-text text-muted">
                        Test WebSocket connections and real-time broadcasting with Laravel Reverb.
                    </p>
                    <a href="{{ route('superuser.settings.websocket-test') }}" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-arrow-right-circle"></i> Open
                    </a>
                </div>
            </div>
        </div>

        <!-- System Configuration (Placeholder) -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-card opacity-50">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-secondary bg-opacity-10 text-secondary me-3">
                            <i class="bi bi-sliders fs-3"></i>
                        </div>
                        <h5 class="card-title mb-0">System Configuration</h5>
                    </div>
                    <p class="card-text text-muted">
                        Configure system-wide settings, application behavior, and defaults.
                    </p>
                    <span class="badge bg-secondary">Coming Soon</span>
                </div>
            </div>
        </div>

        <!-- Database Maintenance (Placeholder) -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-card opacity-50">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-secondary bg-opacity-10 text-secondary me-3">
                            <i class="bi bi-database-gear fs-3"></i>
                        </div>
                        <h5 class="card-title mb-0">Database Maintenance</h5>
                    </div>
                    <p class="card-text text-muted">
                        Run database optimizations, backups, and maintenance tasks.
                    </p>
                    <span class="badge bg-secondary">Coming Soon</span>
                </div>
            </div>
        </div>

        <!-- Logs Viewer (Placeholder) -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-card opacity-50">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-secondary bg-opacity-10 text-secondary me-3">
                            <i class="bi bi-file-text fs-3"></i>
                        </div>
                        <h5 class="card-title mb-0">System Logs</h5>
                    </div>
                    <p class="card-text text-muted">
                        View and analyze application logs, errors, and debugging information.
                    </p>
                    <span class="badge bg-secondary">Coming Soon</span>
                </div>
            </div>
        </div>

        <!-- Cache Management (Placeholder) -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-card opacity-50">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-secondary bg-opacity-10 text-secondary me-3">
                            <i class="bi bi-layers fs-3"></i>
                        </div>
                        <h5 class="card-title mb-0">Cache Management</h5>
                    </div>
                    <p class="card-text text-muted">
                        Clear application cache, route cache, config cache, and views.
                    </p>
                    <span class="badge bg-secondary">Coming Soon</span>
                </div>
            </div>
        </div>

        <!-- Security Settings (Placeholder) -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-card opacity-50">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-secondary bg-opacity-10 text-secondary me-3">
                            <i class="bi bi-shield-lock fs-3"></i>
                        </div>
                        <h5 class="card-title mb-0">Security Settings</h5>
                    </div>
                    <p class="card-text text-muted">
                        Configure security policies, password requirements, and access controls.
                    </p>
                    <span class="badge bg-secondary">Coming Soon</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .hover-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .hover-card:not(.opacity-50):hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush
@endsection
