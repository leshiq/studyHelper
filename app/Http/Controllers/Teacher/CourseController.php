<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\DownloadableFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Auth::user()->taughtCourses()
            ->withCount(['lessons', 'enrollments', 'approvedStudents'])
            ->latest()
            ->get();

        return view('teacher.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('teacher.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_available_to_all' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['teacher_id'] = Auth::id();

        $course = Course::create($validated);

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        if ($course->teacher_id !== Auth::id() && !Auth::user()->is_admin && !Auth::user()->is_superuser) {
            abort(403);
        }

        $course->load([
            'lessons.file',
            'lessons.progress.student',
            'enrollments.student',
            'teacher'
        ]);

        $availableFiles = DownloadableFile::where('is_active', true)->get();
        $pendingEnrollments = $course->pendingEnrollments()->with('student')->get();
        $approvedStudents = $course->approvedStudents()->get();

        // Calculate lesson statistics
        $lessonStats = [];
        foreach ($course->lessons as $lesson) {
            $totalStudents = $approvedStudents->count();
            $completedCount = $lesson->progress->where('is_completed', true)->count();
            $inProgressCount = $lesson->progress->where('is_completed', false)->where('watch_time_seconds', '>', 0)->count();
            $notStartedCount = $totalStudents - $completedCount - $inProgressCount;
            
            $lessonStats[$lesson->id] = [
                'completed' => $completedCount,
                'in_progress' => $inProgressCount,
                'not_started' => $notStartedCount,
                'completion_rate' => $totalStudents > 0 ? round(($completedCount / $totalStudents) * 100, 1) : 0,
            ];
        }

        return view('teacher.courses.show', compact('course', 'availableFiles', 'pendingEnrollments', 'approvedStudents', 'lessonStats'));
    }

    public function edit(Course $course)
    {
        if ($course->teacher_id !== Auth::id() && !Auth::user()->is_admin && !Auth::user()->is_superuser) {
            abort(403);
        }

        return view('teacher.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        if ($course->teacher_id !== Auth::id() && !Auth::user()->is_admin && !Auth::user()->is_superuser) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_available_to_all' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $course->update($validated);

        return redirect()->route('teacher.courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        if ($course->teacher_id !== Auth::id() && !Auth::user()->is_admin && !Auth::user()->is_superuser) {
            abort(403);
        }

        $course->delete();

        return redirect()->route('teacher.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
