<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Project;
use App\Events\Project\Submitted;
use App\Mail\Project\SubmittedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Project\SubmittedNotification;



class SubmittedListener implements ShouldQueue
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
    public function handle(Submitted $event): void
    { 
        $project = Project::find($event->project_id);
        $user = User::find($event->authId);

        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($user->email)->queue(
                    new SubmittedMail($project)
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch SubmittedMail mail: ' . $e->getMessage(), [ 
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        }

        // if notification is true 
        if($event->sendNotification){
              
            try { 
                Notification::send($user, new SubmittedNotification($project));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch SubmittedNotification notification: ' . $e->getMessage(), [ 
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }



        } 
 
        // project log
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' => "Project '".$project->name."' submitted by '".$user->name."'",
            'project_id' =>  $project->id,  
        ]);
    }
}
