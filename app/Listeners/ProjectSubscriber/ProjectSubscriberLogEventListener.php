<?php

namespace App\Listeners\ProjectSubscriber;

use App\Models\User;
use App\Models\ActivityLog;
use App\Events\Role\RoleLogEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ProjectSubscriber\ProjectSubscriberLogEvent;

class ProjectSubscriberLogEventListener implements ShouldQueue
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
    public function handle(RoleLogEvent $event): void
    {
        $message = $event->message;
        $authUser = User::find($event->authId); 

   
        // project log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' => $message ,  
        ]);
    }
}
