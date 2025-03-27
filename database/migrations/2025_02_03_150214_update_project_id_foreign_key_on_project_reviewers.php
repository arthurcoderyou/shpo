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
        Schema::table('project_reviewers', function (Blueprint $table) {
            // Drop the incorrect foreign key first
            $table->dropForeign(['project_id']);

            // Add the correct foreign key constraint
            $table->foreign('project_id')->references('id')->on('projects')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_reviewers', function (Blueprint $table) {
            // Rollback: Drop the new constraint and restore the old one
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }
};
