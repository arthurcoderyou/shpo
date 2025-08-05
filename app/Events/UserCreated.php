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

class UserCreated  implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public $message;
    public $authId;
    /**
     * Create a new event instance.
     */
    public function __construct(User $user, $authId)
    {
        $this->user = $user;
        $this->message = "New user '".$this->user->name."' created"; 
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
            new PrivateChannel('users'),
        ];
    }

    public function broadcastAs(){
        return "created";
    }


    public function broadcastWith(){
         
        return [
            'message' => $this->message ,
                // ? 'Time settings updated by ' . $this->project_timer->updator->name . ' at ' . $this->project_timer->updated_at->toDateTimeString()
                // : 'Time settings updated by ' . $user->name . ' at ' . now()->toDateTimeString(),
            'user_url' => route('user.index'),
        ];
    }
}
