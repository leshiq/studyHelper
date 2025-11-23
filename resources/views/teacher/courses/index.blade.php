@extends('layouts.app')

@section('title', 'My Courses - Study Helper')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5">
                <i class="bi bi-book"></i> My Courses
            </h1>
            <p class="text-muted">Manage your teaching courses</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('teacher.courses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create New Course
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($courses->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> You haven't created any courses yet. Click "Create New Course" to get started!
    </div>
    @else
    <div class="row g-4">
        @foreach($courses as $course)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-book-fill text-primary"></i> {{ $course->title }}
                    </h5>
                    
                    @if($course->description)
                    <p class="card-text text-muted">{{ Str::limit($course->description, 100) }}</p>
                    @endif

                    <div class="mt-3">
                        <div class="d-flex justify-content-between text-muted small mb-2">
                            <span>
                                <i class="bi bi-journal-text"></i> 
                                {{ $course->lessons_count }} lesson{{ $course->lessons_count != 1 ? 's' : '' }}
                            </span>
                            <span>
                                <i class="bi bi-people"></i> 
                                {{ $course->approved_students_count }} student{{ $course->approved_students_count != 1 ? 's' : '' }}
                            </span>
                        </div>

                        @if($course->enrollments_count > $course->approved_students_count)
                        <div class="alert alert-warning py-1 px-2 small mb-2">
                            <i class="bi bi-exclamation-triangle"></i> 
                            {{ $course->enrollments_count - $course->approved_students_count }} pending request{{ ($course->enrollments_count - $course->approved_students_count) != 1 ? 's' : '' }}
                        </div>
                        @endif

                        <div class="mb-2">
                            @if($course->is_active)
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                            @else
                                <span class="badge bg-secondary"><i class="bi bi-pause-circle"></i> Inactive</span>
                            @endif
                            
                            @if($course->is_available_to_all)
                                <span class="badge bg-info"><i class="bi bi-globe"></i> Public</span>
                            @else
                                <span class="badge bg-warning"><i class="bi bi-lock"></i> Private</span>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-primary">
                                <i class="bi bi-eye"></i> View & Manage
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
