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

class ProjectDocumentUpdated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ProjectDocument $project_document;
    public string $message;
    public $projectDocumentId;
    public $authId;


    /**
     * Create a new event instance.
     */
    public function __construct(ProjectDocument $project_document, $authId)
    {
        $this->project_document = $project_document;
        $this->message = "Project document updated on '".$this->project_document->document_type->name."' for project '".$this->project_document->project->name."'";

        $this->projectDocumentId = $this->project_document->id;
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
        return "updated";
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
