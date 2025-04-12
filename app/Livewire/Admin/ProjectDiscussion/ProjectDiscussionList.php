<?php

namespace App\Livewire\Admin\ProjectDiscussion;

use App\Models\Project;
use Livewire\Component;
use App\Models\ProjectDiscussion;

class ProjectDiscussionList extends Component
{

    public Project $project;

    // public bool $onlyPrivate = false;

    public string $discussionVisibility = 'all'; // Options: all, private, public

    // public $discussions;

    protected $listeners = [
        'projectDiscussionAdded' => '$refresh',
        'projectDiscussionEdited' => '$refresh',
        'projectDiscussionReplied' => '$refresh',
        'projectDiscussionDeleted' => '$refresh',
        
    ];

    /**
     * Summary of Reply Function
     * @var 
     */
        public ?int $replyToId = null;

        public function startReply(int $id)
        {
            $this->replyToId = $id;
        }


        public string $replyBody = '';

        public function submitReply()
        {
            $this->validate([
                'replyBody' => 'required|string|min:1',
            ]);

            $parent = ProjectDiscussion::find($this->replyToId);

            if (!$parent) {
                return; // Or optionally show an error/toast
            }

            $reply = ProjectDiscussion::create([
                'project_id' => $this->project->id,
                'parent_id' => $this->replyToId,
                'body' => $this->replyBody,
                'is_private' => $parent->is_private, // inherit privacy from parent
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);


            broadcast(new \App\Events\ProjectDiscussionReplied($parent->project, $parent ,$reply));

            // Refresh  
            // $this->dispatch('projectDiscussionAdded');

            $this->reset(['replyBody', 'replyToId']);
        }


        public function cancelReply()
        {
            $this->reset(['replyBody', 'replyToId']);
        }
    /**
    * ./ Reply Function 
    */



    /** Edit Function  */
        public ?int $editId = null;

        public ?string $editTitle = '';
        public ?string $editBody = '';
        public ?int $editParentId = null;

      

        public function startEdit(int $id)
        {
            $this->editId = $id;
            $discussion = ProjectDiscussion::find($this->editId); 

            $this->editTitle = $discussion->title;
            $this->editBody = $discussion->body;
            $this->editParentId = $discussion->parent_id;

        }

        

        public function submitUpdate()
        {
            $this->validate([
                'editTitle' => 'nullable|string|min:1',
                'editBody' => 'required|string|min:1',
            ]);

            $discussion = ProjectDiscussion::find($this->editId); 
            $discussion->project_id = $this->project->id; 
            $discussion->title = $this->editTitle;
            $discussion->body = $this->editBody;  
            $discussion->updated_by = auth()->id(); 
            $discussion->updated_at = now();
            $discussion->save();

            broadcast(new \App\Events\ProjectDiscussionEdited($discussion->project, $discussion));

            // Refresh  
            $this->dispatch('projectDiscussionEdited');

            $this->reset(['editTitle', 'editBody','editParentId']);
            $this->editId = null;
        }


        public function cancelUpdate()
        {
            $this->reset(['editTitle', 'editBody','editParentId','editId']);
        }

    /** ./ Edit Function  */



    // delete
    public function delete_discussion($id){

        $discussion = ProjectDiscussion::with('replies')->findOrFail($id);

        broadcast(new \App\Events\ProjectDiscussionDeleted($discussion->project, $discussion));


        if (
            $discussion->created_by !== auth()->id() &&
            !auth()->user()->hasRole(['Admin', 'DSI God Admin'])
        ) {
            abort(403);
        }
    
        $discussion->deleteWithReplies();

        
        

        $this->dispatch('projectDiscussionDeleted');


    }


    // public function mount(Project $project)
    // {
    //     $this->project = $project;

    //     // $this->discussions = ProjectDiscussion::with(['creator', 'replies.creator'])
    //     $this->discussions =  ProjectDiscussion::with(['creator', 'parent.creator', 'replies.creator', 'replies.parent.creator'])
    //         ->where('project_id', $this->project->id)
    //         ->whereNull('parent_id')
    //         ->when(!auth()->user()->hasRole(['DSI God Admin','Admin', 'Reviewer']), function ($query) {
    //             $query->where('is_private', false);
    //         })
    //         ->latest()
    //         ->get();
    // }

    public function getDiscussionsProperty()
    {
 

        return ProjectDiscussion::with(['creator', 'parent.creator', 'replies.creator', 'replies.parent.creator'])
            ->where('project_id', $this->project->id)
            ->whereNull('parent_id')
            ->when(!auth()->user()->hasRole(['DSI God Admin', 'Admin', 'Reviewer']), function ($query) {
                $query->where('is_private', false);
            })
            ->when($this->discussionVisibility === 'private', function ($query) {
                $query->where('is_private', true);
            })
            ->when($this->discussionVisibility === 'public', function ($query) {
                $query->where('is_private', false);
            })
            ->latest()
            ->get();
    }


    public function render()
    {

        // dd($this->discussions);
        return view('livewire.admin.project-discussion.project-discussion-list',[
            'discussions' => $this->discussions
        ]);
    }
}
