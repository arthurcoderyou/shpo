<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ProjectReviewerUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectReviewerUpdatedListener
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
        $project_reviewer = $event->project_reviewer;
 

        ActivityLog::create([
            'created_by' => $project_reviewer->created_by,
            'log_username' => auth()->user()->name,
            'log_action' => $event->message,
            'project_id' =>  $project_reviewer->project_id,   
            'project_document_id' => $event->project_reviewer->project_document_id ?? null, 
            'project_reviewer_id' => $event->project_reviewer->id ,
        ]);
    }
}
