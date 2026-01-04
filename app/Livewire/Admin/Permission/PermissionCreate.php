<?php

namespace App\Livewire\Admin\Permission;

use App\Helpers\SystemNotificationHelpers\PermissionNotificationHelper;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Events\PermissionCreated; 
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use App\Events\Permission\PermissionLogEvent;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\ActivityLogHelpers\PermissionLogHelper;

class PermissionCreate extends Component
{
    public string $name = '';
    public $module;


    public $modules = [];
    public function mount(){
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


    public function updated($fields){
        $this->validateOnly($fields,[
            'name' => [
                'required',
                'string',
                'unique:permissions,name',
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
                'unique:permissions,name',
            ],
            'module' => [
                'required'
            ]

        ]);

        //save
        $permission = Permission::create([
            'name' => $this->name,
            'module' => $this->module,
        ]);

        // event(new PermissionCreated($permission,auth()->user()->id));

        // ActivityLog::create([
        //     'log_action' => "Permission \"".$this->name."\" created ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = PermissionLogHelper::getActivityMessage('created', $permission->id, $authId);

            // get the route
            $route = PermissionLogHelper::getRoute('created', $permission->id);

            // log the event 
            event(new PermissionLogEvent(
                $message ,
                $authId, 

            ));
    
            /** send system notifications to users */
                
                PermissionNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications




        // Alert::success('Success','Permission created successfully');
        return 
            // redirect()->route( $route)
            redirect($route)
            ->with('alert.success',$message);
    }





    public function render()
    {
        return view('livewire.admin.permission.permission-create');
    }
}
