<?php

namespace App\Events\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewUserVerificationRequest  implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public int $userIdToNotify;

    public $sendMail = true; 


    /**
     * Create a new event instance.
     */
    public function __construct($userId,$userIdToNotify, $sendMail)
    {
        $this->userId = $userId;
        $this->userIdToNotify = $userIdToNotify;
        $this->sendMail = $sendMail; 
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            //  return new PrivateChannel('system.' . $this->targetUserId);
            new PrivateChannel('user'. $this->userIdToNotify),
        ];
    }
}
