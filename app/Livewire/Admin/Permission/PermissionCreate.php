<?php

namespace App\Livewire\Admin\Permission;

use App\Events\PermissionCreated;
use Livewire\Component;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;

class PermissionCreate extends Component
{
    public string $name = '';
    public $module;


    public $modules = [];
    public function mount(){
        $this->modules = [
            'Dashboard',
            'User',
            'Permission',
            
            // global administrator is an override permission to everything

            'Role',
            'System Access', 
            /**
             * These permissions declare a user's high-level access, independent of roles:

                Permission Name	Description 
                system.access.global admin	    Grants system-wide global admin access
                system.access.admin	            Grants system-wide admin access
                system.access.reviewer	        Grants system-wide reviewer access
                system.access.user	            Grants standard system-wide user access
             * 
             * 
             */


            // only projects has override 


            'Project Own',  
            'Project Own Override',      // lets a role override projects that he does not own     
            'Project All Display',      
            'Project Override', // general override 

            'Project Review',
            'Project Review Override', // lets you review a project for a reviewer and override his review


            'Project Review Attachment',    
            'Project Review Attachment Override',    // lets you add atachment to review in a project for a reviewer and override his review


            // part of the project model
                // own nad 

                'Project Discussion',
                'Project Discussion Override',

                
                'Project Reviewer',
                'Project Reviewer Override',

                'Project Document',
                'Project Document Override',
 
                'Project Attachment', 
                'Project Attachment Override', 

            'Notifications',
            'Review',
            'Reviewer',
            'Timer',
            'Document Type',
            'Activity Logs',
            'Profile',
            'Setting',
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

        event(new PermissionCreated($permission,auth()->user()->id));

        ActivityLog::create([
            'log_action' => "Permission \"".$this->name."\" created ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Permission created successfully');
        return redirect()->route('permission.index');
    }





    public function render()
    {
        return view('livewire.admin.permission.permission-create');
    }
}
