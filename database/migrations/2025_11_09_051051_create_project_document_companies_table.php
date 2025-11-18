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
        Schema::create('project_document_companies', function (Blueprint $table) {
            $table->id();
            $table->longText('name')->nullable();
            $table->foreignId('project_id')->constrained('projects')->onUpdate('cascade')->onDelete(action: 'cascade')->nullable(); 
            $table->foreignId('project_document_id')->constrained('project_documents')->onUpdate('cascade')->onDelete(action: 'cascade')->nullable(); 

            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete(action: 'cascade');
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
        Schema::dropIfExists('project_document_companies');
    }
};
