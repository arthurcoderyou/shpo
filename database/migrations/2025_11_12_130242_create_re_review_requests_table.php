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
        Schema::create('re_review_requests', function (Blueprint $table) {
            $table->id();
            $table->longText('reason')->nullable();
            $table->enum('status',['submitted','approved','rejected']);
            $table->longText('response_notes')->nullable();
            $table->foreignId('requested_to')->nullable()->constrained('project_reviewers')->onDelete('cascade');
            $table->foreignId('requested_by')->nullable()->constrained('project_reviewers')->onDelete('cascade'); // project reqviewer that requested it 
            
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('project_document_id')->nullable()->constrained('project_documents')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade'); 
            $table->timestamps();   
            $table->softDeletes();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('re_review_requests');
    }
};
