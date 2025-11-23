<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\LessonQuiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Store or update a quiz for a lesson
     */
    public function store(Request $request, Course $course, CourseLesson $lesson)
    {
        // Verify the lesson belongs to this course
        if ($lesson->course_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'show_correct_answers' => 'boolean',
            'allow_retakes' => 'boolean',
            'is_active' => 'boolean',
            'is_repeatable' => 'boolean',
        ]);

        // Convert checkboxes to boolean
        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['allow_retakes'] = $request->has('allow_retakes');
        $validated['is_active'] = $request->has('is_active');
        $validated['is_repeatable'] = $request->has('is_repeatable');

        // Create new quiz (no longer update, always create)
        $validated['course_lesson_id'] = $lesson->id;
        $quiz = LessonQuiz::create($validated);

        return redirect()
            ->route('teacher.courses.lessons.edit', [$course, $lesson])
            ->with('success', 'Quiz created successfully!');
    }

    /**
     * Update an existing quiz
     */
    public function update(Request $request, Course $course, CourseLesson $lesson, LessonQuiz $quiz)
    {
        // Verify the lesson belongs to this course and quiz belongs to lesson
        if ($lesson->course_id !== $course->id || $quiz->course_lesson_id !== $lesson->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit' => 'nullable|integer|min:1',
            'show_correct_answers' => 'boolean',
            'allow_retakes' => 'boolean',
            'is_active' => 'boolean',
            'is_repeatable' => 'boolean',
        ]);

        // Convert checkboxes to boolean
        $validated['show_correct_answers'] = $request->has('show_correct_answers');
        $validated['allow_retakes'] = $request->has('allow_retakes');
        $validated['is_active'] = $request->has('is_active');
        $validated['is_repeatable'] = $request->has('is_repeatable');

        $quiz->update($validated);

        return redirect()
            ->route('teacher.courses.lessons.edit', [$course, $lesson])
            ->with('success', 'Quiz updated successfully!');
    }

    /**
     * Delete a quiz
     */
    public function destroy(Course $course, CourseLesson $lesson, LessonQuiz $quiz)
    {
        // Verify the lesson belongs to this course and quiz belongs to lesson
        if ($lesson->course_id !== $course->id || $quiz->course_lesson_id !== $lesson->id) {
            abort(404);
        }

        $quiz->delete();
        
        return redirect()
            ->route('teacher.courses.lessons.edit', [$course, $lesson])
            ->with('success', 'Quiz deleted successfully!');
    }

    /**
     * Store a new question for the quiz
     */
    public function storeQuestion(Request $request, Course $course, CourseLesson $lesson, LessonQuiz $quiz)
    {
        // Verify the lesson belongs to this course and quiz belongs to lesson
        if ($lesson->course_id !== $course->id || $quiz->course_lesson_id !== $lesson->id) {
            abort(404);
        }

        $validated = $request->validate([
            'type' => 'required|in:single_choice,multiple_choice,text_input',
            'question' => 'required|string',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable|string',
            'options' => 'required|array|min:1',
            'options.*.text' => 'required|string',
            'correct_answers' => 'array', // For multiple choice
        ]);

        DB::transaction(function() use ($validated, $quiz, $request) {
            // Get the next order number
            $order = $quiz->questions()->max('order') + 1;

            // Create the question
            $question = $quiz->questions()->create([
                'type' => $validated['type'],
                'question' => $validated['question'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'],
                'order' => $order,
            ]);

            // Create options
            foreach ($validated['options'] as $index => $optionData) {
                $isCorrect = false;

                if ($validated['type'] === 'single_choice') {
                    // For single choice, check if this option's is_correct was set
                    $isCorrect = $request->input("options.{$index}.is_correct") == 1;
                } elseif ($validated['type'] === 'multiple_choice') {
                    // For multiple choice, check if this index is in correct_answers array
                    $isCorrect = in_array($index, $request->input('correct_answers', []));
                } else {
                    // For text_input, all options are correct answers
                    $isCorrect = true;
                }

                $question->options()->create([
                    'option_text' => $optionData['text'],
                    'is_correct' => $isCorrect,
                    'order' => $index,
                ]);
            }
        });

        return redirect()
            ->route('teacher.courses.lessons.edit', [$course, $lesson])
            ->with('success', 'Question added successfully!');
    }

    /**
     * Update an existing question
     */
    public function updateQuestion(Request $request, Course $course, CourseLesson $lesson, LessonQuiz $quiz, QuizQuestion $question)
    {
        // Verify the question belongs to this quiz
        if ($lesson->course_id !== $course->id || 
            $quiz->course_lesson_id !== $lesson->id ||
            $question->lesson_quiz_id !== $quiz->id) {
            abort(404);
        }

        $validated = $request->validate([
            'type' => 'required|in:single_choice,multiple_choice,text_input',
            'question' => 'required|string',
            'points' => 'required|integer|min:1',
            'explanation' => 'nullable|string',
            'options' => 'required|array|min:1',
            'options.*.text' => 'required|string',
            'correct_answers' => 'array', // For multiple choice
        ]);

        DB::transaction(function() use ($validated, $question, $request) {
            // Update the question
            $question->update([
                'type' => $validated['type'],
                'question' => $validated['question'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'],
            ]);

            // Delete existing options
            $question->options()->delete();

            // Create new options
            foreach ($validated['options'] as $index => $optionData) {
                $isCorrect = false;

                if ($validated['type'] === 'single_choice') {
                    // For single choice, check if this option's is_correct was set
                    $isCorrect = $request->input("options.{$index}.is_correct") == 1;
                } elseif ($validated['type'] === 'multiple_choice') {
                    // For multiple choice, check if this index is in correct_answers array
                    $isCorrect = in_array($index, $request->input('correct_answers', []));
                } else {
                    // For text_input, all options are correct answers
                    $isCorrect = true;
                }

                $question->options()->create([
                    'option_text' => $optionData['text'],
                    'is_correct' => $isCorrect,
                    'order' => $index,
                ]);
            }
        });

        return redirect()
            ->route('teacher.courses.lessons.edit', [$course, $lesson])
            ->with('success', 'Question updated successfully!');
    }

    /**
     * Delete a question
     */
    public function destroyQuestion(Course $course, CourseLesson $lesson, LessonQuiz $quiz, QuizQuestion $question)
    {
        // Verify the question belongs to this quiz
        if ($lesson->course_id !== $course->id || 
            $quiz->course_lesson_id !== $lesson->id ||
            $question->lesson_quiz_id !== $quiz->id) {
            abort(404);
        }

        $question->delete();

        return redirect()
            ->route('teacher.courses.lessons.edit', [$course, $lesson])
            ->with('success', 'Question deleted successfully!');
    }
}

