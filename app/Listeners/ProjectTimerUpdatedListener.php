<?php

namespace App\Listeners;

use App\Models\ProjectTimer;
use App\Models\User;
use App\Models\ActivityLog;
use App\Events\ProjectTimerUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectTimerUpdatedNotification;

class ProjectTimerUpdatedListener  implements ShouldQueue
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
    public function handle(ProjectTimerUpdated $event): void
    {
        $user = User::find($event->authId);
        $timer = ProjectTimer::find($event->project_timer_id);

        $logMessage = $timer
            ? 'Time settings updated by ' . $timer->updator->name . ' at ' . $timer->updated_at->toDateTimeString()
            : 'Time settings updated by ' . $user->name . ' at ' . now()->toDateTimeString();

        ActivityLog::create([
            'created_by' => $user->id,
            'log_username' => $logMessage,
            'log_action' => 'Updated Project Timer settings at ' . now()->toDateTimeString(),
        ]);



        

 

    }
}
