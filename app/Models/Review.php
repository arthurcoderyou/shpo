<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = "reviews";
    protected $fillable = [
        'viewed', // true or false
        'project_review',
        'project_id',
        'reviewer_id',
        'review_status',
        # ['pending','approved','rejected']
        # 'submitted' is the special review status for users 
        # re_submitted for resubmission
        'admin_review', // means that this is a review from the admin
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',

        'response_time_hours',
        'review_time_hours',
    ];


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
     * Get the project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project() # : BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
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
                $response_times[] = (float) $review->response_time_hours;
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





}
