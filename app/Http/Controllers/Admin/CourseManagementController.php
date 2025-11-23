<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\LessonQuiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseManagementController extends Controller
{
    /**
     * Display a listing of all courses
     */
    public function index()
    {
        $courses = Course::with(['teacher', 'lessons', 'enrollments'])
            ->withCount(['lessons', 'approvedStudents'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show course details with lessons
     */
    public function show(Course $course)
    {
        $course->load([
            'teacher',
            'lessons.quizzes.attempts',
            'lessons.file',
            'approvedStudents'
        ]);

        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show lesson details with quizzes
     */
    public function showLesson(Course $course, CourseLesson $lesson)
    {
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $lesson->load([
            'file',
            'quizzes.questions.options',
            'quizzes.attempts.student'
        ]);

        return view('admin.courses.lesson', compact('course', 'lesson'));
    }

    /**
     * Show quiz details with all attempts
     */
    public function showQuiz(Course $course, CourseLesson $lesson, LessonQuiz $quiz)
    {
        if ($lesson->course_id !== $course->id || $quiz->course_lesson_id !== $lesson->id) {
            abort(404);
        }

        $quiz->load([
            'questions.options',
            'attempts' => function($query) {
                $query->with('student')
                    ->orderBy('created_at', 'desc');
            }
        ]);

        return view('admin.courses.quiz', compact('course', 'lesson', 'quiz'));
    }

    /**
     * Toggle course active status
     */
    public function toggleActive(Course $course)
    {
        $course->update(['is_active' => !$course->is_active]);

        return back()->with('success', 'Course ' . ($course->is_active ? 'activated' : 'deactivated') . ' successfully.');
    }

    /**
     * Delete a course
     */
    public function destroy(Course $course)
    {
        $courseName = $course->title;
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', "Course '{$courseName}' has been permanently deleted.");
    }

    /**
     * Permanently delete a quiz attempt (admin only)
     */
    public function destroyAttempt(Course $course, CourseLesson $lesson, LessonQuiz $quiz, QuizAttempt $attempt)
    {
        if ($lesson->course_id !== $course->id || $quiz->course_lesson_id !== $lesson->id || $attempt->lesson_quiz_id !== $quiz->id) {
            abort(404);
        }

        $studentName = $attempt->student->name;
        $attempt->delete();

        return redirect()->route('admin.courses.lessons.quizzes.show', [$course, $lesson, $quiz])
            ->with('success', "Quiz attempt by {$studentName} has been permanently deleted.");
    }

    /**
     * View quiz attempt details (admin can see all details)
     */
    public function showAttempt(Course $course, CourseLesson $lesson, LessonQuiz $quiz, QuizAttempt $attempt)
    {
        if ($lesson->course_id !== $course->id || $quiz->course_lesson_id !== $lesson->id || $attempt->lesson_quiz_id !== $quiz->id) {
            abort(404);
        }

        $attempt->load('student');

        return view('admin.courses.attempt', compact('course', 'lesson', 'quiz', 'attempt'));
    }
}
