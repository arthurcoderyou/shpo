<?php

namespace App\Events;

use Spatie\Permission\Models\Role;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoleCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Role $role;
    public $message;
    public $authId;
    /**
     * Create a new event instance.
     */
    public function __construct(Role $role, $authId)
    {
        $this->role = $role;
        $this->message = "New role '".$this->role->name."' created"; 
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
            new PrivateChannel('roles'),
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
            // 'role_url' => route('user.index'),
        ];
    }
}
