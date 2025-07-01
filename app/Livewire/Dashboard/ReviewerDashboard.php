<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use Livewire\Component;

class ReviewerDashboard extends Component
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

    public $projects_for_review;

    public $all_projects;
    public $all_in_review_projects;
    public $all_approved_projects;

    public $pending_update_projects;
    public $my_projects;
    public $in_review_projects;
    public $approved_projects;

    public $all_pending_update_projects;


    public function mount(){
        // owned
        $this->pending_update_projects = Project::countProjectsForUpdate(); 
        $this->my_projects = Project::countProjects(null, "yes");
        $this->in_review_projects  = Project::countProjects("in_review", "yes");
        $this->approved_projects  = Project::countProjects("approved", "yes");


        // reviewer
        $this->projects_for_review = Project::countProjectsForReview();

        // all 
        $this->all_projects = Project::countProjects();
        $this->all_pending_update_projects  = Project::countProjectsForUpdate();
        $this->all_in_review_projects  = Project::countProjectsForReview();
        $this->all_approved_projects  = Project::countProjects("approved");


    }

    public function render()
    {
        return view('livewire.dashboard.reviewer-dashboard');
    }
}
