@extends('layouts.app')

@section('title', $quiz->title . ' - ' . $lesson->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">My Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}#lesson-{{ $lesson->id }}">{{ $lesson->title }}</a></li>
                    <li class="breadcrumb-item active">{{ $quiz->title }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Quiz Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-question-circle"></i> {{ $quiz->title }}</h4>
                </div>
                <div class="card-body">
                    @if($quiz->description)
                        <p class="lead">{{ $quiz->description }}</p>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="bi bi-check-circle text-success"></i> 
                                <strong>Passing Score:</strong> {{ $quiz->passing_score }}%
                            </p>
                            <p class="mb-2">
                                <i class="bi bi-star-fill text-warning"></i> 
                                <strong>Total Points:</strong> {{ $quiz->total_points }}
                            </p>
                            <p class="mb-2">
                                <i class="bi bi-list-ol"></i> 
                                <strong>Questions:</strong> {{ $quiz->questions->count() }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            @if($quiz->time_limit)
                                <p class="mb-2">
                                    <i class="bi bi-clock text-info"></i> 
                                    <strong>Time Limit:</strong> {{ $quiz->time_limit }} minutes
                                </p>
                            @else
                                <p class="mb-2">
                                    <i class="bi bi-clock text-muted"></i> 
                                    <strong>Time Limit:</strong> No limit
                                </p>
                            @endif
                            <p class="mb-2">
                                <i class="bi bi-arrow-repeat {{ $quiz->allow_retakes ? 'text-success' : 'text-danger' }}"></i> 
                                <strong>Retakes:</strong> {{ $quiz->allow_retakes ? 'Allowed' : 'Not allowed' }}
                            </p>
                            @if($quiz->is_repeatable)
                                <p class="mb-2">
                                    <i class="bi bi-infinity text-primary"></i> 
                                    <strong>Repeatable:</strong> Practice anytime
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($quiz->show_correct_answers)
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            Correct answers will be shown after you submit the quiz.
                        </div>
                    @endif

                    <!-- Start Quiz Button -->
                    @if($activeAttempt)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            You have an active attempt in progress.
                        </div>
                        <a href="{{ route('courses.quiz.take', [$course, $lesson, $quiz, $activeAttempt]) }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-play-circle"></i> Continue Quiz
                        </a>
                    @elseif($canStartNewAttempt)
                        <form action="{{ route('courses.quiz.start', [$course, $lesson, $quiz]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-play-circle-fill"></i> Start Quiz
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-x-circle"></i> 
                            @if($quiz->is_repeatable)
                                You can start a new attempt after completing or abandoning your current one.
                            @elseif($quiz->allow_retakes)
                                You can retake this quiz after completing your current attempt.
                            @else
                                You have already completed this quiz. No retakes are allowed.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Best Score -->
            @if($bestAttempt)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-trophy-fill"></i> Best Score</h5>
                    </div>
                    <div class="card-body text-center">
                        <h2 class="mb-0 {{ $bestAttempt->is_passed ? 'text-success' : 'text-danger' }}">
                            {{ $bestAttempt->score }}%
                        </h2>
                        <p class="text-muted mb-0">{{ $bestAttempt->points_earned }} / {{ $bestAttempt->total_points }} points</p>
                        <span class="badge {{ $bestAttempt->is_passed ? 'bg-success' : 'bg-danger' }} mt-2">
                            {{ $bestAttempt->is_passed ? 'PASSED' : 'NOT PASSED' }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- Attempt History -->
            @if($attempts->where('completed_at', '!=', null)->isNotEmpty())
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Attempt History</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($attempts->where('completed_at', '!=', null) as $attempt)
                                <a href="{{ route('courses.quiz.result', [$course, $lesson, $quiz, $attempt]) }}" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="{{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                                                {{ $attempt->score }}%
                                            </strong>
                                            <small class="text-muted d-block">
                                                {{ $attempt->completed_at->format('M d, Y g:i A') }}
                                            </small>
                                        </div>
                                        <span class="badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}">
                                            {{ $attempt->is_passed ? 'Passed' : 'Failed' }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
