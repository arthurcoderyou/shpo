<?php

namespace App\Listeners\ProjectDocument\ProjectReviewer;

use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification; 
use App\Events\ProjectDocument\ProjectReviewer\Updated;
use App\Mail\ProjectDocument\ProjectReviewer\UpdatedMail;
use App\Notifications\ProjectDocument\ProjectReviewer\UpdatedNotification;

class UpdatedListener implements ShouldQueue
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
    public function handle(Updated $event): void
    { 
        $project_document = ProjectDocument::find($event->project_document_id); 
        $project = Project::find($project_document->project_id);
        $user = User::find($event->notify_user_id);

        $viewUrl = route('project.project-document.show',['project' => $project->id, 'project_document' => $project_document->id]);

        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($user->email)->queue(
                    new UpdatedMail( $user, $project, $project_document,$viewUrl)
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch UpdatedMail mail: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'project_document_id' => $project_document->id,
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        }

        // if notification is true 
        if($event->sendNotification){
              
            try { 
                Notification::send($user, new UpdatedNotification($user, $project, $project_document));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch UpdatedNotification notification: ' . $e->getMessage(), [
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
            'log_action' => "Review Request on '".$project_document->document_type->name."' on '".$project->name."' ",
            'project_id' =>  $project_document->project_id, 
            'project_document_id' =>  $project_document->project_document_id, 
            // 'user_id' =>  $user_id->id, 
        ]);
    }
}
