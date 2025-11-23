@extends('layouts.app')

@section('title', $course->title . ' - Study Helper')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5">
                <i class="bi bi-book-fill"></i> {{ $course->title }}
            </h1>
            <p class="text-muted">
                <i class="bi bi-person"></i> Instructor: {{ $course->teacher->name }}
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Courses
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Course Description -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle"></i> About This Course</h5>
                    @if($course->description)
                        <p class="card-text">{{ $course->description }}</p>
                    @else
                        <p class="text-muted">No description provided</p>
                    @endif
                </div>
            </div>

            <!-- Course Lessons -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-journal-text"></i> Course Lessons</h5>
                </div>
                <div class="card-body">
                    @if($course->lessons->isEmpty())
                        <p class="text-muted">No lessons available yet.</p>
                    @else
                        <div class="list-group">
                            @foreach($course->lessons->sortBy('order') as $lesson)
                            <div class="list-group-item">
                                <div class="d-flex w-100 align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <span class="badge bg-primary me-2">{{ $lesson->order }}</span>
                                            {{ $lesson->title }}
                                            @if($enrollment && $lesson->progress->first())
                                                @php $progress = $lesson->progress->first(); @endphp
                                                @if($progress->is_completed)
                                                    <span class="badge bg-success ms-2">
                                                        <i class="bi bi-check-circle"></i> Completed
                                                    </span>
                                                @endif
                                            @endif
                                        </h6>
                                        
                                        @if($enrollment && $lesson->progress->first())
                                            @php $progress = $lesson->progress->first(); @endphp
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="text-muted">
                                                        Progress: {{ $progress->progress_percentage }}%
                                                        @if($progress->formatted_watch_time)
                                                            · {{ $progress->formatted_watch_time }} watched
                                                        @endif
                                                    </small>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar {{ $progress->is_completed ? 'bg-success' : 'bg-primary' }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $progress->progress_percentage }}%"
                                                         aria-valuenow="{{ $progress->progress_percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($lesson->description)
                                            <p class="mb-2 small">{{ $lesson->description }}</p>
                                        @endif
                                        @if($lesson->file)
                                            <div class="mt-2">
                                                @if($enrollment)
                                                    @if(Str::startsWith($lesson->file->mime_type, 'video/'))
                                                        <a href="{{ route('video.watch', $lesson->file) }}" class="btn btn-sm btn-primary me-2">
                                                            <i class="bi bi-play-circle"></i> 
                                                            @if($lesson->progress->first() && $lesson->progress->first()->watch_time_seconds > 0)
                                                                Resume Video
                                                            @else
                                                                Watch Video
                                                            @endif
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('file.download', $lesson->file) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                    <small class="text-muted ms-2">
                                                        {{ number_format($lesson->file->file_size / 1048576, 2) }} MB
                                                    </small>
                                                @else
                                                    <div class="alert alert-warning py-2 mb-0">
                                                        <small><i class="bi bi-lock"></i> Enroll to access course materials</small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Quizzes -->
                                        @if($enrollment && $lesson->activeQuizzes->isNotEmpty())
                                            <div class="mt-3">
                                                <strong class="small text-muted d-block mb-2">
                                                    <i class="bi bi-question-circle"></i> Quizzes ({{ $lesson->activeQuizzes->count() }})
                                                </strong>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($lesson->activeQuizzes as $quiz)
                                                        @php
                                                            $bestAttempt = $quiz->bestAttemptForStudent(Auth::id());
                                                        @endphp
                                                        <a href="{{ route('courses.quiz.show', [$course, $lesson, $quiz]) }}" 
                                                           class="btn btn-sm {{ $bestAttempt && $bestAttempt->is_passed ? 'btn-success' : 'btn-outline-info' }}">
                                                            <i class="bi bi-{{ $bestAttempt && $bestAttempt->is_passed ? 'check-circle-fill' : 'question-circle' }}"></i>
                                                            {{ $quiz->title }}
                                                            @if($bestAttempt)
                                                                <span class="badge bg-white text-dark ms-1">{{ $bestAttempt->score }}%</span>
                                                            @endif
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Enrollment Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-person-check"></i> Enrollment Status</h5>
                    @if($enrollment)
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> You are enrolled in this course!
                            <hr>
                            <small class="text-muted">
                                Enrolled: {{ $enrollment->approved_at->format('M d, Y') }}
                            </small>
                        </div>
                    @else
                        <p class="text-muted">You are not currently enrolled in this course.</p>
                        @if($course->is_available_to_all)
                            <form action="{{ route('courses.request', $course) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-plus-circle"></i> Request Enrollment
                                </button>
                            </form>
                        @else
                            <div class="alert alert-info mb-0">
                                <small><i class="bi bi-lock"></i> This is a private course. Contact the instructor for enrollment.</small>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Course Info -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-list-ul"></i> Course Details</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-journal-text text-primary"></i>
                            <strong>{{ $course->lessons->count() }}</strong> lesson{{ $course->lessons->count() != 1 ? 's' : '' }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-person text-primary"></i>
                            Instructor: <strong>{{ $course->teacher->name }}</strong>
                        </li>
                        @if($course->is_available_to_all)
                        <li class="mb-2">
                            <i class="bi bi-globe text-primary"></i>
                            <span class="badge bg-info">Public Course</span>
                        </li>
                        @else
                        <li class="mb-2">
                            <i class="bi bi-lock text-primary"></i>
                            <span class="badge bg-warning">Private Course</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            @if($enrollment)
            <!-- Course Chat -->
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Course Discussion</h5>
                </div>
                <div class="card-body p-0">
                    <div id="chatMessages" class="p-3 bg-light" style="height: 400px; overflow-y: auto;">
                        <div class="text-center text-muted">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading messages...</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <form id="chatForm" class="d-flex gap-2" data-course-id="{{ $course->id }}" data-user-id="{{ auth()->id() }}">
                        @csrf
                        <input type="text" id="chatInput" class="form-control" placeholder="Type your message..." maxlength="1000" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($enrollment)
@push('scripts')
<!-- Pusher loaded via Vite bundle -->
@endpush
@endif
@endsection
