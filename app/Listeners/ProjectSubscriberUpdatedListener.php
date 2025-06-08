<?php

namespace App\Listeners;

use App\Events\ProjectSubscriberUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProjectSubscriberUpdatedListener
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
    public function handle(ProjectSubscriberUpdated $event): void
    {
        //
    }
}
