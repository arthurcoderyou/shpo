<?php

namespace App\Events\ProjectTimer;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TimeSettingsUpdated implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $projectTimerId;
    public int $targetUserId;
    public string $targetUserEmailForRole;
    public int $authId;

    public $sendMail = true; 


    /**
     * Create a new event instance
     * 
     * @param  int           $projectTimerId      project timer id
     * @param  int           $targetUserId    Extra user IDs that should also be notified. 
     * @param  string        $targetUserEmailForRole    Extra user IDs that should also be notified. 
     * @return \Illuminate\Support\Collection  The user IDs that were targeted.
     */
    public function __construct($projectTimerId,$targetUserId, $targetUserEmailForRole,$authId, $sendMail)
    {
        $this->projectTimerId = $projectTimerId;
        $this->targetUserId = $targetUserId;
        $this->targetUserEmailForRole = $targetUserEmailForRole; 
        $this->authId = $authId;
        $this->sendMail = $sendMail; 
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('project_timer'),
        ];
    }
}
