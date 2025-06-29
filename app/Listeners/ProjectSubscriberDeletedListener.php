<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ProjectSubscriber;
use App\Events\ProjectSubscriberDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectSubscriberDeletedListener  implements ShouldQueue
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
    public function handle(ProjectSubscriberDeleted $event): void
    { 
        $user = User::find($event->authId); 

        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' =>  $event->message, 
        ]);
    }
}
