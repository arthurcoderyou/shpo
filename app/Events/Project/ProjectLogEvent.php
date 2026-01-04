<?php

namespace App\Events\Project;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProjectLogEvent   implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 

    public string $message; 
    public int $authId;
 
     public ?int $modelId; // nullable // the model instance record id 

   /**
     * Create a new event instance.
     */
    public function __construct(string $message, int $authId,  ?int $modelId = null)
    {
        $this->message = $message; 
        $this->authId = $authId; 
        $this->modelId = $modelId; 
    }
 

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {    
        // Always broadcast to the user-scoped project channel
        $channels = [
            new PrivateChannel('project'),
        ];

        // If modelId exists, also broadcast to a more specific channel
        if (!is_null($this->modelId)) { 
            // $channels = [];
            $channels[] = new PrivateChannel('project.'. $this->modelId);
        }

        return $channels;


    } 


    public function broadcastAs(){
        return "event";
    }


}
