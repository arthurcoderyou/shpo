<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ProjectReviewer;
use App\Events\ProjectReviewerUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectReviewerUpdatedListener  implements ShouldQueue
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
    public function handle(ProjectReviewerUpdated $event): void
    {
        $project_reviewer = ProjectReviewer::find($event->projectReviewerId) ;
        $user = User::find($event->authId );
        

        ActivityLog::create([
            'created_by' => $project_reviewer->created_by,
            'log_username' => $user->name,
            'log_action' => $event->message,
            'project_id' =>  $project_reviewer->project_id,   
            'project_document_id' => $project_reviewer->project_document_id ?? null, 
            'project_reviewer_id' => $project_reviewer->id ,
        ]);
    }
}
