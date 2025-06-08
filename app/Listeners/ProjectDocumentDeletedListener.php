<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ProjectDocumentDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectDocumentDeletedListener
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
    public function handle(ProjectDocumentDeleted $event): void
    {
        ActivityLog::create([
            'created_by' => $event->project_document->created_by,
            'log_username' => auth()->user()->name,
            'log_action' =>  $event->message,
            'project_id' =>  $event->project_document->project->id,
            'project_document_id' => $event->project_document->id,
        ]);
    }
}
