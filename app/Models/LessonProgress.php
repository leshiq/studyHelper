<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';
    
    protected $fillable = [
        'student_id',
        'course_lesson_id',
        'watch_time_seconds',
        'video_duration_seconds',
        'completed_at',
        'last_watched_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'last_watched_at' => 'datetime',
    ];

    /**
     * Get the student that owns this progress
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the lesson for this progress
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'course_lesson_id');
    }

    /**
     * Calculate progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if (!$this->video_duration_seconds || $this->video_duration_seconds <= 0) {
            return 0;
        }

        $percentage = ($this->watch_time_seconds / $this->video_duration_seconds) * 100;
        return min(100, round($percentage, 1));
    }

    /**
     * Check if lesson is completed (watched 90% or more)
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->progress_percentage >= 90 || $this->completed_at !== null;
    }

    /**
     * Format watch time for display
     */
    public function getFormattedWatchTimeAttribute(): string
    {
        $seconds = $this->watch_time_seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%d:%02d', $minutes, $secs);
    }
}
