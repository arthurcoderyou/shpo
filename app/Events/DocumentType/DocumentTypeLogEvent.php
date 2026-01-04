<?php

namespace App\Events\DocumentType;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DocumentTypeLogEvent  implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message; 
    public int $authId;

    /**
     * Create a new event instance.
     */
    public function __construct($message,$authId )
    {
        $this->message = $message; 
        $this->authId = $authId; 
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {    
        // Always broadcast to the user-scoped document_type channel
        $channels = [
            new PrivateChannel('document_type'),
        ];
 
        return $channels;


    } 

    public function broadcastAs()
    {
        // Echo will listen to ".notify"
        return 'event';
    }
}
