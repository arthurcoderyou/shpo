<?php

namespace App\Listeners\ProjectTimer;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ProjectTimer\ProjectTimerLogEvent;

class ProjectTimerLogEventListener implements ShouldQueue
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
    public function handle(ProjectTimerLogEvent $event): void
    {
        $message = $event->message;
        $authUser = User::find($event->authId); 
        $modelId = $event->modelId;

   
        // project log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' => $message ,  
        ]);
    }
}
