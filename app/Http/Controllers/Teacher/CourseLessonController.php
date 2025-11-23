<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseLessonController extends Controller
{
    public function store(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id() && !Auth::user()->is_admin && !Auth::user()->is_superuser) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'downloadable_file_id' => 'nullable|exists:downloadable_files,id',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['course_id'] = $course->id;
        
        if (!isset($validated['order'])) {
            $validated['order'] = $course->lessons()->max('order') + 1;
        }

        CourseLesson::create($validated);

        return back()->with('success', 'Lesson added successfully.');
    }

    public function update(Request $request, Course $course, CourseLesson $lesson)
    {
        if ($course->teacher_id !== Auth::id() && !Auth::user()->is_admin && !Auth::user()->is_superuser) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'downloadable_file_id' => 'nullable|exists:downloadable_files,id',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $lesson->update($validated);

        return back()->with('success', 'Lesson updated successfully.');
    }

    public function destroy(Course $course, CourseLesson $lesson)
    {
        if ($course->teacher_id !== Auth::id() && !Auth::user()->is_admin && !Auth::user()->is_superuser) {
            abort(403);
        }

        $lesson->delete();

        return back()->with('success', 'Lesson deleted successfully.');
    }
}
