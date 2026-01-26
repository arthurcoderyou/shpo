<?php

namespace App\Listeners\Project\Review;

use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Models\ProjectReviewer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Project\Review\FollowupReviewRequest;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Mail\Project\Review\FollowupReviewRequestMail;
use App\Notifications\ProjectDocument\Review\FollowupReviewRequestNotification;

class FollowupReviewRequestListener
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
    public function handle(FollowupReviewRequest $event): void
    {
        $project_reviewer = ProjectReviewer::where('id',$event->project_reviewer_id)->first();  
        $project = Project::find($project_reviewer->project_id);
        $user = User::find($project_reviewer->user_id);


        


        $reviewUrl = route('project.review',['project' => $project->id]);

        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($user->email)->queue(
                    new FollowupReviewRequestMail( $project_reviewer, $project,$reviewUrl)
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch FollowupReviewRequestMail mail: ' . $e->getMessage(), [
                    'project_reviewer_id' => $project_reviewer->id ?? null, 
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        }

        // // if notification is true 
        // if($event->sendNotification){
              
        //     try { 
        //         Notification::send($user, new FollowupReviewRequestNotification($project_reviewer, $project, $project_document));
        //     } catch (\Throwable $e) {
        //         // Log the error without interrupting the flow
        //         Log::error('Failed to dispatch FollowupReviewRequestNotification notification: ' . $e->getMessage(), [
        //             'project_document_id' => $project_document->id,
        //             'project_id' => $project->id,
        //             'trace' => $e->getTraceAsString(),
        //         ]);
        //     }



        // } 
 
        // project log
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' => "Review Request on '".$project->name."' ",
            'project_id' =>  $project->id,  
            'project_reviewer_id' =>  $project_reviewer->id, 
        ]);
    }
}
