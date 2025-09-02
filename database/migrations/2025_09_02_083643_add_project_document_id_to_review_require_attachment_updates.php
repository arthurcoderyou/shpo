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
        Schema::table('review_require_attachment_updates', function (Blueprint $table) {
            $table->foreignId('project_document_id')->constrained('project_documents')->onUpdate('cascade')->onDelete('cascade')->after('document_type_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('review_require_attachment_updates', function (Blueprint $table) {
            $table->dropColumn('project_document_id');
        });
    }
};
