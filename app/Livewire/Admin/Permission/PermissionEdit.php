<?php

namespace App\Livewire\Admin\Permission;

use Livewire\Component;
use App\Models\ActivityLog;
use App\Events\PermissionUpdated;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use App\Events\Permission\PermissionLogEvent;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\ActivityLogHelpers\PermissionLogHelper;
use App\Helpers\SystemNotificationHelpers\PermissionNotificationHelper;

class PermissionEdit extends Component
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
                "permissionEvent.{$this->permission_id}" => 'loadData',
            ]);
        }
    // ./ dynamic listener 

    public string $name = '';
    public $module;

    public $permission_id;

    public $modules = [];
      
    public function mount($id){
        $permission = Permission::findOrFail($id);
        $this->permission_id = $permission->id;

        $this->loadData(); // load the default data 

        $this->modules = [ 
            'Dashboard' => 'Dashboard',
            'User' => 'User',
            'Permission' => 'Permission',

            // Global administrator override
            'Role' => 'Role',
            'System Access' => 'System Access',

            // Project ownership & overrides
            'Project Own' => 'Project Own',
            'Project Own Override' => 'Project Own Override',
            'Project All Display' => 'Project All Display',
            'Project Override' => 'Project Override',

            // Project review
            'Project Review' => 'Project Review',
            'Project Review Override' => 'Project Review Override',

            // Review attachments
            'Project Review Attachment' => 'Project Review Attachment',
            'Project Review Attachment Override' => 'Project Review Attachment Override',

            // Project sub-modules
            'Project Discussion' => 'Project Discussion',
            'Project Discussion Override' => 'Project Discussion Override',

            'Project Reviewer' => 'Project Reviewer',
            'Project Reviewer Override' => 'Project Reviewer Override',

            'Project Document' => 'Project Document',
            'Project Document Override' => 'Project Document Override',

            'Project Attachment' => 'Project Attachment',
            'Project Attachment Override' => 'Project Attachment Override',

            // System modules
            'Notifications' => 'Notifications',
            'Review' => 'Review',
            'Reviewer' => 'Reviewer',
            'Timer' => 'Timer',
            'Document Type' => 'Document Type',
            'Activity Logs' => 'Activity Logs',
            'Profile' => 'Profile',
            'Setting' => 'Setting',
        ];

    }

     

    // load the default data of the form
    public function loadData(){ 

        $permission = Permission::findOrFail($this->permission_id);
 

        $this->name = $permission->name;
        $this->module = $permission->module;
        $this->permission_id = $permission->id;
 
    }



    public function updated($fields){
        $this->validateOnly($fields,[
            'name' => [
                'required',
                'string', 
                'unique:permissions,name,'.$this->permission_id,
            ],
            'module' => [
                'required'
            ]

        ]);
    }


    /**
     * Handle an incoming registration request.
     */
    public function save()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name,'.$this->permission_id,
            ],
            'module' => [
                'required'
            ]

        ]);

        //save
        $permission = Permission::findOrFail($this->permission_id);
        


        $permission->name = $this->name;
        $permission->module = $this->module;
        $permission->updated_at = now();
        $permission->save();

        // event(new PermissionUpdated($permission,auth()->user()->id));

        // ActivityLog::create([
        //     'log_action' => "Permission \"".$this->name."\" updated ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        // Alert::success('Success','Permission updated successfully');


        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = PermissionLogHelper::getActivityMessage('updated', $permission->id, $authId);

            // get the route
            $route = PermissionLogHelper::getRoute('updated', $permission->id);

            // log the event 
            event(new PermissionLogEvent(
                $message ,
                $authId, 
                $permission->id, // add this modelId connected to the current model instance for the listener to reload the same page same model instance record 

            ));
    
            /** send system notifications to users */
                
                PermissionNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications

        return 
            // redirect()->route('permission.index')
            redirect($route)
            ->with('alert.success',$message);
    }



    public function render()
    {
        return view('livewire.admin.permission.permission-edit');
    }
}
