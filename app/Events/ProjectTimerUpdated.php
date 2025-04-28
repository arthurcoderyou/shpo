<?php

namespace App\Events;

use App\Models\ProjectTimer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ProjectTimerUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ?ProjectTimer $project_timer;

    /**
     * Create a new event instance.
     */
    public function __construct(ProjectTimer $project_timer = null)
    {
        $this->project_timer = $project_timer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('project.timer'),
        ];
    }


    public function broadcastAs(){
        return "updated";
    }


    public function broadcastWith(){
        $user = auth()->user();

        return [
            'message' => $this->project_timer
                ? 'Time settings updated by ' . $this->project_timer->updator->name . ' at ' . $this->project_timer->updated_at->toDateTimeString()
                : 'Time settings updated by ' . $user->name . ' at ' . now()->toDateTimeString(),
            'project_timer_url' => route('project_timer.index'),
        ];
    }
}
