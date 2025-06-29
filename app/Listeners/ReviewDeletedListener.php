<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\ActivityLog;
use App\Events\ReviewDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewDeletedListener  implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewDeleted $event): void
    { 
        $user = User::find($event->authId);

        // project log
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' => "Project review deleted by '".$user->name."'", 
        ]);


        // review log 
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' => $event->message, 
        ]);
    }
}
