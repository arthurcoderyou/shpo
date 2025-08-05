<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\UserDeleted;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserDeletedListener  implements ShouldQueue
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
    public function handle(UserDeleted $event): void
    {
        $user = User::find($event->authId);

        if(Auth::check() && !empty($user)){
            ActivityLog::create([
                'created_by' => $event->authId,
                'log_username' => $user->name,
                'log_action' => $event->message,
            ]);
        }
    }
}
