<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use Livewire\Component;

class UserDashboard extends Component
{

    public $pending_update_projects;
    public $all_projects;
    public $in_review_projects;
    public $approved_projects;

    public function mount(){
        $this->pending_update_projects = Project::countProjectsForUpdate();

        
        
        $this->all_projects = Project::countProjects();
        $this->in_review_projects  = Project::countProjects("in_review");
        $this->approved_projects  = Project::countProjects("approved");



    }


    public function render()
    {

         
        return view('livewire.dashboard.user-dashboard');
    }
}
