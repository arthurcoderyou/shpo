<?php

namespace App\Listeners\ProjectSubscriber;

use App\Models\ProjectSubscriber;
use App\Models\User;
use App\Models\ActivityLog; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ProjectSubscriber\ProjectSubscriberLogEvent;

class ProjectSubscriberLogEventListener implements ShouldQueue
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
    public function handle(ProjectSubscriberLogEvent $event): void
    {
        $message = $event->message;
        $authUser = User::find($event->authId); 

        $projectSubscriber = ProjectSubscriber::find($event->projectSubscriberId); 
        $project = Project::find($event->projectId); 

        // $projectDocument = ProjectDocument::find($event->projectDocumentId); 
  


        ActivityLog::create([
            'created_by' => $authUser->id, // add it to the activiy logs of the person this is connected to 
            'log_username' => $authUser->name,
            'log_action' =>  $message ,
            'project_id' =>  $project->id,   
            'project_subscriber_id' => $projectSubscriber->id, 
            // 'project_document_id' => $projectDocument->id ?? null,
        ]);
    }
}
