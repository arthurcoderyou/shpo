<?php

namespace App\Events;

use App\Models\Reviewer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ReviewerDeleted  implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public Reviewer $reviewer;
    public $authId;
    /**
     * Create a new event instance.
     */
    public function __construct($authId)
    {
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
            new PrivateChannel('reviewer'),
        ];
    }

    public function broadcastAs(){
        return "deleted";
    }


    public function broadcastWith(){
         

        $reviewer = $this->reviewer;

        if(!empty($reviewer->document_type)){
            $message = "Reviewer '".$reviewer->user->name."' deleted from the document type '".$reviewer->document_type->name."'"; 
        }else{
            if($reviewer->reviewer_type == "initial"){
                $message = "Reviewer '".$reviewer->user->name."' deleted from the initial reviewers";
            }elseif($reviewer->reviewer_type == "final"){
                $message = "Reviewer '".$reviewer->user->name."' deleted from the final reviewers";
            }
        }


        return [
            'message' => $message, 
            'reviewer_url' => route('reviewer.index',[
                'document_type_id' => $reviewer->document_type_id,
                'reviewer_type' => $reviewer->reviewer_type,
            ]),
        ];
    }
}
