<?php

namespace App\Livewire\Admin\ProjectDiscussion;

use App\Models\Project;
use Livewire\Component;
use App\Models\ProjectDocument;
use App\Models\ProjectDiscussion;
use Illuminate\Support\Facades\Auth;
use App\Events\Project\ProjectLogEvent;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;

class ProjectDiscussionCreate extends Component
{

    public Project $project;
    public ?ProjectDiscussion $parent = null;
    public ?ProjectDocument $project_document = null;
     
    public $title = '';
    public $body = '';
    public $is_private = false;

    // protected $listeners = ['projectDiscussionAdded' => '$refresh'];

    public function save()
    {
        // dd("here");
        $this->validate([
            'body' => 'required|string|min:3',
            'title' => 'nullable|string|min:3',
        ]);

        $project_discussion = ProjectDiscussion::create([
            'project_id' => $this->project->id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'title' => $this->parent ? null : $this->title,
            'body' => $this->body,
            'parent_id' => $this->parent?->id,
            'project_document_id' => $this->project_document?->id,
            'is_private' => $this->is_private,
        ]);


        $project = Project::find($this->project->id);


        broadcast(new \App\Events\ProjectDiscussionCreated($project  ,$project_discussion));


        // temporary || should be on ProjectDiscussion events
            $authId = Auth::id() ?? null;

            // Success message from the activity log project helper 
            $message =  ProjectLogHelper::getProjectActivityMessage('updated',$project->id,$authId);
    
            // get the route 
            $route = ProjectLogHelper::getRoute('updated', $project->id);
            

            // // log the event 
            event(new ProjectLogEvent(
                $message ,
                $authId, 

            ));


        // Refresh  
        $this->dispatch('projectDiscussionAdded');

        $this->reset(['title', 'body', 'is_private']);



    }

    public function render()
    {
        return view('livewire.admin.project-discussion.project-discussion-create');
    }
}
