<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\ActivityLog;
use App\Events\ReviewerListUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\ReviewerListUpdatedDB;
use Illuminate\Support\Facades\Notification;

class ReviewerListUpdatedListener implements ShouldQueue
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
    public function handle(ReviewerListUpdated $event): void
    { 
        $auth_user = User::find($event->authId);

        ActivityLog::create([
            'created_by' => $auth_user->id,
            'log_username' => $auth_user->name,
            'log_action' => $event->message,
        ]);


        // website notification for every user that has permissions
         $targetPermissions = [
            'reviewer list view', 
            'system access global admin',
        ];

        $users = User::permission($targetPermissions)->get();

        foreach ($users as $user) {
             

            try {
                Notification::send($user, new ReviewerListUpdatedDB($event->authId, $event->message));
            } catch (\Throwable $e) {
                Log::error('Failed to send ReviewerListUpdatedDB notification: ' . $e->getMessage(), [ 
                    'user_id' => $user->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        }


    }
}
