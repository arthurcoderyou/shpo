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

class ProjectCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Project $project;
    /**
     * Create a new event instance.
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
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
        return "created";
    }


    public function broadcastWith(){
         
        return [
            'message' => "New project '".$this->project->name."' created", 
            'project_id' => $this->project->id,
            'project_url' => route('project.show',['project' => $this->project->id]),
        ];
    }
}
