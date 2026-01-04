<?php

namespace App\Livewire\Admin\User;
 
use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;
use Illuminate\Validation\Rules;
use App\Events\User\UserLogEvent;
use Spatie\Permission\Models\Role;
use App\Events\User\NewUserVerified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;  
use Illuminate\Support\Facades\Notification;
use App\Helpers\ActivityLogHelpers\UserLogHelper;
use App\Notifications\UserRoleUpdatedNotification;
use App\Notifications\UserRoleUpdatedNotificationDB;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\SystemNotificationHelpers\UserNotificationHelper;

class UserRoleEdit extends Component
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
    public string $password = '';
    public string $password_confirmation = '';

    public $selectedRoles = [];


    // public $role;

    public $password_hidden = 1;

    public $user_id;
 
    public $role_empty = false;

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
        // $this->role = !empty($user->roles->first()->id) ? $user->roles->first()->id : null;

        $this->role_empty = $user->roles->isEmpty();

        $this->selectedRoles = $user->roles->pluck('id')->toArray();
         
 
    }


    
    public function updated($fields){

        $this->validateOnly($fields,[ 
            'selectedRoles' => 'required|array|min:1',
        ]);

    }



     private function hasConnectedRecords(User $user, $role_to_check = "user"): bool
    {

        if($role_to_check == "user"){
            // Check if the user has created any projects
            $projects = Project::where('created_by', $user->id);

            // If the user has any related projects, we check if any of those have connected child records
            foreach ($projects->get() as $project) {
                if (
                    $project->project_subscribers()->exists() ||
                    $project->project_documents()->exists() ||
                    $project->attachments()->exists() ||
                    $project->project_reviewers()->exists() ||
                    $project->project_reviews()->exists()
                ) {
                    return true;
                }
            }
        }
        
        if($role_to_check == "reviewer"){
            // If the user is a saved document reviewer
            if (count($user->document_reviewers) > 0 ) {
                return true;
            }


            // If the user is a saved project document reviewer
            if (count($user->reviewed_projects) > 0 ) {
                return true;
            }
        }


        if($role_to_check == "user"){
            // If the user has at least one project, that's a connection too
            if ($projects->exists()) {
                return true;
            }
        }
        

        // Check notifications (optional, based on your usage)
        // if ($user->notifications()->exists()) {
        //     return true;
        // }

        return false;
    }


    


    /**
     * Handle an incoming registration request.
     */
    public function save()
    {

        // dd($this->selectedRoles);
        $this->validate([
             
            'selectedRoles' => 'required|array|min:1',
        ]);

 
        

        $user = User::findOrFail($this->user_id);

        
        // Get current roles before update
        $currentRoles = $user->roles()->pluck('name')->toArray();

        // Get new roles after update
        $newRoleNames = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();

        // Get current permissions and roles 
        $hadReviewerPermission = $user->hasPermissionTo('system access reviewer');
        $hadUserPermission = $user->hasPermissionTo('system access user');
        $hadAdminPermission = $user->hasPermissionTo('system access admin');

       

        $newRoleNames = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();

        // Simulate permissions after update (without modifying DB)
        $newPermissions = collect();
        foreach ($newRoleNames as $roleName) {
            $role = Role::findByName($roleName);
            $newPermissions = $newPermissions->merge($role->permissions->pluck('name'));
        }

        $hasReviewerPermissionAfter = $newPermissions->unique()->contains('system access reviewer');
        $hasUserPermissionAfter = $newPermissions->unique()->contains('system access user');
        $hasAdminPermissionAfter = $newPermissions->unique()->contains('system access admin');



        // If the user is a saved document reviewer
        // dd($user->document_reviewers);
        // if (count($user->document_reviewers) > 0 ) {
        //     return true;
        // }


        // // If the user is a saved project document reviewer
        // if (count($user->reviewed_projects) > 0 ) {
        //     return true;
        // }




        //  dd($hadReviewerPermission && !$hasReviewerPermissionAfter);
        // dd($this->hasConnectedRecords($user));

        // If reviewer permission is being removed, check for connections
        if ($hadReviewerPermission && !$hasReviewerPermissionAfter) {
            if ($this->hasConnectedRecords($user,'reviewer')) {
                // Alert::error('Error', 'Cannot remove reviewer role. This user is connected to existing records.');
                return redirect()->route('user.index')
                    ->with('alert.error','Cannot remove reviewer role. This user is connected to existing records.')
                    ;
            }
        }

        // If user permission is being removed, check for connections
        if ($hadUserPermission && !$hasUserPermissionAfter) {
            if ($this->hasConnectedRecords($user,'user')) {
                // Alert::error('Error', 'Cannot remove user role. This user is connected to existing records.');
                return redirect()->route('user.index')
                ->with('alert.error','Cannot remove reviewer role. This user is connected to existing records.')
                ;
            }
        }

        // If admin permission is being removed, check for connections
        if ($hadAdminPermission && !$hasAdminPermissionAfter) {
            if ($this->hasConnectedRecords($user,'admin')) {
                // Alert::error('Error', 'Cannot remove admin role. This user is connected to existing records.');
                return redirect()->route('user.index')
                ->with('alert.error','Cannot remove reviewer role. This user is connected to existing records.')
                ;
            }
        }
        //  dd("All Gods");
        

        $user->updated_at = now();
        $user->updated_by = Auth::user()->id;

        $user->save();


       



        $roleNames = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();

        $user->syncRoles($roleNames);

         // if the user has no roles before
        if( $this->role_empty ){
            
            // the user in the context is also the one to be notified 
            $userId = $user->id;
            $userIdToNotify = $user->id;

            // You can adapt this to your actual event
            event(new NewUserVerified(
                $userId ,
                $userIdToNotify,
                true

            ));

        }



        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = UserLogHelper::getActivityMessage('role-updated', $user->id, $authId);

            // get the route
            $route = UserLogHelper::getRoute('role-updated', $user->id);

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
        return 
            // redirect()->route('user.index')
            redirect($route)
            ->with('alert.success',$message)
        ;
    }


    public function render()
    {

        $roles = Role::query();

        if (!Auth::user()->can('system access global admin')) {
            // DO not show roles that do not HAVE the 'system access global admin' permission
            $roles = Role::whereHas('permissions', function ($query) {
                $query->whereNot('name', 'system access global admin');
            });
        }  

        
        $roles  = $roles->orderBy('name','asc')->get();


        return view('livewire.admin.user.user-role-edit', compact('roles'));
    }
}
