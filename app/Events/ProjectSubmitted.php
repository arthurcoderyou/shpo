<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProjectSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public Project $project;
    public string $message;
    public $submission_type;


    /**
     * Create a new event instance.
     */
    public function __construct(Project $project, $submission_type = null)
    {
        $this->project = $project;
        $this->message = "Project '".$this->project->name."' submitted";

        if($submission_type == "submission"){
             $this->message = "Project '".$this->project->name."' submitted";
        }elseif($submission_type == "re-submission"){
             $this->message = "Project '".$this->project->name."' re-submitted";
        }

        $this->submission_type = $submission_type;
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
