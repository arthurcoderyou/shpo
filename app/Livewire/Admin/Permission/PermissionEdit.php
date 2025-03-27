<?php

namespace App\Livewire\Admin\Permission;

use Livewire\Component;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;

class PermissionEdit extends Component
{

    public string $name = '';
    public $module;

    public $permission_id;

    public $modules = [];
     

    public function mount($id){
        $permission = Permission::findOrFail($id);
        $this->name = $permission->name;
        $this->module = $permission->module;
        $this->permission_id = $permission->id;

        $this->modules = [
            'Dashboard',
            'User',
            'Permission',
            'Role',
            'Project',
            'Notifications',
            'Review',
            'Reviewer',
            'Timer',
            'Document Type',
            'Activity Logs',
            'Profile'
        ];

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
        $permission = Permission::findOrFail($this->permission_id,);

        $permission->name = $this->name;
        $permission->module = $this->module;
        $permission->updated_at = now();
        $permission->save();



        ActivityLog::create([
            'log_action' => "Permission \"".$this->name."\" updated ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Permission updated successfully');
        return redirect()->route('permission.index');
    }



    public function render()
    {
        return view('livewire.admin.permission.permission-edit');
    }
}
