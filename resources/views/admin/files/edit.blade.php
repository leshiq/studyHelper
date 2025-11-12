@extends('layouts.app')

@section('title', 'Edit ' . $file->title . ' - Admin')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-6">
                <i class="bi bi-pencil"></i> Edit File
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.files.update', $file) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $file->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $file->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="filename" class="form-label">Filename <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('filename') is-invalid @enderror" 
                                   id="filename" name="filename" value="{{ old('filename', $file->filename) }}" required>
                            @error('filename')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file_path" class="form-label">File Path <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('file_path') is-invalid @enderror" 
                                   id="file_path" name="file_path" value="{{ old('file_path', $file->file_path) }}" required>
                            @error('file_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="file_size" class="form-label">File Size (bytes)</label>
                                <input type="number" class="form-control @error('file_size') is-invalid @enderror" 
                                       id="file_size" name="file_size" value="{{ old('file_size', $file->file_size) }}">
                                @error('file_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="mime_type" class="form-label">MIME Type</label>
                                <input type="text" class="form-control @error('mime_type') is-invalid @enderror" 
                                       id="mime_type" name="mime_type" value="{{ old('mime_type', $file->mime_type) }}">
                                @error('mime_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="max_downloads" class="form-label">Max Downloads per Student</label>
                            <input type="number" class="form-control @error('max_downloads') is-invalid @enderror" 
                                   id="max_downloads" name="max_downloads" value="{{ old('max_downloads', $file->max_downloads) }}" 
                                   min="1">
                            @error('max_downloads')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" 
                                   name="is_active" value="1" {{ old('is_active', $file->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (visible to students)
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update File
                            </button>
                            <a href="{{ route('admin.files.show', $file) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
