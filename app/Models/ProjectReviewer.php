<?php

namespace App\Models;

use App\Http\Controllers\ProjectReviewerController;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectReviewer extends Model
{

    use SoftDeletes;
    protected $table = "project_reviewers";
    protected $fillable  = [
        'order',
        'status', /// true or false, tells if the reviewer is the active reviewer or not
        'project_id',
        'user_id',
        'created_by',
        'updated_by',
        'review_status', // 'pending','approved','rejected','changes_requested'
        'reviewer_type',
        'project_document_id',

        'slot_type',
        'slot_role',
        
        'requires_project_update',
        'requires_document_update',
        'requires_attachment_update',

     
        # ['pending','approved','rejected']
    ];


    public static function boot()
    {
        parent::boot();
        

        static::created(function ($project_reviewer) {
            // event(new  \App\Events\ProjectReviewerCreated($project_reviewer));

            try {
                event(new \App\Events\ProjectReviewerCreated($project_reviewer,auth()->user()->id  ));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectReviewerCreated event: ' . $e->getMessage(), [
                    'project_reviewer_id' => $project_reviewer->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


        });

        static::updated(function ($project_reviewer) {
            // event(new  \App\Events\ProjectReviewerUpdated($project_reviewer));

            try {
                event(new \App\Events\ProjectReviewerUpdated($project_reviewer, auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectReviewerUpdated event: ' . $e->getMessage(), [
                    'project_reviewer_id' => $project_reviewer->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


        });

        static::deleted(function ($project_reviewer) {
            // event(new  \App\Events\ProjectReviewerDeleted($project_reviewer));

            try {
                event(new \App\Events\ProjectReviewerDeleted($project_reviewer->id,auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectReviewerDeleted event: ' . $e->getMessage(), [
                    'project_reviewer_id' => $project_reviewer->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        });


    }


    /**
     * Get the user that owns the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project() # : BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }


    /**
     * Get the Project Document
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
    public function user() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get all of the reviews for the ProjectReviewer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews() 
    {
        return $this->hasMany(Review::class, 'project_reviewer_id');
    }
    


    // /**
    //  * Get the reviews of the project reviewer
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  */
    // public function reviews() # : BelongsTo
    // {
    //     return $this->hasMany(Review::class, 'user_id', 'id');
    // }


    static public function getLastSubmitterReview(ProjectDocument $project_document,ProjectReviewer $project_reviewer){

        return Review::where(function (\Illuminate\Database\Eloquent\Builder $q) use ($project_reviewer) {
            $q->where('project_reviewer_id', $project_reviewer->id)
              ->orWhere('reviewer_id', $project_reviewer->user_id);
        })
        ->where('project_document_id', $project_document->id)
        ->orderByDesc('id')
        ->first();
         
    }

    static public function getLastSubmitterDocumentReview(ProjectDocument $project_document,ProjectReviewer $project_reviewer){

        return Review::where(function (\Illuminate\Database\Eloquent\Builder $q) use ($project_reviewer) {
            $q->where('project_reviewer_id', $project_reviewer->id)
              ->orWhere('reviewer_id', $project_reviewer->user_id);
        })
        ->where('project_document_id', $project_document->id)
        ->orderByDesc('id')
        ->first(); 
    }
 
    public function getReviewStatus(){


        switch($this->review_status){
            case "approved": 
                return '<span class="font-bold text-lime-500">Approved</span>';
                // break;
            case "rejected": 
                return '<span class="font-bold text-red-500">Rejected</span>';
                // break;

            default: 
                return '<span class="font-bold text-yellow-500">Pending</span>';
                // break;

        }

    }



    public static function getProjectReviewer($project_id, $user_id){

        return ProjectReviewer::where('project_id',$project_id)
            ->where('user_id',$user_id)
            ->where('status',true)
            ->whereNot('review_status','approved')
            ->first();

    }




}
