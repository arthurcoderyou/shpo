<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Reviewer;
use App\Models\DocumentType;
use App\Models\ProjectTimer;

class ProjectRequirementsPanel extends Component
{

    protected $listeners = [
        'projectTimerUpdated' => '$refresh',
        'documentTypeCreated' => '$refresh',
        'documentTypeUpdated' => '$refresh',
        'documentTypeDeleted' => '$refresh',
        'reviewerCreated' => '$refresh',
        'reviewerUpdated' => '$refresh',
        'reviewerDeleted' => '$refresh',
    ];


    public function render()
    {

        $projectTimer = ProjectTimer::first();

        // DocumentTypes that don't have any reviewers
        $documentTypesWithoutReviewers = DocumentType::whereDoesntHave('reviewers')->pluck('name')->toArray();

        // Check if all document types have at least one reviewer
        $allDocumentTypesHaveReviewers = empty($documentTypesWithoutReviewers);

        // Check if there are reviewers by type
        // $hasInitialReviewers = Reviewer::where('reviewer_type', 'initial')->exists();
        // $hasFinalReviewers = Reviewer::where('reviewer_type', 'final')->exists();


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
            'no_document_types' => DocumentType::count() === 0, // Add a new error condition
            'document_types_missing_reviewers' => !$allDocumentTypesHaveReviewers,
            // 'no_initial_reviewers' => !$hasInitialReviewers,
            // 'no_final_reviewers' => !$hasFinalReviewers,
        ];
        
        // dd(Reviewer::count() === 0);
        return view('livewire.dashboard.project-requirements-panel',compact(
            'errors',
            'projectTimer',
                'documentTypesWithoutReviewers'
        ));
    }
}
