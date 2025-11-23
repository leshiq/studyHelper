<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseMessage extends Model
{
    protected $fillable = [
        'course_id',
        'student_id',
        'message',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
