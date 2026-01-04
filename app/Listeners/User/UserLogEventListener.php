<?php

namespace App\Listeners\User;

use App\Models\User;
use App\Models\ActivityLog;
use App\Events\User\UserLogEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLogEventListener implements ShouldQueue
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
    public function handle(UserLogEvent $event): void
    {
        $message = $event->message;
        $authUser = User::find($event->authId); 
        $modelId = $event->modelId ?? null; // to be used when we want to log use metadata 
 
 
        // project log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' => $message ,  
        ]); 


    }
}
