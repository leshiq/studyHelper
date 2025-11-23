@extends('layouts.app')

@section('title', 'Quiz Attempt Details')

@section('content')
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.show', $course) }}">{{ $course->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.lessons.show', [$course, $lesson]) }}">{{ $lesson->title }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.lessons.quizzes.show', [$course, $lesson, $quiz]) }}">{{ $quiz->title }}</a></li>
            <li class="breadcrumb-item active">Attempt #{{ $attempt->id }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2>Quiz Attempt Details</h2>
                    <p class="text-muted mb-2">
                        <i class="bi bi-person"></i> Student: {{ $attempt->student->name }}
                        <br>
                        <small>{{ $attempt->student->email }}</small>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.courses.lessons.quizzes.show', [$course, $lesson, $quiz]) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Quiz
                    </a>
                    <form action="{{ route('admin.courses.lessons.quizzes.attempts.destroy', [$course, $lesson, $quiz, $attempt]) }}" 
                          method="POST" 
                          onsubmit="return confirm('⚠️ ADMIN ACTION: PERMANENTLY DELETE THIS ATTEMPT?\n\nStudent: {{ $attempt->student->name }}\nScore: {{ $attempt->completed_at ? number_format($attempt->score, 1) . '%' : 'In Progress' }}\n\nThis will PERMANENTLY delete:\n- All student answers\n- Quiz snapshot data\n- Score and attempt history\n\nThis action CANNOT be undone!\n\nOnly admins can do this. Teachers cannot recover deleted attempts.\n\nAre you absolutely sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Permanently Delete Attempt
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($attempt->completed_at)
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center shadow-sm border-{{ $attempt->is_passed ? 'success' : 'danger' }}">
                    <div class="card-body">
                        <h6 class="text-muted">Score</h6>
                        <h2 class="{{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                            {{ number_format($attempt->score, 1) }}%
                        </h2>
                        <span class="badge {{ $attempt->is_passed ? 'bg-success' : 'bg-danger' }}">
                            {{ $attempt->is_passed ? 'PASSED' : 'FAILED' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Points Earned</h6>
                        <h2>{{ $attempt->points_earned }}</h2>
                        <small class="text-muted">out of {{ $attempt->total_points }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Duration</h6>
                        <h2 class="h4">{{ $attempt->started_at->diffForHumans($attempt->completed_at, true) }}</h2>
                        <small class="text-muted">to complete</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Passing Score</h6>
                        <h2 class="h4">{{ $attempt->quiz_snapshot['passing_score'] ?? 'N/A' }}%</h2>
                        <small class="text-muted">required</small>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="bi bi-clock-history"></i> This attempt is still in progress. The student has not completed the quiz yet.
        </div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <strong><i class="bi bi-info-circle"></i> Attempt Information</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Quiz Title:</strong> {{ $attempt->quiz_snapshot['title'] ?? 'N/A' }}
                        </li>
                        <li class="mb-2">
                            <strong>Student:</strong> {{ $attempt->student->name }} ({{ $attempt->student->email }})
                        </li>
                        <li class="mb-2">
                            <strong>Started At:</strong> {{ $attempt->started_at->format('M d, Y h:i A') }}
                        </li>
                        <li class="mb-2">
                            <strong>Completed At:</strong> 
                            {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y h:i A') : 'Still in progress' }}
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Time Limit:</strong> 
                            {{ isset($attempt->quiz_snapshot['time_limit']) && $attempt->quiz_snapshot['time_limit'] ? $attempt->quiz_snapshot['time_limit'] . ' minutes' : 'No time limit' }}
                        </li>
                        <li class="mb-2">
                            <strong>Show Correct Answers:</strong> 
                            <span class="badge {{ ($attempt->quiz_snapshot['show_correct_answers'] ?? false) ? 'bg-success' : 'bg-secondary' }}">
                                {{ ($attempt->quiz_snapshot['show_correct_answers'] ?? false) ? 'Yes' : 'No' }}
                            </span>
                        </li>
                        <li class="mb-2">
                            <strong>Total Questions:</strong> {{ count($attempt->quiz_snapshot['questions'] ?? []) }}
                        </li>
                        <li class="mb-2">
                            <strong>Attempt ID:</strong> #{{ $attempt->id }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if($attempt->completed_at && isset($attempt->quiz_snapshot['questions']))
        <div class="card shadow-sm">
            <div class="card-header">
                <strong><i class="bi bi-list-check"></i> Answer Review</strong>
            </div>
            <div class="card-body">
                @php
                    $studentAnswers = collect($attempt->answers ?? []);
                @endphp

                @foreach($attempt->quiz_snapshot['questions'] as $index => $question)
                    @php
                        $studentAnswer = $studentAnswers->firstWhere('question_id', $question['id']);
                        $questionPoints = $question['points'] ?? 0;
                        $earnedPoints = $studentAnswer['points_earned'] ?? 0;
                        $isCorrect = $earnedPoints >= $questionPoints;
                    @endphp

                    <div class="card mb-3 border-{{ $isCorrect ? 'success' : 'danger' }}">
                        <div class="card-header bg-{{ $isCorrect ? 'success' : 'danger' }} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Question {{ $index + 1 }}</strong>
                                <span class="badge bg-light text-dark">
                                    {{ $earnedPoints }} / {{ $questionPoints }} points
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="mb-3"><strong>{{ $question['question'] }}</strong></p>

                            @if($question['type'] === 'single_choice' || $question['type'] === 'multiple_choice')
                                <div class="mb-3">
                                    @foreach($question['options'] ?? [] as $option)
                                        @php
                                            $isStudentChoice = false;
                                            if ($question['type'] === 'single_choice') {
                                                $isStudentChoice = ($studentAnswer['answer'] ?? null) == $option['id'];
                                            } else {
                                                $isStudentChoice = in_array($option['id'], $studentAnswer['answer'] ?? []);
                                            }
                                            $isCorrectOption = $option['is_correct'];
                                        @endphp

                                        <div class="form-check mb-2 p-3 rounded {{ $isCorrectOption ? 'bg-success bg-opacity-10' : ($isStudentChoice ? 'bg-danger bg-opacity-10' : '') }}">
                                            <input class="form-check-input" 
                                                   type="{{ $question['type'] === 'single_choice' ? 'radio' : 'checkbox' }}" 
                                                   disabled 
                                                   {{ $isStudentChoice ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                {{ $option['option_text'] }}
                                                @if($isCorrectOption)
                                                    <span class="badge bg-success ms-2">Correct Answer</span>
                                                @endif
                                                @if($isStudentChoice && !$isCorrectOption)
                                                    <span class="badge bg-danger ms-2">Your Answer</span>
                                                @endif
                                                @if($isStudentChoice && $isCorrectOption)
                                                    <span class="badge bg-success ms-2">Your Answer ✓</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                            @elseif($question['type'] === 'text_input')
                                <div class="mb-3">
                                    <strong>Student's Answer:</strong>
                                    <div class="alert alert-{{ $isCorrect ? 'success' : 'danger' }} mt-2">
                                        {{ $studentAnswer['answer'] ?? 'No answer provided' }}
                                    </div>

                                    @if(!$isCorrect)
                                        <strong>Correct Answer(s):</strong>
                                        <ul class="list-group mt-2">
                                            @foreach($question['options'] ?? [] as $option)
                                                @if($option['is_correct'])
                                                    <li class="list-group-item list-group-item-success">
                                                        {{ $option['option_text'] }}
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif

                            @if(!empty($question['explanation']))
                                <div class="alert alert-info mb-0">
                                    <strong>Explanation:</strong> {{ $question['explanation'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(!$attempt->completed_at)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i> Student answers will appear here once the quiz is completed.
        </div>
    @endif
</div>
@endsection
