<?php

namespace App\Events\ProjectDocument\ReReview;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReReviewRequest implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $re_review_request_id; 
    public int $review_id;

    public int $notify_user_id;
    public int $authId;

    public $sendMail = true;
    public $sendNotification = true;


    /**
     * Create a new event instance.
     */
    public function __construct($re_review_request_id, $review_id,$notify_user_id, $authId, $sendMail, $sendNotification)
    {
        $this->re_review_request_id = $re_review_request_id; 
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
        return "re_review_request";
    }


 


}
