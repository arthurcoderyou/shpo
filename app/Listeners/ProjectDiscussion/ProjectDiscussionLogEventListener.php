<?php

namespace App\Listeners\ProjectDiscussion;

use App\Models\ProjectDiscussion;
use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ProjectDiscussion\ProjectDiscussionLogEvent;

class ProjectDiscussionLogEventListener implements ShouldQueue

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
    public function handle(ProjectDiscussionLogEvent $event): void
    {

        $message = $event->message;
        $authUser = User::find($event->authId); 

        $projectDiscussion = ProjectDiscussion::find($event->projectDiscussionId); 
        $project = Project::find($event->projectId); 

        $projectDocument = ProjectDocument::find($event->projectDocumentId); 
  


        ActivityLog::create([
            'created_by' => $authUser->id, // add it to the activiy logs of the person this is connected to 
            'log_username' => $authUser->name,
            'log_action' =>  $message ,
            'project_id' =>  $project->id,   
            'project_discussion_id' => $projectDiscussion->id, 
            'project_document_id' => $projectDocument->id ?? null,
        ]);
    }
}
