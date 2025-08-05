<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Component;
use App\Models\ActivityLog;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;  
use Illuminate\Support\Facades\Notification;
use App\Notifications\UserRoleUpdatedNotification;
use App\Notifications\UserRoleUpdatedNotificationDB;


class UserEdit extends Component
{

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // public $selectedRoles = [];


    // public $role;

    public $password_hidden = 1;

    public $user_id;
 
    // public $role_empty = false;

    public function mount($id){
        $user = User::findOrFail($id);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->user_id = $user->id;
         
    }

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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',id,'.$this->user_id],
            // 'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
             
        ]);

    }


    /**
     * Handle an incoming registration request.
     */
    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',id,'.$this->user_id],
            // 'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            // 'role' => ['required'] 
        ]);

  

        $user = User::findOrFail($this->user_id);

        


        $user->name = $this->name;
        $user->email = $this->email;
        if(empty($user->email_verified_at)){
            $user->email_verified_at = now();
        }

        if(!empty($this->password)){

            $this->validate([
                'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            ]);


            $password = Hash::make($this->password);
            $user->password = $password;
        }

        // if(!empty($this->role)){
        //     //add role
        //     $role = Role::find($this->role);
        //     $user->assignRole($role);
        // }

        // if (!empty($this->role)) {
        //     $role = Role::find($this->role);
        //     if ($role) {
        //         // Remove previous roles before assigning a new one
        //         $user->syncRoles([$role->name]);
        //     }
        // }
        

        $user->updated_at = now();
        $user->updated_by = Auth::user()->id;

        $user->save();

 

        Alert::success('Success','User updated successfully');
        return redirect()->route('user.index');
    }


    public function render()
    {
        
        
        return view('livewire.admin.user.user-edit');
    }
}
