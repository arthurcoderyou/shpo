<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ProjectAttachments;
use App\Events\ProjectAttachmentCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectAttachmentCreatedListener  implements ShouldQueue
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
    public function handle(ProjectAttachmentCreated $event): void
    {

        $project_attachment = ProjectAttachments::find($event->projectAttachmentId );
        $user = User::find($event->authId); 

        ActivityLog::create([
            'created_by' => $project_attachment->created_by,
            'log_username' =>  $user->name,
            'log_action' =>  $event->message,
            'project_id' =>  $project_attachment->project_document->project->id,
            'project_document_id' => $project_attachment->project_document->id,
            'project_document_attachment_id' => $project_attachment->id,
        ]);
    }
}
