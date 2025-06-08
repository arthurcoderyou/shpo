<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ProjectDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectDeletedListener
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
    public function handle(ProjectDeleted $event): void
    {
        $project = $event->project;


        ActivityLog::create([
            'created_by' => $project->created_by,
            'log_username' => auth()->user()->name,
            'log_action' =>  "Project '".$project->name."' deleted",
            'project_id' => $project->id,
        ]);
    }
}
