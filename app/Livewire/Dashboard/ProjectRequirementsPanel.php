<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class ProjectRequirementsPanel extends Component
{
    public function render()
    {

        $errors = [
            'response_duration' => true,
            'submitter_response_duration_type' => true,
            'submitter_response_duration' => true,
            'reviewer_response_duration' => true,
            'reviewer_response_duration_type' => true,
            'project_submission_times' => true,
            'project_submission_open_time' => true,
            'project_submission_close_time' => true,
            'message_on_open_close_time' => true,
            'project_submission_restrict_by_time' => true,
            'no_reviewers' => true,
        ];
        
       
        return view('livewire.dashboard.project-requirements-panel',compact('errors'));
    }
}
