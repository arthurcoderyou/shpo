<?php

namespace App\Events\System;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SystemEvent implements ShouldBroadcast,ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public int $targetUserId;
    public string $message;
    public string $notification_type; // success, error, info, etc.
    public string $link;

    public ?bool $show_notification_popup = true;
    public ?bool $add_notification = true;
    
    public function __construct(
        int $targetUserId,
        string $message,
        string $notification_type = 'success', 
        string $link = '', 
        bool $show_notification_popup = true, 
        bool $add_notification = true
    )
    {
        $this->targetUserId = $targetUserId;
        $this->message      = $message;
        $this->notification_type         = $notification_type;
        $this->link         = $link;
        $this->show_notification_popup = $show_notification_popup;
        $this->add_notification = $add_notification;
    }

    public function broadcastOn()
    {
        // Only the user with $targetUserId can subscribe to this channel
        return new PrivateChannel('system.' . $this->targetUserId);
    }

    public function broadcastAs()
    {
        // Echo will listen to ".notify"
        return 'event';
    }


}
