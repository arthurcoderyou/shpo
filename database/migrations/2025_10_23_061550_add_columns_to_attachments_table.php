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
        Schema::table('attachments', function (Blueprint $table) {
            $table->string('original_name')->nullable()->after('attachment');   // original client filename
            $table->string('stored_name')->nullable()->after('original_name');  // actual stored filename
            $table->string('disk', 50)->default('ftp')->after('stored_name');
            $table->string('path')->nullable()->after('disk');                  // directory path (e.g. files_multiple)

            $table->string('category', 20)->nullable()->index()->after('path'); // image|video|audio|pdf|word|excel|ppt|text|archive|other
            $table->string('mime_type', 100)->nullable()->after('category');
            $table->string('extension', 10)->nullable()->after('mime_type');
            $table->unsignedBigInteger('size_bytes')->default(0)->after('extension');

            // Optional extras
            $table->unsignedInteger('width')->nullable()->after('size_bytes');   // for images
            $table->unsignedInteger('height')->nullable()->after('width');       // for images
            $table->unsignedInteger('duration_seconds')->nullable()->after('height'); // for audio/video if you add FFmpeg/getID3 later
            $table->string('sha256', 64)->nullable()->index()->after('duration_seconds'); // dedupe / integrity
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn([
                'original_name','stored_name','disk','path',
                'category','mime_type','extension','size_bytes',
                'width','height','duration_seconds','sha256'
            ]);
        });
    }
};
