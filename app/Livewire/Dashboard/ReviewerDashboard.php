<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use Livewire\Component;

class ReviewerDashboard extends Component
{

    public $projects_for_review;

    public $all_projects;
    public $in_review_projects;
    public $approved_projects;

    public function mount(){
        $this->projects_for_review = Project::countProjectsForReview("pending");
        $this->all_projects = Project::countProjects();
        // $this->in_review_projects  = Project::countProjects("in_review");
        $this->in_review_projects  = Project::countProjectsForReview();
        $this->approved_projects  = Project::countProjects("approved");


    }

    public function render()
    {
        return view('livewire.dashboard.reviewer-dashboard');
    }
}
