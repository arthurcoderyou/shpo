<?php

namespace App\Livewire\Component\Project;

use App\Events\ProjectSubscriber\ProjectSubscriberLogEvent;
use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\ProjectSubscriber;
use Illuminate\Support\Facades\Auth;
use App\Events\Project\ProjectLogEvent;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;  
use App\Helpers\ActivityLogHelpers\ProjectSubscriberLogHelper;
use App\Helpers\SystemNotificationHelpers\ProjectSubscriberNotificationHelper;

class ProjectSubscribersModal extends Component
{
   

     // dynamic listener 
        protected $listeners = [
            // add custom listeners as well
            // 'systemUpdate'       => 'handleSystemUpdate',
            // 'SystemNotification' => 'handleSystemNotification',
        ];

        protected function getListeners(): array
        {

            $listeners = [
                "projectSubscriberEvent.{$this->project_id}" => 'loadSelectedUsers',
            ];

            if(!empty($this->project_document_id)){
                $listeners = array_merge($listeners, [
                    "projectDocumentSubscriberEvent.{$this->project_document_id}" => '$refresh',
                ]);

            }


            return array_merge($this->listeners, $listeners);
        }
    // ./ dynamic listener



    public Project $project;
    public int $project_id;
    public int $project_document_id;

 


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

        $authId = Auth::id() ?? null;

        // Save Project Subscribers (if any)
        if (!empty($this->selectedUsers)) {
            foreach ($this->selectedUsers as $user) {
                $project_subscriber = ProjectSubscriber::create([
                    'project_id' => $project->id,
                    'user_id' => $user['id'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                $message =  ProjectSubscriberLogHelper::getActivityMessage('added',$project_subscriber->id,$authId);


                 // // log the event 
                event(new ProjectSubscriberLogEvent(
                    $message ,
                    $authId, 
                    $project_subscriber->id,
                    $project_subscriber->project_id ?? null, 


                ));
 
            }
        }


        

         
 

        // return redirect()->route('project.edit',['project' => $this->project->id])
        //     ->with('alert.success',$message)
        // ;

        // Flash for your alert
        // session()->flash('alert.success', $message);


        // // return redirect()->back()->with('alert.success', $message);
        // return back()->with('alert.success', $message);

         // Success message from the activity log project helper 
                $message =  ProjectSubscriberLogHelper::getActivityMessage('updated',null,$authId,$project->id);

                // get route
                $route =  ProjectSubscriberLogHelper::getRoute('updated',null, $project->id);


               
                // send notification
                    ProjectSubscriberNotificationHelper::sendSystemNotification(
                        $message,
                        $route,  
                    );

                    // log event
                     // // log the event 
                    event(new ProjectSubscriberLogEvent(
                        $message ,
                        $authId,  
                        null,
                        $project->id ?? null, 


                    ));

                    // notify the project creator 

                        // check first it the authenticated user is the same as the project creator 
                        if($project->created_by == $authId){
                            //get creator id
                            $creatorId = $project->created_by;

                                // dd("false");
                                ProjectSubscriberNotificationHelper::sendSystemNotificationForConnectedProjectCreator(
                                    $message,
                                    $route, 
                                    null,
                                    $authId, 
                                    $project->id,
                                ); 
                        }

                        
                    // ./ notify the project creator 

                // ./ send notification



        // Fire Livewire browser event for Notyf
        $this->dispatch('notify', type: 'success', message: $message);


    }


    public function render()
    {
        return view('livewire.component.project.project-subscribers-modal');
    }
}
