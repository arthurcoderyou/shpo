<?php

namespace App\Events;

use App\Models\Reviewer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReviewerUpdated implements ShouldBroadcastNow
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
        return "updated";
    }


    public function broadcastWith(){
         

        $reviewer = $this->reviewer;

        if(!empty($reviewer->document_type)){
            $message =  "Reviewer '".$reviewer->user->name."' order updated to ".$reviewer->order." on the document type '".$reviewer->document_type->name."'"; 
        }else{
            if($reviewer->reviewer_type == "initial"){
                $message = "Reviewer '".$reviewer->user->name."' order updated to ".$reviewer->order." on the initial reviewers";
            }elseif($reviewer->reviewer_type == "final"){
                $message = "Reviewer '".$reviewer->user->name."' order updated to ".$reviewer->order." on the final reviewers";
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
