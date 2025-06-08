<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ProjectAttachments extends Model
{
    use SoftDeletes;
    protected $table = "project_attachments";

    protected $fillable = [
        'attachment',
        'project_id',
        'project_document_id',
        'created_by',
        'updated_by'
    ];


    public static function boot()
    {
        parent::boot();
        

        static::created(function ($project_attachment) {
            // event(new  \App\Events\ProjectAttachmentCreated($project_attachment));

            try {
                event(new \App\Events\ProjectAttachmentCreated($project_attachment));
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
                event(new \App\Events\ProjectAttachmentUpdated($project_attachment));
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
                event(new \App\Events\ProjectAttachmentDeleted($project_attachment));
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
