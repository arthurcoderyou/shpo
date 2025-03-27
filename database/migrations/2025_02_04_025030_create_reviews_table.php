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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->boolean('viewed')->default(false);
            $table->longText('project_review');
            $table->foreignId('project_id')->constrained('projects')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignId('updated_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade'); 
            $table->enum('review_status',['pending','approved','rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
