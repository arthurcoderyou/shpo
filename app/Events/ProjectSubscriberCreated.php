<?php

namespace App\Events;

use App\Models\ProjectSubscriber;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProjectSubscriberCreated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public ProjectSubscriber $project_subscriber;
     public string $message;
    public $projectSubscriberId;
    public $authId;


    /**
     * Create a new event instance.
     */
    public function __construct(ProjectSubscriber $project_subscriber, $authId)
    {
        $this->project_subscriber = $project_subscriber;
        $this->projectSubscriberId = $this->project_subscriber->id;
        $this->authId = $authId;

        $this->message = "New project subscriber '".$this->project_subscriber->user->name."' added for project '".$this->project_subscriber->project->name."'";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('project_subscriber'),
        ];
    }

    public function broadcastAs(){
        return "created";
    }


    public function broadcastWith(){
         
         
        return [
            'message' => $this->message,
            'project_id' => $this->project_subscriber->project->id,
            'project_url' => route('project.show',['project' => $this->project_subscriber->project->id]),
        ];
    }


}
