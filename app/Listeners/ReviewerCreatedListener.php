<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\ReviewerCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewerCreatedListener  implements ShouldQueue
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
    public function handle(ReviewerCreated $event): void
    {

        $reviewer = $event->reviewer;

        if(!empty($reviewer->document_type)){
            $message =  "New Reviewer '".$reviewer->user->name."' added to the document type '".$reviewer->document_type->name."'";
        }else{
            if($reviewer->reviewer_type == "initial"){
                $message = "New Reviewer '".$reviewer->user->name."' added to the initial reviewers'";
            }elseif($reviewer->reviewer_type == "final"){
                $message = "New Reviewer '".$reviewer->user->name."' added to the final reviewers '";
            }
 
        }


        ActivityLog::create([
            'created_by' => $reviewer->created_by,
            'log_username' => auth()->user()->name,
            'log_action' => $message,
        ]);
    }
}
