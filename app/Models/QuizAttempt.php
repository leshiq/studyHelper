<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = [
        'lesson_quiz_id',
        'student_id',
        'quiz_snapshot',
        'score',
        'points_earned',
        'total_points',
        'answers',
        'started_at',
        'completed_at',
        'is_passed',
    ];

    protected $casts = [
        'quiz_snapshot' => 'array',
        'score' => 'integer',
        'points_earned' => 'integer',
        'total_points' => 'integer',
        'answers' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_passed' => 'boolean',
    ];

    /**
     * Get the quiz for this attempt
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LessonQuiz::class, 'lesson_quiz_id');
    }

    /**
     * Get the student who made this attempt
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the score percentage
     */
    public function getScorePercentageAttribute(): int
    {
        if ($this->total_points === 0) {
            return 0;
        }
        return round(($this->points_earned / $this->total_points) * 100);
    }

    /**
     * Get the time taken for this attempt
     */
    public function getTimeTakenAttribute(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }
        return $this->started_at->diffInSeconds($this->completed_at);
    }

    /**
     * Get formatted time taken
     */
    public function getFormattedTimeTakenAttribute(): string
    {
        $seconds = $this->time_taken;
        if ($seconds === null) {
            return 'N/A';
        }

        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }
}
