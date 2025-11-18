<?php

namespace App\Events\ProjectDocument\Review;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReviewSubmitted implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $review_id;
    public int $notify_user_id;
    public int $authId;

    public $sendMail = true;
    public $sendNotification = true;


    /**
     * Create a new event instance.
     */
    public function __construct($review_id, $notify_user_id, $authId, $sendMail, $sendNotification)
    {
        $this->review_id = $review_id;
        $this->notify_user_id = $notify_user_id;  
        $this->authId = $authId; 
        $this->sendMail = $sendMail;
        $this->sendNotification = $sendNotification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('project-document'),
        ];
    }

    public function broadcastAs(){
        return "reviewed";
    }
    
    
}
