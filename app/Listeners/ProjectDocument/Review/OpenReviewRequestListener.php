<?php

namespace App\Listeners\ProjectDocument\Review;

use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;

use App\Models\ProjectReviewer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Livewire\Admin\Project\ProjectReview;
use Illuminate\Support\Facades\Notification; 
use App\Events\ProjectDocument\Review\ReviewRequest;
use App\Mail\ProjectDocument\Review\ReviewRequestMail;
use App\Events\ProjectDocument\Review\OpenReviewRequest;
use App\Mail\ProjectDocument\Review\OpenReviewRequestMail;
use App\Notifications\ProjectDocument\SubmittedNotification;
use App\Notifications\ProjectDocument\Review\ReviewRequestNotification;
use App\Notifications\ProjectDocument\Review\OpenReviewRequestNotification;


class OpenReviewRequestListener implements ShouldQueue
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
        $project_document = ProjectDocument::find($project_reviewer->project_document_id); 
        $project = Project::find($project_reviewer->project_id); 
        $user = User::find($event->notify_user_id);

        $reviewUrl = route('project-document.index',[
            'review_status' => 'open_review',
        ]);

        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($user->email)->queue(
                    new OpenReviewRequestMail( $project_reviewer, $project, $project_document,$reviewUrl)
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch OpenReviewRequestMail mail: ' . $e->getMessage(), [
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
                Notification::send($user, new OpenReviewRequestNotification($project_reviewer, $project, $project_document));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch OpenReviewRequestNotification notification: ' . $e->getMessage(), [
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
            'log_action' => "Open Review Request on '".$project_document->document_type->name."' on '".$project->name."' ",
            'project_id' =>  $project_document->project_id, 
            'project_document_id' =>  $project_document->project_document_id, 
            'project_reviewer_id' =>  $project_reviewer->id, 
        ]);


    }
}
