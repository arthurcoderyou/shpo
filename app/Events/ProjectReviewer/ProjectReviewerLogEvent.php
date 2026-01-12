<?php

namespace App\Events\ProjectReviewer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProjectReviewerLogEvent  implements ShouldBroadcast, ShouldQueue 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message; 
    public int $authId;
    public int $projectReviewerId;
    public int $projectId;
    /** OPTIONAL */
    public ?int $projectDocumentId;

    

    /**
     * Create a new event instance.
     */
    public function __construct(string  $message,int $authId ,int  $projectReviewerId,int  $projectId, ?int $projectDocumentId = null)
    {    
        $this->message = $message; 
        $this->authId = $authId; 
        $this->projectReviewerId = $projectReviewerId;
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
        $channels = [
            // new PrivateChannel('project_reviewer'), // not needed as of the momment
            new PrivateChannel('project'),
            new PrivateChannel('project.' . $this->projectId),
        ];

        // Only add document channels if a document is present
        if (!is_null($this->projectDocumentId)) {
            $channels[] = new PrivateChannel('project_document');
            $channels[] = new PrivateChannel('project_document.' . $this->projectDocumentId);
        }


        return $channels;
    }

    public function broadcastAs(){
        return "event";
    }
}
