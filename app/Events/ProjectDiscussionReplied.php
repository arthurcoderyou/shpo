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

class ProjectDiscussionReplied implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $project;
    public $project_discussion;
    public $reply;

    /**
     * Create a new event instance.
     */
    public function __construct($project, $project_discussion, $reply)
    {
        $this->project = $project;
        $this->project_discussion = $project_discussion;
        $this->reply = $reply;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("project.discussions.global")
        ];
    }

    /**
     * Custom broadcast name.
     */
    public function broadcastAs()
    {
        return "reply";
    }

    /**
     * Data to send with the broadcast.
     */
    public function broadcastWith()
    {
        return [
            'project_id' => $this->project->id,
            'project_name' => $this->project->name,
            'project_url' => route('project.show', $this->project->id),
            'discussion_id' => $this->project_discussion->id,
            'reply_id' => $this->reply->id,
            'reply_message' => $this->reply->message,
            'replied_by' => $this->reply->user->name ?? 'Someone',
            'is_private' => $this->project_discussion->is_private,
            'timestamp' => $this->reply->created_at->toDateTimeString(),
            'message' => "New reply added on project discussion '{$this->project_discussion->title}' in project '{$this->project->name}'",
        ];
    }
}
