<?php

namespace App\Events\ProjectReferences;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProjectReferencesLogEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

     public string $message; 
    public int $authId; 
    public int $projectId;
    /** OPTIONAL */
    // public ?int $projectDocumentId;

    

    /**
     * Create a new event instance.
     */
    public function __construct(string  $message,int $authId ,int  $projectId, 
    // ?int $projectDocumentId = null
    )
    {    
        $this->message = $message; 
        $this->authId = $authId;  
        $this->projectId = $projectId;
        // $this->projectDocumentId = $projectDocumentId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            // new PrivateChannel('project_reference'), // not needed as of the momment
            new PrivateChannel('project.project_reference'),
            new PrivateChannel('project.project_reference.' . $this->projectId),
        ];

        // // Only add document channels if a document is present
        // if (!is_null($this->projectDocumentId)) {
        //     $channels[] = new PrivateChannel('project.project_document.project_reference');
        //     $channels[] = new PrivateChannel('project.project_document.project_reference..' . $this->projectDocumentId);
        // }


        return $channels;
    }

    public function broadcastAs(){
        return "event";
    }
}
