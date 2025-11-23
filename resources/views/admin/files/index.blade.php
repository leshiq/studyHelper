@extends('layouts.sidebar')

@section('title', 'Manage Files - Admin')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                <i class="bi bi-file-earmark-text"></i> Manage Files
            </h1>
            <p class="text-muted">Files in storage/app/uploads/lessons/</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(empty($filesInStorage))
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No files found in storage directory.
    </div>
    @else
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Type</th>
                            <th>Modified</th>
                            <th>Status</th>
                            <th>Students</th>
                            <th>Downloads</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filesInStorage as $fileInfo)
                        <tr>
                            <td>
                                <code>{{ $fileInfo['filename'] }}</code>
                                @if(isset($fileInfo['db_record']))
                                <br><small class="text-muted">{{ $fileInfo['db_record']->title }}</small>
                                @endif
                            </td>
                            <td>{{ number_format($fileInfo['size'] / 1048576, 2) }} MB</td>
                            <td>
                                @if(str_starts_with($fileInfo['mime_type'], 'video/'))
                                <i class="bi bi-camera-video text-primary"></i> Video
                                @elseif(str_starts_with($fileInfo['mime_type'], 'application/pdf'))
                                <i class="bi bi-file-pdf text-danger"></i> PDF
                                @else
                                <i class="bi bi-file-earmark"></i> {{ $fileInfo['mime_type'] }}
                                @endif
                            </td>
                            <td>{{ date('M d, Y H:i', $fileInfo['modified']) }}</td>
                            <td>
                                @if(isset($fileInfo['db_record']))
                                    @if($fileInfo['db_record']->is_active)
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                @else
                                <span class="badge bg-warning text-dark">Not Saved</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($fileInfo['db_record']))
                                {{ $fileInfo['db_record']->students_count }}
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($fileInfo['db_record']))
                                {{ $fileInfo['db_record']->download_logs_count }}
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($fileInfo['db_record']))
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.files.show', $fileInfo['db_record']) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.files.edit', $fileInfo['db_record']) }}" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $fileInfo['db_record']->id }}" title="Delete from DB">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $fileInfo['db_record']->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete <strong>{{ $fileInfo['db_record']->title }}</strong> from the database?
                                                <div class="alert alert-warning mt-3">
                                                    <i class="bi bi-exclamation-triangle"></i> The physical file will remain in storage.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.files.destroy', $fileInfo['db_record']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete from DB</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#saveModal{{ md5($fileInfo['filename']) }}">
                                    <i class="bi bi-save"></i> Save to DB
                                </button>

                                <!-- Save Modal -->
                                <div class="modal fade" id="saveModal{{ md5($fileInfo['filename']) }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.files.store') }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Save File to Database</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="filename" value="{{ $fileInfo['filename'] }}">
                                                    <input type="hidden" name="file_path" value="{{ $fileInfo['path'] }}">
                                                    <input type="hidden" name="file_size" value="{{ $fileInfo['size'] }}">
                                                    <input type="hidden" name="mime_type" value="{{ $fileInfo['mime_type'] }}">
                                                    <input type="hidden" name="is_active" value="1">

                                                    <div class="mb-3">
                                                        <label class="form-label">Filename</label>
                                                        <input type="text" class="form-control" value="{{ $fileInfo['filename'] }}" readonly>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="title" value="{{ pathinfo($fileInfo['filename'], PATHINFO_FILENAME) }}" required>
                                                        <div class="form-text">Display name for students</div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Max Downloads per Student</label>
                                                        <input type="number" class="form-control" name="max_downloads" min="1" placeholder="Unlimited">
                                                    </div>

                                                    <div class="alert alert-info">
                                                        <strong>File Info:</strong><br>
                                                        Size: {{ number_format($fileInfo['size'] / 1048576, 2) }} MB<br>
                                                        Type: {{ $fileInfo['mime_type'] }}
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-save"></i> Save File
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection