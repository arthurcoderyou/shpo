<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectDocument extends Model
{
    use SoftDeletes;
    protected $table = "project_documents";

    protected $fillable = [ 
        'project_id',
        'document_type_id',
        'created_by',
        'updated_by',
        'status'
    ];
     
    public static function boot()
    {
        parent::boot();
        

        static::created(function ($project_document) {
            // event(new  \App\Events\ProjectDocumentCreated($project_document));

             try {
                event(new \App\Events\ProjectDocumentCreated($project_document));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectDocumentCreated event: ' . $e->getMessage(), [
                    'project_document_id' => $project_document->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


        });

        static::updated(function ($project_document) {
            // event(new  \App\Events\ProjectDocumentUpdated($project_document));

            try {
                event(new \App\Events\ProjectDocumentUpdated($project_document));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectDocumentUpdated event: ' . $e->getMessage(), [
                    'project_document_id' => $project_document->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        });

        static::deleted(function ($project_document) {
            // event(new  \App\Events\ProjectDocumentDeleted($project_document));

            try {
                event(new \App\Events\ProjectDocumentDeleted($project_document));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectDocumentDeleted event: ' . $e->getMessage(), [
                    'project_document_id' => $project_document->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        });


    }




    /**
     * Get all of the project attachments 
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_attachments()
    {
        return $this->hasMany(ProjectAttachments::class, 'project_document_id', 'id');
    }

    
    /**
     * Get all of the project discussions 
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_discussions()
    {
        return $this->hasMany(ProjectDiscussion::class, 'project_document_id', 'id');
    }


    /**
     * Get all of the project reviewers  
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_reviewers()
    {
        return $this->hasMany(ProjectReviewer::class, 'project_document_id', 'id');
    }



    /**
     * Get the Document Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document_type() # : BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id', 'id');
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



}
