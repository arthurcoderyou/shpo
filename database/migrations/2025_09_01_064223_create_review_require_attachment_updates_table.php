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
        Schema::create('review_require_attachment_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignId('project_id')->constrained('projects')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignId('document_type_id')->constrained('document_types')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
             $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_require_attachment_updates');
    }
};
