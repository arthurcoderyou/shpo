<?php

namespace App\Livewire\Admin\Role;

use Livewire\Component;
use App\Events\RoleUpdated;

use App\Models\ActivityLog;
use App\Events\Role\RoleLogEvent;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Helpers\ActivityLogHelpers\RoleLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\SystemNotificationHelpers\RoleNotificationHelper;

class RoleEdit extends Component
{   

    // dynamic listener 
        protected $listeners = [
            // add custom listeners as well
            // 'systemUpdate'       => 'handleSystemUpdate',
            // 'SystemNotification' => 'handleSystemNotification',
        ];

        protected function getListeners(): array
        {
            return array_merge($this->listeners, [
                "roleEvent.{$this->role_id}" => 'loadData',
            ]);
        }
    // ./ dynamic listener 


    public string $name = '';
    public string $description = '';

    public $role_id;

    public function mount($id){
        $role = Role::findOrFail($id);

        $this->role_id = $role->id; 

        $this->loadData();

    }

    // load the default data of the form
    public function loadData(){ 

        $role = Role::findOrFail($this->role_id);

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


        // event(new RoleUpdated($role, auth()->user()->id));

        // ActivityLog::create([
        //     'log_action' => "Role \"".$this->name."\" updated ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = RoleLogHelper::getActivityMessage('updated', $role->id, $authId);

            // get the route
            $route = RoleLogHelper::getRoute('updated', $role->id);

            // log the event 
            event(new RoleLogEvent(
                $message ,
                $authId, 
                $role->id

            ));
    
            /** send system notifications to users */
                
                RoleNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications



        // Alert::success('Success','Role updated successfully');
        return 
        // redirect()->route('role.index')
                redirect($route)
            ->with('alert.success',$message)
        ;
    }


    public function render()
    {
        return view('livewire.admin.role.role-edit');
    }
}
