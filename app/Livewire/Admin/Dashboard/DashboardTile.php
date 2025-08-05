<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class DashboardTile extends Component
{

    public $title;
    public $permission;
    public $role;
    public $icon;
    public $route;
    public $dataKey;
    public $count; 

    public function mount($title, $permission = null, $role = null, $icon = null, $route = null, $dataKey = null )
    {


        // \App\Services\DashboardCacheService::updateUserStats();


        $this->title = $title;
        $this->permission = $permission;
        $this->role = $role;
        $this->icon = $icon;
        $this->route = $route;
        $this->dataKey = $dataKey;
         
        $this->refreshCount();
    }

    public function refreshCount()
    {
        $this->count = Cache::get($this->dataKey, 0);
    }


    public function render()
    {
        return view('livewire.admin.dashboard.dashboard-tile');
    }
}
