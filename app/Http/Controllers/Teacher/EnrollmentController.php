<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Student;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function approve(Course $course, CourseEnrollment $enrollment)
    {
        if ($course->teacher_id !== auth()->id() && !auth()->user()->is_admin && !auth()->user()->is_superuser) {
            abort(403);
        }

        $enrollment->approve(auth()->user());

        return back()->with('success', 'Student enrollment approved.');
    }

    public function reject(Course $course, CourseEnrollment $enrollment)
    {
        if ($course->teacher_id !== auth()->id() && !auth()->user()->is_admin && !auth()->user()->is_superuser) {
            abort(403);
        }

        $enrollment->reject();

        return back()->with('success', 'Student enrollment rejected.');
    }

    public function enroll(Request $request, Course $course)
    {
        if ($course->teacher_id !== auth()->id() && !auth()->user()->is_admin && !auth()->user()->is_superuser) {
            abort(403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $existing = CourseEnrollment::where('course_id', $course->id)
            ->where('student_id', $validated['student_id'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Student is already enrolled in this course.');
        }

        CourseEnrollment::create([
            'course_id' => $course->id,
            'student_id' => $validated['student_id'],
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Student enrolled successfully.');
    }

    public function remove(Course $course, Student $student)
    {
        if ($course->teacher_id !== auth()->id() && !auth()->user()->is_admin && !auth()->user()->is_superuser) {
            abort(403);
        }

        CourseEnrollment::where('course_id', $course->id)
            ->where('student_id', $student->id)
            ->delete();

        return back()->with('success', 'Student removed from course.');
    }
}
