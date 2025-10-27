<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ProjectAttachments extends Model
{
    use SoftDeletes;
    
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
    
    protected $table = "project_attachments";

    protected $fillable = [
        'attachment',
        'filesystem',
        'project_id',
        'project_document_id',

        'original_name',
        'stored_name',

        'disk',
        'path',
        'category',
        'mime_type',
        'extension',

        'width',
        'height',

        'duration_seconds',

        'sha256',



        'created_by',
        'updated_by',
        'last_submitted_at',
        'last_submitted_by',
        'created_at',
        'updated_at',
    ];


    public static function boot()
    {
        parent::boot();
        

        static::created(function ($project_attachment) {
            // event(new  \App\Events\ProjectAttachmentCreated($project_attachment));

            try {
                event(new \App\Events\ProjectAttachmentCreated($project_attachment, auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectAttachmentCreated event: ' . $e->getMessage(), [
                    'project_attachment_id' => $project_attachment->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }



        });

        static::updated(function ($project_attachment) {
            // event(new  \App\Events\ProjectAttachmentUpdated($project_attachment));

            try {
                event(new \App\Events\ProjectAttachmentUpdated($project_attachment, auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectAttachmentUpdated event: ' . $e->getMessage(), [
                    'project_attachment_id' => $project_attachment->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        });

        static::deleted(function ($project_attachment) {
            // event(new  \App\Events\ProjectAttachmentDeleted($project_attachment));

            try {
                event(new \App\Events\ProjectAttachmentDeleted($project_attachment->id, auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectAttachmentDeleted event: ' . $e->getMessage(), [
                    'project_attachment_id' => $project_attachment->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            
        });


    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_submitted_at' => 'datetime', 
        ];
    }
    


    /**
     * Get the Document Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project_document() # : BelongsTo
    {
        return $this->belongsTo(ProjectDocument::class, 'project_document_id', 'id');
    }


    /**
     * Get the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project() # : BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    
}
