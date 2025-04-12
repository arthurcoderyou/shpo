<?php

namespace App\Events;

use App\Models\Discussion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscussionCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $discussion;

    /**
     * Create a new event instance.
     */
    public function __construct(Discussion $discussion)
    {
        $this->discussion = $discussion; 
     
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
        return [new Channel('discussions')];


    }

    public function broadcastAs(){
        return "create";
    }


    public function broadcastWith(){
        return [
            'message' => "[{$this->discussion->created_at}] New Discussion received with title '{$this->discussion->title}'",
        ];
    }
}
