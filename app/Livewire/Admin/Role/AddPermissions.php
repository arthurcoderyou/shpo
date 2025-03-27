<?php

namespace App\Livewire\Admin\Role;

use Livewire\Component;
use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;

class AddPermissions extends Component
{


    public $selected_permissions = [];
    public $role_id;
    public $role;

    public function mount($role_id){

        $this->role_id = $role_id;
        $this->role = Role::find($role_id);

        // Get the role's current permissions and set them as the selected permissions
        $this->selected_permissions = $this->role->permissions->pluck('id')->toArray();

    }


    // Method to select/deselect all checkboxes
    public function selectAll($value)
    {
        if ($value) {
            // Select all within the module
            $this->selected_permissions = Permission::pluck('id')->toArray();
        }else{
            $this->selected_permissions = [];
        }
    }

    public function updated($fields){

        $this->validateOnly($fields,[
            'selected_permissions' => 'required|array|min:1', // Ensure it's an array with at least one selection
        ]);

    }

    public function save(){
        $this->validate([
            'selected_permissions' => 'required|array|min:1', // Ensure it's an array with at least one selection
        ]);

        $sync_permissions = [];
        if(!empty($this->selected_permissions)){
            $sync_permissions = Permission::whereIn('id',$this->selected_permissions)->get();
        }

        // Sync permissions (this will remove permissions that are not selected and add the new ones)
        $this->role->syncPermissions($sync_permissions);



        ActivityLog::create([
            'log_action' => "Role \"".$this->role->name."\" has updated permisssions ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Role permissions updated successfully.');
        return redirect()->route('role.add_permissions',['role' => $this->role_id]);




    }



    public function render()
    {

        $module_permissions = Permission::all();

        if (!Auth::user()->hasRole('DSI God Admin')) {
            $module_permissions = $module_permissions->reject(function ($permission) {
                return $permission->module === 'Permission';
            });
        }

        $module_permissions = $module_permissions->groupBy('module');

        return view('livewire.admin.role.add-permissions',[
            'module_permissions' => $module_permissions
        ]);
    }
}
