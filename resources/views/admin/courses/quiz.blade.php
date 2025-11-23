@extends('layouts.app')

@section('title', $quiz->title . ' - Quiz Details')

@section('content')
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.lessons.show', [$course, $lesson]) }}">{{ $lesson->title }}</a></li>
            <li class="breadcrumb-item active">{{ $quiz->title }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2>{{ $quiz->title }}</h2>
                    <p class="text-muted mb-2">
                        <i class="bi bi-book"></i> Lesson: {{ $lesson->title }}
                    </p>
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge {{ $quiz->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $quiz->is_active ? 'Active' : 'Inactive' }}
                        </span>
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
                </div>
                <a href="{{ route('admin.courses.lessons.show', [$course, $lesson]) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Lesson
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Questions</h6>
                    <h3>{{ $quiz->questions->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Points</h6>
                    <h3>{{ $quiz->questions->sum('points') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Passing Score</h6>
                    <h3>{{ $quiz->passing_score }}%</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Attempts</h6>
                    <h3>{{ $quiz->attempts->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    @if($quiz->description)
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <strong>Quiz Description</strong>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $quiz->description }}</p>
            </div>
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <strong><i class="bi bi-gear"></i> Quiz Settings</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Time Limit:</strong> 
                            {{ $quiz->time_limit ? $quiz->time_limit . ' minutes' : 'No time limit' }}
                        </li>
                        <li class="mb-2">
                            <strong>Passing Score:</strong> {{ $quiz->passing_score }}%
                        </li>
                        <li class="mb-2">
                            <strong>Allow Retakes:</strong> 
                            <span class="badge {{ $quiz->allow_retakes ? 'bg-success' : 'bg-danger' }}">
                                {{ $quiz->allow_retakes ? 'Yes' : 'No' }}
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Repeatable:</strong> 
                            <span class="badge {{ $quiz->is_repeatable ? 'bg-success' : 'bg-danger' }}">
                                {{ $quiz->is_repeatable ? 'Yes' : 'No' }}
                            </span>
                        </li>
                        <li class="mb-2">
                            <strong>Show Correct Answers:</strong> 
                            <span class="badge {{ $quiz->show_correct_answers ? 'bg-success' : 'bg-danger' }}">
                                {{ $quiz->show_correct_answers ? 'Yes' : 'No' }}
                            </span>
                        </li>
                        <li class="mb-2">
                            <strong>Created:</strong> {{ $quiz->created_at->format('M d, Y h:i A') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <strong><i class="bi bi-list-ol"></i> Questions ({{ $quiz->questions->count() }})</strong>
        </div>
        <div class="card-body">
            @if($quiz->questions->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> No questions have been added to this quiz yet.
                </div>
            @else
                <div class="accordion" id="questionsAccordion">
                    @foreach($quiz->questions->sortBy('order') as $index => $question)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#question{{ $question->id }}">
                                    <strong>Q{{ $question->order }}:</strong>&nbsp;{{ Str::limit($question->question, 80) }}
                                    <span class="ms-auto me-3">
                                        <span class="badge bg-primary">{{ $question->points }} pts</span>
                                        <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $question->type)) }}</span>
                                    </span>
                                </button>
                            </h2>
                            <div id="question{{ $question->id }}" 
                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                 data-bs-parent="#questionsAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3"><strong>Question:</strong> {{ $question->question }}</p>
                                    
                                    @if($question->explanation)
                                        <p class="mb-3 text-muted">
                                            <strong>Explanation:</strong> {{ $question->explanation }}
                                        </p>
                                    @endif

                                    <div class="mb-3">
                                        <strong>Options:</strong>
                                        <ul class="list-group mt-2">
                                            @foreach($question->options->sortBy('order') as $option)
                                                <li class="list-group-item {{ $option->is_correct ? 'list-group-item-success' : '' }}">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ $option->option_text }}</span>
                                                        @if($option->is_correct)
                                                            <span class="badge bg-success">Correct Answer</span>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <strong><i class="bi bi-graph-up"></i> All Quiz Attempts ({{ $quiz->attempts->count() }})</strong>
        </div>
        <div class="card-body">
            @if($quiz->attempts->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> No students have attempted this quiz yet.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Score</th>
                                <th>Points</th>
                                <th>Status</th>
                                <th>Started</th>
                                <th>Completed</th>
                                <th>Duration</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quiz->attempts as $attempt)
                                <tr>
                                    <td>
                                        <i class="bi bi-person"></i> {{ $attempt->student->name }}
                                        <br>
                                        <small class="text-muted">{{ $attempt->student->email }}</small>
                                    </td>
                                    <td>
                                        @if($attempt->completed_at)
                                            <strong class="{{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($attempt->score, 1) }}%
                                            </strong>
                                        @else
                                            <span class="text-muted">In Progress</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->completed_at)
                                            {{ $attempt->points_earned }} / {{ $attempt->total_points }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->completed_at)
                                            <span class="badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}">
                                                {{ $attempt->is_passed ? 'Passed' : 'Failed' }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning">In Progress</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        {{ $attempt->started_at->format('M d, Y') }}
                                        <br>
                                        {{ $attempt->started_at->format('h:i A') }}
                                    </td>
                                    <td class="text-muted small">
                                        @if($attempt->completed_at)
                                            {{ $attempt->completed_at->format('M d, Y') }}
                                            <br>
                                            {{ $attempt->completed_at->format('h:i A') }}
                                        @else
                                            <span class="text-warning">Ongoing</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        @if($attempt->completed_at)
                                            {{ $attempt->started_at->diffForHumans($attempt->completed_at, true) }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.courses.lessons.quizzes.attempts.show', [$course, $lesson, $quiz, $attempt]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
