@extends('layouts.app')

@section('title', 'Add New File - Admin')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                <i class="bi bi-plus-circle"></i> Add New File
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.files.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="filename" class="form-label">Filename <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('filename') is-invalid @enderror" 
                                   id="filename" name="filename" value="{{ old('filename') }}" 
                                   placeholder="lesson-01.mp4" required>
                            @error('filename')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">The actual filename on disk</small>
                        </div>

                        <div class="mb-3">
                            <label for="file_path" class="form-label">File Path <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('file_path') is-invalid @enderror" 
                                   id="file_path" name="file_path" value="{{ old('file_path') }}" 
                                   placeholder="uploads/lessons/lesson-01.mp4" required>
                            @error('file_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Relative path from storage/app/</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="file_size" class="form-label">File Size (bytes)</label>
                                <input type="number" class="form-control @error('file_size') is-invalid @enderror" 
                                       id="file_size" name="file_size" value="{{ old('file_size') }}">
                                @error('file_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="mime_type" class="form-label">MIME Type</label>
                                <input type="text" class="form-control @error('mime_type') is-invalid @enderror" 
                                       id="mime_type" name="mime_type" value="{{ old('mime_type', 'video/mp4') }}">
                                @error('mime_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="max_downloads" class="form-label">Max Downloads per Student</label>
                            <input type="number" class="form-control @error('max_downloads') is-invalid @enderror" 
                                   id="max_downloads" name="max_downloads" value="{{ old('max_downloads') }}" 
                                   min="1">
                            @error('max_downloads')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave empty for unlimited downloads</small>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" 
                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (visible to students)
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create File
                            </button>
                            <a href="{{ route('admin.files.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle"></i> Instructions</h5>
                    <p class="card-text small">
                        <strong>Before creating a file entry:</strong>
                    </p>
                    <ol class="small">
                        <li>Upload your video file to the server (e.g., via FTP/SSH)</li>
                        <li>Place it in the <code>storage/app/</code> directory</li>
                        <li>Note the exact path and filename</li>
                        <li>Fill in the form with the file details</li>
                        <li>Grant access to students after creating</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
