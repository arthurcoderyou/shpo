<?php

namespace App\Livewire\Dashboard\DashboardTile;

use App\Models\Project;

use App\Models\ProjectDocument;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;


class ProjectTile extends Component
{   

    protected $listeners = [
        'projectCreated' => 'refreshCount',
        'projectUpdated' => 'refreshCount',
        'projectDeleted' => 'refreshCount',
    ];

    public $title; 
    public $icon;

    public $iconColor;
    public $iconBg;
    public $route;
    public $routeKey;        // routeKey is the route name connected
    public $count; 
 

    public function mount($title,  $icon = null, $route = null, $routeKey = null ,$iconBg = "bg-blue-600", $iconColor = "text-white")
    {
 

        // \App\Services\DashboardCacheService::updateUserStats(); 

        $this->title = $title; 
        $this->icon = $icon;
        $this->iconColor = $iconColor;
        $this->iconBg = $iconBg;
        $this->route = $route;
        $this->routeKey = $routeKey; 
         
        $this->refreshCount();
    }

    public function refreshCount()
    {
        // $this->count = Cache::get($this->routeKey, 0);
  
        $this->count = Project::countProjects($this->routeKey);
         
        

    }


    public function render()
    {
        return view('livewire.dashboard.dashboard-tile.project-tile');
    }
}
