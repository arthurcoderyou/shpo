<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDocument extends Model
{
    protected $table = "project_documents";

    protected $fillable = [ 
        'project_id',
        'document_type_id',
        'created_by',
        'updated_by'
    ];


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
