<?php

namespace App\Livewire\Dashboard\DashboardTile;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class UserTile extends Component
{

    protected $listeners = [
        'userCreated' => 'refreshCount',
        'userUpdated' => 'refreshCount',
        'userDeleted' => 'refreshCount',
    ];

    public $title; 
    public $icon;

    public $iconColor;
    public $iconBg;
    public $route;
    public $selected_role;
    public $role_request;
    public $count; 

    public function mount($title,  $icon = null, $route = null, $selected_role = null, $role_request = null ,$iconBg = "bg-blue-600", $iconColor = "text-white")
    {
 

        // \App\Services\DashboardCacheService::updateUserStats(); 

        $this->title = $title; 
        $this->icon = $icon;
        $this->iconColor = $iconColor;
        $this->iconBg = $iconBg;
        $this->route = $route;
        $this->selected_role = $selected_role;
        $this->role_request = $role_request;
         
        $this->refreshCount(); 
    }

    public function refreshCount()
    {
        $this->count = User::countUsers($this->selected_role, $this->role_request);
    }



    public function render()
    {
        return view('livewire.dashboard.dashboard-tile.user-tile');
    }
}
