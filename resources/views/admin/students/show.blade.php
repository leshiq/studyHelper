@extends('layouts.sidebar')

@section('title', $student->name . ' - Admin')

@section('content')
<div class="container">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                <i class="bi bi-person-circle"></i> {{ $student->name }}
            </h1>
            @if($student->is_active)
            <span class="badge bg-success">Active</span>
            @else
            <span class="badge bg-danger">Inactive</span>
            @endif
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Student Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="100">Name:</th>
                            <td>{{ $student->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $student->email }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($student->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Joined:</th>
                            <td>{{ $student->created_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Files Access:</span>
                        <strong>{{ $student->downloadableFiles->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Downloads:</span>
                        <strong>{{ $student->downloadLogs->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Accessible Files</h5>
                    @if(!$availableFiles->isEmpty())
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#grantAccessModal">
                        <i class="bi bi-plus-circle"></i> Grant Access
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($student->downloadableFiles->isEmpty())
                    <p class="text-muted">No files assigned yet.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Granted</th>
                                    <th>Expires</th>
                                    <th>Downloads</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->downloadableFiles as $file)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.files.show', $file) }}">
                                            {{ $file->title }}
                                        </a>
                                    </td>
                                    <td>{{ $file->pivot->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($file->pivot->expires_at)
                                        {{ $file->pivot->expires_at->format('M d, Y') }}
                                        @else
                                        <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $student->downloadLogs->where('downloadable_file_id', $file->id)->count() }}
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.students.revoke-access', [$student, $file]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Revoke access to this file?')">
                                                <i class="bi bi-x-circle"></i>
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
            </div>

            <!-- Grant Access Modal -->
            @if(!$availableFiles->isEmpty())
            <div class="modal fade" id="grantAccessModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.students.grant-access', $student) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Grant File Access</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Select File <span class="text-danger">*</span></label>
                                    <select class="form-select" name="file_id" required>
                                        <option value="">Choose a file...</option>
                                        @foreach($availableFiles as $file)
                                        <option value="{{ $file->id }}">{{ $file->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Expiration Date (Optional)</label>
                                    <input type="date" class="form-control" name="expires_at" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    <div class="form-text">Leave blank for permanent access</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Grant Access
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Recent Downloads</h5>
                </div>
                <div class="card-body">
                    @if($student->downloadLogs->isEmpty())
                    <p class="text-muted">No downloads yet.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Date</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->downloadLogs as $log)
                                <tr>
                                    <td>{{ $log->downloadableFile->title }}</td>
                                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                    <td><code>{{ $log->ip_address }}</code></td>
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
