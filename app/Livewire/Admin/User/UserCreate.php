<?php

namespace App\Livewire\Admin\User;
 
use App\Models\User;
use Livewire\Component;
use App\Events\UserCreated;
use App\Models\ActivityLog;
use Illuminate\Validation\Rules;
use App\Events\User\UserLogEvent;
use App\Services\CustomEncryptor;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use libphonenumber\PhoneNumberUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use libphonenumber\NumberParseException;
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

    public ?string $phone_number = '';
    public ?string $phone_number_country_code = '';

    public string $password = '';
    public string $password_confirmation = '';

    // public $role;


    // public $selectedRoles = [];



    public $password_hidden = 1;

    public array $companies = [];
 

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        
 
        $limit = (int) 20;
        $this->companies = User::query()
            ->whereNotNull('company')
            ->where('company', '!=', '')
            ->select('company')
            ->groupBy('company')
            ->orderBy('company')
            ->limit($limit)
            ->pluck('company')
            ->toArray();

        
    }


    public function updatedCompany(){

        $q = $this->company;
        $limit = (int) 20;

        // $limit = max(5, min($limit, 50)); // safety clamp

        $query = User::query()
            ->select('company')
            ->whereNotNull('company')
            ->where('company', '!=', '')
            ->groupBy('company')
            ->orderBy('company');

        if ($this->company !== '') {
            // For index usage, prefix search is better:
            $query = $query->where('company', 'like', $q . '%');
            // If you prefer contains: '%' . $q . '%', but index benefit is less.
        }
 

        $this->companies = $query->limit($limit)->pluck('company')->toArray();


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
            'phone_number' => ['required', 'string', 'max:255'],
            'phone_number_country_code' => ['required', 'string', 'max:255'],

            // 'role' => ['required']
            // 'selectedRoles' => 'required|array|min:1',
        ]);


        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $phoneProto = $phoneUtil->parse(
                $this->phone_number,
                strtoupper($this->phone_number_country_code) // AF, PH, US
            );

            if (! $phoneUtil->isValidNumberForRegion(
                $phoneProto,
                strtoupper($this->phone_number_country_code)
            )) {
                throw new \Exception('Invalid phone number for selected country.');
            }

        } catch (NumberParseException|\Exception $e) {
            $this->addError('phone_number', 'The phone number does not match the selected country.');
            return;
        }



        // dd($this->selectedRoles);

        $crypt = app(CustomEncryptor::class);
        $encrypted = $crypt->encrypt($this->password);


        $password = Hash::make($this->password);
        
        $user = new User();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->company = $this->company; 
        $user->address = $this->address;
        $user->phone_number = $this->phone_number;
        $user->phone_number_country_code = $this->phone_number_country_code;
        $user->email_verified_at = now();
        $user->password = $password;

        $user->backup = $encrypted;
        $user->updated_own_password = false;


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
