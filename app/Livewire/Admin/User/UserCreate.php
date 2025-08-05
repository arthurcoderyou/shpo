<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Component;
use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rules;

class UserCreate extends Component
{

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // public $role;


    // public $selectedRoles = [];



    public $password_hidden = 1;
 


    //show password to text toggle
    public function show_hide_password($status){
        // dd($status);
        if($this->password_hidden == 0){
            $this->password_hidden = 1;
        }elseif($this->password_hidden == 1){
            $this->password_hidden = 0;
        }
    }


    public function updated($fields){

        $this->validateOnly($fields,[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            // 'role' => ['required'],
            // 'selectedRoles' => 'required|array|min:1',
        ]);

    }


    /**
     * Handle an incoming registration request.
     */
    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            // 'role' => ['required']
            // 'selectedRoles' => 'required|array|min:1',
        ]);


        // dd($this->selectedRoles);

        $password = Hash::make($this->password);


        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->email_verified_at = now();
        $user->password = $password;
        $user->created_by = Auth::user()->id;
        $user->updated_by = Auth::user()->id;

        $user->save();

        // if(!empty($this->role)){
        //     //add role
        //     $role = Role::find($this->role);
        //     $user->assignRole($role);
        // }

        // if(!empty($this->role)){
        //     //add role
        //     $role = Role::find($this->role);
        //     $user->assignRole($role);
        // }

        // $user->syncRoles($this->selectedRoles);

        // $roles = Role::whereIn('id', $this->selectedRoles)->get();

        // $user->syncRoles($roles);

         

        Alert::success('Success','New User created successfully');
        return redirect()->route('user.index');
    }



    public function render()
    {
        //  $roles = Role::query();

        // if (!Auth::user()->can('system access global admin')) {
        //     // DO not show roles that do not HAVE the 'system access global admin' permission
        //     $roles = Role::whereHas('permissions', function ($query) {
        //         $query->whereNot('name', 'system access global admin');
        //     });
        // }  
 
        
        // $roles  = $roles->orderBy('name','asc')->get();
        return view('livewire.admin.user.user-create',
            // compact('roles')
        );
    }
}
