<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    protected $fillable = [
        'lesson_quiz_id',
        'type',
        'question',
        'explanation',
        'points',
        'order',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the quiz that owns the question
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LessonQuiz::class, 'lesson_quiz_id');
    }

    /**
     * Get all options for this question
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuizQuestionOption::class)->orderBy('order');
    }

    /**
     * Get correct options for this question
     */
    public function correctOptions(): HasMany
    {
        return $this->options()->where('is_correct', true);
    }

    /**
     * Check if an answer is correct
     */
    public function isAnswerCorrect(mixed $answer): bool
    {
        if ($this->type === 'single_choice') {
            return $this->options()->where('id', $answer)->where('is_correct', true)->exists();
        }

        if ($this->type === 'multiple_choice') {
            $correctIds = $this->correctOptions()->pluck('id')->sort()->values()->toArray();
            $answerIds = collect($answer)->sort()->values()->toArray();
            return $correctIds === $answerIds;
        }

        if ($this->type === 'text_input') {
            $correctAnswers = $this->correctOptions()->pluck('option_text')->map(function($text) {
                return strtolower(trim($text));
            })->toArray();
            return in_array(strtolower(trim($answer)), $correctAnswers);
        }

        return false;
    }
}
