<?php

namespace App\Events;

use App\Models\ProjectDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ProjectDocumentDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProjectDocument $project_document;
    public string $message;
    /**
     * Create a new event instance.
     */
    public function __construct(ProjectDocument $project_document)
    {
        $this->project_document = $project_document;
        $this->message = "Project document deleted on '".$this->project_document->document_type->name."' for project '".$this->project_document->project->name."'";
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
            'project_id' => $this->project_document->project->id,
            'project_url' => route('project.show',['project' => $this->project_document->project->id]),
        ];
    }
}
