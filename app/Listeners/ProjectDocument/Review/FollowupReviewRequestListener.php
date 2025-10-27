<?php

namespace App\Listeners\ProjectDocument\Review;

use App\Mail\ProjectDocument\Review\FollowupReviewRequestMail;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use App\Notifications\ProjectDocument\Review\FollowupReviewRequestNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ProjectDocument\Review\FollowupReviewRequest;

 
use App\Models\ActivityLog; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Notification;  



class FollowupReviewRequestListener  implements ShouldQueue
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
        $project_reviewer = ProjectReviewer::find($event->project_reviewer_id); 
        $project_document = ProjectDocument::find($project_reviewer->project_document_id); 
        $project = Project::find($project_reviewer->project_id);
        $user = User::find($project_reviewer->user_id);

        $reviewUrl = route('project-document.index',[
            'review_status' => 'pending'
        ]);

        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($user->email)->queue(
                    new FollowupReviewRequestMail( $project_reviewer, $project, $project_document,$reviewUrl)
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch FollowupReviewRequestMail mail: ' . $e->getMessage(), [
                    'project_reviewer_id' => $project_reviewer->id,
                    'project_document_id' => $project_document->id,
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        }

        // if notification is true 
        if($event->sendNotification){
              
            try { 
                Notification::send($user, new FollowupReviewRequestNotification($project_reviewer, $project, $project_document));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch FollowupReviewRequestNotification notification: ' . $e->getMessage(), [
                    'project_document_id' => $project_document->id,
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }



        } 
 
        // project log
        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' => "Review Request on '".$project_document->document_type->name."' on '".$project->name."' ",
            'project_id' =>  $project_document->project_id, 
            'project_document_id' =>  $project_document->project_document_id, 
            'project_reviewer_id' =>  $project_reviewer->id, 
        ]);
    }
}
