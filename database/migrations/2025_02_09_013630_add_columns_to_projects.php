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

            $table->string('project_number')->nullable()->after('name'); // Auto-generated project number
            $table->string('shpo_number')->nullable()->after('project_number'); // Manually added SHPO number
            
            $table->enum('submitter_response_timer_type',['day','week','month'])->nullable()->after('status'); // Time for submitter response
            $table->integer('submitter_response_timer_count')->default(1)->after('submitter_response_timer_type'); // Time for review response
            $table->dateTime('submitter_due_date')->nullable()->after('submitter_response_timer_count'); // The due/review due date
            
            $table->integer('reviewer_response_timer_count')->default(1)->after('submitter_due_date'); // Time for review response
            $table->enum('reviewer_response_timer_type',['day','week','month'])->nullable()->after('reviewer_response_timer_count'); // Time for review response
            $table->dateTime('reviewer_due_date')->nullable()->after('reviewer_response_timer_type'); // The due/review due date
            
            // $table->boolean('submitter_is_flagged_due')->default(false)->after('due_date'); // Flag if project has a due date
            // $table->integer('global_timer_days')->default(21)->after('is_flagged_due'); // Default global timer days
           




            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('project_number');
            $table->dropColumn('shpo_number');
            
            $table->dropColumn('submitter_response_timer_type');
            $table->dropColumn('submitter_response_timer_count');
            $table->dropColumn('submitter_due_date');

            $table->dropColumn('reviewer_response_timer_count');
            $table->dropColumn('reviewer_response_timer_type');
            $table->dropColumn('reviewer_due_date');
            
        });
    }
};
