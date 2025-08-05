<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Models\ProjectReviewer;
use App\Events\ProjectSubmitted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;

class ProjectSubmittedListener  implements ShouldQueue
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
    public function handle(ProjectSubmitted $event): void
    {


 
        $project = Project::find($event->projectId);
        $user = User::find($event->authId);

        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' =>   $event->message,
            'project_id' => $project->id,
        ]);
    }
}
