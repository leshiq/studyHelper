@extends('layouts.app')

@section('title', 'Student Invitations')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                <i class="bi bi-envelope-plus"></i> Student Invitations
            </h1>
            <p class="text-muted">Create temporary registration links for new students</p>
        </div>
        <div class="col-auto">
            <form action="{{ route('admin.invitations.create') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Generate New Invitation Link
                </button>
            </form>
        </div>
    </div>

    @if(session('invitation_created'))
    @php
        $invitation = session('invitation_created');
        $link = route('register.show', $invitation->token);
    @endphp
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <h5 class="alert-heading"><i class="bi bi-check-circle"></i> Invitation Link Created!</h5>
        <p class="mb-3">Share this link with the new student. It will expire in 24 hours or when used.</p>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="invitationLink" value="{{ $link }}" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="copyLink(event)">
                <i class="bi bi-clipboard"></i> Copy
            </button>
        </div>
        <small class="text-muted">
            <i class="bi bi-clock"></i> Expires: {{ $invitation->expires_at->format('M d, Y H:i') }}
        </small>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">All Invitation Links</h5>
        </div>
        <div class="card-body p-0">
            @if($invitations->isEmpty())
            <div class="p-4 text-center text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2">No invitation links created yet.</p>
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Link</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>Used By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invitations as $invitation)
                        <tr>
                            <td>
                                <code class="text-muted small">...{{ substr($invitation->token, -12) }}</code>
                            </td>
                            <td>
                                @if($invitation->used_at)
                                <span class="badge bg-secondary">
                                    <i class="bi bi-check-circle"></i> Used
                                </span>
                                @elseif($invitation->expires_at->isPast())
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle"></i> Expired
                                </span>
                                @else
                                <span class="badge bg-success">
                                    <i class="bi bi-clock"></i> Active
                                </span>
                                @endif
                            </td>
                            <td>{{ $invitation->creator->name }}</td>
                            <td>{{ $invitation->created_at->format('M d, H:i') }}</td>
                            <td>{{ $invitation->expires_at->format('M d, H:i') }}</td>
                            <td>
                                @if($invitation->student)
                                    <a href="{{ route('admin.students.show', $invitation->student) }}">
                                        {{ $invitation->student->name }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($invitation->isValid())
                                <button class="btn btn-sm btn-outline-primary copy-invitation-btn" data-url="{{ route('register.show', $invitation->token) }}">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                                @endif
                                <form action="{{ route('admin.invitations.destroy', $invitation) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Delete this invitation link?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @if($invitations->hasPages())
        <div class="card-footer">
            {{ $invitations->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
@endpush
@endsection
