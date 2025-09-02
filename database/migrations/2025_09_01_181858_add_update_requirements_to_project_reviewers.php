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
            $table->boolean('requires_project_update')->default(false)->after('project_document_id');     
            $table->boolean('requires_document_update')->default(false)->after('requires_project_update');
            $table->boolean('requires_attachment_update')->default(false)->after('requires_document_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_reviewers', function (Blueprint $table) {
            //
        });
    }
};
