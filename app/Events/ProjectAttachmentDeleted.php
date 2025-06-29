<?php

namespace App\Events;

use App\Models\ProjectAttachments;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ProjectAttachmentDeleted implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public string $message;

    public $projectAttachmentId;
    public $authId;

    /**
     * Create a new event instance.
     */
    // public function __construct(ProjectAttachments $project_attachment, $authId)
    public function __construct( $projectAttachmentId, $authId)
    {
  
        $this->message = "Project attachment '".$projectAttachmentId."' deleted ";

        $this->projectAttachmentId = $projectAttachmentId;
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
            new PrivateChannel('project_attachment'),
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
