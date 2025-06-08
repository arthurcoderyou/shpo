<?php

namespace App\Events;

use App\Models\ProjectDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectDocumentCreated implements ShouldBroadcastNow
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
        $this->message = "New project document added on '".$this->project_document->document_type->name."' for project '".$this->project_document->project->name."'";
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
        return "created";
    }


    public function broadcastWith(){
         
        


        return [
            'message' => $this->message,
            'project_id' => $this->project_document->project->id,
            'project_url' => route('project.project_document',[
                'project' => $this->project_document->project->id,
                'project_document' => $this->project_document->id,
            ]),
        ];
    }
}
