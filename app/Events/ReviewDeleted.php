<?php

namespace App\Events;

use App\Models\Review;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReviewDeleted  implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public string $message; 

    public $reviewId;
    public $authId;


    /**
     * Create a new event instance.
     */
    public function __construct($reviewId, $authId)
    {
        $review = Review::find($this->reviewId);

        $this->reviewId = $reviewId;

        $this->authId = $authId ;

        $this->message =  "Review '".$review->id."' deleted ";
  
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('review'),
        ];
    }

    public function broadcastAs(){
        return "deleted";
    }


    public function broadcastWith(){
         
 
        return [
            'message' => $this->message,    
            'project_url' => route('project.index'), 
        ];
    }
}
