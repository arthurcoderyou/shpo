<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReReviewRequest extends Model
{
    use SoftDeletes;
    /**
     * Schema::create('re_review_requests', function (Blueprint $table) {
            $table->id();
            $table->longText('reason')->nullable();
            $table->enum('status',['submitted','approved','rejected']);
            $table->longText('response_notes')->nullable();
            $table->foreignId('requested_to')->nullable()->constrained('project_reviewers')->onDelete('cascade');
            $table->foreignId('requested_by')->nullable()->constrained('project_reviewers')->onDelete('cascade'); // project reqviewer that requested it 
            
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('cascade');
            $table->foreignId('project_document_id')->nullable()->constrained('project_documents')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade'); 
            $table->timestamps();   
            $table->softDeletes();  
        });

     */


    protected $table = "re_review_requests";

    protected $fillable = [
        'reason',  
        'status', // true or false
        'response_notes',
        'requested_to',
        'requested_by',
        'project_id',
        'project_document_id', 
        'created_by',
        'updated_by',
        'created_at',
        'updated_at', 
    ];


    /**
     * Get the user that owns the review
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user that updates the review
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
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
     * Get the Project document connected to the review
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project_document() # : BelongsTo
    {
        return $this->belongsTo(ProjectDocument::class, 'project_document_id', 'id');
    }



    /**
     * Get the ProjectReviewer Requested to 
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project_reviewer_requested_to() # : BelongsTo
    {
        return $this->belongsTo(ProjectReviewer::class, 'requested_to', 'id');
    }


     /**
     * Get the ProjectReviewer Requested by 
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project_reviewer_requested_by() # : BelongsTo
    {
        return $this->belongsTo(ProjectReviewer::class, 'requested_by', 'id');
    }



    /**
     * Check if a reviewer already has a SUBMITTED re-review request,
     * optionally constrained to a specific project and/or project document.
     *
     * @param  int       $projectReviewerId
     * @param  int|null  $projectId
     * @param  int|null  $projectDocumentId
     * @return bool
     */
    public static function checkIfProjectReviewerHasSubmittedReReview(
        int $projectReviewerId,
        ?int $projectId = null,
        ?int $projectDocumentId = null,
    ): bool {
        return static::query()
            ->where('requested_by', $projectReviewerId)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($projectDocumentId, fn ($q) => $q->where('project_document_id', $projectDocumentId))
            ->where('status', 'submitted')
            ->whereNull('deleted_at')   // if using SoftDeletes
            ->exists();
    }

    /**
     * (Optional) Get the latest SUBMITTED re-review record for the reviewer.
     * Returns null if none exists.
     */
    public static function latestSubmittedForReviewer(
        int $projectReviewerId,
        ?int $projectId = null,
        ?int $projectDocumentId = null,
    ): ?self {
        return static::query()
            ->where('project_reviewer_id', $projectReviewerId)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($projectDocumentId, fn ($q) => $q->where('project_document_id', $projectDocumentId))
            ->where('status', 'submitted')
            ->whereNull('deleted_at')
            ->latest('created_at')
            ->first();
    }

}
