<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ProjectCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectCreatedListener
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
        $project = $event->project;


        ActivityLog::create([
            'created_by' => $project->created_by,
            'log_username' => auth()->user()->name,
            'log_action' =>  "New project '".$project->name."' created",
            'project_id' => $project->id,
        ]);
    }
}
