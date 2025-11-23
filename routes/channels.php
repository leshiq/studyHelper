<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public test channel for WebSocket testing
Broadcast::channel('test-channel', function () {
    return true;
});

// Course chat channels - accessible to enrolled students and teacher
Broadcast::channel('course.{courseId}', function ($user, $courseId) {
    $course = \App\Models\Course::find($courseId);
    
    if (!$course) {
        return false;
    }
    
    // Allow if user is teacher, enrolled student, admin, or superuser
    $isTeacher = $course->teacher_id === $user->id;
    $isEnrolled = $course->approvedStudents()->where('student_id', $user->id)->exists();
    
    return $isTeacher || $isEnrolled || $user->is_admin || $user->is_superuser;
});
