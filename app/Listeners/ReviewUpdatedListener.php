<?php

namespace App\Listeners;

use App\Events\ReviewUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReviewUpdatedListener
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
    public function handle(ReviewUpdated $event): void
    {
        //
    }
}
