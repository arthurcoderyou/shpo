<?php

namespace App\Listeners;

use App\Events\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UpdateUserActivityTimestamp
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
    public function handle(UserActivity $event): void
    {
        Cache::put('user-activity-' . $event->userId, Carbon::now()->timestamp);
    }
}
