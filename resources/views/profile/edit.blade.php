@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> Please fix the following errors:
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Profile Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            @if($user->avatar_large)
                                <img src="{{ asset('avatars/large/' . $user->avatar_large) }}" alt="{{ $user->name }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col">
                            <h3 class="mb-1">{{ $user->name }}</h3>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            @if($user->is_superuser)
                                <span class="badge bg-danger"><i class="bi bi-shield-fill-exclamation"></i> Super Administrator</span>
                            @elseif($user->is_admin)
                                <span class="badge bg-primary"><i class="bi bi-shield-fill-check"></i> Administrator</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-person"></i> Student</span>
                            @endif
                        </div>
                        <div class="col-auto">
                            <small class="text-muted">
                                <i class="bi bi-calendar-check"></i> Member since {{ $user->created_at->format('M Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="avatar-tab" data-bs-toggle="tab" data-bs-target="#avatar" type="button" role="tab">
                        <i class="bi bi-image"></i> Avatar
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                        <i class="bi bi-person-circle"></i> Personal Info
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="preferences-tab" data-bs-toggle="tab" data-bs-target="#preferences" type="button" role="tab">
                        <i class="bi bi-palette"></i> Preferences
                    </button>
                </li>
                @if($allowPasswordChange)
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                        <i class="bi bi-shield-lock"></i> Security
                    </button>
                </li>
                @endif
            </ul>

            <div class="tab-content" id="profileTabsContent">
                <!-- Avatar Tab -->
                <div class="tab-pane fade show active" id="avatar" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="bi bi-image"></i> Profile Picture</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <form action="{{ route('profile.avatar.upload') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        
                                        <div class="mb-4">
                                            <label for="avatar" class="form-label">Upload New Avatar</label>
                                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" required>
                                            <div class="form-text">
                                                Maximum file size: 5MB. Supported formats: JPG, PNG, GIF, WebP
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-upload"></i> Upload Avatar
                                            </button>
                                            @if($user->avatar_original)
                                            <form action="{{ route('profile.avatar.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove your avatar?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Remove Avatar
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <h6 class="text-info"><i class="bi bi-info-circle"></i> Avatar Sizes</h6>
                                            <p class="small text-muted mb-2">Your avatar will be automatically optimized in multiple sizes:</p>
                                            <ul class="small text-muted mb-0 ps-3">
                                                <li>Large: 400x400px</li>
                                                <li>Medium: 200x200px</li>
                                                <li>Small: 64x64px</li>
                                            </ul>
                                            <p class="small text-muted mt-2 mb-0">
                                                Images are converted to WebP format for optimal performance.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Info Tab -->
                <div class="tab-pane fade" id="personal" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="bi bi-person-circle"></i> Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input 
                                            type="text" 
                                            class="form-control @error('name') is-invalid @enderror" 
                                            id="name" 
                                            name="name" 
                                            value="{{ old('name', $user->name) }}" 
                                            required
                                        >
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input 
                                            type="email" 
                                            class="form-control @error('email') is-invalid @enderror" 
                                            id="email" 
                                            name="email" 
                                            value="{{ old('email', $user->email) }}" 
                                            required
                                        >
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Preferences Tab -->
                <div class="tab-pane fade" id="preferences" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="bi bi-palette"></i> Display Preferences</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <!-- Hidden fields to prevent validation errors -->
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">

                                <h6 class="mb-3">Theme</h6>
                                
                                <div class="mb-4">
                                    <label class="form-label">Choose your preferred theme</label>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="theme_preference" id="theme_light" value="light" {{ $user->theme_preference === 'light' ? 'checked' : '' }}>
                                            <label class="card theme-option w-100 h-100" for="theme_light" style="cursor: pointer; transition: all 0.2s;">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-sun-fill text-warning" style="font-size: 2rem;"></i>
                                                    <div class="mt-2 fw-bold">Light</div>
                                                    <small class="text-muted">Bright and clean</small>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="theme_preference" id="theme_dark" value="dark" {{ $user->theme_preference === 'dark' ? 'checked' : '' }}>
                                            <label class="card theme-option w-100 h-100" for="theme_dark" style="cursor: pointer; transition: all 0.2s;">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-moon-stars-fill text-primary" style="font-size: 2rem;"></i>
                                                    <div class="mt-2 fw-bold">Dark</div>
                                                    <small class="text-muted">Easy on the eyes</small>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="theme_preference" id="theme_auto" value="auto" {{ $user->theme_preference === 'auto' ? 'checked' : '' }}>
                                            <label class="card theme-option w-100 h-100" for="theme_auto" style="cursor: pointer; transition: all 0.2s;">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-circle-half text-info" style="font-size: 2rem;"></i>
                                                    <div class="mt-2 fw-bold">Auto</div>
                                                    <small class="text-muted">Match system</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="bi bi-info-circle"></i> Auto mode will match your system's theme preference
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Save Preferences
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                @if($allowPasswordChange)
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0"><i class="bi bi-shield-lock"></i> Security Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <h6 class="mb-3">Change Password</h6>

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input 
                                        type="password" 
                                        class="form-control @error('current_password') is-invalid @enderror" 
                                        id="current_password" 
                                        name="current_password"
                                    >
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave blank to keep your current password</div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input 
                                            type="password" 
                                            class="form-control @error('new_password') is-invalid @enderror" 
                                            id="new_password" 
                                            name="new_password"
                                            minlength="8"
                                        >
                                        @error('new_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Minimum 8 characters</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                        <input 
                                            type="password" 
                                            class="form-control" 
                                            id="new_password_confirmation" 
                                            name="new_password_confirmation"
                                            minlength="8"
                                        >
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .theme-option {
        border: 2px solid #dee2e6;
        transition: all 0.2s ease-in-out;
    }
    
    .theme-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #0d6efd;
    }
    
    .btn-check:checked + .theme-option {
        border-color: #0d6efd;
        border-width: 3px;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .btn-check:checked + .theme-option .card-body {
        background-color: transparent;
    }
</style>
@endpush
