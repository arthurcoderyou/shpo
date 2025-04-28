<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ReviewerUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewerUpdatedListener
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
    public function handle(ReviewerUpdated $event): void
    {
        $reviewer = $event->reviewer;


        ActivityLog::create([
            'created_by' => $reviewer->created_by,
            'log_username' => auth()->user()->name,
            'log_action' => "Reviewer '".$reviewer->user->name."' order updated to ".$reviewer->order." on the document type '".$reviewer->document_type->name."'",
        ]);
    }
}
