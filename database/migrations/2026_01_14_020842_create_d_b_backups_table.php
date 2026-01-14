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
        Schema::create('d_b_backups', function (Blueprint $table) {
            $table->id();  
            $table->longText('file')->nullable();
            $table->longText('folder')->nullable();
            $table->boolean('emailed_status')->default(false);
            $table->boolean('ftp_copied_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_b_backups');
    }
};
