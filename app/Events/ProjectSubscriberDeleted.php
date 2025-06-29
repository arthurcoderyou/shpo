<?php

namespace App\Events;

use App\Models\ProjectSubscriber;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProjectSubscriberDeleted implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public string $message;
    public $projectSubscriberId;
    public $authId;


    /**
     * Create a new event instance.
     */
    public function __construct($projectSubscriberId, $authId)
    { 
        $this->projectSubscriberId = $projectSubscriberId;
        $this->authId = $authId;
 
        $this->message = "Project subscriber '".$projectSubscriberId."' deleted ";
        
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('project_subscriber'),
        ];
    }

    public function broadcastAs(){
        return "deleted";
    }


    public function broadcastWith(){
         
         
        return [
            'message' => $this->message, 
            'project_url' => route('project.index'), // show project list
        ];
    }
}
