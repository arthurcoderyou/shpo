<?php

namespace App\Events\Attachment;

use App\Models\Attachments;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
 
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Created implements ShouldBroadcast, ShouldQueue 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 

    public Attachments $attachment;

    public string $message;
    public $attachment_id;
    public $authId;

    /**
     * Create a new event instance.
     */
    public function __construct(Attachments $attachment, $authId)
    {
        $this->attachment = $attachment;

        $this->message = "New attachment added";

        $this->attachment_id = $attachment->id;
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
        return "created";
    }


    public function broadcastWith(){
         
        return [
            'message' =>  $this->message,
            'attachment_id' => $this->attachment->id,
            'auth_id' => $this->authId,
            'url' => route('test.attachment.create'),
        ];
    }
}
