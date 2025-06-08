<?php

namespace App\Listeners;

use App\Events\ProjectSubscriberDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProjectSubscriberDeletedListener
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
    public function handle(ProjectSubscriberDeleted $event): void
    {
        //
    }
}
