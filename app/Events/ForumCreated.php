<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ForumCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $forum;
    /**
     * Create a new event instance.
     */
    public function __construct($forum)
    {
        $this->forum = $forum;
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
        return [new Channel('forums')];


    }

    public function broadcastAs(){
        return "create";
    }


    public function broadcastWith(){
        return [
            'message' => "[{$this->forum->created_at}] New Forum created with title '{$this->forum->title}'",
        ];
    }


}
