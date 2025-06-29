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
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ReviewCreated  implements ShouldBroadcast, ShouldQueue
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

        $this->message =  "New Review by '".$review->reviewer->name."' added to the project '".$review->project->name."'";

        if(!empty($review->project_document_id)){
            $this->message =  "New Review by '".$review->reviewer->name."' added to the project '".$review->project->name."' for document '".$review->project_document->document_type->name."'. Project review is '".$review->review_status."'";
        } 


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
        return "created";
    }


    public function broadcastWith(){
         

        
        return [
            'message' => $this->message,   
            'project_id' => $this->review->project->id,
            'project_url' => route('project.show',['project' => $this->review->project->id]), 
        ];
    }
}
