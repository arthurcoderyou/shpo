<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTimer extends Model
{

    /**
     * $table->time('project_submission_open_time')->nullable();
     * $table->time('project_submission_close_time')->nullable();
     * $table->longText('message_on_open_close_time')->nullable();
     * $table->boolean('project_submission_restrict_by_time')->default(false);
     *  
     */
    protected $table = "project_timers";
    protected $fillable = [
        
        'submitter_response_duration_type',
        'submitter_response_duration', 
        'reviewer_response_duration',
        'reviewer_response_duration_type', 
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'project_submission_open_time',
        'project_submission_close_time',
        'message_on_open_close_time',
        'project_submission_restrict_by_time'
        
    ];


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
