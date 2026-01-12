<?php

namespace App\Listeners\ProjectReviewer;

use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ProjectReviewer\ProjectReviewerLogEvent;

class ProjectReviewerLogEventListener implements ShouldQueue
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
    public function handle(ProjectReviewerLogEvent $event): void
    {
        $message = $event->message;
        $authUser = User::find($event->authId); 

        $projectReviewer = ProjectReviewer::find($event->projectReviewerId); 
        $project = Project::find($event->projectId); 

        $projectDocument = ProjectDocument::find($event->projectDocumentId); 
 
        // project log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' => $message , 
            'project_reviewer_id' => $projectReviewer->id, 
            'project_id' =>  $project->id ?? null,
            'project_document_id' =>  $projectDocument->id ?? null,
        ]); 

    }
}
