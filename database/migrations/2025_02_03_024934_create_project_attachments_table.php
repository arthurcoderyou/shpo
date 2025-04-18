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
        Schema::create('project_attachments', function (Blueprint $table) {
            $table->id();
            $table->longText('attachment');
            $table->foreignId('project_id')->constrained('projects')->onUpdate('cascade')->onDelete('cascade'); 
             
            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignId('updated_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade'); 
            


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_attachments');
    }
};
