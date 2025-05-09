<?php

namespace App\Livewire\Admin\Role;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class RoleList extends Component
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
        Role::whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records

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


        $role->delete();


        Alert::success('Success','Role deleted successfully');
        return redirect()->route('role.index');

    }


    public function render()
    {
        $roles = Role::select('roles.*');


        if (!empty($this->search)) {
            $search = $this->search;


            $roles = $roles->where(function($query) use ($search){
                $query =  $query->where('roles.name','LIKE','%'.$search.'%');
            });


        }

        /*
            // Find the role
            $role = Role::where('name', 'DSI God Admin')->first();

            if ($role) {
                // Get user IDs only if role exists
                $dsiGodAdminUserIds = $role->roles()->pluck('id');
            } else {
                // Set empty array if role doesn't exist
                $dsiGodAdminUserIds = [];
            }


            // if(!Auth::user()->hasRole('DSI God Admin')){
            //     $roles =  $roles->where('roles.created_by','=',Auth::user()->id);
            // }

            // Adjust the query
            if (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {
                $roles = $roles->where('roles.created_by', '=', Auth::user()->id);
            }elseif(Auth::user()->hasRole('Admin')){
                $roles = $roles->whereNotIn('roles.created_by', $dsiGodAdminUserIds);
            } else {

            }
        */

        

        

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





        $roles = $roles->paginate($this->record_count);


        return view('livewire.admin.role.role-list',[
            'roles' => $roles
        ]);
    }
}
