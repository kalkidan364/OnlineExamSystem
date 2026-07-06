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
        // Question Banks
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Instructor owner
            $table->string('title');
            $table->string('course_code');
            $table->text('description')->nullable();
            $table->integer('questions_count')->default(0);
            $table->timestamps();
        });

        // Questions
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['multiple_choice', 'true_false', 'short_answer', 'essay'])->default('multiple_choice');
            $table->text('text');
            $table->json('options')->nullable(); // For multiple choice: ["A", "B", "C", "D"]
            $table->string('correct_answer')->nullable();
            $table->integer('marks')->default(1);
            $table->timestamps();
        });

        // Exam-Student enrollment (which students are assigned to which exams)
        Schema::create('exam_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Student
            $table->unique(['exam_id', 'user_id']);
            $table->timestamps();
        });

        // Exam Attempts (when a student takes an exam)
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Student
            $table->integer('score')->nullable();
            $table->integer('total_marks');
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');
            $table->json('answers')->nullable();
            $table->dateTime('started_at');
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_student');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_banks');
    }
};
