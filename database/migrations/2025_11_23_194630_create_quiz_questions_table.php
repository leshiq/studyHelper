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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_quiz_id')->constrained('lesson_quizzes')->onDelete('cascade');
            $table->enum('type', ['single_choice', 'multiple_choice', 'text_input']); // Question type
            $table->text('question'); // Question text
            $table->text('explanation')->nullable(); // Optional explanation for the answer
            $table->integer('points')->default(1); // Points for correct answer
            $table->integer('order')->default(0); // Display order
            $table->timestamps();
            
            $table->index(['lesson_quiz_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
