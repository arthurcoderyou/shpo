<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MainDashboard extends Component
{

     protected $listeners = [
        'projectTimerUpdated' => '$refresh',
        'documentTypeCreated' => '$refresh',
        'documentTypeUpdated' => '$refresh',
        'documentTypeDeleted' => '$refresh',
        'reviewerCreated' => '$refresh',
        'reviewerUpdated' => '$refresh',
        'reviewerDeleted' => '$refresh',
        'roleCreated' => '$refresh',
        'roleUpdated' => '$refresh',
        'roleDeleted' => '$refresh',
        'userCreated' => '$refresh',
        'userUpdated' => '$refresh',
        'userDeleted' => '$refresh',
    ];

    
    public function render()
    {   
  
        // set the iconBg and iconColor based on user access
        $user = Auth::user();

        $accessColors = [
            'system access user' => ['bg' => 'bg-blue-900', 'text' => 'text-blue-100'],
            'system access reviewer' => ['bg' => 'bg-yellow-900', 'text' => 'text-yellow-100'],
            'system access admin' => ['bg' => 'bg-purple-900', 'text' => 'text-purple-100'],
            'system access global admin' => ['bg' => 'bg-green-900', 'text' => 'text-green-100'],
        ];

        // default fallback
        $iconBg = 'bg-gray-900';
        $iconColor = 'text-gray-100';

        foreach ($accessColors as $permission => $colors) {
            if ($user->can($permission)) {
                $iconBg = $colors['bg'];
                $iconColor = $colors['text'];
                break; // stop at the first matching role
            }
        }


        

        return view('livewire.dashboard.main-dashboard',[ 
            'iconBg' => $iconBg,
            'iconColor' => $iconColor,
        ]);
    }
}
