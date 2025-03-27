<?php

namespace App\Livewire\Admin\Permission;

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
        Permission::create([
            'name' => $this->name,
            'module' => $this->module,
        ]);



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
