<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\ActivityLog;
use App\Events\ProjectReviewerDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectReviewerDeletedListener  implements ShouldQueue
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
    public function handle(ProjectReviewerDeleted $event): void
    { 
        $user = User::find($event->authId );

        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' =>  $user->name,
            'log_action' => $event->message, 
        ]);
    }
}
