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
    /**
     * Create a new event instance.
     */
    public function __construct($project,$project_discussion)
    {
        $this->project = $project;
        $this->project_discussion = $project_discussion;
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
            'project_url' => route('project.show', $this->project->id),
            'is_private' => $this->project_discussion->is_private,
            'message' => "Discussion deleted on project '{$this->project->name}'",
            'timestamp' => $this->project_discussion->created_at->toDateTimeString(),
        ];
    }
}
