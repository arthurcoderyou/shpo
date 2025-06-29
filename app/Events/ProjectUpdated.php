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

class ProjectUpdated  implements ShouldBroadcast 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Project $project; 
    public string $message;
    public $projectId;
    public $authId;
    /**
     * Create a new event instance.
     */
    public function __construct(Project $project,$authId )
    {
        $this->project = $project;
        $this->message = "Project '".$this->project->name."' updated";

        $this->projectId = $project->id;
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
        return "updated";
    }


    public function broadcastWith(){
         
        return [
            'message' => $this->message,
            'project_id' => $this->project->id,
            'project_url' => route('project.show',['project' => $this->project->id]),
        ];
    }
}
