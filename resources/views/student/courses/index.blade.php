@extends('layouts.app')

@section('title', 'Courses - Study Helper')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5">
                <i class="bi bi-book"></i> Courses
            </h1>
            <p class="text-muted">Browse and enroll in available courses</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- My Courses -->
    @if($myCourses->isNotEmpty())
    <div class="mb-5">
        <h3 class="mb-3"><i class="bi bi-mortarboard"></i> My Courses</h3>
        <div class="row g-4">
            @foreach($myCourses as $course)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-primary">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-book-fill text-primary"></i> {{ $course->title }}
                        </h5>
                        
                        @if($course->description)
                        <p class="card-text text-muted">{{ Str::limit($course->description, 100) }}</p>
                        @endif

                        <div class="mt-3">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-person"></i> Teacher: {{ $course->teacher->name }}
                            </div>
                            <div class="text-muted small mb-2">
                                <i class="bi bi-journal-text"></i> {{ $course->lessons_count }} lesson{{ $course->lessons_count != 1 ? 's' : '' }}
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <a href="{{ route('courses.show', $course) }}" class="btn btn-primary">
                                    <i class="bi bi-play-circle"></i> Continue Learning
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Pending Requests -->
    @if($pendingRequests->isNotEmpty())
    <div class="mb-5">
        <h3 class="mb-3"><i class="bi bi-clock-history"></i> Pending Enrollment Requests</h3>
        <div class="row g-4">
            @foreach($pendingRequests as $enrollment)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-warning">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-book text-warning"></i> {{ $enrollment->course->title }}
                        </h5>
                        
                        @if($enrollment->course->description)
                        <p class="card-text text-muted">{{ Str::limit($enrollment->course->description, 100) }}</p>
                        @endif

                        <div class="mt-3">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-person"></i> Teacher: {{ $enrollment->course->teacher->name }}
                            </div>
                            <div class="text-muted small mb-2">
                                <i class="bi bi-clock"></i> Requested: {{ $enrollment->created_at->diffForHumans() }}
                            </div>

                            <div class="alert alert-warning py-2 mt-3 mb-2">
                                <small><i class="bi bi-hourglass-split"></i> Waiting for teacher approval</small>
                            </div>

                            <form action="{{ route('courses.cancel-request', $enrollment->course) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                    <i class="bi bi-x"></i> Cancel Request
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Available Courses -->
    <div class="mb-5">
        <h3 class="mb-3"><i class="bi bi-grid"></i> Available Courses</h3>
        
        @if($availableCourses->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No new courses available at this time. Check back later!
        </div>
        @else
        <div class="row g-4">
            @foreach($availableCourses as $course)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-book-fill text-success"></i> {{ $course->title }}
                        </h5>
                        
                        @if($course->description)
                        <p class="card-text text-muted">{{ Str::limit($course->description, 100) }}</p>
                        @endif

                        <div class="mt-3">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-person"></i> Teacher: {{ $course->teacher->name }}
                            </div>
                            <div class="text-muted small mb-2">
                                <i class="bi bi-journal-text"></i> {{ $course->lessons_count }} lesson{{ $course->lessons_count != 1 ? 's' : '' }}
                            </div>

                            <div class="d-grid gap-2 mt-3">
                                <form action="{{ route('courses.request', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-plus-circle"></i> Request Enrollment
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
