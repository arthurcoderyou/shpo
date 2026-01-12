<?php

namespace App\Listeners\ProjectReferences;

use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;
use App\Models\ProjectReferences;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ProjectReferences\ProjectReferencesLogEvent;

class ProjectReferencesLogEventListener
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
    public function handle(ProjectReferencesLogEvent $event): void
    {
        $message = $event->message;
        $authUser = User::find($event->authId); 
 
        $project = Project::find($event->projectId); 

        // $projectDocument = ProjectDocument::find($event->projectDocumentId); 
  


        ActivityLog::create([
            'created_by' => $authUser->id, // add it to the activiy logs of the person this is connected to 
            'log_username' => $authUser->name,
            'log_action' =>  $message ,
            'project_id' =>  $project->id,    
            // 'project_document_id' => $projectDocument->id ?? null,
        ]);
    }
}
