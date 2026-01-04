<?php

namespace App\Listeners\Project;

use App\Events\Project\Reviewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Events\Project\Submitted;
use App\Mail\Project\ReviewedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Notification; 
use App\Notifications\Project\SubmittedNotification;


class ReviewedListener  implements ShouldQueue
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
    public function handle(Reviewed $event): void
    {
        $project = Project::find($event->project_id);


        $user = User::find($project->created_by);
        

        // get message 
        $authId = $event->authId;

        // get the message from the helper 
        $message = ProjectLogHelper::getActivityMessage('rc-reviewed', $project->id, $authId);
         
        
        // dd($authId);

 

        $viewUrl = ProjectLogHelper::getRoute('rc-reviewed', $project->id, $authId);

        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($user->email)->queue(
                    new ReviewedMail($project,$viewUrl)
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ReviewedMail mail: ' . $e->getMessage(), [ 
                    'project_id' => $project->id,
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
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' =>  $message,
            'project_id' =>  $project->id,  
        ]);
    }
}
