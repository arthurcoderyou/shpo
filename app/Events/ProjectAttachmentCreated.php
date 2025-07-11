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

class ProjectAttachmentCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProjectAttachments $project_attachment;
    public string $message;
    public $projectAttachmentId;
    public $authId;


    /**
     * Create a new event instance.
     */
    public function __construct(ProjectAttachments $project_attachment,$authId)
    {
        $this->project_attachment = $project_attachment;
        $this->projectAttachmentId = $this->project_attachment->id;
        $this->authId = $authId;

        $this->message = "New project attachment '".$this->project_attachment->attachment."' added on '".$this->project_attachment->project_document->document_type->name."' for project '".$this->project_attachment->project_document->project->name."'";
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
        return "created";
    }


    public function broadcastWith(){
         
         
        return [
            'message' => $this->message,
            'project_id' => $this->project_attachment->project_document->project->id,
            'project_url' => route('project.show',['project' => $this->project_attachment->project_document->project->id]),
        ];
    }
}
