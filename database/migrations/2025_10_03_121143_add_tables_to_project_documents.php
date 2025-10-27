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
            $table->boolean('allow_project_submission')->default(true);

            $table->string('rc_number')->nullable()->after('project_id'); // Manually added SHPO number
            
            $table->enum('submitter_response_timer_type',['day','week','month'])->nullable()->after('status'); // Time for submitter response
            $table->integer('submitter_response_timer_count')->default(1)->after('submitter_response_timer_type'); // Time for review response
            $table->dateTime('submitter_due_date')->nullable()->after('submitter_response_timer_count'); // The due/review due date
            
            $table->integer('reviewer_response_timer_count')->default(1)->after('submitter_due_date'); // Time for review response
            $table->enum('reviewer_response_timer_type',['day','week','month'])->nullable()->after('reviewer_response_timer_count'); // Time for review response
            $table->dateTime('reviewer_due_date')->nullable()->after('reviewer_response_timer_type'); // The due/review due date

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropColumn('allow_project_submission');

            $table->dropColumn('rc_number');
            
            $table->dropColumn('submitter_response_timer_type');
            $table->dropColumn('submitter_response_timer_count');
            $table->dropColumn('submitter_due_date');

            $table->dropColumn('reviewer_response_timer_count');
            $table->dropColumn('reviewer_response_timer_type');
            $table->dropColumn('reviewer_due_date');

        });
    }
};
