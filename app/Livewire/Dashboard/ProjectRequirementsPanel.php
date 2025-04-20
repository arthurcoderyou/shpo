<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\ProjectTimer;
use App\Models\Reviewer;

class ProjectRequirementsPanel extends Component
{
    public function render()
    {

        $projectTimer = ProjectTimer::first();

        $errors = [
            'response_duration' => !$projectTimer || (
                !$projectTimer->submitter_response_duration_type ||
                !$projectTimer->submitter_response_duration ||
                !$projectTimer->reviewer_response_duration ||
                !$projectTimer->reviewer_response_duration_type
            ),
            'project_submission_times' => !$projectTimer || (
                !$projectTimer->project_submission_open_time ||
                !$projectTimer->project_submission_close_time ||
                !$projectTimer->message_on_open_close_time
            ),
            'no_reviewers' => Reviewer::count() === 0,
        ];
        
        // dd(Reviewer::count() === 0);
        return view('livewire.dashboard.project-requirements-panel',compact('errors','projectTimer'));
    }
}
