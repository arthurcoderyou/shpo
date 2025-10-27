<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectDocumentReferences extends Model
{
    // use SoftDeletes;

    /**
     * 
     * Schema::create('project_references', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('project_document_id'); // The project doing the referencing
            $table->unsignedBigInteger('referenced_project_document_id'); // The project being referenced 
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');

            
            $table->softDeletes();
            $table->timestamps();
        });
     */

    protected $table = "project_document_references";
    protected $fillable = [
        'project_document_id',
        'referenced_project_document_id', 
        'created_by',
        'updated_by',  

    ];


    /**
     * Get the user that owns the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    

    /**
     * Get the user that owns the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }




    /**
     * Get the project  
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document() # : BelongsTo
    {
        return $this->belongsTo(ProjectDocument::class, 'project_document_id', 'id');
    }

    
    /**
     * Get the referenced_project  
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referenced_document() # : BelongsTo
    {
        return $this->belongsTo(ProjectDocument::class, 'referenced_project_document_id', 'id');
    }
}
