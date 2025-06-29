<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ForumDeleted implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $forum;
    public $deletedBy;
    public $deletedAt;

    /**
     * Create a new event instance.
     */
    public function __construct($forum = null)
    {
        // If a forum is provided, it means a single forum is being deleted.
        // If null, it indicates a bulk delete, and we use the current date/time.
        $this->forum = $forum;
        $this->deletedBy = auth()->user() ? auth()->user()->name : 'System';  // Assuming you're using authentication.
        $this->deletedAt = now();  // Current date/time for both single and bulk delete.
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [new Channel('forums')];
    }

    /**
     * The event name that will be broadcasted.
     */
    public function broadcastAs()
    {
        return 'delete';
    }

    /**
     * The data that will be broadcasted with the event.
     */
    public function broadcastWith()
    {
        // If $forum is not null, we are dealing with a single delete.
        if ($this->forum !== null) {
            return [
                'message' => "[{$this->forum->created_at}] Forum deleted with title '{$this->forum->title}' by {$this->deletedBy} at {$this->deletedAt}",
            ];
        }

        // If $forum is null, we are dealing with a bulk delete.
        return [
            'message' => "Bulk delete operation performed by {$this->deletedBy} at {$this->deletedAt}",
        ];
    }


}
