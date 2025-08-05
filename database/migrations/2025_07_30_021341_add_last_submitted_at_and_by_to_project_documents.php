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
            $table->timestamp('last_submitted_at')->nullable()->after('document_type_id');
            $table->foreignId('last_submitted_by')->nullable()->constrained('users')->after('last_submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropColumn('last_submitted_at');
            $table->dropColumn('last_submitted_by');
        });
    }
};
