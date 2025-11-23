@extends('layouts.app')

@section('title', $lesson->title . ' - Lesson Details')

@section('content')
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item active">{{ $lesson->title }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2>{{ $lesson->order }}. {{ $lesson->title }}</h2>
                    <p class="text-muted mb-2">
                        <i class="bi bi-mortarboard"></i> Course: {{ $course->title }}
                    </p>
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge {{ $lesson->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $lesson->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="badge bg-info">{{ $lesson->quizzes->count() }} {{ Str::plural('Quiz', $lesson->quizzes->count()) }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Course
                </a>
            </div>
        </div>
    </div>

    @if($lesson->description)
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <strong>Lesson Description</strong>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $lesson->description }}</p>
            </div>
        </div>
    @endif

    @if($lesson->file)
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <strong><i class="bi bi-file-earmark"></i> Lesson File</strong>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $lesson->file->filename }}</strong>
                        <br>
                        <small class="text-muted">
                            Size: {{ number_format($lesson->file->size / 1024, 2) }} KB
                            | Uploaded: {{ $lesson->file->created_at->format('M d, Y h:i A') }}
                        </small>
                    </div>
                    <a href="{{ route('file.download', $lesson->file->id) }}" 
                       class="btn btn-sm btn-primary">
                        <i class="bi bi-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <strong><i class="bi bi-clipboard-check"></i> Quizzes ({{ $lesson->quizzes->count() }})</strong>
        </div>
        <div class="card-body">
            @if($lesson->quizzes->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> No quizzes have been created for this lesson yet.
                </div>
            @else
                <div class="row">
                    @foreach($lesson->quizzes as $quiz)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-{{ $quiz->is_active ? 'success' : 'secondary' }}">
                                <div class="card-header bg-{{ $quiz->is_active ? 'success' : 'secondary' }} text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>{{ $quiz->title }}</strong>
                                        <span class="badge bg-light text-dark">
                                            {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($quiz->description)
                                        <p class="text-muted small mb-3">{{ Str::limit($quiz->description, 100) }}</p>
                                    @endif

                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <small class="text-muted d-block">Questions</small>
                                                <strong>{{ $quiz->questions->count() }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <small class="text-muted d-block">Total Points</small>
                                                <strong>{{ $quiz->questions->sum('points') }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <small class="text-muted d-block">Passing Score</small>
                                                <strong>{{ $quiz->passing_score }}%</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded">
                                                <small class="text-muted d-block">Time Limit</small>
                                                <strong>{{ $quiz->time_limit ? $quiz->time_limit . ' min' : 'None' }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 mb-3">
                                        @if($quiz->allow_retakes)
                                            <span class="badge bg-info">Retakes Allowed</span>
                                        @endif
                                        @if($quiz->is_repeatable)
                                            <span class="badge bg-warning">Repeatable</span>
                                        @endif
                                        @if($quiz->show_correct_answers)
                                            <span class="badge bg-success">Shows Answers</span>
                                        @endif
                                    </div>

                                    <div class="border-top pt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">
                                                <i class="bi bi-graph-up"></i> 
                                                <strong>{{ $quiz->attempts->count() }}</strong> attempts
                                            </span>
                                            <a href="{{ route('admin.courses.lessons.quizzes.show', [$course, $lesson, $quiz]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View Details
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
    </div>
</div>
@endsection
