<?php

namespace App\Listeners;

use App\Events\ProjectSubscriberCreated ;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProjectSubscriberCreatedListener
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
    public function handle(ProjectSubscriberCreated  $event): void
    {
        //
    }
}
