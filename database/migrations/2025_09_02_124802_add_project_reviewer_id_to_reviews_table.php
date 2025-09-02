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
            $table->foreignId('project_reviewer_id')
            ->nullable() // allow null values
            ->constrained('project_reviewers')
            ->cascadeOnUpdate()
            ->nullOnDelete(); // better than cascade if you want to keep the review but clear the link
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('project_reviewer_id');
        });
    }
};
