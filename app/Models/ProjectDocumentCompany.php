<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDocumentCompany extends Model
{

    use SoftDeletes;

    /**
     *   Schema::create('project_document_companies', function (Blueprint $table) {
            $table->id();
            $table->longText('name')->nullable();
            $table->foreignId('project_id')->constrained('projects')->onUpdate('cascade')->onDelete(action: 'cascade')->nullable(); 
            $table->foreignId('project_document_id')->constrained('project_documents')->onUpdate('cascade')->onDelete(action: 'cascade')->nullable(); 

            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete(action: 'cascade');
            $table->foreignId('updated_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->softDeletes();

            $table->timestamps();
        });
     * 
     */

    protected $table = "project_document_companies";
    protected $fillable = [
        "name",
        "project_id",
        "project_document_id",
        "created_by",
        "updated_by", 
    ]; 



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
     * Get the ProjectDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project_document() # : BelongsTo
    {
        return $this->belongsTo(ProjectDocument::class, 'project_document_id', 'id');
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
