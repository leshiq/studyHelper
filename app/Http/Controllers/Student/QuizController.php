<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\LessonQuiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Show quiz with option to start attempt
     */
    public function show(Course $course, CourseLesson $lesson, LessonQuiz $quiz)
    {
        // Verify student is enrolled
        if (!$course->approvedStudents()->where('student_id', Auth::id())->exists()) {
            abort(403, 'You are not enrolled in this course.');
        }

        // Verify quiz belongs to lesson
        if ($quiz->course_lesson_id !== $lesson->id || $lesson->course_id !== $course->id) {
            abort(404);
        }

        // Check if quiz is active
        if (!$quiz->is_active) {
            abort(403, 'This quiz is not currently available.');
        }

        $quiz->load('questions.options');
        
        // Get student's attempts
        $attempts = $quiz->attempts()
            ->where('student_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Get active attempt (started but not completed)
        $activeAttempt = $attempts->where('completed_at', null)->first();

        // Check if student can start new attempt
        $canStartNewAttempt = false;
        if ($quiz->is_repeatable) {
            $canStartNewAttempt = !$activeAttempt; // Can start if no active attempt
        } elseif ($quiz->allow_retakes) {
            $canStartNewAttempt = !$activeAttempt; // Can start if no active attempt
        } else {
            // Only one attempt allowed
            $canStartNewAttempt = $attempts->where('completed_at', '!=', null)->isEmpty() && !$activeAttempt;
        }

        $bestAttempt = $attempts->where('completed_at', '!=', null)
            ->sortByDesc('score')
            ->first();

        return view('student.quiz.show', compact('course', 'lesson', 'quiz', 'attempts', 'activeAttempt', 'canStartNewAttempt', 'bestAttempt'));
    }

    /**
     * Start a new quiz attempt
     */
    public function start(Course $course, CourseLesson $lesson, LessonQuiz $quiz)
    {
        // Verify student is enrolled
        if (!$course->approvedStudents()->where('student_id', Auth::id())->exists()) {
            abort(403);
        }

        // Verify quiz belongs to lesson and is active
        if ($quiz->course_lesson_id !== $lesson->id || $lesson->course_id !== $course->id || !$quiz->is_active) {
            abort(404);
        }

        // Check if student already has an active attempt
        $activeAttempt = $quiz->attempts()
            ->where('student_id', Auth::id())
            ->whereNull('completed_at')
            ->first();

        if ($activeAttempt) {
            return redirect()->route('courses.quiz.take', [$course, $lesson, $quiz, $activeAttempt]);
        }

        // Check if student can start new attempt
        $completedAttempts = $quiz->attempts()
            ->where('student_id', Auth::id())
            ->whereNotNull('completed_at')
            ->count();

        if (!$quiz->is_repeatable && !$quiz->allow_retakes && $completedAttempts > 0) {
            return redirect()->route('courses.quiz.show', [$course, $lesson, $quiz])
                ->with('error', 'You have already completed this quiz and retakes are not allowed.');
        }

        // Create snapshot of quiz
        $snapshot = $quiz->createSnapshot();

        // Calculate total points
        $totalPoints = $quiz->questions->sum('points');

        // Create new attempt
        $attempt = QuizAttempt::create([
            'lesson_quiz_id' => $quiz->id,
            'student_id' => Auth::id(),
            'quiz_snapshot' => $snapshot,
            'started_at' => now(),
            'total_points' => $totalPoints,
            'points_earned' => 0,
            'score' => 0,
            'is_passed' => false,
            'answers' => [],
        ]);

        return redirect()->route('courses.quiz.take', [$course, $lesson, $quiz, $attempt]);
    }

    /**
     * Take the quiz (show questions)
     */
    public function take(Course $course, CourseLesson $lesson, LessonQuiz $quiz, QuizAttempt $attempt)
    {
        // Verify ownership and enrollment
        if ($attempt->student_id !== Auth::id()) {
            abort(403);
        }

        if (!$course->approvedStudents()->where('student_id', Auth::id())->exists()) {
            abort(403);
        }

        // If already completed, redirect to results
        if ($attempt->completed_at) {
            return redirect()->route('courses.quiz.result', [$course, $lesson, $quiz, $attempt]);
        }

        // Check time limit
        $timeRemaining = null;
        if ($quiz->time_limit) {
            $elapsedMinutes = $attempt->started_at->diffInMinutes(now());
            $timeRemaining = $quiz->time_limit - $elapsedMinutes;
            
            if ($timeRemaining <= 0) {
                // Auto-submit quiz
                return redirect()->route('courses.quiz.submit', [$course, $lesson, $quiz, $attempt]);
            }
        }

        return view('student.quiz.take', compact('course', 'lesson', 'quiz', 'attempt', 'timeRemaining'));
    }

    /**
     * Submit quiz answers
     */
    public function submit(Request $request, Course $course, CourseLesson $lesson, LessonQuiz $quiz, QuizAttempt $attempt)
    {
        // Verify ownership
        if ($attempt->student_id !== Auth::id()) {
            abort(403);
        }

        // If already completed, redirect to results
        if ($attempt->completed_at) {
            return redirect()->route('courses.quiz.result', [$course, $lesson, $quiz, $attempt]);
        }

        $answers = $request->input('answers', []);
        
        // Grade the quiz using the snapshot
        $snapshot = $attempt->quiz_snapshot;
        $pointsEarned = 0;
        $gradedAnswers = [];

        foreach ($snapshot['questions'] as $question) {
            $questionId = $question['id'];
            $studentAnswer = $answers[$questionId] ?? null;
            $isCorrect = false;

            if ($question['type'] === 'single_choice') {
                // Check if selected option ID is correct
                $correctOption = collect($question['options'])->firstWhere('is_correct', true);
                $isCorrect = $studentAnswer && $correctOption && $studentAnswer == $correctOption['id'];
            } elseif ($question['type'] === 'multiple_choice') {
                // Compare arrays of selected option IDs
                $correctOptionIds = collect($question['options'])
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->sort()
                    ->values()
                    ->toArray();
                
                $studentAnswerArray = is_array($studentAnswer) ? collect($studentAnswer)->sort()->values()->toArray() : [];
                $isCorrect = $correctOptionIds === $studentAnswerArray;
            } else {
                // Text input - case-insensitive comparison
                $correctAnswers = collect($question['options'])
                    ->where('is_correct', true)
                    ->pluck('option_text')
                    ->map(fn($text) => strtolower(trim($text)))
                    ->toArray();
                
                $studentAnswerText = strtolower(trim($studentAnswer ?? ''));
                $isCorrect = in_array($studentAnswerText, $correctAnswers);
            }

            if ($isCorrect) {
                $pointsEarned += $question['points'];
            }

            $gradedAnswers[$questionId] = [
                'answer' => $studentAnswer,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question['points'] : 0,
            ];
        }

        // Calculate score percentage
        $score = $attempt->total_points > 0 ? round(($pointsEarned / $attempt->total_points) * 100) : 0;
        $isPassed = $score >= $quiz->passing_score;

        // Update attempt
        $attempt->update([
            'answers' => $gradedAnswers,
            'points_earned' => $pointsEarned,
            'score' => $score,
            'is_passed' => $isPassed,
            'completed_at' => now(),
        ]);

        return redirect()->route('courses.quiz.result', [$course, $lesson, $quiz, $attempt])
            ->with('success', $isPassed ? 'Congratulations! You passed the quiz!' : 'Quiz completed. You can review your answers below.');
    }

    /**
     * Show quiz results
     */
    public function result(Course $course, CourseLesson $lesson, LessonQuiz $quiz, QuizAttempt $attempt)
    {
        // Verify ownership
        if ($attempt->student_id !== Auth::id()) {
            abort(403);
        }

        // Must be completed
        if (!$attempt->completed_at) {
            return redirect()->route('courses.quiz.take', [$course, $lesson, $quiz, $attempt]);
        }

        return view('student.quiz.result', compact('course', 'lesson', 'quiz', 'attempt'));
    }
}
