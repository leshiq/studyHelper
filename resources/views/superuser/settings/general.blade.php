@extends('layouts.sidebar')

@section('title', 'General Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-toggles"></i> General Settings
        </h2>
        <a href="{{ route('superuser.settings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Settings
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-sliders"></i> Feature Toggles
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superuser.settings.general.update') }}" method="POST">
                        @csrf

                        <!-- Allow Password Change -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <label for="allow_password_change" class="form-label fw-bold">
                                        <i class="bi bi-key"></i> Allow Password Change
                                    </label>
                                    <p class="text-muted small mb-2">
                                        Enable or disable the ability for users to change their passwords through the profile settings page.
                                        When disabled, password fields will be hidden from the profile page.
                                    </p>
                                </div>
                                <div class="form-check form-switch ms-3" style="min-width: 60px;">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        role="switch" 
                                        id="allow_password_change" 
                                        name="allow_password_change" 
                                        value="1"
                                        {{ $allowPasswordChange ? 'checked' : '' }}
                                        style="width: 3em; height: 1.5em;"
                                    >
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card shadow-sm mt-4 border-info">
                <div class="card-body">
                    <h6 class="text-info">
                        <i class="bi bi-info-circle"></i> About Feature Toggles
                    </h6>
                    <p class="small text-muted mb-0">
                        Feature toggles allow you to enable or disable specific functionality across the application without code changes.
                        Changes take effect immediately for all users.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Current Settings Summary -->
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0">
                        <i class="bi bi-list-check"></i> Current Settings
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">Password Change</span>
                        @if($allowPasswordChange)
                            <span class="badge bg-success">Enabled</span>
                        @else
                            <span class="badge bg-secondary">Disabled</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card shadow-sm mt-3 border-warning">
                <div class="card-body">
                    <h6 class="text-warning">
                        <i class="bi bi-lightbulb"></i> Tips
                    </h6>
                    <ul class="small text-muted mb-0 ps-3">
                        <li>Disable password change if using SSO authentication</li>
                        <li>Enable when email password reset is configured</li>
                        <li>Changes apply to all users immediately</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
