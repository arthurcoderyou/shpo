<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotificationsDeleted implements ShouldBroadcast,ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public DatabaseNotification $databaseNotification;
    /** 
     * Create a new event instance.
     */
    public function __construct( DatabaseNotification $databaseNotification)
    {
        $this->databaseNotification = $databaseNotification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications'),
        ];
    }

    public function broadcastAs(){
        return "deleted";
    }


    public function broadcastWith(){
         
        return [ 
            'message' => 'Notification deleted',
        ];
    }

}
