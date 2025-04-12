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
        Schema::create('project_discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
           
            $table->string('title')->nullable(); // Only parent discussions have a title
            $table->text('body');
            $table->foreignId('parent_id')->nullable()->constrained('project_discussions')->onDelete('cascade');
            $table->boolean('is_private')->default(false);
            $table->foreignId('creator')->constrained('users')->onDelete('cascade');
            $table->foreignId('updater')->constrained('users')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_discussions');
    }
};
