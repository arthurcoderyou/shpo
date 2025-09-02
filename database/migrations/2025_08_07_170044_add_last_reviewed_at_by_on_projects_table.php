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
        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('last_reviewed_at')->after('last_submitted_at')->nullable();
            $table->foreignId('last_reviewed_by')->nullable()->constrained('users')->after('last_reviewed_at')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('last_reviewed_at');
            $table->dropColumn('last_reviewed_by');
        });
    }
};
