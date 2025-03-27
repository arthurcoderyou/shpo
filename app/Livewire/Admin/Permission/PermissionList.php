<?php

namespace App\Livewire\Admin\Permission;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;

class PermissionList extends Component
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
        Permission::whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records

        Alert::success('Success','Selected permissions deleted successfully');
        return redirect()->route('permission.index');
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
            $this->selected_records = Permission::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){
        $permission = Permission::find($id);


        $permission->delete();


        Alert::success('Success','Permission deleted successfully');
        return redirect()->route('permission.index');

    }




    public function render()
    {


        $permissions = Permission::select('permissions.*');


        if (!empty($this->search)) {
            $search = $this->search;


            $permissions = $permissions->where(function($query) use ($search){
                $query =  $query->where('permissions.name','LIKE','%'.$search.'%');
            });


        }

        /*
            // Find the role
            $role = Role::where('name', 'DSI God Admin')->first();

            if ($role) {
                // Get user IDs only if role exists
                $dsiGodAdminUserIds = $role->permissions()->pluck('id');
            } else {
                // Set empty array if role doesn't exist
                $dsiGodAdminUserIds = [];
            }


            // if(!Auth::user()->hasRole('DSI God Admin')){
            //     $permissions =  $permissions->where('permissions.created_by','=',Auth::user()->id);
            // }

            // Adjust the query
            if (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {
                $permissions = $permissions->where('permissions.created_by', '=', Auth::user()->id);
            }elseif(Auth::user()->hasRole('Admin')){
                $permissions = $permissions->whereNotIn('permissions.created_by', $dsiGodAdminUserIds);
            } else {

            }
        */


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Name A - Z":
                    $permissions =  $permissions->orderBy('permissions.name','ASC');
                    break;

                case "Name Z - A":
                    $permissions =  $permissions->orderBy('permissions.name','DESC');
                    break;

                case "Module A - Z":
                    $permissions =  $permissions->orderBy('permissions.module','ASC');
                    break;
 
                case "Module Z - A":
                    $permissions =  $permissions->orderBy('permissions.module','DESC');
                    break;


                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $permissions =  $permissions->orderBy('permissions.created_at','DESC');
                    break;

                case "Oldest Added":
                    $permissions =  $permissions->orderBy('permissions.created_at','ASC');
                    break;

                case "Latest Updated":
                    $permissions =  $permissions->orderBy('permissions.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $permissions =  $permissions->orderBy('permissions.updated_at','ASC');
                    break;
                default:
                    $permissions =  $permissions->orderBy('permissions.updated_at','DESC');
                    break;

            }


        }else{
            $permissions =  $permissions->orderBy('permissions.updated_at','DESC');

        }





        $permissions = $permissions->paginate($this->record_count);

        
        return view('livewire.admin.permission.permission-list',[
            'permissions' => $permissions
        ]);
    }
}
