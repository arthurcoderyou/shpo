<?php

namespace App\Events;

use App\Models\ActiveDays;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DayStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $day;

    /**
     * Create a new event instance.
     */
    public function __construct(ActiveDays $day)
    {
        $this->day = $day;
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
        return "update";
    }


    public function broadcastWith(){
        return [
            'message' => 'Updated day status of '.$this->day->day.' to ' . ($this->day->is_active ? 'active' : 'inactive') . 'at '.$this->day->updated_at->toDateTimeString() ,
            'project_timer_url' => route('project_timer.index'),
        ];
    }


}
