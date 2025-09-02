<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewRequireDocumentUpdates extends Model
{

    /**
     * Schema::create('review_require_document_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignId('project_id')->constrained('projects')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignId('document_type_id')->constrained('document_types')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreignId('project_reviewer_id')->constrained('project_reviewers')->onUpdate('cascade')->onDelete('cascade')->after('document_type_id'); 
            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('updated_by')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
     * 
     */

    use SoftDeletes;
    protected $table = "review_require_document_updates";
    protected $fillable = [
        'review_id',  
        'project_id',
        'document_type_id',
        'project_reviewer_id',
        'created_by',
        'updated_by',
         
    ];




    /**
     * Get the user that owns the ReviewRequireDocumentUpdates
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() // : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', );
    }

    /**
     * Get the document type that connects the ReviewRequireDocumentUpdates
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document_type() // : BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id', );
    }
}
