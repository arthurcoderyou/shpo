<?php

namespace App\Listeners\Project;

use App\Events\Project\Created;
use App\Helpers\ActivityLogHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatedListener
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
    public function handle(Created $event): void
    { 
        $project = Project::find( $event->projectId);
        $authUser = User::find($event->authId); 
        ActivityLogHelper::logProjectActivity('created',$project->id,$authUser->id );
        

    }
}
