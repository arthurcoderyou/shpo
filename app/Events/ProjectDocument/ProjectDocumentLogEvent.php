<?php

namespace App\Events\ProjectDocument;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProjectDocumentLogEvent implements ShouldBroadcast, ShouldQueue

{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message; 
    public int $authId;

    public int $projectId;
    public int $projectDocumentId;

    

    /**
     * Create a new event instance.
     */
    public function __construct(string  $message,int $authId ,int  $projectId, int $projectDocumentId)
    {    
        $this->message = $message; 
        $this->authId = $authId; 
        $this->projectId = $projectId;
        $this->projectDocumentId = $projectDocumentId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Always broadcast to the user-scoped role channel
        $channels = [
            new PrivateChannel('project_document'),
        ];

        // broadcast to the project channel
        $channels[] = new PrivateChannel('project'); 


        // If  $projectId  exists, also broadcast to a more specific channel
        if (!is_null($this->projectId )) { 
            // $channels = [];
            $channels[] = new PrivateChannel('project.'. $this->projectId );
        }

        // If  $projectDocumentId  exists, also broadcast to a more specific channel
        if (!is_null($this->projectDocumentId )) { 
            // $channels = [];
            $channels[] = new PrivateChannel('project_document.'. $this->projectDocumentId );
        }


        return $channels;
    }

    public function broadcastAs(){
        return "event";
    }
}
