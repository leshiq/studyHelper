@extends('layouts.sidebar')

@section('title', 'All Users - Superuser Panel')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                <i class="bi bi-people-fill"></i> All Users
            </h1>
            <p class="text-muted">Manage all system users (Superusers, Admins, Students)</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">User List</h5>
        </div>
        <div class="card-body p-0">
            @if($users->isEmpty())
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2">No users found.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($user->avatar_small)
                                    <img src="{{ asset('avatars/small/' . $user->avatar_small) }}" alt="{{ $user->name }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                    <div class="rounded-circle me-2 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 32px; height: 32px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-size: 0.75rem;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    @endif
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->must_change_credentials)
                                        <span class="badge bg-warning text-dark ms-1">
                                            <i class="bi bi-exclamation-triangle"></i> Must Change Credentials
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="text-muted">{{ $user->email }}</code>
                            </td>
                            <td>
                                @if($user->is_superuser)
                                <span class="badge bg-danger">
                                    <i class="bi bi-shield-fill-exclamation"></i> Superuser
                                </span>
                                @elseif($user->is_admin)
                                <span class="badge bg-primary">
                                    <i class="bi bi-shield-fill-check"></i> Admin
                                </span>
                                @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-person"></i> Student
                                </span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Active
                                </span>
                                @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle"></i> Inactive
                                </span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary" disabled>
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
