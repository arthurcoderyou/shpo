<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ProjectQueued;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectQueuedListener
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
    public function handle(ProjectQueued $event): void
    {
        $project = $event->project;
  
        ActivityLog::create([
            'created_by' => $project->created_by,
            'log_username' => auth()->user()->name,
            'log_action' =>  $event->message,
            'project_id' => $project->id,
        ]);
    }
}
