<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ProjectSubmitted implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public Project $project;
    public string $message;
    public $submission_type;

    public $projectId;
    public $authId;


    /**
     * Create a new event instance.
     */
    public function __construct(Project $project, $submission_type = null, $authId)
    {
        $this->project = $project;
        $this->message = "Project '".$this->project->name."' submitted";

        if($submission_type == "submission"){
             $this->message = "Project '".$this->project->name."' submitted";
        }elseif($submission_type == "re-submission"){
             $this->message = "Project '".$this->project->name."' re-submitted";
        }

        $this->submission_type = $submission_type;


        $this->projectId = $this->project->id;
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
            new PrivateChannel('project'),
        ];
    }

    public function broadcastAs(){
        return "submitted";
    }


    public function broadcastWith(){
         
        return [
            'message' =>  $this->message, 
            'project_id' => $this->project->id,
            'project_url' => route('project.show',['project' => $this->project->id]),
        ];
    }




}
