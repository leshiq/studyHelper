@extends('layouts.app')

@section('title', 'Manage Files - Admin')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                <i class="bi bi-file-earmark-text"></i> Manage Files
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.files.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New File
            </a>
        </div>
    </div>

    @if($files->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No files have been added yet.
    </div>
    @else
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Students</th>
                            <th>Downloads</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                        <tr>
                            <td>
                                <strong>{{ $file->title }}</strong>
                                @if($file->description)
                                <br><small class="text-muted">{{ Str::limit($file->description, 50) }}</small>
                                @endif
                            </td>
                            <td><code>{{ $file->filename }}</code></td>
                            <td>{{ number_format($file->file_size / 1048576, 2) }} MB</td>
                            <td>{{ $file->students_count }}</td>
                            <td>{{ $file->download_logs_count }}</td>
                            <td>
                                @if($file->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.files.show', $file) }}" class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.files.edit', $file) }}" class="btn btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $file->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $file->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete <strong>{{ $file->title }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.files.destroy', $file) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
