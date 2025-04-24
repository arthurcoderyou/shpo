<?php

namespace App\Models;

use App\Events\ActivityLogCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model 
{
    use SoftDeletes;
    protected $table = "activity_logs";
    protected $fillable = [
        'log_action',
        'log_username',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($activityLog) {
            // Trigger an event or perform some action when a new Activity
            // Log is created
            event(new ActivityLogCreated($activityLog));
        });
    }


    /**
     * Get the user that owns the ActivityLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()  # : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by' );
    }



}

