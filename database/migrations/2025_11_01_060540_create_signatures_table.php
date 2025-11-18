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
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
 
             $table->morphs('signable');                // signable_type, signable_id
             $table->foreignId('user_id')->constrained()->cascadeOnDelete();
             $table->string('signer_name')->nullable();
             $table->string('signature_path');          // storage/app/signatures/xxxx.png
             $table->timestamp('signed_at');
             $table->string('ua')->nullable();          // user agent at signing
             $table->string('ip', 64)->nullable();
             $table->string('hash', 128);               // HMAC over critical fields
             $table->json('meta')->nullable();          // reason, location, etc.
             $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
