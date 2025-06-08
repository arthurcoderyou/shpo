<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ProjectUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectUpdatedListener
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
    public function handle(ProjectUpdated $event): void
    {
        $project = $event->project;


        ActivityLog::create([
            'created_by' => $project->created_by,
            'log_username' => auth()->user()->name,
            'log_action' =>  "Project '".$project->name."' updated",
            'project_id' => $project->id,
        ]);
    }
}
