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
use Illuminate\Support\Facades\Notification;   
use App\Events\Project\Review\OpenReviewRequest;
use App\Mail\Project\Review\OpenReviewRequestMail;
use App\Notifications\Project\Review\OpenReviewRequestNotification;


class OpenReviewRequestListener  implements ShouldQueue
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
    public function handle(OpenReviewRequest $event): void
    {
        $project_reviewer = ProjectReviewer::find($event->project_reviewer_id);  
        $project = Project::find($project_reviewer->project_id); 
        $user = User::find($event->notify_user_id);

        $reviewUrl = "";
        // if($project->created_by == $user->id && $user->can('project list view')){

        //      $reviewUrl = route('project.index',[
        //         'review_status' => 'open_review',
        //         'pending_rc_number' => true,
        //      ]);
        // }elseif($user->can('project list view all') || $user->can('system access global admin')){
        //    $reviewUrl = route('project.index.all',[
        //      'review_status' => 'open_review',
        //         'pending_rc_number' => true,
        //    ]);
        // }elseif($user->can('project list view all no drafts')){
        //      $reviewUrl = route('project.index.all.no-drafts',[
        //          'review_status' => 'open_review',
        //         'pending_rc_number' => true,
        //     ]);
        // }else{
        //     $reviewUrl =  route('dashboard',[
        //          'review_status' => 'open_review',
        //         'pending_rc_number' => true,
        //     ]);
        // }   

        $reviewUrl = route('project.review',['project' => $project->id]);


        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($user->email)->queue(
                    new OpenReviewRequestMail( $project_reviewer, $project,$reviewUrl)
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch OpenReviewRequestMail mail: ' . $e->getMessage(), [
                    'project_reviewer_id' => $project_reviewer->id, 
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        }

        // // if notification is true 
        // if($event->sendNotification){
              
        //     try { 
        //         Notification::send($user, new OpenReviewRequestNotification($project_reviewer, $project));
        //     } catch (\Throwable $e) {
        //         // Log the error without interrupting the flow
        //         Log::error('Failed to dispatch OpenReviewRequestNotification notification: ' . $e->getMessage(), [ 
        //             'project_id' => $project->id,
        //             'trace' => $e->getTraceAsString(),
        //         ]);
        //     }



        // } 



        // project log
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' => "Open Review Request on '".$project->name."' ",
            'project_id' =>  $project->id,  
            'project_reviewer_id' =>  $project_reviewer->id, 
        ]);


    }
}
