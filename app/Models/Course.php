<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'teacher_id',
        'is_available_to_all',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_available_to_all' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(Student::class, 'teacher_id');
    }

    public function lessons()
    {
        return $this->hasMany(CourseLesson::class)->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_enrollments')
            ->withPivot('status', 'approved_at', 'approved_by')
            ->withTimestamps();
    }

    public function approvedStudents()
    {
        return $this->belongsToMany(Student::class, 'course_enrollments')
            ->wherePivot('status', 'approved')
            ->withPivot('approved_at', 'approved_by')
            ->withTimestamps();
    }

    public function pendingEnrollments()
    {
        return $this->enrollments()->where('status', 'pending');
    }

    public function messages()
    {
        return $this->hasMany(CourseMessage::class);
    }
}
