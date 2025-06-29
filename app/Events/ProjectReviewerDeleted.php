<?php

namespace App\Events;

use App\Models\ProjectReviewer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ProjectReviewerDeleted implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public string $message;

    public $projectReviewerId;
    public $authId;

    /**
     * Create a new event instance.
     */
    public function __construct( $projectReviewerId, $authId)
    {
         

        $project_reviewer = ProjectReviewer::find($projectReviewerId);

        // if(!empty($project_reviewer->document_type)){
        //     $this->message =  "Reviewer '".$project_reviewer->user->name."' added to the document type '".$project_reviewer->project_document->document_type->name."'";
        // }else{
        //     if($project_reviewer->reviewer_type == "initial"){
        //         $this->message = "New Reviewer '".$project_reviewer->user->name."' added to the initial reviewers'";
        //     }elseif($project_reviewer->reviewer_type == "final"){
        //         $this->message = "New Reviewer '".$project_reviewer->user->name."' added to the final reviewers '";
        //     }
 
        // }
 
        $this->message  =  "Project Reviewer '".$project_reviewer->id."' deleted "; 
        
        $this->projectReviewerId = $projectReviewerId;
        $this->authId = $authId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('project_reviewer'),
        ];
    }

    public function broadcastAs(){
        return "deleted";
    }


    public function broadcastWith(){
         

        
 
        return [
            'message' => $this->message, 
            'reviewer_url' => route('project.index'),
        ];
    }
}
