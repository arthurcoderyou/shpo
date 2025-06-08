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
            $table->enum('type',['initial','document','final'])->default('document');
            $table->unsignedBigInteger('project_document_id')->nullable()->after('project_id');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_reviewers', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropSoftDeletes();
            $table->dropColumn('project_document_id');
        });
    }
};
