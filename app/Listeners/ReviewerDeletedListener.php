<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ReviewerDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewerDeletedListener
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
    public function handle(ReviewerDeleted $event): void
    {
        $reviewer = $event->reviewer;


        ActivityLog::create([
            'created_by' => $reviewer->created_by,
            'log_username' => auth()->user()->name,
            'log_action' => "Reviewer '".$reviewer ->name."' deleted from the document type '".$reviewer ->document_type->name."'",
        ]);
    }
}
