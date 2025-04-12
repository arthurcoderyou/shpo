<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class UserList extends Component
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

    public $selected_role;
    public $roles;

    public $role_request;

    public $user_count;

    public function mount(){
        $this->selected_role = request()->query('selected_role', ''); // Default to empty string if not set
        $this->role_request = request()->query('role_request', ''); // Default to empty string if not set

        $this->roles = Role::select('roles.*');

        if(!Auth::user()->hasRole('DSI God Admin')){
            $this->roles = $this->roles->whereNot('name', 'DSI God Admin');
                
        } 
        $this->roles = $this->roles->get();

    }



    public function deleteSelected()
    {
        // Fetch all projects created by the selected users
        $projects = Project::whereIn('created_by', $this->selected_records)->get();
    
        foreach ($projects as $project) {
            // Delete related project details
            $project->project_subscribers()->delete();
            $project->project_documents()->delete();
            $project->attachments()->delete();
            $project->project_reviewers()->delete();
            $project->project_reviews()->delete();
    
            // Finally, delete the project itself
            $project->delete();
        }
    
        // Delete the selected users
        User::whereIn('id', $this->selected_records)->delete();
    
        // Clear selected records
        $this->selected_records = [];
    
        // Show success message
        Alert::success('Success', 'Selected users and their projects deleted successfully');
        return redirect()->route('user.index');
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
            $this->selected_records = User::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){
        $user = User::find($id);
        // Fetch all projects created by the selected users
        $projects = Project::where('created_by', $user->id)->get();
    
        foreach ($projects as $project) {
            // Delete related project details
            $project->project_subscribers()->delete();
            $project->project_documents()->delete();
            $project->attachments()->delete();
            $project->project_reviewers()->delete();
            $project->project_reviews()->delete();
    
            // Finally, delete the project itself
            $project->delete();
        }

        $user->delete();    
        $user->notifications()->delete();

        Alert::success('Success','User deleted successfully');
        return redirect()->route('user.index');

    }



    public function render()
    {

        $users = User::select('users.*');


        if (!empty($this->search)) {
            $search = $this->search;


            $users = $users->where(function($query) use ($search){
                $query =  $query->where('users.name','LIKE','%'.$search.'%')
                    ->orWhere('users.email','LIKE','%'.$search.'%');
            });


        }


        $users = $users->when($this->selected_role, function ($query) {
            // $query->whereHas('roles', function ($roleQuery) {
            //     $roleQuery->where('id', $this->selected_role);
            // });

            if ($this->selected_role === 'no_role') {
                // Users without roles
                $query->whereDoesntHave('roles');
            } else {
                // Users with the selected role
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('id', $this->selected_role);
                });
            }
        });


        if(!empty($this->role_request)){
            $users = $users->where('role_request',$this->role_request); 

        }
        
            // Find the role
            $role = Role::where('name', 'DSI God Admin')->first();

            if ($role) {
                // Get user IDs only if role exists
                $dsiGodAdminUserIds = $role->users()->pluck('id');
            } else {
                // Set empty array if role doesn't exist
                $dsiGodAdminUserIds = [];
            }


            // if(!Auth::user()->hasRole('DSI God Admin')){
            //     $users =  $users->where('users.created_by','=',Auth::user()->id);
            // }

            // Adjust the query
            // if (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {
            //     $users = $users->where('users.created_by', '=', Auth::user()->id);
            // }else
            
            if(!Auth::user()->hasRole('DSI God Admin')){
                $users = $users->whereNotIn('users.id', $dsiGodAdminUserIds);
            } 
            
        


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Name A - Z":
                    $users =  $users->orderBy('users.name','ASC');
                    break;

                case "Name Z - A":
                    $users =  $users->orderBy('users.name','DESC');
                    break;

                case "Email A - Z":
                    $users =  $users->orderBy('users.email','ASC');
                    break;

                case "Email Z - A":
                    $users =  $users->orderBy('users.email','DESC');
                    break;
                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $users =  $users->orderBy('users.created_at','DESC');
                    break;

                case "Oldest Added":
                    $users =  $users->orderBy('users.created_at','ASC');
                    break;

                case "Latest Updated":
                    $users =  $users->orderBy('users.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $users =  $users->orderBy('users.updated_at','ASC');
                    break;
                default:
                    $users =  $users->orderBy('users.updated_at','DESC');
                    break;

            }


        }else{
            $users =  $users->orderBy('users.updated_at','DESC');

        }



        $this->user_count = $users->count();

        $users = $users->paginate($this->record_count);


        return view('livewire.admin.user.user-list',[
            'users' => $users
        ]);




    }
}
