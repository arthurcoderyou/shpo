<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Events\ReviewerUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewerUpdatedListener  implements ShouldQueue
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
        
        $reviewer = Reviewer::find($event->reviewer_id) ;

        $auth_user = User::find($event->authId);

        if(!empty($reviewer->document_type)){
            $message =  "Reviewer '".$reviewer->user->name."' order updated to ".$reviewer->order." on the document type '".$reviewer->document_type->name."'";
        }else{
            if($reviewer->reviewer_type == "initial"){
                $message = "Reviewer '".$reviewer->user->name."' order updated to ".$reviewer->order." on the initial reviewers";
            }elseif($reviewer->reviewer_type == "final"){
                $message = "Reviewer '".$reviewer->user->name."' order updated to ".$reviewer->order." on the final reviewers";
            }
 
        }


        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $auth_user->name,
            'log_action' => $message,
        ]);
    }
}
