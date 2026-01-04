<?php

namespace App\Listeners\System;

use App\Models\User;
use App\Events\System\SystemEvent;
use Illuminate\Support\Facades\Log; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification; 
use App\Notifications\System\SystemEventNotification;

class SystemEventListener implements ShouldQueue
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
    public function handle(SystemEvent $event): void
    {
        // send in website notifications 
        $targetUserId = $event->targetUserId;
        $message = $event->message; 
        $link = $event->link;



        // get the user to be notified
        $user = User::find($targetUserId );


        if($event->add_notification){
            // --- Notification 
            try {
                Notification::send($user, new SystemEventNotification(
                    $targetUserId,
                    $message,
                    $link,    // NEW
                ));
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch SystemEventNotification notification: ' . $e->getMessage(), [
                    'targetUserId'            => $targetUserId,
                    'message'  => $message,
                    'link'              => $link, 
                    'trace'                => $e->getTraceAsString(),
                ]);
            } 
        }
            

    }
}
