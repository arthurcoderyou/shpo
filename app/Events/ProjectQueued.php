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

class ProjectQueued implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Project $project;
    public string $message;


    /**
     * Create a new event instance.
     */
    public function __construct(Project $project, $message)
    {
        $this->project = $project;
        $this->message = $message;
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
        return "queued";
    }


    public function broadcastWith(){
         
        return [
            'message' =>  $this->message, 
            'project_id' => $this->project->id,
            'project_url' => route('project.show',['project' => $this->project->id]),
        ];
    }
}
