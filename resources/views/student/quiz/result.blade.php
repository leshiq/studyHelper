@extends('layouts.app')

@section('title', 'Quiz Results - ' . $quiz->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">My Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.quiz.show', [$course, $lesson, $quiz]) }}">{{ $quiz->title }}</a></li>
                    <li class="breadcrumb-item active">Results</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Score Card -->
            <div class="card shadow-sm mb-4 border-{{ $attempt->is_passed ? 'success' : 'danger' }}">
                <div class="card-header bg-{{ $attempt->is_passed ? 'success' : 'danger' }} text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-{{ $attempt->is_passed ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                        {{ $attempt->is_passed ? 'Congratulations! You Passed!' : 'Quiz Completed' }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h2 class="mb-0 {{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">
                                {{ $attempt->score }}%
                            </h2>
                            <p class="text-muted">Your Score</p>
                        </div>
                        <div class="col-md-4">
                            <h2 class="mb-0 text-info">
                                {{ $attempt->points_earned }} / {{ $attempt->total_points }}
                            </h2>
                            <p class="text-muted">Points Earned</p>
                        </div>
                        <div class="col-md-4">
                            <h2 class="mb-0 text-primary">
                                {{ $attempt->formatted_time_taken }}
                            </h2>
                            <p class="text-muted">Time Taken</p>
                        </div>
                    </div>
                    
                    @if(!$attempt->is_passed)
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i> 
                            Passing score required: {{ $quiz->passing_score }}%
                            @if($quiz->allow_retakes || $quiz->is_repeatable)
                                <br>You can retake this quiz to improve your score.
                            @endif
                        </div>
                    @endif

                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <a href="{{ route('courses.quiz.show', [$course, $lesson, $quiz]) }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Back to Quiz
                        </a>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Back to Course
                        </a>
                    </div>
                </div>
            </div>

            <!-- Answer Review -->
            @if($quiz->show_correct_answers || $attempt->quiz_snapshot['show_correct_answers'])
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-list-check"></i> Answer Review</h5>
                    </div>
                    <div class="card-body">
                        @foreach($attempt->quiz_snapshot['questions'] as $index => $question)
                            @php
                                $questionId = $question['id'];
                                $studentAnswer = $attempt->answers[$questionId] ?? null;
                                $isCorrect = $studentAnswer['is_correct'] ?? false;
                            @endphp

                            <div class="mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6>
                                        <span class="badge bg-secondary">Q{{ $index + 1 }}</span>
                                        {{ $question['question'] }}
                                    </h6>
                                    <div>
                                        <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-danger' }}">
                                            {{ $isCorrect ? '✓ Correct' : '✗ Incorrect' }}
                                        </span>
                                        <span class="badge bg-info">{{ $question['points'] }} pts</span>
                                    </div>
                                </div>

                                @if($question['type'] === 'single_choice' || $question['type'] === 'multiple_choice')
                                    <ul class="list-unstyled ms-3">
                                        @foreach($question['options'] as $option)
                                            @php
                                                $wasSelected = false;
                                                if ($question['type'] === 'single_choice') {
                                                    $wasSelected = ($studentAnswer['answer'] ?? null) == $option['id'];
                                                } else {
                                                    $wasSelected = in_array($option['id'], $studentAnswer['answer'] ?? []);
                                                }
                                            @endphp

                                            <li class="mb-1">
                                                @if($option['is_correct'])
                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                    <strong class="text-success">{{ $option['option_text'] }}</strong>
                                                    @if($wasSelected)
                                                        <span class="badge bg-success ms-2">Your answer</span>
                                                    @endif
                                                @elseif($wasSelected)
                                                    <i class="bi bi-x-circle-fill text-danger"></i>
                                                    <span class="text-danger">{{ $option['option_text'] }}</span>
                                                    <span class="badge bg-danger ms-2">Your answer</span>
                                                @else
                                                    <i class="bi bi-circle text-muted"></i>
                                                    <span class="text-muted">{{ $option['option_text'] }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <!-- Text Input -->
                                    <div class="ms-3">
                                        <p class="mb-1">
                                            <strong>Your answer:</strong> 
                                            <span class="{{ $isCorrect ? 'text-success' : 'text-danger' }}">
                                                {{ $studentAnswer['answer'] ?? '(No answer)' }}
                                            </span>
                                        </p>
                                        @if(!$isCorrect)
                                            <p class="mb-0 text-success">
                                                <strong>Correct answer(s):</strong>
                                                @foreach($question['options'] as $option)
                                                    @if($option['is_correct'])
                                                        <span class="badge bg-success">{{ $option['option_text'] }}</span>
                                                    @endif
                                                @endforeach
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if($question['explanation'])
                                    <div class="alert alert-info mt-2 mb-0">
                                        <i class="bi bi-lightbulb"></i> 
                                        <strong>Explanation:</strong> {{ $question['explanation'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-eye-slash"></i> 
                    Answer review is not available for this quiz.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
