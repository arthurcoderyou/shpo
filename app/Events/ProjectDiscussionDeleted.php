<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ProjectDiscussionDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $project;
    public $project_discussion;

    public $message;
    public $project_url;
    /**
     * Create a new event instance.
     */
    public function __construct($project,$project_discussion)
    {
        $this->project = $project;
        $this->project_discussion = $project_discussion;
        $this->message = "Discussion deleted on project '{$this->project->name}'";

        $this->project_url = route('project.show', $this->project->id);

        if(!empty($this->project_discussion->project_document_id)){
            $this->project_url = route('project.project_document', [
                'project' => $this->project->id,
                'project_document' => $this->project_discussion->project_document_id,
            ]);

            $this->message = "Discussion deleted on document '{$this->project_discussion->project_document->document_type->name}' for project '{$this->project->name}'";
        }


    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // return [
        //     new PrivateChannel('channel-name'),
        // ];
        // return [new Channel('project_discussions')];
        return [
            new PrivateChannel("project.discussions.global")
        ];

    }

    public function broadcastAs(){
        return "deleted";
    }


    public function broadcastWith(){
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'project_url' => $this->project_url,
            'is_private' => $this->project_discussion->is_private,
            'message' => $this->message,
            'timestamp' => $this->project_discussion->created_at->toDateTimeString(),
        ];
    }
}
