<?php

namespace App\Livewire\Admin\Role;

use App\Events\RoleUpdated;
use Livewire\Component;

use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class RoleEdit extends Component
{

    public string $name = '';
    public string $description = '';

    public $role_id;

    public function mount($id){
        $role = Role::findOrFail($id);

        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->description = $role->description ?? '';

    }

    public function updated($fields){
        $this->validateOnly($fields,[
            'name' => [
                'required',
                'string',
                'unique:roles,name,'.$this->role_id,
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
                'unique:roles,name,'.$this->role_id,
            ],
            'description' => [
                'nullable',
                'string', 
            ],

        ]);

        //save
        $role = Role::findOrFail($this->role_id);
        $role->name = $this->name;
        $role->description = $this->description;  
        $role->updated_at = now();
        $role->save();


        event(new RoleUpdated($role, auth()->user()->id));

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
