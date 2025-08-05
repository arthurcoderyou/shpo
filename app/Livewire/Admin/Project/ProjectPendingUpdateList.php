<?php

namespace App\Livewire\Admin\Project;

use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectReviewer;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification; 
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ReviewerReviewNotification;
use App\Notifications\ProjectReviewFollowupNotification;

class ProjectPendingUpdateList extends Component
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

    // Method to delete selected records
    public function deleteSelected()
    {
        Project::whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records

        Alert::success('Success','Selected projects deleted successfully');
        return redirect()->route('projects.index');
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
            $this->selected_records = Project::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){
        $project = Project::find($id);




        if(!empty($project->attachments)){

            foreach($project->attachments as $attachment){ 
 
                // Construct the full file path
                $filePath = "public/uploads/project_attachments/{$attachment->attachment}";

                // Check if the file exists in storage and delete it
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }

                // Delete the record from the database
                $attachment->delete();
            }

            

        }



        $project->delete();







        Alert::success('Success','Project deleted successfully');
        return redirect()->route('project.index');

    }



    public function submit_project($project_id){

        ProjectHelper::submit_project($project_id);

    }


    

    public function render()
    {


        $projects = Project::select('projects.*');

        // $projects = $projects->whereNotIn('projects.created_by', $dsiGodAdminUserIds);
        


        if(Auth::user()->can('system access user')){
            // $projects = $projects->where('projects.created_by', '=', Auth::user()->id);
        
            $projects = $projects->whereNot('status','approved') // show projects that are pending update because the project needs to be updated after being reviewed
                ->whereNot('status','draft')
                ->where('allow_project_submission',true)
                
                ->where('created_by',Auth::user()->id);

        }


        if (!empty($this->search)) {
            $search = $this->search;


            $projects = $projects->where(function($query) use ($search){
                $query =  $query->where('projects.name','LIKE','%'.$search.'%');
            });


        }

         
        // Get project IDs associated with users who have 'system access global admin' permission
        $dsiGodAdminUserIds = \Spatie\Permission\Models\Permission::where('name', 'system access global admin')
            ->first()
            ?->users()
            ->pluck('id') ?? [];

        // Restrict project access if the user does NOT have either global admin or admin access
        if (!Auth::user()->can('system access global admin') && !Auth::user()->can('system access admin')) {
            $projects = $projects->where('projects.created_by', Auth::user()->id);
        }
            

            
         


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Name A - Z":
                    $projects =  $projects->orderBy('projects.name','ASC');
                    break;

                case "Name Z - A":
                    $projects =  $projects->orderBy('projects.name','DESC');
                    break;

                case "Description A - Z":
                    $projects =  $projects->orderBy('projects.description','ASC');
                    break;
 
                case "Description Z - A":
                    $projects =  $projects->orderBy('projects.description','DESC');
                    break;

                case "Federal Agency A - Z":
                    $projects =  $projects->orderBy('projects.federal_agency','ASC');
                    break;
    
                case "Federal Agency Z - A":
                    $projects =  $projects->orderBy('projects.federal_agency','DESC');
                    break;
    

                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $projects =  $projects->orderBy('projects.created_at','DESC');
                    break;

                case "Oldest Added":
                    $projects =  $projects->orderBy('projects.created_at','ASC');
                    break;

                case "Latest Updated":
                    $projects =  $projects->orderBy('projects.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $projects =  $projects->orderBy('projects.updated_at','ASC');
                    break;
                default:
                    $projects =  $projects->orderBy('projects.updated_at','DESC');
                    break;

            }


        }else{
            // $projects =  $projects->orderBy('projects.updated_at','DESC');
            // prioritize records that are already on due
            $projects =  $projects->orderBy('projects.reviewer_due_date','ASC');

        }





        $projects = $projects->paginate($this->record_count);



        return view('livewire.admin.project.project-pending-update-list',[
            'projects' => $projects
        ]);
    }


 
}
