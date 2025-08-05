<?php

namespace App\Livewire\Notification;

use Livewire\Component;

class NotificationCounter extends Component
{

    protected $listeners = [ 
        'notificationsCreated' => '$refresh',
        'notificationsDeleted' => '$refresh',
        'notificationsUpdated' => '$refresh',
        
        'projectTimerUpdated' => '$refresh',
        'documentTypeCreated' => '$refresh',
        'documentTypeUpdated' => '$refresh',
        'documentTypeDeleted' => '$refresh',
        'activitylogCreated' => '$refresh',
        'reviewerCreated' => '$refresh', 
        'reviewerUpdated' => '$refresh',
        'reviewerDeleted' => '$refresh',
    ];

    public function getUnreadProperty(){
        return auth()->user()->notifications()
            ->whereNull('read_at') // Unread notifications
            ->count();
    }

    public function render()
    {
        return view('livewire.notification.notification-counter',[
            'unread_count' => $this->unread,
        ]);
    }
}
