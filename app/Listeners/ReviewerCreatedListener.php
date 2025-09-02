<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Reviewer;
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

        $reviewer = Reviewer::find($event->reviewer_id);
        $auth_user = User::find($event->authId);

        if(!empty($reviewer->document_type)){


            if(!empty($reviewer->user_id)){

                $message =  "New Reviewer '".$reviewer->user->name."' added to the document type '".$reviewer->document_type->name."'";
            }else{
                // open review
                $message =  "New Reviewer 'Open Review' added to the document type '".$reviewer->document_type->name."'";

            }

                
        }else{
            if($reviewer->reviewer_type == "initial"){
                if(!empty($reviewer->user_id)){

                    $message = "New Reviewer '".$reviewer->user->name."' added to the initial reviewers";
                }else{
                    // open review
                    $message =  "New Reviewer 'Open Review' added to the initial reviewers";

                }

                
            }elseif($reviewer->reviewer_type == "final"){

                if(!empty($reviewer->user_id)){

                    $message = "New Reviewer '".$reviewer->user->name."' added to the final reviewers ";
                }else{
                    // open review
                    $message = "New Reviewer 'Open Review' added to the final reviewers ";

                }

                
            }
 
        }


        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $auth_user->name,
            'log_action' => $message,
        ]);
    }
}
