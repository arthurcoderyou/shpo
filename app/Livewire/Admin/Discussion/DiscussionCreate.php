<?php

namespace App\Livewire\Admin\Discussion;

use App\Models\Discussion;
use App\Models\Forum;
use Livewire\Component;
use App\Models\ActivityLog;
use Livewire\WithPagination; 
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;



class DiscussionCreate extends Component
{   
 
    

    public string $title = '';
    public $description = '';

    protected $listeners = ['discussionCreated' => '$refresh'];



    public $forum_id;


    public $forum_search = '';
    public $selected_forum = null;

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
            'forum_id' => [
                'required'
            ]

        ],[
            'forum_id.required' => 'Please select a forum',
            'description.required' => 'Please enter a description',
            'title.required' => 'Please enter a title',
        ]);
    }

    public function select_forum($id){
        $this->selected_forum = Forum::findOrFail($id);
        $this->forum_id = $this->selected_forum->id;
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
                'unique:permissions,name',
            ],
            'description' => [
                'required'
            ],
            'forum_id' => [
                'required'
            ]

        ],[
            'forum_id.required' => 'Please select a project',
            'description.required' => 'Please enter a description',
            'title.required' => 'Please enter a title',
        ]);

        //save
        $discussion = Discussion::create([
            'forum_id' => $this->forum_id,
            'description' => $this->description,
            'title' => $this->title,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);


        $this->reset(['title', 'description','forum_id']);

        // Broadcast event for other users
        broadcast(new \App\Events\DiscussionCreated($discussion))->toOthers();

        // Refresh only current user list
        $this->dispatch('discussionCreated');



        ActivityLog::create([
            'log_action' => "Discussion \"".$this->title."\" created ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        // Alert::success('Success','Discussion created successfully');
        // return redirect()->route('discussions.index');
    }


    public function render()
    {


        



        $results = Forum::select('forums.*');
        if (!empty($this->forum_search) && strlen($this->forum_search) > 0) {
            $search = $this->forum_search;

            // $results = $results->where(function ($query) use ($search) {
            //     $query->where('forums.name', 'LIKE', '%' . $search . '%')
            //     ->where('forums.name', 'LIKE', '%' . $search . '%')
            //         ;
            // });


            $results = $results->where(function($query) use ($search) {
                $query->where('forums.title', 'LIKE', '%' . $search . '%')
                    ->orWhere('forums.description', 'LIKE', '%' . $search . '%');
            });


        }
        $results =  $results->limit(10)->get();




        return view('livewire.admin.discussion.discussion-create',[
            'results' => $results, 
        ]);
    }


    // public function render()
    // {
    //     return view('livewire.admin.discussion.discussion-create');
    // }
}
