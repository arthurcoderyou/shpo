<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = "activity_logs";
    protected $fillable = [
        'log_action',
        'log_username',
        'created_by',
    ];

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

