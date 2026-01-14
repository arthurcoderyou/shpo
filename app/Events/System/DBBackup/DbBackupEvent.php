<?php

namespace App\Events\System\DBBackup;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DbBackupEvent implements ShouldBroadcast,ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
 
    public int $dbBackupId; 
    public int $targetUserId; 
    
    public function __construct( 
        int $dbBackupId, 
         int $targetUserId, 
    )
    { 
        $this->dbBackupId      = $dbBackupId; 
        $this->targetUserId      = $targetUserId; 
    }

    public function broadcastOn()
    {
        // Only the user with $targetUserId can subscribe to this channel
        return new PrivateChannel('backup.database');
    }

    public function broadcastAs()
    {
        // Echo will listen to ".notify"
        return 'event';
    }
}
