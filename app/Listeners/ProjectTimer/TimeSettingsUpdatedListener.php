<?php

namespace App\Listeners\ProjectTimer;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ProjectTimer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ProjectTimer\TimeSettingsUpdated;
use App\Mail\ProjectTimer\TimeSettingsUpdatedMail;
use App\Helpers\ActivityLogHelpers\ProjectTimerLogHelper;

class TimeSettingsUpdatedListener  implements ShouldQueue
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
    public function handle(TimeSettingsUpdated $event): void
    {
        $projectTimer = ProjectTimer::find($event->projectTimerId);

        $targetUser = User::find($event->targetUserId); // user to be notified
        $targetUserEmailForRole = $event->targetUserEmailForRole;

        $authUser = User::find($event->authId); // authenticated 
        

        // get message 
        $authId = $event->authId; 

        // get the message from the helper 
        $message = ProjectTimerLogHelper::getActivityMessage('default', $authId);
 

        // get route 
        $viewUrl = ProjectTimerLogHelper::getRoute('default');

        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($targetUser->email)->queue(
                    new TimeSettingsUpdatedMail(
                        $projectTimer,
                        $targetUser,
                        $viewUrl,
                        $targetUserEmailForRole,
                        
                    )
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch TimeSettingsUpdatedMail mail: ' . $e->getMessage(), [ 
                    'projectTimer_id' => $projectTimer->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        }

        // // if notification is true 
        // if($event->sendNotification){
              
        //     try { 
        //         Notification::send($user, new SubmittedNotification($project));
        //     } catch (\Throwable $e) {
        //         // Log the error without interrupting the flow
        //         Log::error('Failed to dispatch SubmittedNotification notification: ' . $e->getMessage(), [ 
        //             'project_id' => $project->id,
        //             'trace' => $e->getTraceAsString(),
        //         ]);
        //     }



        // } 
 
 
        // project log
        // ActivityLog::create([
        //     'created_by' => $event->authId,
        //     'log_username' => $authUser->name,
        //     'log_action' =>  $message, 
        // ]);
    }
}
