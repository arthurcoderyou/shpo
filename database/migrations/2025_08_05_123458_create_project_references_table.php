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
        Schema::create('project_references', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_id'); // The project doing the referencing
            $table->unsignedBigInteger('referenced_project_id'); // The project being referenced 
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('referenced_project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->unique(['project_id', 'referenced_project_id']); // Optional: to avoid duplicate references

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_references');
    }
};
