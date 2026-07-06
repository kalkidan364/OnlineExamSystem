<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the single assigned course to the instructors (users) table.
     * Each instructor teaches exactly ONE course.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('course_code')->nullable()->after('role');
            $table->string('course_name')->nullable()->after('course_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['course_code', 'course_name']);
        });
    }
};
