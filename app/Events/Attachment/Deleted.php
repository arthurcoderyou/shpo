<?php

namespace App\Events\Attachment;

use App\Models\Attachments;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Deleted implements ShouldBroadcast, ShouldQueue 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;
    public $authId;

    /**
     * Create a new event instance.
     */
    public function __construct($authId)
    {
        

        $this->message = "Attachment Deleted";
 
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
            new PrivateChannel('attachments'),
        ];
    }

    public function broadcastAs(){
        return "deleted";
    }


    public function broadcastWith(){
         
        return [
            'message' =>  $this->message, 
            'auth_id' => $this->authId,
            'url' => route('test.attachment.create'),
        ];
    }




}
