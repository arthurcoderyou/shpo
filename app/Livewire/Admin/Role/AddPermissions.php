<?php

namespace App\Livewire\Admin\Role;

use Livewire\Component;
use App\Events\RoleUpdated;
use App\Models\ActivityLog;
use App\Events\Role\RoleLogEvent;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use App\Helpers\ActivityLogHelpers\RoleLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\SystemNotificationHelpers\RoleNotificationHelper;

class AddPermissions extends Component
{
    // dynamic listener 
        protected $listeners = [
            // add custom listeners as well
            // 'systemUpdate'       => 'handleSystemUpdate',
            // 'SystemNotification' => 'handleSystemNotification',
        ];

        protected function getListeners(): array
        {
            return array_merge($this->listeners, [
                "roleEvent.{$this->role_id}" => 'loadData',
            ]);
        }
    // ./ dynamic listener 

    public $selected_permissions = [];
    public $role_id;
    public $role;

    public function mount($role_id){

        $this->role_id = $role_id;
        $this->loadData();
    }


    // load the default data of the form
    public function loadData(){ 

        $this->role = Role::find($this->role_id);

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

        $this->role->updated_at = now();
        $this->role->save();

        // event(new RoleUpdated( $this->role, auth()->user()->id));

        // ActivityLog::create([
        //     'log_action' => "Role \"".$this->role->name."\" has updated permisssions ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        $role = Role::find($this->role->id);


        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = RoleLogHelper::getActivityMessage('role-permissions-updated', $role->id, $authId);

            // get the route
            $route = RoleLogHelper::getRoute('role-permissions-updated', $role->id);

            // log the event 
            event(new RoleLogEvent(
                $message ,
                $authId, 
                $role->id

            ));
    
            /** send system notifications to users */
                
                RoleNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications





        // Alert::success('Success','Role permissions updated successfully.');
        return redirect()->route('role.add_permissions',['role' => $this->role_id])
            ->with('alert.success', $message)
        ;




    }



    public function render()
    {

        $module_permissions = Permission::orderBy('module', 'asc')->get();

        if (!Auth::user()->can('system access global admin')) {
            $module_permissions = $module_permissions->reject(function ($permission) {
                return $permission->module === 'Permission';
            });

            $module_permissions = $module_permissions->reject(function ($permission) {
                return $permission->module === 'System Access';
            });

             $module_permissions = $module_permissions->reject(function ($permission) {
                return $permission->module === 'Setting';
            });
        }

        $module_permissions = $module_permissions->groupBy('module');


        return view('livewire.admin.role.add-permissions',[
            'module_permissions' => $module_permissions
        ]);
    }
}
