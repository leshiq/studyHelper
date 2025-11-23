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
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_lesson_id')->constrained()->onDelete('cascade');
            $table->integer('watch_time_seconds')->default(0);
            $table->integer('video_duration_seconds')->nullable(); // Store video duration for percentage calculation
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_watched_at')->nullable();
            $table->timestamps();
            
            // Ensure one record per student per lesson
            $table->unique(['student_id', 'course_lesson_id']);
            $table->index(['student_id', 'course_lesson_id']);
            $table->index('last_watched_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
