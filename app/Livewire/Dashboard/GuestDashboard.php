<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class GuestDashboard extends Component
{   

    protected $listeners = [
        'projectCreated' => '$refresh',
        'projectUpdated' => '$refresh',
        'projectDeleted' => '$refresh',
        'projectSubmitted' => '$refresh',
        'projectQueued' => '$refresh',
        // 'projectDocumentCreated' => '$refresh',
        // 'projectDocumentUpdated' => '$refresh',
        // 'projectDocumentDeleted' => '$refresh',
    ];

    public function render()
    {
        return view('livewire.dashboard.guest-dashboard');
    }
}
