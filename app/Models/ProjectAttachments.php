<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAttachments extends Model
{
    protected $table = "project_attachments";

    protected $fillable = [
        'attachment',
        'project_id',
        'project_document_id',
        'created_by',
        'updated_by'
    ];


    /**
     * Get the Document Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project_document() # : BelongsTo
    {
        return $this->belongsTo(ProjectDocument::class, 'project_document_id', 'id');
    }
    
}
