<?php

namespace App\Livewire\Admin\Role;

use Livewire\Component;

use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class RoleEdit extends Component
{

    public string $name = '';

    public $role_id;

    public function mount($id){
        $role = Role::findOrFail($id);

        $this->role_id = $role->id;
        $this->name = $role->name;

    }

    public function updated($fields){
        $this->validateOnly($fields,[
            'name' => [
                'required',
                'string',
                'unique:roles,name,'.$this->role_id,
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
                'unique:roles,name,'.$this->role_id,
            ],

        ]);

        //save
        $role = Role::findOrFail($this->role_id);
        $role->name = $this->name;
        $role->updated_at = now();
        $role->save();

        ActivityLog::create([
            'log_action' => "Role \"".$this->name."\" updated ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Role updated successfully');
        return redirect()->route('role.index');
    }


    public function render()
    {
        return view('livewire.admin.role.role-edit');
    }
}
