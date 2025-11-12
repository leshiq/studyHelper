@extends('layouts.app')

@section('title', $file->title . ' - Admin')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                <i class="bi bi-file-earmark-text"></i> {{ $file->title }}
            </h1>
            @if($file->is_active)
            <span class="badge bg-success">Active</span>
            @else
            <span class="badge bg-secondary">Inactive</span>
            @endif
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.files.edit', $file) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">File Details</h5>
                </div>
                <div class="card-body">
                    @if($file->description)
                    <p class="text-muted">{{ $file->description }}</p>
                    @endif

                    <table class="table table-sm">
                        <tr>
                            <th width="200">Filename:</th>
                            <td><code>{{ $file->filename }}</code></td>
                        </tr>
                        <tr>
                            <th>File Path:</th>
                            <td><code>{{ $file->file_path }}</code></td>
                        </tr>
                        <tr>
                            <th>File Size:</th>
                            <td>{{ number_format($file->file_size / 1048576, 2) }} MB</td>
                        </tr>
                        <tr>
                            <th>MIME Type:</th>
                            <td>{{ $file->mime_type }}</td>
                        </tr>
                        <tr>
                            <th>Max Downloads:</th>
                            <td>{{ $file->max_downloads ?? 'Unlimited' }}</td>
                        </tr>
                        <tr>
                            <th>Total Downloads:</th>
                            <td>{{ $file->downloadLogs->count() }}</td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $file->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Recent Downloads</h5>
                </div>
                <div class="card-body">
                    @if($file->downloadLogs->isEmpty())
                    <p class="text-muted">No downloads yet.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Date</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($file->downloadLogs as $log)
                                <tr>
                                    <td>{{ $log->student->name }}</td>
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

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Student Access</h5>
                </div>
                <div class="card-body">
                    <h6>Grant Access</h6>
                    <form action="{{ route('admin.files.grant-access', $file) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select @error('student_id') is-invalid @enderror" 
                                    id="student_id" name="student_id" required>
                                <option value="">Select a student...</option>
                                @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="expires_at" class="form-label">Expires At (Optional)</label>
                            <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                   id="expires_at" name="expires_at">
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-plus-circle"></i> Grant Access
                        </button>
                    </form>

                    <hr>

                    <h6>Current Access</h6>
                    @if($file->students->isEmpty())
                    <p class="text-muted small">No students have access yet.</p>
                    @else
                    <div class="list-group list-group-flush">
                        @foreach($file->students as $student)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $student->name }}</strong>
                                    @if($student->pivot->expires_at)
                                    <br><small class="text-muted">
                                        Expires: {{ $student->pivot->expires_at->format('M d, Y') }}
                                    </small>
                                    @endif
                                </div>
                                <form action="{{ route('admin.files.revoke-access', [$file, $student]) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Revoke access for {{ $student->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
