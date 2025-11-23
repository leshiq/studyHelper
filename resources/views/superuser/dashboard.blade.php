@extends('layouts.sidebar')

@section('title', 'Superuser Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                <i class="bi bi-speedometer2"></i> Dashboard
            </h1>
            <p class="text-muted">System overview and user activities</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Users</p>
                            <h3 class="mb-0">{{ $stats['total_users'] }}</h3>
                        </div>
                        <div class="fs-1 text-primary">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    <div class="mt-2 small">
                        <span class="text-danger"><i class="bi bi-shield-fill-exclamation"></i> {{ $stats['superusers'] }} Superusers</span> |
                        <span class="text-primary"><i class="bi bi-shield-fill-check"></i> {{ $stats['admins'] }} Admins</span> |
                        <span class="text-secondary"><i class="bi bi-person"></i> {{ $stats['students'] }} Students</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Active Users</p>
                            <h3 class="mb-0">{{ $stats['active_users'] }}</h3>
                        </div>
                        <div class="fs-1 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        {{ number_format(($stats['active_users'] / max($stats['total_users'], 1)) * 100, 1) }}% of total users
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Downloads</p>
                            <h3 class="mb-0">{{ $stats['total_downloads'] }}</h3>
                        </div>
                        <div class="fs-1 text-info">
                            <i class="bi bi-download"></i>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        <i class="bi bi-calendar-day"></i> {{ $stats['downloads_today'] }} today
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Invitations</p>
                            <h3 class="mb-0">{{ $stats['invitations_sent'] }}</h3>
                        </div>
                        <div class="fs-1 text-warning">
                            <i class="bi bi-envelope-plus"></i>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        {{ $stats['invitations_used'] }} used
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Users -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-plus"></i> Recent Registrations</h5>
                </div>
                <div class="card-body p-0">
                    @if($recent_users->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <p>No users yet</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_users as $user)
                                <tr>
                                    <td>
                                        <strong>{{ $user->name }}</strong><br>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </td>
                                    <td>
                                        @if($user->is_superuser)
                                        <span class="badge bg-danger">Superuser</span>
                                        @elseif($user->is_admin)
                                        <span class="badge bg-primary">Admin</span>
                                        @else
                                        <span class="badge bg-secondary">Student</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $user->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Most Active Users -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Most Active Users</h5>
                </div>
                <div class="card-body p-0">
                    @if($active_users->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <p>No activity yet</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Downloads</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($active_users as $user)
                                <tr>
                                    <td>
                                        <strong>{{ $user->name }}</strong><br>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $user->download_count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Downloads -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Download Activity</h5>
                </div>
                <div class="card-body p-0">
                    @if($recent_downloads->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <p>No downloads yet</p>
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>File</th>
                                    <th>IP Address</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_downloads as $log)
                                <tr>
                                    <td>
                                        <strong>{{ $log->student->name }}</strong>
                                    </td>
                                    <td>
                                        {{ $log->downloadableFile->title }}
                                    </td>
                                    <td>
                                        <code class="text-muted">{{ $log->ip_address }}</code>
                                    </td>
                                    <td>
                                        <small>{{ $log->created_at->diffForHumans() }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
