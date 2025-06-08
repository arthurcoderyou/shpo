<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\ActivityLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreatedListener
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
    public function handle(UserCreated $event): void
    {
        $user = $event->user;

        if(auth()::check() && !empty($user)){
            ActivityLog::create([
                'created_by' => auth()::user->id,
                'log_username' => $user->name,
                'log_action' => "New User '".$user->name."' created",
            ]);
        }
        
    }
}
