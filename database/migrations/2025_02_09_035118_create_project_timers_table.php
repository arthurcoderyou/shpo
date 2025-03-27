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
        Schema::create('project_timers', function (Blueprint $table) {
            $table->id();

            
            $table->enum('submitter_response_timer_type',['day','week','month'])->nullable(); // Time for submitter response
            $table->integer('submitter_response_timer_count')->default(1); // Time for review response 
            
            $table->integer('reviewer_response_timer_count')->default(1); // Time for review response
            $table->enum('reviewer_response_timer_type',['day','week','month']); // Time for review response 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_timers');
    }
};
