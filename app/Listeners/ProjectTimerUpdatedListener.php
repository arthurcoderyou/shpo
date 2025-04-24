<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ProjectTimerUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectTimerUpdatedListener
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
        $user = auth()->user();
        $timer = $event->project_timer;

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
