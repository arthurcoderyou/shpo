<?php

namespace App\Events\Attachment;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Copied implements ShouldBroadcast, ShouldQueue 
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ?User $user;
    public string $type;
    public string $message;
    public array $meta;

    /**
     * @param  User    $user   Target user 
     * @param  string $type     e.g. 'success' | 'error' | 'info'
     * @param  string $message  Notification text
     * @param  array  $meta     Any extra data you want to send
     */
    public function __construct(
        int $authId,
        string $type = 'success',
        string $message = 'Attachment created.',
        array $meta = []
    ) {
       
       
       
        // $this->authId  = $authId;
        $this->user = User::find($authId);
 
        $this->type    = $type;  
        $this->message =  "Attachment copied successfully"; 
        $this->meta    = $meta;
    }

    /** Limit broadcast to a private user-scoped channel */
    public function broadcastOn(): array
    {
        return [ new PrivateChannel("attachment.{$this->user->id}") ];
    }

    /** Optional: custom event name on the frontend */
    public function broadcastAs(): string
    {
        return 'attachment.copied';
    }

    /** Data sent to the client */
    public function broadcastWith(): array
    {
        return [
            'type'    => $this->type,
            'message' => $this->message,
            'meta'    => $this->meta,
        ];
    }
}
