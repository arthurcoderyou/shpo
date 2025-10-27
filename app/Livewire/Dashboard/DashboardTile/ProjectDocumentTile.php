<?php

namespace App\Livewire\Dashboard\DashboardTile;

use App\Models\ProjectDocument;
use Livewire\Component;

class ProjectDocumentTile extends Component
{

    protected $listeners = [
        'projectDocumentCreated' => 'refreshCount',
        'projectDocumentUpdated' => 'refreshCount',
        'projectDocumentDeleted' => 'refreshCount',
    ];

    public $title; 
    public $icon;

    public $iconColor;
    public $iconBg;
    public $route;
    public $routeKey;        // routeKey is the route name connected
    public $count; 

    public $tileType;
    public $reviewStatus;

    public function mount($title,  $icon = null, $route = null, $routeKey = null ,$iconBg = "bg-blue-600", $iconColor = "text-white", $reviewStatus = null)
    {
 

        // \App\Services\DashboardCacheService::updateUserStats(); 

        $this->title = $title; 
        $this->icon = $icon;
        $this->iconColor = $iconColor;
        $this->iconBg = $iconBg;
        $this->route = $route;
        $this->routeKey = $routeKey; 
        $this->reviewStatus = $reviewStatus; 
         
        $this->refreshCount();
    }

    public function refreshCount()
    {
        // $this->count = Cache::get($this->routeKey, 0);
        // dd($this->routeKey);
        // $this->count = ProjectDocument::countProjectDocuments($this->routeKey);
        // dd($this->count);

        $this->count = ProjectDocument::countBasedOnReviewStatus($this->reviewStatus) ?? 0;

          

    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-tile.project-document-tile');
    }
}
