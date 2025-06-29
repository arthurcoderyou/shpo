<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ProjectDiscussionEdited;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectDiscussionEditedListener  implements ShouldQueue
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
    public function handle(ProjectDiscussionEdited $event): void
    {
        ActivityLog::create([
            'created_by' => $event->project_discussion->created_by,
            'log_username' => auth()->user()->name,
            'log_action' =>  $event->message,
            'project_id' =>  $event->project->id,
            'project_discussion_id' => $event->project_discussion->id, 
            'project_document_id' => $event->project_discussion->project_document_id ?? null,
        ]);
    }
}
