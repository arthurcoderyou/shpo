<?php

namespace App\Livewire\Admin\Role;

use App\Events\Role\RoleLogEvent;
use App\Helpers\SystemNotificationHelpers\RoleNotificationHelper;
use App\Models\User;
use Livewire\Component;
use App\Events\RoleCreated;
use App\Models\ActivityLog;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Helpers\ActivityLogHelpers\RoleLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;

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

          
        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = RoleLogHelper::getActivityMessage('created', $role->id, $authId);

            // get the route
            $route = RoleLogHelper::getRoute('created', $role->id);

            // log the event 
            event(new RoleLogEvent(
                $message ,
                $authId, 

            ));
    
            /** send system notifications to users */
                
                RoleNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications


        // Alert::success('Success','Role created successfully');
        return 
            // redirect()->route('role.index')
            redirect($route)
            ->with('alert.success',$message);
            ;
    }



    public function render()
    {
        return view('livewire.admin.role.role-create');
    }
}
