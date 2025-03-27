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
        Schema::table('reviews', function (Blueprint $table) {
            $table->integer('response_time_hours')->nullable()->after('review_status'); // Time taken by project creators to respond
            $table->integer('review_time_hours')->nullable()->after('response_time_hours'); // Time taken by reviewers to review
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['response_time_hours', 'review_time_hours']);
        });
    }
};
