@extends('layouts.app')

@section('title', $student->name . ' - Admin')

@section('content')
<div class="container">
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
                <div class="card-header">
                    <h5 class="mb-0">Accessible Files</h5>
                </div>
                <div class="card-body">
                    @if($student->downloadableFiles->isEmpty())
                    <p class="text-muted">No files assigned yet.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Granted</th>
                                    <th>Expires</th>
                                    <th>Downloads</th>
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
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

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
