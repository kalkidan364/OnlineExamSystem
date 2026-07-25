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
            if (!Schema::hasColumn('questions', 'title')) {
                $table->string('title')->nullable()->after('type');
            }
            if (!Schema::hasColumn('questions', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('questions', 'explanation')) {
                $table->text('explanation')->nullable()->after('correct_answer');
            }
            if (!Schema::hasColumn('questions', 'image_url')) {
                $table->string('image_url')->nullable()->after('explanation');
            }
            if (!Schema::hasColumn('questions', 'settings')) {
                $table->json('settings')->nullable()->after('tags');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'description', 'explanation', 'image_url', 'settings'
            ]);
        });
    }
};
