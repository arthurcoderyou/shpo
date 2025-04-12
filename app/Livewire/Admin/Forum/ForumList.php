<?php

namespace App\Livewire\Admin\Forum;

use App\Models\Forum;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use RealRashid\SweetAlert\Facades\Alert;

class ForumList extends Component
{

    use WithFileUploads;
    use WithPagination;

    public $search = '';
    public $sort_by = '';
    public $record_count = 10;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;

    public $file;

    protected $listeners = [
        'forumUpdated' => '$refresh',
        'forumCreated' => '$refresh',
        'forumDeleted' => '$refresh',
        
    ];

    // protected $listeners = [
    //     'echo:forums,create' => '$refresh',
    //     'echo:forums,update' => '$refresh',
    // ];

    

    // Method to delete selected records
    public function deleteSelected()
    {
        Forum::whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records

        // Broadcast event for other users
        // broadcast(new \App\Events\ForumDeleted(  ))->toOthers();
        broadcast(new \App\Events\ForumDeleted(  ));

        // Refresh only current user list
        $this->dispatch('forumDeleted');

        // Alert::success('Success','Selected forums deleted successfully');
        // return redirect()->route('forum.index');
    }

    // This method is called automatically when selected_records is updated
    public function updateSelectedCount()
    {
        // Update the count when checkboxes are checked or unchecked
        $this->count = count($this->selected_records);
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selected_records = Forum::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){
        $forum = Forum::find($id);


        $forum->delete();
        
        // Broadcast event for other users
        broadcast(new \App\Events\ForumDeleted( $forum ));

        // Refresh only current user list
        $this->dispatch('forumDeleted');

        // Alert::success('Success','Forum deleted successfully');
        // return redirect()->route('forum.index');

        $this->dispatch('browser', name: 'forum-deleted', data: [
            'title' => 'Success',
            'message' => 'Forum deleted successfully',
        ]);



    }




    public function render()
    {


        $forums = Forum::select('forums.*');


        if (!empty($this->search)) {
            $search = $this->search;


            $forums = $forums->where(function($query) use ($search){
                $query =  $query->where('forums.title','LIKE','%'.$search.'%')
                    ->orWhere('forums.description','LIKE','%'.$search.'%');
            });


        }
 


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Title A - Z":
                    $forums =  $forums->orderBy('forums.title','ASC');
                    break;

                case "Title Z - A":
                    $forums =  $forums->orderBy('forums.title','DESC');
                    break;

                case "Description A - Z":
                    $forums =  $forums->orderBy('forums.description','ASC');
                    break;
 
                case "Description Z - A":
                    $forums =  $forums->orderBy('forums.description','DESC');
                    break;


                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $forums =  $forums->orderBy('forums.created_at','DESC');
                    break;

                case "Oldest Added":
                    $forums =  $forums->orderBy('forums.created_at','ASC');
                    break;

                case "Latest Updated":
                    $forums =  $forums->orderBy('forums.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $forums =  $forums->orderBy('forums.updated_at','ASC');
                    break;
                default:
                    $forums =  $forums->orderBy('forums.updated_at','DESC');
                    break;

            }


        }else{
            $forums =  $forums->orderBy('forums.updated_at','DESC');

        }





        $forums = $forums->paginate($this->record_count);

        
        return view('livewire.admin.forum.forum-list',[
            'forums' => $forums
        ]);
    }


    // public function render()
    // {
    //     return view('livewire.admin.forum.forum-list');
    // }
}
