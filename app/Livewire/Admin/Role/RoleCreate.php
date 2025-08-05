<?php

namespace App\Livewire\Admin\Role;

use App\Events\RoleCreated;
use App\Models\User;
use Livewire\Component;
use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class RoleCreate extends Component
{

    public string $name = '';
    public string $description = '';


    public function updated($fields){
        $this->validateOnly($fields,[
            'name' => [
                'required',
                'string',
                'unique:roles,name',
            ],

            'description' => [
                'nullable',
                'string', 
            ],

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
                'unique:roles,name',
            ],
            'description' => [
                'nullable',
                'string', 
            ],

        ]);

        //save
        $role = Role::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        event(new RoleCreated($role, auth()->user()->id));

        ActivityLog::create([
            'log_action' => "Role \"".$this->name."\" created ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Role created successfully');
        return redirect()->route('role.index');
    }



    public function render()
    {
        return view('livewire.admin.role.role-create');
    }
}
