<?php

namespace App\Listeners\Project;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Project\ProjectRCNumberReviewed;
use App\Mail\Project\ProjectRCNumberReviewedMail;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Mail\Project\ProjectRCNumberReviewConfirmationMail;


class ProjectRCNumberReviewedListener  implements ShouldQueue
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
    public function handle(ProjectRCNumberReviewed $event): void
    {

        $review = Review::find($event->review_id);


        $project = Project::find($review->project_id);


        $submitter_user = User::find($project->created_by);
        

        // get message 
        $reviewer_auth_id = $event->authId;

        $reviewer_user = User::find($reviewer_auth_id);

        // get the message from the helper 
        $message = ProjectLogHelper::getActivityMessage('rc-reviewed', $project->id, $reviewer_auth_id);
          
        // dd($authId); 

        $viewUrl = ProjectLogHelper::getRoute('rc-reviewed', $project->id, $reviewer_auth_id);

        // if mail is true 
        if($event->sendMail){
            
            // mail for the reviewer to notify him about the successful submission of his review
            try {
                Mail::to($reviewer_user->email)->queue(
                    new ProjectRCNumberReviewConfirmationMail($review,$viewUrl)
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectRCNumberReviewedMail mail: ' . $e->getMessage(), [ 
                    'review_id' => $review->id,
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }



            // mail for the submitter of the project to notify about the project rc number review success || regardless that it can have status reviewed or changes requested
            try {
                Mail::to($submitter_user->email)->queue(
                    new ProjectRCNumberReviewedMail($review,$viewUrl)
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectRCNumberReviewConfirmationMail mail: ' . $e->getMessage(), [ 
                    'review_id' => $review->id,
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


            



        }

        // // if notification is true 
        // if($event->sendNotification){
              
        //     try { 
        //         Notification::send($user, new SubmittedNotification($project));
        //     } catch (\Throwable $e) {
        //         // Log the error without interrupting the flow
        //         Log::error('Failed to dispatch SubmittedNotification notification: ' . $e->getMessage(), [ 
        //             'project_id' => $project->id,
        //             'trace' => $e->getTraceAsString(),
        //         ]);
        //     }



        // } 


         


 
        // project log
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $reviewer_user->name,
            'log_action' =>  $message,
            'project_id' =>  $project->id,  
        ]);
    }
}
