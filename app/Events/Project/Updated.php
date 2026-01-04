<?php

namespace App\Events\Project;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Updated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $projectId;
    public int $authId; 

    /**
     * Create a new event instance.
     */
    public function __construct($projectId,$authId )
    {
        $this->projectId = $projectId;
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
            new PrivateChannel('project'.$this->projectId),
        ];
    }

    public function broadcastAs(){
        return "updated";
    }


}
