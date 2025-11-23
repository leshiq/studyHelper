<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LessonProgress;
use App\Models\CourseLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonProgressController extends Controller
{
    /**
     * Update or create lesson progress
     */
    public function update(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:course_lessons,id',
            'watch_time_seconds' => 'required|integer|min:0',
            'video_duration_seconds' => 'nullable|integer|min:0',
        ]);

        $lesson = CourseLesson::findOrFail($request->lesson_id);
        
        // Check if student has access to this lesson's course
        $enrollment = Auth::user()->enrollments()
            ->where('course_id', $lesson->course_id)
            ->where('status', 'approved')
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        // Update or create progress
        $progress = LessonProgress::updateOrCreate(
            [
                'student_id' => Auth::id(),
                'course_lesson_id' => $request->lesson_id,
            ],
            [
                'watch_time_seconds' => max($request->watch_time_seconds, 0),
                'video_duration_seconds' => $request->video_duration_seconds,
                'last_watched_at' => now(),
                'completed_at' => $this->shouldMarkComplete($request->watch_time_seconds, $request->video_duration_seconds) 
                    ? now() 
                    : null,
            ]
        );

        return response()->json([
            'success' => true,
            'progress' => [
                'watch_time_seconds' => $progress->watch_time_seconds,
                'progress_percentage' => $progress->progress_percentage,
                'is_completed' => $progress->is_completed,
            ]
        ]);
    }

    /**
     * Determine if lesson should be marked as complete
     */
    private function shouldMarkComplete($watchTime, $duration)
    {
        if (!$duration || $duration <= 0) {
            return false;
        }

        $percentage = ($watchTime / $duration) * 100;
        return $percentage >= 90; // Consider complete at 90%
    }
}
