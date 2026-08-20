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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('module')->nullable()->after('type'); // e.g. Users, Courses, Departments
            $table->string('ip_address')->nullable()->after('details');
            $table->string('log_status')->default('Success')->after('ip_address'); // log_status so it doesn't conflict with any other status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn(['module', 'ip_address', 'log_status']);
        });
    }
};
