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
        Schema::create('project_discussion_mentions', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); 
            $table->foreignId('project_discussion_id')->nullable()->constrained('project_discussions')->onDelete('cascade'); 
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_discussion_mentions');
    }
};
