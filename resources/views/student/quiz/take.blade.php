@extends('layouts.app')

@section('title', 'Taking: ' . $quiz->title)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h3><i class="bi bi-pencil-square"></i> {{ $quiz->title }}</h3>
            @if($timeRemaining !== null)
                <div class="alert alert-warning" id="timerAlert">
                    <i class="bi bi-clock"></i> 
                    <strong>Time Remaining:</strong> <span id="timeRemaining">{{ $timeRemaining }}</span> minutes
                </div>
            @endif
        </div>
    </div>

    <form action="{{ route('courses.quiz.submit', [$course, $lesson, $quiz, $attempt]) }}" method="POST" id="quizForm">
        @csrf
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                @foreach($attempt->quiz_snapshot['questions'] as $index => $question)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <span class="badge bg-secondary">Question {{ $index + 1 }}</span>
                                    <span class="badge bg-info">
                                        @if($question['type'] === 'single_choice')
                                            Single Choice
                                        @elseif($question['type'] === 'multiple_choice')
                                            Multiple Choice
                                        @else
                                            Text Input
                                        @endif
                                    </span>
                                </h5>
                                <span class="badge bg-success">{{ $question['points'] }} pts</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="lead mb-4">{{ $question['question'] }}</p>

                            @if($question['type'] === 'single_choice')
                                <!-- Single Choice Options -->
                                <div class="list-group">
                                    @foreach($question['options'] as $option)
                                        <label class="list-group-item list-group-item-action">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" 
                                                       name="answers[{{ $question['id'] }}]" 
                                                       value="{{ $option['id'] }}"
                                                       id="q{{ $question['id'] }}_opt{{ $option['id'] }}">
                                                <label class="form-check-label w-100" for="q{{ $question['id'] }}_opt{{ $option['id'] }}">
                                                    {{ $option['option_text'] }}
                                                </label>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                            @elseif($question['type'] === 'multiple_choice')
                                <!-- Multiple Choice Options -->
                                <div class="alert alert-info small mb-3">
                                    <i class="bi bi-info-circle"></i> Select all correct answers
                                </div>
                                <div class="list-group">
                                    @foreach($question['options'] as $option)
                                        <label class="list-group-item list-group-item-action">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="answers[{{ $question['id'] }}][]" 
                                                       value="{{ $option['id'] }}"
                                                       id="q{{ $question['id'] }}_opt{{ $option['id'] }}">
                                                <label class="form-check-label w-100" for="q{{ $question['id'] }}_opt{{ $option['id'] }}">
                                                    {{ $option['option_text'] }}
                                                </label>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                            @else
                                <!-- Text Input -->
                                <div class="form-group">
                                    <label for="answer{{ $question['id'] }}" class="form-label">Your Answer:</label>
                                    <input type="text" class="form-control form-control-lg" 
                                           name="answers[{{ $question['id'] }}]" 
                                           id="answer{{ $question['id'] }}"
                                           placeholder="Type your answer here">
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Submit Button -->
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Submit your quiz? You cannot change your answers after submission.')">
                            <i class="bi bi-check-circle-fill"></i> Submit Quiz
                        </button>
                        <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="bi bi-x-circle"></i> Save & Exit (Resume Later)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@if($timeRemaining !== null)
<script>
document.addEventListener('DOMContentLoaded', function() {
    let timeLeft = {{ $timeRemaining * 60 }}; // Convert to seconds
    
    const timerDisplay = document.getElementById('timeRemaining');
    const timerAlert = document.getElementById('timerAlert');
    const quizForm = document.getElementById('quizForm');
    
    const countdown = setInterval(function() {
        timeLeft--;
        
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        timerDisplay.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        
        // Change alert color when time is running out
        if (timeLeft <= 60) {
            timerAlert.classList.remove('alert-warning');
            timerAlert.classList.add('alert-danger');
        } else if (timeLeft <= 300) {
            timerAlert.classList.remove('alert-warning');
            timerAlert.classList.add('alert-warning');
        }
        
        if (timeLeft <= 0) {
            clearInterval(countdown);
            alert('Time is up! Your quiz will be submitted automatically.');
            quizForm.submit();
        }
    }, 1000);
});
</script>
@endif
@endsection
