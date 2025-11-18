<?php

namespace App\Livewire\Admin\Test;

use App\Models\Project;
use Livewire\Component;
use App\Models\ProjectDocument;

class TestReReview extends Component
{


    // we will use project document 255
    // http://127.0.0.1:8000/project_document_review/254/256 

    // 73 Cerbs cerbscervania@gmail.com  2025-09-05 

    public $project_document;
    public $project;

    public function mount(){

        $this->project_document = ProjectDocument::find(256);

        $this->project = Project::find(254);

        // dd($this->project->name);

        // dd($this->project_document->getLastReview());
        $this->loadLastReview();

    }

    


    public bool $showRereviewModal = false; // can be kept if you want future server toggle

    public array $previous_reviewers = [
        ['id' => 1, 'name' => 'Jane Reviewer', 'iteration' => 1],
        ['id' => 2, 'name' => 'Mark Analyst', 'iteration' => 2],
    ];

    public array $rereview = [
        'to_reviewer_id' => null,
        'reason' => '',
        'issues' => [],
    ];

    public array $common_issues = ['Incomplete data', 'Wrong format', 'Missing signatures', 'Outdated info'];

    public $last_review = null;

    public function loadLastReview( )
    {
        // if (!$id) {
        //     $this->last_review = null;
        //     return;
        // }

        $this->last_review = $this->project_document->getLastReview();
        // dd($last_review );
        // $reviewer = $this->project_document->getCurrentReviewerUser();

        // $this->last_review = $last_review ? [
        //     'review_status'        => $last_review->review_status,
        //     'reviewer_name' => $reviewer?->name,
        //     'reviewed_at'   => optional($r->created_at ?? $last_review->created_at)->format('Y-m-d H:i'),
        //     'iteration'     => $last_review->iteration,
        //     // 'role'          => $last_review->role,
        //     'review'          => $last_review->project_review,
        //     // 'issues'        => $last_review->issues ?? [],
        // ] : null;

 
    }

    public function toggleRereviewIssue($tag)
    {
        $i = array_search($tag, $this->rereview['issues'], true);
        if ($i === false) {
            $this->rereview['issues'][] = $tag;
        } else {
            unset($this->rereview['issues'][$i]);
            $this->rereview['issues'] = array_values($this->rereview['issues']);
        }
    }

    public function submitRereview()
    {
        $this->validate([
            'rereview.to_reviewer_id' => 'required',
            'rereview.reason' => 'required|min:5',
        ]);

        // Save logic here
        // ProjectReReviewRequest::create([...])

        $this->dispatch('toast', type:'success', message:'Re-review request sent successfully!');
        $this->rereview = ['to_reviewer_id' => null, 'reason' => '', 'issues' => []];
    }



    public array $eligible_reviewers = [
        ['id' => 1, 'name' => 'Alex Cruz', 'dept' => 'Technical'],
        ['id' => 2, 'name' => 'Maria Lopez', 'dept' => 'Legal'],
        ['id' => 3, 'name' => 'John Tan', 'dept' => 'Environmental'],
    ];

    public array $pre = [
        'reviewers' => [],
        'scope' => '',
        'due_on' => '',
        'instructions' => '',
        'block_my_review' => true,
        'notify_all' => true,
    ];

    public function submitPreReview()
    {
        $this->validate([
            'pre.reviewers' => 'required|array|min:1',
            'pre.scope' => 'required|string',
            'pre.due_on' => 'nullable|date',
            'pre.instructions' => 'required|string|min:5',
        ]);

        // Example save logic
        // ProjectPreReviewRequest::create([
        //     'project_document_id' => $this->docId,
        //     'requested_by' => auth()->id(),
        //     'reviewers' => $this->pre['reviewers'],
        //     'scope' => $this->pre['scope'],
        //     'instructions' => $this->pre['instructions'],
        //     'due_on' => $this->pre['due_on'],
        //     'block_my_review' => $this->pre['block_my_review'],
        //     'notify_all' => $this->pre['notify_all'],
        // ]);

        // Reset
        $this->pre = [
            'reviewers' => [],
            'scope' => '',
            'due_on' => '',
            'instructions' => '',
            'block_my_review' => true,
            'notify_all' => true,
        ];

        $this->dispatch('toast', type:'success', message:'Review request sent to additional reviewers.');
    }


    public function render()
    {
        return view('livewire.admin.test.test-re-review',[
            'last_review' => $this->last_review,
        ]);
    }
}
