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
        Schema::table('project_documents', function (Blueprint $table) {
            $table->longText('applicant')->nullable();
            $table->longText('document_from')->nullable();
            $table->longText('company')->nullable();
            $table->longText('comments')->nullable();
            $table->longText('findings')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropColumn([
                'applicant',
                'document_from',
                'company',
                'comments',
                'findings',
            ]);
        });
    }
};
