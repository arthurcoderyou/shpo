<?php

namespace App\Listeners\ProjectDiscussion;

use App\Events\ProjectDiscussion\MessageNotificationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MessageNotificationEmailListener
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
    public function handle(MessageNotificationEmail $event): void
    {
        //
    }
}
