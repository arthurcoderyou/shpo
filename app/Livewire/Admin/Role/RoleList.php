<?php

namespace App\Livewire\Admin\Role;

use App\Helpers\SystemNotificationHelpers\RoleNotificationHelper;
use Livewire\Component;
use App\Events\RoleDeleted;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Events\Role\RoleLogEvent;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Helpers\ActivityLogHelpers\RoleLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;

class RoleList extends Component
{
     protected $listeners = [ 
        // 'roleCreated' => '$refresh',
        // 'roleUpdated' => '$refresh',
        // 'roleDeleted' => '$refresh',
        // 'systemEvent' => '$refresh',

        'roleEvent' => '$refresh',
    ];

    use WithFileUploads;
    use WithPagination;

    public $search = '';
    public $sort_by = '';

    public array $sorting_options = [
        "" => "Sort by",
        "Name A - Z" => "Name A - Z",
        "Name Z - A" => "Name Z - A",
        "Latest Added" => "Latest Added",
        "Oldest Added" => "Oldest Added",
        "Latest Updated" => "Latest Updated",
        "Oldest Updated" => "Oldest Updated", 
    ];
    public $record_count = 10;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;

    public $file;

    public $role_count;

    public function resetFilters(){
        $this->search = '';
        $this->sort_by = '';
    }


    // Method to delete selected records
    public function deleteSelected()
    {
        Role::whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records

        event(new RoleDeleted(null, auth()->user()->id));

        Alert::success('Success','Selected roles deleted successfully');
        return redirect()->route('role.index');
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
            $this->selected_records = Role::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){
        $role = Role::find($id);


        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = RoleLogHelper::getActivityMessage('deleted', $role->id, $authId);

            // get the route
            $route = RoleLogHelper::getRoute('deleted', $role->id);

            // log the event 
            event(new RoleLogEvent(
                $message ,
                $authId, 
                $role->id, // add this modelId connected to the current model instance for the listener to reload the same page same model instance record 

            ));
    
            /** send system notifications to users */
                
                RoleNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications




        $role->delete(); 

        // Alert::success('Success','Role deleted successfully');
        return redirect()->route('role.index')
            ->with('alert.success',$message)
        ;

    }


    public function render()
    {
        $roles = Role::select('roles.*');


        if (!empty($this->search)) {
            $search = $this->search;


            $roles = $roles->where(function($query) use ($search){
                $query =  $query->where('roles.name','LIKE','%'.$search.'%');
            });


            // dd($this->search);

        }

        // if (Auth::user()->can('system access global admin')) {
        //     // Show roles that HAVE the 'system access global admin' permission
        //     $roles = Role::whereHas('permissions', function ($query) {
        //         $query->where('name', 'system access global admin');
        //     });
        // }
        
        if (!Auth::user()->can('system access global admin')) {
            // Show roles that DO NOT HAVE the 'system access global admin' permission
            $roles = $roles->whereDoesntHave('permissions', function ($query) {
                $query->where('name', 'system access global admin');
            });
        }


        

        

        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Name A - Z":
                    $roles =  $roles->orderBy('roles.name','ASC');
                    break;

                case "Name Z - A":
                    $roles =  $roles->orderBy('roles.name','DESC');
                    break;


                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $roles =  $roles->orderBy('roles.created_at','DESC');
                    break;

                case "Oldest Added":
                    $roles =  $roles->orderBy('roles.created_at','ASC');
                    break;

                case "Latest Updated":
                    $roles =  $roles->orderBy('roles.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $roles =  $roles->orderBy('roles.updated_at','ASC');
                    break;
                default:
                    $roles =  $roles->orderBy('roles.updated_at','DESC');
                    break;

            }


        }else{
            $roles =  $roles->orderBy('roles.updated_at','DESC');

        }


        $this->role_count = $roles->count();


        $roles = $roles->paginate($this->record_count);


        return view('livewire.admin.role.role-list',[
            'roles' => $roles
        ]);
    }
}
