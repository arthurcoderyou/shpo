<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;
use App\Events\ProjectDocumentCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectDocumentCreatedListener  implements ShouldQueue
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
    public function handle(ProjectDocumentCreated $event): void
    {   
        $project_document = ProjectDocument::find($event->projectDocumentId);
        $user = User::find($event->authId);
         
        ActivityLog::create([
            'created_by' => $project_document->created_by,
            'log_username' =>$user->name,
            'log_action' =>  $event->message,
            'project_id' =>  $project_document->project->id,
            'project_document_id' => $project_document->id,
        ]);
    }
}
