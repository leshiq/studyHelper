<?php

namespace App\Http\Controllers;

use App\Events\CourseChatMessage;
use App\Models\Course;
use App\Models\CourseMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseChatController extends Controller
{
    public function index(Course $course)
    {
        // Check if user is enrolled or is the teacher
        $user = Auth::user();
        $isEnrolled = $course->approvedStudents()->where('student_id', $user->id)->exists();
        $isTeacher = $course->teacher_id === $user->id;
        
        if (!$isEnrolled && !$isTeacher && !$user->is_admin && !$user->is_superuser) {
            abort(403, 'You must be enrolled in this course to access the chat.');
        }

        $messages = $course->messages()
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json($messages);
    }

    public function store(Request $request, Course $course)
    {
        // Check if user is enrolled or is the teacher
        $user = Auth::user();
        $isEnrolled = $course->approvedStudents()->where('student_id', $user->id)->exists();
        $isTeacher = $course->teacher_id === $user->id;
        
        if (!$isEnrolled && !$isTeacher && !$user->is_admin && !$user->is_superuser) {
            abort(403, 'You must be enrolled in this course to send messages.');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = CourseMessage::create([
            'course_id' => $course->id,
            'student_id' => $user->id,
            'message' => $validated['message'],
        ]);

        $message->load('student');

        // Broadcast the message
        broadcast(new CourseChatMessage($message));

        return response()->json($message, 201);
    }
}
