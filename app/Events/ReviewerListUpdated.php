<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReviewerListUpdated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public $authId;
    public $message;
    /**
     * Create a new event instance.
     */
    public function __construct($authId)
    { 
        $this->authId = $authId;
        $auth_user = User::find($this->authId);

        $this->message =  "Reviewer list updated by ".$auth_user->name;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('reviewer'),
        ];
    }

    public function broadcastAs(){
        return "updated";
    }


    public function broadcastWith(){
        
        
        return [
            'message' => $this->message, 
            'reviewer_url' => route('reviewer.index'),
        ];
    }
}
