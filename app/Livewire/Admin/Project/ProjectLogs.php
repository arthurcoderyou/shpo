<?php

namespace App\Livewire\Admin\Project;

use App\Models\ProjectDocument;
use Livewire\Component;
use App\Models\ActivityLog;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectLogs extends Component
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
        'projectReviewCreated' => '$refresh', 
        'projectCreated' => '$refresh',
        'projectUpdated' => '$refresh',
        'projectDeleted' => '$refresh',
        'activitylogCreated' => '$refresh',
        'projectDocumentCreated' => '$refresh',
        'projectDocumentUpdated' => '$refresh',
        'projectDocumentDeleted' => '$refresh',
        'projectDiscussionAdded' => '$refresh',
        'projectDiscussionEdited' => '$refresh',
        'projectDiscussionReplied' => '$refresh',
        'projectDiscussionDeleted' => '$refresh',
    ]; 


    public $log_filter = "Project Logs";
 
    public $project_id;
    public $project_document_id;
    public function mount($project_id, $project_document_id = null){


        $this->project_id = $project_id;
        $this->project_document_id = $project_document_id;

         
        if(request()->routeIs('project.project_document') ){

            $this->log_filter = "Project Attachment Logs";
        }


    }

    public function getProjectDocumentsProperty(){
        return ProjectDocument::where('project_id',$this->project_id)->get();
    }

        

    // Method to delete selected records
    public function deleteSelected()
        {
        $user = Auth::user();
        // Check if the user has the role "DSI God Admin" OR the permission "activity log delete"
        if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('activity log delete'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('activity_logs.index');
                
                
        }



        ActivityLog::whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records

        Alert::success('Success','Selected Activity Logs deleted successfully');
        return redirect()->route('activity_logs.index');
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
            $this->selected_records = ActivityLog::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){


        $user = Auth::user();
        // Check if the user has the role "DSI God Admin" OR the permission "activity log delete"
        if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('activity log delete'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('activity_logs.index');
               
             
        }



        $activity_logs = ActivityLog::find($id);


        $activity_logs->delete();


        Alert::success('Success','Activity Logs deleted successfully');
        return redirect()->route('activity_logs.index');

    }




    public function getActivityLogsProperty()
    {


        $activity_logs = ActivityLog::select('activity_logs.*')
            ->where('project_id','=',$this->project_id);

            if(!empty($this->project_document_id)){
                $activity_logs = $activity_logs->where('project_document_id','=',$this->project_document_id);
            }

            if (!empty($this->log_filter) && $this->log_filter === "Project Reviewer Logs" && !empty($this->review_status)) {
                $activity_logs = $activity_logs->whereHas('review', function ($query) {
                    $query->where('review_status', $this->review_status);
                });
            }

            // dd($activity_logs->get());


            switch ($this->log_filter) {
                case 'Project Logs':
                    $activity_logs = $activity_logs
                        ->whereNull('project_document_id')
                        ->whereNull('project_document_attachment_id')
                        ->whereNull('project_discussion_id')
                        ->whereNull('project_review_id')
                        ->whereNull('project_reviewer_id')
                        ->whereNull('project_subscriber_id');
                    break;
        
                case 'Discussion Logs':
                    $activity_logs = $activity_logs->whereNotNull('project_discussion_id');
                    break;
        
                case 'Project Reviewer Logs':
                    $activity_logs = $activity_logs->whereNotNull('project_reviewer_id');
                    break;
        
                case 'Project Document Logs':
                    $activity_logs = $activity_logs->whereNotNull('project_document_id');
                    break;
        
                case 'Project Attachment Logs':
                    $activity_logs = $activity_logs->whereNotNull('project_document_attachment_id');
                    break;

                // case 'Project Attachment Logs':
                //     $activity_logs = $activity_logs->whereNotNull('project_document_attachment_id');
                //     break;
            }

        // dd($this->project_id);
        // // Find the role
        // $role = Role::where('name', 'Admin')->first();

        // if ($role) {
        //     // Get user IDs only if role exists
        //     $dsiGodAdminUserIds = $role->users()->pluck('id');
        // } else {
        //     // Set empty array if role doesn't exist
        //     $dsiGodAdminUserIds = [];
        // }


        // if(!Auth::user()->hasRole('DSI God Admin')){
        //     $activity_logs =  $activity_logs->where('activity_logs.created_by','=',Auth::user()->id);
        // }

        // Adjust the query
        // if(Auth::user()->hasRole('DSI God Admin'))
        // {


        // }
        // elseif (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {
        //     $activity_logs = $activity_logs->where('activity_logs.created_by', '=', Auth::user()->id);
        // }elseif(!Auth::user()->hasRole('Admin')){
        //     $activity_logs = $activity_logs->whereNotIn('activity_logs.created_by', $dsiGodAdminUserIds);
        // } 
         
            


        if (!empty($this->search)) {
            $search = $this->search;


            $activity_logs = $activity_logs->where(function($query) use ($search){
                $query =  $query->where('activity_logs.log_action','LIKE','%'.$search.'%')
                    ->orWhere('activity_logs.log_username','LIKE','%'.$search.'%');
                    
            })
            ->orWhere(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            });

            
            // Adjust the query
            // if (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {
            //     $activity_logs = $activity_logs->where('activity_logs.created_by', '=', Auth::user()->id);
            // }elseif(!Auth::user()->hasRole('Admin')){
            //     $activity_logs = $activity_logs->whereNotIn('activity_logs.created_by', $dsiGodAdminUserIds);
            // } 

        }

    
        

        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){

            switch($this->sort_by){
                case "Log Activity A - Z":
                    $activity_logs =  $activity_logs->orderBy('activity_logs.log_action','ASC');
                    break;

                case "Log Activity Z - A":
                    $activity_logs =  $activity_logs->orderBy('activity_logs.log_action','DESC');
                    break;


                case "User A - Z":
                    $activity_logs =  $activity_logs->orderBy('activity_logs.log_username','ASC');
                    break;

                case "User Z - A":
                    $activity_logs =  $activity_logs->orderBy('activity_logs.log_username','DESC');
                    break;

                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $activity_logs =  $activity_logs->orderBy('activity_logs.created_at','DESC');
                    break;

                case "Oldest Added":
                    $activity_logs =  $activity_logs->orderBy('activity_logs.created_at','ASC');
                    break;

                case "Latest Updated":
                    $activity_logs =  $activity_logs->orderBy('activity_logs.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $activity_logs =  $activity_logs->orderBy('activity_logs.updated_at','ASC');
                    break;
                default:
                    $activity_logs =  $activity_logs->orderBy('activity_logs.updated_at','DESC');
                    break;

            }


        }else{
            $activity_logs =  $activity_logs->orderBy('activity_logs.created_at','DESC');

        }





        $activity_logs = $activity_logs->paginate($this->record_count);
        return  $activity_logs;

        // return view('livewire.activity-logs.activity-logs-list',[
        //     'activity_logs' => $activity_logs
        // ]);


    }


    public function render()
    {   

        // dd(count($this->activityLogs));
        return view('livewire.admin.project.project-logs',[
            'activity_logs' => $this->activityLogs,
            'project_documents' => $this->projectDocuments
        ]);
    }
}
