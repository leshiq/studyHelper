@extends('layouts.app')

@section('title', 'Create Course - Study Helper')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5">
                <i class="bi bi-plus-circle"></i> Create New Course
            </h1>
            <p class="text-muted">Set up a new course for your students</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('teacher.courses.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Course Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Provide an overview of what students will learn in this course</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_available_to_all" 
                                       name="is_available_to_all" value="1" {{ old('is_available_to_all') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_available_to_all">
                                    <i class="bi bi-globe"></i> Make course publicly available
                                </label>
                                <div class="form-text">If enabled, all students can request enrollment. Otherwise, you must manually enroll students.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="bi bi-check-circle"></i> Course is active
                                </label>
                                <div class="form-text">Inactive courses are hidden from students</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Create Course
                            </button>
                            <a href="{{ route('teacher.courses.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle"></i> Course Setup Tips</h5>
                    <ul class="small">
                        <li>Choose a clear, descriptive title</li>
                        <li>Write a detailed description to help students understand the course content</li>
                        <li>Make the course public if you want students to discover and request enrollment</li>
                        <li>Keep the course inactive until you've added all lessons</li>
                        <li>You can add lessons and manage enrollments after creating the course</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
