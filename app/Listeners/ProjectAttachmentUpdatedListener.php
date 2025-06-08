<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ProjectAttachmentUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectAttachmentUpdatedListener
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
    public function handle(ProjectAttachmentUpdated $event): void
    {
        ActivityLog::create([
            'created_by' => $event->project_attachment->created_by,
            'log_username' => auth()->user()->name,
            'log_action' =>  $event->message,
            'project_id' =>  $event->project_attachment->project_document->project->id,
            'project_document_id' => $event->project_attachment->project_document->id,
            'project_document_attachment_id' => $event->project_attachment->id,
        ]);
    }
}
