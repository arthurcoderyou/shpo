<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use Livewire\Component;

class UserDashboard extends Component
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


    public $pending_update_projects;
    public $my_projects;
    public $in_review_projects;
    public $approved_projects;

    public function mount(){



        $this->pending_update_projects = Project::countProjectsForUpdate(); 
        $this->my_projects = Project::countProjects(null, "yes");
        $this->in_review_projects  = Project::countProjects("in_review", "yes");
        $this->approved_projects  = Project::countProjects("approved", "yes");



    }


    public function render()
    {

         
        return view('livewire.dashboard.user-dashboard');
    }
}
