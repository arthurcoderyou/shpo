<?php

namespace App\Livewire\Component\Project;

use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\ProjectSubscriber;
use Illuminate\Support\Facades\Auth;
use App\Events\Project\ProjectLogEvent;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;

class ProjectSubscribersModal extends Component
{
    protected $listeners = [
        'systemEvent' => 'loadSelectedUsers', 

        'projectEvent' => 'loadSelectedUsers',

    ];

    public Project $project;
    public int $project_id;
 


    public function mount($id){

        $project = Project::find($id);

        $this->project = $project; 
        $this->project_id = $project->id;

        
        $this->loadSelectedUsers();

    }


    public function loadSelectedUsers(){
 
        $this->selectedUsers = [];

        $project = Project::find($this->project->id);
        if(!empty($project->project_subscribers)){
            foreach($project->project_subscribers as $subscriber){
                $this->selectedUsers[] = ['id' => $subscriber->user->id, 'name' => $subscriber->user->name];
            }
            
        }
    }



    // For the Search Subscriber Functionality
        

        public $query = ''; // Search input
        public $users = []; // Search results
        public $selectedUsers = []; // Selected subscribers

        public function updatedQuery()
        {
            if (!empty($this->query)) {
                $user      = Auth::user();
                $term      = '%' . $this->query . '%';
                $creator_user_id = $this->project->created_by;

                $this->users = User::query()
                    // 🔍 Group all search columns together
                    ->where(function ($search) use ($term) {
                        $search->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term)
                            ->orWhere('address', 'like', $term)
                            ->orWhere('company', 'like', $term)
                            ->orWhere('phone_number', 'like', $term);
                    })

                    // 🔐 Apply access restrictions only if NOT global admin or admin
                    ->when(
                        ! $user->can('system access global admin') && ! $user->can('system access admin'),
                        function ($query) use ($user) {
                            $query->where(function ($q) use ($user) {

                                if ($user->can('system access reviewer')) {
                                    // Reviewers can see users with 'system access user' or 'system access reviewer'
                                    $q->where(function ($inner) {
                                        $inner->whereHas('permissions', function ($permQuery) {
                                            $permQuery->whereIn('name', [
                                                'system access user',
                                                'system access reviewer',
                                            ]);
                                        })->orWhereHas('roles.permissions', function ($permQuery) {
                                            $permQuery->whereIn('name', [
                                                'system access user',
                                                'system access reviewer',
                                            ]);
                                        });
                                    });

                                } elseif ($user->can('system access user')) {
                                    // Normal users can only see users with 'system access user'
                                    $q->where(function ($inner) {
                                        $inner->whereHas('permissions', function ($permQuery) {
                                            $permQuery->where('name', 'system access user');
                                        })->orWhereHas('roles.permissions', function ($permQuery) {
                                            $permQuery->where('name', 'system access user');
                                        });
                                    });
                                }

                            });
                        }
                    )
                     ->where('id',"!=", $creator_user_id) // do not include the current creator of the project
                    ->limit(10)
                    ->get();


            } else {
                $this->users = [];
            }

            // dd($this->users);
        }


        public function addSubscriber($userId)
        {
            // Prevent duplicate selection
            if (!in_array($userId, array_column($this->selectedUsers, 'id'))) {
                $user = User::find($userId);
                $this->selectedUsers[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'company' => $user->company
                ];
            }

            // Clear search results
            $this->query = '';
            $this->users = [];
        }

        public function removeSubscriber($index)
        {
            unset($this->selectedUsers[$index]);
            $this->selectedUsers = array_values($this->selectedUsers); // Re-index array
        }
    // ./// For the Search Subscriber Functionality





    /**
     * Handle project update.
     */
    public function save()
    {
          
 

        //save
        $project = Project::find( $this->project_id);

  

        $project->updated_by = Auth::user()->id;
        $project->updated_at = now();
        $project->save();

        
 


        // delete existing subscribers 
        if(!empty($project->project_subscribers)){
            // delete project subscribers
            if(!empty($project->project_subscribers)){
                foreach($project->project_subscribers as $subcriber){
                    $subcriber->delete();
                } 
            }
        }



        // Save Project Subscribers (if any)
        if (!empty($this->selectedUsers)) {
            foreach ($this->selectedUsers as $user) {
                ProjectSubscriber::create([
                    'project_id' => $project->id,
                    'user_id' => $user['id'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }




 


        $authId = Auth::id() ?? null;

        // Success message from the activity log project helper 
        $message =  ProjectLogHelper::getProjectActivityMessage('updated',$project->id,$authId);
 
        // get the route 
        $route = ProjectLogHelper::getRoute('updated', $project->id);
        

        // // log the event 
        event(new ProjectLogEvent(
            $message ,
            $authId, 

        ));

           

        /** send system notifications to users */
            // check  ActivityLogHelper::sendSystemNotificationEvent() function to understand how to user this.

             $users_roles_to_notify = [
                'admin',
                'global_admin',
                // 'reviewer',
                // 'user'
            ];  

            // set custom users that will not be notified 
            $excluded_users = []; 
            $excluded_users[] = Auth::user()->id ?? null; // exclude the current user to the notified user list 
            // $excluded_users[] = 72; // for testing only
            // dd($excluded_users);


            // check if the auth user that is updating the project is the main submitter, if not, send an update notification to the main submitter
            $customIds = [];
            if($authId !== $project->created_by){
                $customIds[] = $project->created_by;

            }


            // notified users without hte popup notification | ideal in notifying the user that triggered the event without the popu
            $customIdsNotifiedWithoutPopup = [];
            $customIdsNotifiedWithoutPopup[] = Auth::user()->id ?? null; // exclude the current user to the notified user list 

            // dd("good ");
            ActivityLogHelper::sendSystemNotificationEvent(
                $users_roles_to_notify,
                $message,
                $customIds,
                'info', // use info, for information type notifications
                [$excluded_users],
                $route, // nullable
                $customIdsNotifiedWithoutPopup
            );
        /** ./ send system notifications to users */
 

 


        // return redirect()->route('project.edit',['project' => $this->project->id])
        //     ->with('alert.success',$message)
        // ;

        // Flash for your alert
        // session()->flash('alert.success', $message);


        // // return redirect()->back()->with('alert.success', $message);
        // return back()->with('alert.success', $message);

        // Fire Livewire browser event for Notyf
        $this->dispatch('notify', type: 'success', message: $message);


    }


    public function render()
    {
        return view('livewire.component.project.project-subscribers-modal');
    }
}
