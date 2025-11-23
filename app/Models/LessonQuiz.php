<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonQuiz extends Model
{
    protected $fillable = [
        'course_lesson_id',
        'title',
        'description',
        'passing_score',
        'time_limit',
        'show_correct_answers',
        'allow_retakes',
        'is_active',
        'is_repeatable',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'time_limit' => 'integer',
        'show_correct_answers' => 'boolean',
        'allow_retakes' => 'boolean',
        'is_active' => 'boolean',
        'is_repeatable' => 'boolean',
    ];

    /**
     * Get the lesson that owns the quiz
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    /**
     * Get all questions for this quiz
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    /**
     * Get all attempts for this quiz
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get attempts for a specific student
     */
    public function attemptsForStudent(int $studentId): HasMany
    {
        return $this->attempts()->where('student_id', $studentId)->orderBy('created_at', 'desc');
    }

    /**
     * Get the best attempt for a student
     */
    public function bestAttemptForStudent(int $studentId): ?QuizAttempt
    {
        return $this->attempts()
            ->where('student_id', $studentId)
            ->whereNotNull('completed_at')
            ->orderBy('score', 'desc')
            ->first();
    }

    /**
     * Get total possible points for this quiz
     */
    public function getTotalPointsAttribute(): int
    {
        return $this->questions()->sum('points');
    }

    /**
     * Create a snapshot of the quiz (for preserving state when student starts attempt)
     */
    public function createSnapshot(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'passing_score' => $this->passing_score,
            'time_limit' => $this->time_limit,
            'show_correct_answers' => $this->show_correct_answers,
            'questions' => $this->questions->map(function($question) {
                return [
                    'id' => $question->id,
                    'type' => $question->type,
                    'question' => $question->question,
                    'explanation' => $question->explanation,
                    'points' => $question->points,
                    'order' => $question->order,
                    'options' => $question->options->map(function($option) {
                        return [
                            'id' => $option->id,
                            'option_text' => $option->option_text,
                            'is_correct' => $option->is_correct,
                            'order' => $option->order,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
        ];
    }
}
