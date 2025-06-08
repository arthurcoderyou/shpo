<?php

namespace App\Listeners;

use App\Events\ReviewDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReviewDeletedListener
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
    public function handle(ReviewDeleted $event): void
    {
        //
    }
}
