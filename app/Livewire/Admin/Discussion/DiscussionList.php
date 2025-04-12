<?php

namespace App\Livewire\Admin\Discussion;

use Livewire\Component;
use App\Models\Discussion;
use Livewire\WithPagination;

class DiscussionList extends Component
{

    use WithPagination;

    public $search = '';
    public $sort_by = '';
    public $record_count = 10;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;


    
    public function render()
    {


        $discussions = Discussion::select('discussions.*');


        if (!empty($this->search)) {
            $search = $this->search;


            $discussions = $discussions->where(function($query) use ($search){
                $query =  $query->where('discussions.title','LIKE','%'.$search.'%')
                    ->orWhere('discussions.description','LIKE','%'.$search.'%');
            });


        }
 


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Title A - Z":
                    $discussions =  $discussions->orderBy('discussions.title','ASC');
                    break;

                case "Title Z - A":
                    $discussions =  $discussions->orderBy('discussions.title','DESC');
                    break;

                case "Description A - Z":
                    $discussions =  $discussions->orderBy('discussions.description','ASC');
                    break;
 
                case "Description Z - A":
                    $discussions =  $discussions->orderBy('discussions.description','DESC');
                    break;


                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $discussions =  $discussions->orderBy('discussions.created_at','DESC');
                    break;

                case "Oldest Added":
                    $discussions =  $discussions->orderBy('discussions.created_at','ASC');
                    break;

                case "Latest Updated":
                    $discussions =  $discussions->orderBy('discussions.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $discussions =  $discussions->orderBy('discussions.updated_at','ASC');
                    break;
                default:
                    $discussions =  $discussions->orderBy('discussions.updated_at','DESC');
                    break;

            }


        }else{
            $discussions =  $discussions->orderBy('discussions.updated_at','DESC');

        }





        $discussions = $discussions->paginate($this->record_count);

        return view('livewire.admin.discussion.discussion-list',[
            'discussions' => $discussions
        ]);
    }
}
