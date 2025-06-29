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

class ReviewUpdated  implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Review $review;
    public string $message; 

    public $reviewId;
    public $authId;

    /**
     * Create a new event instance.
     */
    public function __construct(Review $review,  $authId)
    {
        $this->review = $review;

        $review = $this->review;

        $this->message =  "Review by '".$review->reviewer->name."' updated to the project '".$review->project->name."'";
 
        $this->reviewId = $review->id;
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
            new PrivateChannel('review'),
        ];
    }

    public function broadcastAs(){
        return "updated";
    }


    public function broadcastWith(){
         
 
        return [
            'message' => $this->message,   
            'project_id' => $this->review->project->id,
            'project_url' => route('project.show',['project' => $this->review->project->id]), 
        ];
    }
}
