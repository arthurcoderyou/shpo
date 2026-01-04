<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use SoftDeletes;
    protected $table = "reviews";
    protected $fillable = [
        'iteration', // iteration
        'viewed', // true or false
        'project_review',
        'project_id',
        'project_document_id',
        'reviewer_id', // user id of hte user that made the review
        'project_reviewer_id', // project reviewer 
        'review_status',
        
        # ['pending','approved','rejected','submitted','re_submitted','changes_requested','reviewed','re_review_requested']
        # 'submitted' is the special review status for users 
        # re_submitted for resubmission

        'project_document_status', 
        # 'draft','submitted','in_review','approved','rejected','completed','cancelled',','changes_requested','reviewed', 're_review_requested'
        'next_reviewer_name',

        'admin_review', // means that this is a review from the admin
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
 
        'response_time_hours',
        'review_time_hours',
 
        'requires_project_update',
        'requires_document_update',
        'requires_attachment_update',

        're_review_requests_id'
    ];


    public static function boot()
    {
        parent::boot();
        

        // static::created(function ($review) { 

        //     try {
        //         event(new \App\Events\ReviewCreated($review->id, auth()->user()->id));
        //     } catch (\Throwable $e) {
        //         // Log the error without interrupting the flow
        //         Log::error('Failed to dispatch ReviewCreated event: ' . $e->getMessage(), [
        //             'review_id' => $review->id,
        //             'trace' => $e->getTraceAsString(),
        //         ]);
        //     }


        // });

        // static::updated(function ($project_reviewer) {
        //     // event(new  \App\Events\ProjectReviewerUpdated($project_reviewer));

        //     try {
        //         event(new \App\Events\ProjectReviewerUpdated($project_reviewer));
        //     } catch (\Throwable $e) {
        //         // Log the error without interrupting the flow
        //         Log::error('Failed to dispatch ProjectReviewerUpdated event: ' . $e->getMessage(), [
        //             'project_reviewer_id' => $project_reviewer->id,
        //             'trace' => $e->getTraceAsString(),
        //         ]);
        //     }


        // });

        // static::deleted(function ($project_reviewer) {
        //     // event(new  \App\Events\ProjectReviewerDeleted($project_reviewer));

        //     try {
        //         event(new \App\Events\ProjectReviewerDeleted($project_reviewer));
        //     } catch (\Throwable $e) {
        //         // Log the error without interrupting the flow
        //         Log::error('Failed to dispatch ProjectReviewerDeleted event: ' . $e->getMessage(), [
        //             'project_reviewer_id' => $project_reviewer->id,
        //             'trace' => $e->getTraceAsString(),
        //         ]);
        //     }
        // });


    }



    // the submission of the updater of hte project or the creator itself must be added on the reviews 
    // if the reviewer_id is the same to the project creator, means that the review is not on reviewer side but on the user side




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
     * Get all of the comments for the ReviewAttachments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments() # : HasMany
    {
        return $this->hasMany(ReviewAttachments::class, 'review_id', 'id');
    }


    /**
     * Get the user that Reviewed the review
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reviewer() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id', 'id');
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
     * Get the Project reviewer connected to the review
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project_reviewer() # : BelongsTo
    {
        return $this->belongsTo(ProjectReviewer::class, 'project_reviewer_id', 'id');
    }



    /**
     * Get the project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project() # : BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }


    /**
     * Get all of the attachments for the review
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function required_attachment_updates() // : HasMany
    {
        return $this->hasMany(ReviewRequireAttachmentUpdates::class, 'review_id');
    }

    /**
     * Get all of the comments for the Review
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function required_document_updates() // : HasMany
    {
        return $this->hasMany(ReviewRequireDocumentUpdates::class, 'review_id');
    }

    static public function returnReviewCount($project_reviewer_id, $project_document_id = null){

        $query = Review::where('project_reviewer_id', $project_reviewer_id);
        
        if(!empty($project_document_id)){
            $query = $query->where('project_document_id',$project_document_id);

        }
        $query = $query->count();

        return $query;
    }


    static public function countUnseenEmails($project_id){
        return Review::where('project_id',$project_id)
            ->where('viewed',false)
            ->count();
    }



    /**
     * Check if the review is a submitter review (i.e., reviewer is the project creator)
     *
     * @return bool
     */
    public function isSubmitterReview(): bool
    {
        return $this->reviewer_id === $this->project->created_by;
    }



    /**
     * Calculate the average response time for project creators based on a given time range.
     *
     * @param string $timeRange (Options: today, this_week, this_month, this_year, all)
     * @return float
     */
    public static function getAverageResponseTime($timeRange = 'all')
    {
        $query = self::whereIn('review_status', ['submitted', 're_submitted']);

        // Apply date filters based on time range
        switch ($timeRange) {
            case 'today':
                $query->whereDate('created_at', Carbon::now());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'this_year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'all':
            default:
                // No additional filters, include all records
                break;
        }

        $reviews = $query->orderBy('created_at', 'asc')->whereNotNull('response_time_hours')->get();
        $response_times = [];

        foreach ($reviews as $review) {
            // If response_time_hours exists, use it
            if (!is_null($review->response_time_hours)) {
                $response_times[] = (int) $review->response_time_hours;
                continue;
            }
        }

        return count($response_times) > 0 ? array_sum($response_times) / count($response_times) : 0;
    }




    public static function getAverageReviewTime($timeRange = 'all')
    {
        $query = self::whereIn('review_status', ['approved', 'rejected']);

        // Apply time filters based on range
        switch ($timeRange) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'this_year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'all':
            default:
                // No additional filters, include all records
                break;
        }

        $reviews = $query->orderBy('created_at', 'asc')->whereNotNull('review_time_hours')->get();
        $review_times = [];

        foreach ($reviews as $review) {
             // If review_time_hours exists, use it
             if (!is_null($review->review_time_hours)) {
                $review_times[] = (float) $review->review_time_hours;
                continue;
            }
        }

        return count($review_times) > 0 ? array_sum($review_times) / count($review_times) : 0;
    }


    public function getStatus() #: string
    {
        switch ($this->project_status) {
            case 'submitted': 
                return "<span class='font-bold text-blue-500'>Submitted</span>";
            case 'in_review': 
                return "<span class='font-bold text-sky-500'>In Review</span>";
            case 'on_que': 
                return "<span class='font-bold text-pink-500'>On Que</span>";
            case 'approved': 
                return "<span class='font-bold text-lime-500'>Approved</span>";
            case 'rejected': 
                return "<span class='font-bold text-red-500'>Rejected</span>";
            case 'completed': 
                return "Completed";
            case 'cancelled': 
                return "Cancelled";
            default: 
                return "Draft";
        }
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->project_status) {
            'submitted'  => "Submitted",
            'in_review'  => "In Review",
            'approved'   => "Approved",
            'rejected'   => "Rejected",
            'completed'  => "Completed",
            'cancelled'  => "Cancelled",
            'on_que'    =>  "On Que",
            default      => "Draft",
        };
    }


}
