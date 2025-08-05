<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationsUpdated implements ShouldBroadcast,ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DatabaseNotification $databaseNotification;
    public $authId;
    /** 
     * Create a new event instance.
     */
    public function __construct( DatabaseNotification $databaseNotification,$authId)
    {
        $this->databaseNotification = $databaseNotification;
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
            new PrivateChannel('notifications' ),
        ];
    }

    public function broadcastAs(){
        return "updated";
    }


    public function broadcastWith(){
         
        return [ 
            'message' => 'Notification updated',
        ];
    }
}
