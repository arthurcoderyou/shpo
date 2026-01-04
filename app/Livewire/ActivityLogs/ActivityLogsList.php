<?php

namespace App\Livewire\ActivityLogs;

use App\Models\User;
use Livewire\Component;
use App\Models\ActivityLog;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ActivityLogsList extends Component
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
        'activitylogCreated' => '$refresh',
        'systemEvent' => '$refresh',
    ]; 



     // Method to delete selected records
     public function deleteSelected()
     {
        $user = Auth::user();
        // Check if the user has the role "system access global admin" OR the permission "activity log delete"
        if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('activity log delete'))) {
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
        // Check if the user has the role "system access global admin" OR the permission "activity log delete"
        if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('activity log delete'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('activity_logs.index');
               
             
        }



        $activity_logs = ActivityLog::find($id);


        $activity_logs->delete();


        Alert::success('Success','Activity Logs deleted successfully');
        return redirect()->route('activity_logs.index');

    }




    public function render()
    {


        $activity_logs = ActivityLog::select('activity_logs.*');


        // All users who effectively have the "system access global admin" permission (directly OR via roles)
        $globalAdminUserIds = User::permission('system access global admin')->pluck('id');

        // Visibility rules
        // for global admin
        if (Auth::user()->can('system access global admin')) {
            // See everything (no filters)
        }
        // for admin
        elseif (Auth::user()->can('system access admin')) {
            // If the user lacks "system access admin", hide logs created by admin users
            $activity_logs->whereNotIn('activity_logs.created_by', $globalAdminUserIds);
        }
        // for users and reviewers
        elseif (!Auth::user()->can('system access global admin') && !Auth::user()->can('system access admin')) {
            // Regular users: only their own logs
            $activity_logs->where('activity_logs.created_by', Auth::id());
        }
         
        if (!empty($this->search)) {
            $search = $this->search;


            $activity_logs = $activity_logs->orWhere(function($query) use ($search){
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
            if (!Auth::user()->can('system access global admin') && !Auth::user()->can('system access admin')) {
                $activity_logs = $activity_logs->where('activity_logs.created_by', '=', Auth::user()->id);
            }elseif(!Auth::user()->can('system access admin')){
                $activity_logs = $activity_logs->whereNotIn('activity_logs.created_by', $globalAdminUserIds);
            } 

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


        return view('livewire.activity-logs.activity-logs-list',[
            'activity_logs' => $activity_logs
        ]);


    }
}
