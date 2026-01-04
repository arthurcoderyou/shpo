<?php

namespace App\Listeners\Project;

use App\Events\Permission\PermissionLogEvent;
use App\Events\Project\ProjectBroadcastEvent;
use App\Events\Project\ProjectLogEvent;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProjectLogEventListener implements ShouldQueue
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
    public function handle(ProjectLogEvent $event): void
    {
        $message = $event->message;
        $authUser = User::find($event->authId); 
        $project = Project::find($event->modelId); 
        $project_id = $project->id ?? null;



        // event(new ProjectBroadcastEvent());

   
        // project log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' => $message ,  

            'project_id' => $project_id ,
        ]);
    }
}
