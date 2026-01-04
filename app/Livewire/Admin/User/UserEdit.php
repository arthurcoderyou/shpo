<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Events\User\UserLogEvent;
use Illuminate\Validation\Rules; 
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;  
use Illuminate\Support\Facades\Notification;
use App\Helpers\ActivityLogHelpers\UserLogHelper;
use App\Notifications\UserRoleUpdatedNotification;
use App\Notifications\UserRoleUpdatedNotificationDB;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\SystemNotificationHelpers\UserNotificationHelper;


class UserEdit extends Component
{

    // dynamic listener 
        protected $listeners = [
            // add custom listeners as well
            // 'systemUpdate'       => 'handleSystemUpdate',
            // 'SystemNotification' => 'handleSystemNotification',

            // 'userEvent' => 'loadData'
        ];

        protected function getListeners(): array
        {
            return array_merge($this->listeners, [
                "userEvent.{$this->user_id}" => 'loadData',
            ]);
        }
    // ./ dynamic listener

    public string $name = '';
    public string $email = '';
    public string $address = '';
    public string $company = '';
    public string $phone_number = '';
    public string $password = '';
    public string $password_confirmation = '';

    // public $selectedRoles = [];


    // public $role;

    public $password_hidden = 1;

    public $user_id;
 
    // public $role_empty = false;

    public function mount($id){
        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->loadData();
    }

    // load the default data of the form
    public function loadData(){ 

        $user = User::findOrFail($this->user_id);
         
        $this->name = $user->name;
        $this->email = $user->email;
        $this->address = $user->address ?? '';
        $this->company = $user->company ?? '';
        $this->phone_number = $user->phone_number ?? '';
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
            'address' => ['required', 'string'], 
            'company' => ['required', 'string'], 
            'phone_number' => ['required', 'string'],
             
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
            'address' => ['required', 'string'], 
            'company' => ['required', 'string'], 
            'phone_number' => ['required', 'string'],
            // 'role' => ['required'] 
        ]);

  

        $user = User::findOrFail($this->user_id);

        


        $user->name = $this->name;
        $user->email = $this->email;
        if(empty($user->email_verified_at)){
            $user->email_verified_at = now();
        }

        $user->address = $this->address;

        $user->company = $this->company;

        $user->phone_number = $this->phone_number;

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


        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = UserLogHelper::getActivityMessage('updated', $user->id, $authId);

            // get the route
            $route = UserLogHelper::getRoute('updated', $user->id);

            // log the event 
            event(new UserLogEvent(
                $message ,
                $authId, 
                $user->id,

            ));
    
            /** send system notifications to users */
                
                UserNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route , 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications

        // Alert::success('Success','User updated successfully');
        return redirect()->route('user.index')
            ->with('alert.success',$message);
    }


    public function render()
    {
        
        
        return view('livewire.admin.user.user-edit');
    }
}
