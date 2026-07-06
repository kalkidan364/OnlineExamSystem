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
        Schema::table('questions', function (Blueprint $table) {
            $table->string('difficulty')->default('Medium');
            $table->string('chapter')->nullable();
            $table->string('topic')->nullable();
            $table->integer('negative_marks')->default(0);
            $table->integer('time_seconds')->default(0);
            $table->boolean('status')->default(true);
            $table->string('tags')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn([
                'difficulty', 'chapter', 'topic', 'negative_marks', 'time_seconds', 'status', 'tags'
            ]);
        });
    }
};
