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
        Schema::create('quiz_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_question_id')->constrained('quiz_questions')->onDelete('cascade');
            $table->text('option_text'); // The option text (for single/multiple choice)
            $table->boolean('is_correct')->default(false); // Whether this option is correct
            $table->integer('order')->default(0); // Display order
            $table->timestamps();
            
            $table->index(['quiz_question_id', 'order']);
        });
        
        // For text_input questions, we'll store the correct answer(s) as option_text with is_correct = true
        // This allows for multiple acceptable answers for text input questions
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_question_options');
    }
};
