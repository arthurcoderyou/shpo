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
            $table->unsignedBigInteger('project_id')->nullable()->after('id');
            $table->unsignedBigInteger('project_document_id')->nullable()->after('project_id');
            $table->unsignedBigInteger('project_document_attachment_id')->nullable()->after('project_document_id');
            $table->unsignedBigInteger('project_discussion_id')->nullable()->after('project_document_attachment_id');
            $table->unsignedBigInteger('project_review_id')->nullable()->after('project_discussion_id');
            $table->unsignedBigInteger('project_reviewer_id')->nullable()->after('project_review_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn([
                'project_id',
                'project_document_id',
                'project_document_attachment_id',
                'project_discussion_id',
                'project_review_id',
                'project_reviewer_id'
            ]);
        });
    }
};
