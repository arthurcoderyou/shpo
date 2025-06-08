<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ReviewCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewCreatedListener
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
    public function handle(ReviewCreated $event): void
    {
        $review = $event->review;
        ActivityLog::create([
            'created_by' => $review->created_by,
            'log_username' => auth()->user()->name,
            'log_action' => "Project '".$review->project->name."' reviewed by '".$review->reviewer->name."'",
            'project_id' =>  $review->project_id,   
        ]);

        ActivityLog::create([
            'created_by' => $review->created_by,
            'log_username' => auth()->user()->name,
            'log_action' => $event->message,
            'project_id' =>  $review->project_id,   
            'project_document_id' => $review->project_document_id ?? null, 
            'project_reviewer_id' => $review->reviewer_id ,
            'project_review_id' => $review->id ,
        ]);
    }
}
