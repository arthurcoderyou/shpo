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

class ReviewerCreated  implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Reviewer $reviewer;
    public $reviewer_id;
    public $authId;
    /**
     * Create a new event instance.
     */
    public function __construct($reviewer_id, $authId)
    {
        $this->reviewer = Reviewer::find($reviewer_id);
        $this->reviewer_id = $reviewer_id;
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
        return "created";
    }


    public function broadcastWith(){
         

        $reviewer = Reviewer::find($this->reviewer_id); 

        
        if(!empty($reviewer->document_type)){


            if(!empty($reviewer->user_id)){

                $message =  "New Reviewer '".$reviewer->user->name."' added to the document type '".$reviewer->document_type->name."'";
            }else{
                // open review
                $message =  "New Reviewer 'Open Review' added to the document type '".$reviewer->document_type->name."'";

            }

                
        }else{
            if($reviewer->reviewer_type == "initial"){
                if(!empty($reviewer->user_id)){

                    $message = "New Reviewer '".$reviewer->user->name."' added to the initial reviewers";
                }else{
                    // open review
                    $message =  "New Reviewer 'Open Review' added to the initial reviewers";

                }

                
            }elseif($reviewer->reviewer_type == "final"){

                if(!empty($reviewer->user_id)){

                    $message = "New Reviewer '".$reviewer->user->name."' added to the final reviewers ";
                }else{
                    // open review
                    $message = "New Reviewer 'Open Review' added to the final reviewers ";

                }

                
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
