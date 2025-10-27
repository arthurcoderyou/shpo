<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\FileCategory;

class Attachments extends Model
{


    /**
     * $table->string('original_name')->nullable()->after('attachment');   // original client filename
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
     * 
     */

    protected $table = "attachments";
    protected $fillable = [
        'attachment',

        'original_name',

        'disk',
        'path',
        'category',
        'mime_type',

        'width',
        'height',

        'duration_seconds',

        'sha256',

        'created_by',
        'updated_by', 
    ];

 
    protected $casts = [
        'category' => FileCategory::class,
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'duration_seconds' => 'integer',
    ];



    /**
     * Get the user that owns the Reviewer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user that owns the Reviewer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }


    // when using this, remove the word scope

    public function scopeOwnedBy(Builder $query, $userId)
    {
        return $query->where('created_by', $userId);
    }


}   
