<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_quiz_id')->constrained('lesson_quizzes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->integer('score')->nullable(); // Score achieved (percentage)
            $table->integer('points_earned')->default(0); // Total points earned
            $table->integer('total_points')->default(0); // Total possible points
            $table->json('answers'); // JSON storage of student answers
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_passed')->default(false);
            $table->timestamps();
            
            $table->index(['lesson_quiz_id', 'student_id']);
            $table->index(['student_id', 'completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
