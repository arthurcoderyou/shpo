<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTimer extends Model
{
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
