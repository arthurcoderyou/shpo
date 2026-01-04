<?php

namespace App\Livewire\Admin\User;
 
use App\Models\User;
use Livewire\Component;
use App\Events\UserCreated;
use App\Models\ActivityLog;
use Illuminate\Validation\Rules;
use App\Events\User\UserLogEvent;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use App\Helpers\ActivityLogHelpers\UserLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\SystemNotificationHelpers\UserNotificationHelper;

class UserCreate extends Component
{

    public string $name = '';
    public string $email = '';

    public string $address = '';

    public string $company = '';

    public string $phone_number = '';
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
            'address' => ['required', 'string'], 
            'company' => ['required', 'string'], 
            'phone_number' => ['required', 'string'],

            


            // 'role' => ['required'],
            // 'selectedRoles' => 'required|array|min:1',
        ]);

    }


    /**
     * Handle an incoming registration request.
     */
    public function save()
    {
        $success_message = "New user created succesfully"; // success message that will be sent to the log and system notifications for successfull transactions

        
         
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'address' => ['required', 'string'], 
            'company' => ['required', 'string'], 
            'phone_number' => ['required', 'string'],

            // 'role' => ['required']
            // 'selectedRoles' => 'required|array|min:1',
        ]);


        // dd($this->selectedRoles);

        $password = Hash::make($this->password);
        
        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->company = $this->company; 
        $user->address = $this->address;
        $user->phone_number = $this->phone_number;
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

         

        // Alert::success('Success','New User created successfully');
  

        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = UserLogHelper::getActivityMessage('created', $user->id, $authId);

            // get the route
            $route = UserLogHelper::getRoute('created', $user->id);

            // log the event 
            event(new UserLogEvent(
                $message ,
                $authId, 

            ));
    
            /** send system notifications to users */
                
                UserNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications

        return 
            // redirect()->route('user.index')
            redirect($route)
            ->with('alert.success',$message);
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
