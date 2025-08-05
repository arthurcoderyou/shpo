<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Permission;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PermissionUpdated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Permission $permission;
    public $message;
    public $authId;
    /**
     * Create a new event instance.
     */
    public function __construct(Permission $permission, $authId)
    {
        $this->permission = $permission;
        $this->message = "Permission '".$this->permission->name."' updated"; 
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
            new PrivateChannel('permissions'),
        ];
    }

    public function broadcastAs(){
        return "updated";
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
