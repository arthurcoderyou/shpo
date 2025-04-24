<?php

namespace App\Events;

use App\Models\Reviewer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ReviewerDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Reviewer $reviewer;
    /**
     * Create a new event instance.
     */
    public function __construct(Reviewer $reviewer)
    {
        $this->reviewer = $reviewer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('reviewer'),
        ];
    }

    public function broadcastAs(){
        return "deleted";
    }


    public function broadcastWith(){
         
        return [
            'message' => "Reviewer '".$this->reviewer->name."' deleted from the document type '".$this->reviewer->document_type->name."'", 
            'reviewer_url' => route('reviewer.index'),
        ];
    }
}
