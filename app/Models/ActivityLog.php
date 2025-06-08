<?php

namespace App\Models;

use App\Events\ActivityLogCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model 
{
    use SoftDeletes;
    protected $table = "activity_logs";

    /**
     * activity logs will be adjusted to have the logs for
     * log_type:
     * - project -> create | update | delete | submitted | reviewed 
     * 
     */
    //  
    // project -> create | update | delete | submitted | reviewed | 
        // THe log on the project should also eb displayed in timeline style


    
        // project review
        // project document review 
        // project discussion review    

    /**
     * [longtext] log_type : general (general) | project | project_document | project_document_attachment | discussion | project_review | project_reviewer | 
     * [int] project_id nullable 
     * [int] project_document_id nullable 
     * [int] project_document_attachment_id nullable 
     * [int] project_discussion_id nullable
     * [int] project_review_id nullable 
     * [int] project_reviewer_id nullable 
     * 
     */


    protected $fillable = [
        'log_action',
        'log_username',
        'created_by',

        'project_id',
        'project_document_id',
        'project_document_attachment_id',
        'project_discussion_id',
        'project_review_id',
        'project_reviewer_id',
        'project_subscriber_id', 
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($activityLog) {
            // Trigger an event or perform some action when a new Activity
            // Log is created
            // event(new ActivityLogCreated($activityLog));
        });
    }


    /**
     * Get the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()  # : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by' );
    }

    /**
     * Get the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()  # : BelongsTo
    {
        return $this->belongsTo(Project::class, 'created_by' );
    }

     /**
     * Get the Review
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function review()  # : BelongsTo
    {
        return $this->belongsTo(Review::class, 'project_review_id' );
    }


    /** Fall Back Methods */
    public function getUserNameAttribute()
    {
        return $this->user?->name ?? 'User deleted';
    }

    public function getUserEmailAttribute()
    {
        return $this->user?->email ?? 'Email not available';
    }


}

