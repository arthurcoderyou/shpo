<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\DayStatusUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DayStatusUpdatedListener
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
    public function handle(DayStatusUpdated $event): void
    {
        ActivityLog::create([
            'created_by' => auth()->id(), 
            'log_username' => auth()->user()->name,
            'log_action' => 'Updated day status of '.$event->day->day.' to ' . ($event->day->is_active ? 'active' : 'inactive'),
        ]);

    }
}
