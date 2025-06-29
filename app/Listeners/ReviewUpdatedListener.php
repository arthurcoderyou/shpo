<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Review;
use App\Models\ActivityLog;
use App\Events\ReviewUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewUpdatedListener  implements ShouldQueue
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
    public function handle(ReviewUpdated $event): void
    {

        $review = Review::find($event->reviewId);
        $user = User::find($event->authId);

        // project log
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' => "Project '".$review->project->name."' review updated by '".$review->reviewer->name."'",
            'project_id' =>  $review->project_id,   
        ]);


        // review log 
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' => $event->message,
            'project_id' =>  $review->project_id,   
            'project_document_id' => $review->project_document_id ?? null, 
            'project_reviewer_id' => $review->reviewer_id ,
            'project_review_id' => $review->id ,
        ]);
    }
}
