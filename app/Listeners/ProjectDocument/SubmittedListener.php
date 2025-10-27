<?php

namespace App\Listeners\ProjectDocument;

use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\ProjectDocument\Submitted;
use App\Mail\ProjectDocument\SubmittedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectDocument\SubmittedNotification;

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
        $project_document = ProjectDocument::find($event->project_document_id); 
        $project = Project::find($project_document->project_id);
        $user = User::find($event->authId);

        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($user->email)->queue(
                    new SubmittedMail($project, $project_document)
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch SubmittedMail mail: ' . $e->getMessage(), [
                    'project_document_id' => $project_document->id,
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        }

        // if notification is true 
        if($event->sendNotification){
              
            try { 
                Notification::send($user, new SubmittedNotification($project, $project_document));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch SubmittedNotification notification: ' . $e->getMessage(), [
                    'project_document_id' => $project_document->id,
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }



        } 
 
        // project log
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' => "Project document '".$project_document->document_type->name."' submitted by '".$user->name."'",
            'project_id' =>  $project_document->project_id, 
            'project_document_id' =>  $project_document->project_document_id,  
        ]);
    }
}
