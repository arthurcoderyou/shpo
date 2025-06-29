<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Events\ProjectCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectCreatedListener  implements ShouldQueue
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
    public function handle(ProjectCreated $event): void
    {
        $project = Project::find( $event->projectId);
        $user = User::find($event->authId);


        ActivityLog::create([
            'created_by' => $project->created_by,
            'log_username' => $user->name,
            'log_action' =>  $event->message,
            'project_id' => $project->id,
        ]);
    }
}
