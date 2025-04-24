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
        Schema::create('document_type_reviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained()->onDelete('cascade');
            $table->integer('review_order'); // position/order of the reviewer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_type_reviewers');
    }
};
