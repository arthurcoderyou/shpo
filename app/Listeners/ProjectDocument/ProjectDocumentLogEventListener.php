<?php

namespace App\Listeners\ProjectDocument;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Project\ProjectBroadcastEvent;
use App\Events\ProjectDocument\ProjectDocumentLogEvent;
use App\Events\ProjectDocument\ProjectDocumentBroadcastEvent;

class ProjectDocumentLogEventListener
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
    public function handle(ProjectDocumentLogEvent $event): void
    {
        $message = $event->message;
        $authUser = User::find($event->authId); 

        $projectDocument = ProjectDocument::find($event->projectDocumentId); 

        // 🔄 Sync project status based on documents
        $projectDocument->syncProjectStatusFromDocuments(); 

   
        // project log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' => $message ,  
            'porject_id' =>  $projectDocument->project_id ?? null,
            'project_document_id' =>  $projectDocument->id ?? null,
        ]);
    }
}
