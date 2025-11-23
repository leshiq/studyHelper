@extends('layouts.app')

@section('title', 'Change Credentials')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-75 align-items-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock text-warning" style="font-size: 3rem;"></i>
                        <h2 class="mt-2">Change Default Credentials</h2>
                        <p class="text-muted">For security reasons, you must change your login credentials before continuing.</p>
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle"></i> You cannot use the default credentials (superadmin/superadmin)
                        </div>
                    </div>

                    <form method="POST" action="{{ route('credentials.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="email" class="form-label">New Email/Login</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">This will be your new login email</small>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Update Credentials
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
