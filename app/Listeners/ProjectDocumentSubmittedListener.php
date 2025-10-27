<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;
use Illuminate\Support\Facades\Mail;
use App\Events\ProjectDocumentSubmitted;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\ProjectDocumentSubmittedMail;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectDocumentSubmittedListener implements ShouldQueue
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
    public function handle(ProjectDocumentSubmitted $event): void
    { 

        $project_document = ProjectDocument::find($event->project_document_id); 
        $project = Project::find($project_document->project_id);
        $user = User::find($event->authId);

        Mail::to($user->email)->queue(
            new ProjectDocumentSubmittedMail($project, $project_document)
        );

 
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
