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
        Schema::create('semester_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('academic_year')->nullable();
            $table->string('semester')->nullable();
            $table->string('department')->nullable();
            $table->string('section')->nullable();
            $table->enum('status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable(); // For department head remarks
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester_submissions');
    }
};
