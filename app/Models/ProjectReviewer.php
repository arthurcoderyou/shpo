<?php

namespace App\Models;

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
        'review_status',
        'reviewer_type',
        'project_document_id',

    


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





}
