<?php

namespace App\Events\Permission;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PermissionLogEvent  implements ShouldBroadcast, ShouldQueue
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
        // Always broadcast to the user-scoped permission channel
        $channels = [
            new PrivateChannel('permission'),
        ];

        // If modelId exists, also broadcast to a more specific channel
        if (!is_null($this->modelId)) { 
            // $channels = [];
            $channels[] = new PrivateChannel('permission.'. $this->modelId);
        }

        return $channels;


    } 
    public function broadcastAs()
    {
        // Echo will listen to ".notify"
        return 'event';
    }



}
