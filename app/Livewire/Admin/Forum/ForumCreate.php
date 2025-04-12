<?php

namespace App\Livewire\Admin\Forum;

use App\Models\Forum;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ForumCreate extends Component
{

    public string $title = '';
    public $description = '';


    public $project_id;

    public $project_search = '';
    public $selected_project = null;

    // protected $listeners = ['forumCreated' => '$refresh'];

    public function mount(){
         
    }


    public function updated($fields){
        $this->validateOnly($fields,[
            'title' => [
                'required',
                'string', 
            ],
            'description' => [
                'required'
            ],
            'project_id' => [
                'required'
            ]

        ],[
            'project_id.required' => 'Please select a project',
            'description.required' => 'Please enter a description',
            'title.required' => 'Please enter a title',
        ]);
    }

    public function select_project($id){
        $this->selected_project = Project::findOrFail($id);
        $this->project_id = $this->selected_project->id;
    }
    


    /**
     * Handle an incoming registration request.
     */
    public function save()
    {
        $this->validate([
            'title' => [
                'required',
                'string', 
            ],
            'description' => [
                'required'
            ],
            'project_id' => [
                'required'
            ]

        ],[
            'project_id.required' => 'Please select a project',
            'description.required' => 'Please enter a description',
            'title.required' => 'Please enter a title',
        ]);

        //save
        $forum = Forum::create([
            'project_id' => $this->project_id,
            'description' => $this->description,
            'title' => $this->title,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        $this->reset(['title', 'description','project_id']);

        ActivityLog::create([
            'log_action' => "Forum \"".$this->title."\" created ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);


        // Broadcast event for other users
        // broadcast(new \App\Events\ForumCreated($forum ))->toOthers();
        broadcast(new \App\Events\ForumCreated($forum ));

        // Refresh only current user list
        $this->dispatch('forumCreated');

        // Alert::success('Success','Forum created successfully');
        // return redirect()->route('forum.index');
    }


    public function render()
    {

        $results = Project::select('projects.*');
        if (!empty($this->project_search) && strlen($this->project_search) > 0) {
            $search = $this->project_search;

            // $results = $results->where(function ($query) use ($search) {
            //     $query->where('projects.name', 'LIKE', '%' . $search . '%')
            //     ->where('projects.name', 'LIKE', '%' . $search . '%')
            //         ;
            // });


            $results = $results->where(function($query) use ($search) {
                $query->where('projects.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.federal_agency', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.description', 'LIKE', '%' . $search . '%')
                    // ->orWhereHas('creator', function ($query) use ($search) {
                    //     $query->where('users.name', 'LIKE', '%' . $search . '%')
                    //         ->where('users.email', 'LIKE', '%' . $search . '%');
                    // })
                    // ->orWhereHas('updator', function ($query) use ($search) {
                    //     $query->where('users.name', 'LIKE', '%' . $search . '%')
                    //         ->where('users.email', 'LIKE', '%' . $search . '%');
                    // })
                    ->orWhereHas('project_reviewers.user', function ($query) use ($search) {
                        $query->where('users.name', 'LIKE', '%' . $search . '%')
                        ->where('users.email', 'LIKE', '%' . $search . '%');
                    });
            });


        }
        $results =  $results->limit(10)->get();




        return view('livewire.admin.forum.forum-create',[
            'results' => $results,
        ]);
    }
}
