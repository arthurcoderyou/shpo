<?php

namespace App\Events;

use App\Models\ProjectDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ProjectDocumentDeleted implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public string $message;

    public $projectDocumentId;
    public $authId;
    
    /**
     * Create a new event instance.
     */
    public function __construct( $projectDocumentId, $authId)
    {
        
        $this->message = "Project document '".$projectDocumentId."' deleted ";

        $this->projectDocumentId = $projectDocumentId;
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
            new PrivateChannel('project_document'),
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
