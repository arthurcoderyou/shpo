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
        Schema::table('project_timers', function (Blueprint $table) {
            $table->time('project_submission_open_time')->nullable();
            $table->time('project_submission_close_time')->nullable();
            $table->longText('message_on_open_close_time')->nullable();
            $table->boolean('project_submission_restrict_by_time')->default(false);
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_timers', function (Blueprint $table) {
            $table->dropColumn('project_submission_open_time');
            $table->dropColumn('project_submission_close_time');
            $table->dropColumn('message_on_open_close_time');
            $table->dropColumn('project_submission_restrict_by_time');

        });
    }
};
