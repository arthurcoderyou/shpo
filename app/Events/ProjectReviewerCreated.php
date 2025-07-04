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

class ProjectReviewerCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProjectReviewer $project_reviewer;
    public string $message;

    public $projectReviewerId;
    public $authId;

    /**
     * Create a new event instance.
     */
    public function __construct(ProjectReviewer $project_reviewer, $authId)
    {
        $this->project_reviewer = $project_reviewer;

        $project_reviewer = $this->project_reviewer;

        if(!empty($project_reviewer->project_document_id)){
            $this->message =  "New Project Reviewer '".$project_reviewer->user->name."' added to the document type '".$project_reviewer->project_document->document_type->name."' for project '".$project_reviewer->project->name."'";
        }else{
            if($project_reviewer->reviewer_type == "initial"){
                $this->message = "New Project Reviewer '".$project_reviewer->user->name."' added to the initial reviewers for project '".$project_reviewer->project->name."'";
            }elseif($project_reviewer->reviewer_type == "final"){
                $this->message = "New Project Reviewer '".$project_reviewer->user->name."' added to the final reviewers for project '".$project_reviewer->project->name."'";
            }
 
        }

        $this->projectReviewerId =$project_reviewer->id;
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
        return "created";
    }


    public function broadcastWith(){
         

        
 
        return [
            'message' => $this->message, 
            'reviewer_url' => route('project.reviewer.index',[ 
                'project' => $this->project_reviewer->project->id,
                'project_document_id' => $this->project_reviewer->project_document_id,
                'reviewer_type' => $this->project_reviewer->reviewer_type

            ]),
        ];
    }



}
