<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ProjectSubscriber; 
use App\Events\ProjectSubscriberUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectSubscriberUpdatedListener  implements ShouldQueue
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
    public function handle(ProjectSubscriberUpdated $event): void
    {
        $project_subscriber = ProjectSubscriber::find($event->projectSubscriberId );
        $user = User::find($event->authId); 

        ActivityLog::create([
            'created_by' => $project_subscriber->created_by,
            'log_username' =>  $user->name,
            'log_action' =>  $event->message,
            'project_id' =>  $project_subscriber->project->id,
            'project_subscriber_id' => $project_subscriber->id, 
        ]);
    }
}
