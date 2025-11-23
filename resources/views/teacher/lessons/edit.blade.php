@extends('layouts.app')

@section('title', 'Edit Lesson - ' . $lesson->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.courses.index') }}">My Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.courses.show', $course) }}">{{ $course->title }}</a></li>
                    <li class="breadcrumb-item active">Edit: {{ $lesson->title }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Lesson Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-journal-text"></i> Lesson Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.courses.lessons.update', [$course, $lesson]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Lesson Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           name="title" value="{{ old('title', $lesson->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Order</label>
                                    <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                           name="order" value="{{ old('order', $lesson->order) }}" min="0">
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      name="description" rows="3">{{ old('description', $lesson->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Attached File</label>
                            <select class="form-select @error('downloadable_file_id') is-invalid @enderror" 
                                    name="downloadable_file_id">
                                <option value="">No file</option>
                                @foreach($availableFiles as $file)
                                    <option value="{{ $file->id }}" 
                                            {{ old('downloadable_file_id', $lesson->downloadable_file_id) == $file->id ? 'selected' : '' }}>
                                        {{ $file->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('downloadable_file_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_published" value="1" 
                                   id="is_published" {{ old('is_published', $lesson->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">
                                Published (visible to students)
                            </label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Lesson
                            </button>
                            <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @include('teacher.lessons._quizzes_section')
        </div>
    </div>
</div>
@endsection
