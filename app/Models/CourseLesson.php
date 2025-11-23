<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $fillable = [
        'course_id',
        'downloadable_file_id',
        'title',
        'description',
        'order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function file()
    {
        return $this->belongsTo(DownloadableFile::class, 'downloadable_file_id');
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Get progress for a specific student
     */
    public function progressForStudent($studentId)
    {
        return $this->hasOne(LessonProgress::class)
            ->where('student_id', $studentId);
    }
}
