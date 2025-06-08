<?php

namespace App\Events;

use App\Models\Project;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ProjectDeleted implements ShouldBroadcastNow
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
        return "deleted";
    }


    public function broadcastWith(){
         
        return [
            'message' => "Project '".$this->project->name."' deleted", 
            'project_id' => $this->project->id,
            'project_url' => route('project.index'), // show project list
        ];
    }
}
