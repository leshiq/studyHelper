<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $myCourses = Auth::user()->approvedCourses()
            ->with(['teacher', 'lessons' => function($query) {
                $query->where('is_published', true);
            }])
            ->withCount('lessons')
            ->get();

        $availableCourses = Course::where('is_active', true)
            ->where('is_available_to_all', true)
            ->whereDoesntHave('enrollments', function($query) {
                $query->where('student_id', Auth::id());
            })
            ->with('teacher')
            ->withCount('lessons')
            ->get();

        $pendingRequests = Auth::user()->enrollments()
            ->where('status', 'pending')
            ->with('course.teacher')
            ->get();

        return view('student.courses.index', compact('myCourses', 'availableCourses', 'pendingRequests'));
    }

    public function show(Course $course)
    {
        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('student_id', Auth::id())
            ->where('status', 'approved')
            ->first();

        if (!$enrollment && !$course->is_available_to_all) {
            abort(403, 'You do not have access to this course.');
        }

        $course->load(['teacher', 'lessons' => function($query) {
            $query->where('is_published', true)->with('file');
        }]);

        return view('student.courses.show', compact('course', 'enrollment'));
    }

    public function request(Course $course)
    {
        $existing = CourseEnrollment::where('course_id', $course->id)
            ->where('student_id', Auth::id())
            ->first();

        if ($existing) {
            if ($existing->status === 'approved') {
                return back()->with('error', 'You are already enrolled in this course.');
            } elseif ($existing->status === 'pending') {
                return back()->with('error', 'You have already requested enrollment in this course.');
            } elseif ($existing->status === 'rejected') {
                return back()->with('error', 'Your enrollment request was rejected.');
            }
        }

        CourseEnrollment::create([
            'course_id' => $course->id,
            'student_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Enrollment request submitted successfully.');
    }

    public function cancelRequest(Course $course)
    {
        CourseEnrollment::where('course_id', $course->id)
            ->where('student_id', Auth::id())
            ->where('status', 'pending')
            ->delete();

        return back()->with('success', 'Enrollment request cancelled.');
    }
}
