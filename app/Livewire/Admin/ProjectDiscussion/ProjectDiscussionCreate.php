<?php

namespace App\Livewire\Admin\ProjectDiscussion;

use App\Events\Project\ProjectLogEvent;
use App\Events\ProjectDiscussion\ProjectDiscussionLogEvent;
use App\Helpers\ActivityLogHelpers\ProjectDiscussionLogHelper;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Helpers\SystemNotificationHelpers\ProjectDiscussionMentionNotificationHelper;
use App\Helpers\SystemNotificationHelpers\ProjectDiscussionNotificationHelper;
use App\Models\Project;
use App\Models\ProjectDiscussion;
use App\Models\ProjectDiscussionMentions;
use App\Models\ProjectDocument;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectDiscussionCreate extends Component
{

    public Project $project;
    public ?ProjectDiscussion $parent = null;
    public ?ProjectDocument $project_document = null;
     
    public $title = '';
    public $body = '';
    public $is_private = false;

    public $notify_email = false;
 

    // protected $listeners = ['projectDiscussionAdded' => '$refresh'];

    public function save()
    {


        // dd($this->all());


        // dd("here");
        $this->validate([
            'body' => 'required|string',
            'title' => 'nullable|string',
        ]);

        // save the project discussion 
        $project_discussion = ProjectDiscussion::create([
            'project_id' => $this->project->id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'title' => $this->parent ? null : $this->title,
            'body' => $this->body,
            'parent_id' => $this->parent?->id,
            'project_document_id' => $this->project_document?->id,
            'is_private' => $this->is_private,
        ]);


        // save the project_discussion_mentions
        if(!empty($this->selected_users)){
           foreach($this->selected_users as $user_id => $user){
                ProjectDiscussionMentions::create([
                    'project_discussion_id' => $project_discussion->id,
                    'user_id' => $user_id , 
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

           } 
        }
 


        // broadcast(new \App\Events\ProjectDiscussionCreated($project  ,$project_discussion));


        
        $authId = Auth::id() ?? null;

        // Success message from the activity log project helper 
        $message =  ProjectDiscussionLogHelper::getActivityMessage('created',$project_discussion->id,$authId);

        // get the route 
        $route = ProjectDiscussionLogHelper::getRoute('created', $project_discussion->id);
        

        // // log the event 
        event(new ProjectDiscussionLogEvent(
            $message ,
            $authId, 
            $project_discussion->id,
            $project_discussion->project_id ?? null,
            $project_discussion->project_document_id ?? null,

        ));

        // send notification
            ProjectDiscussionNotificationHelper::sendSystemNotification(
            $message,
                $route,  
            );

            // notify the project creator 

                // check first it the authenticated user is the same as the project creator
                $project = Project::find($project_discussion->project_id);

                if($project->created_by !== $authId){
                    //get creator id
                    $creatorId = $project->created_by;

                    // check if its private 
                    if($project_discussion->is_private == true){

                        // dd("true");
                        // check if the creator is allowed to recieve private messages
                        $creator = User::find($creatorId);
                        
                        $canSeePrivate = $creator->hasAnyPermission([
                            'system access global admin',
                            'system access admin',
                            'system access reviewer',
                        ]);

                        if($canSeePrivate){
                            // dd($canSeePrivate);
                            ProjectDiscussionNotificationHelper::sendSystemNotificationForConnectedProjectCreator(
                                $message,
                                $route, 
                                $project_discussion->id,
                                $authId, 
                            );
                            
                        }


                    }else{if($project_discussion->is_private == false)
                        // dd("false");
                        ProjectDiscussionNotificationHelper::sendSystemNotificationForConnectedProjectCreator(
                            $message,
                            $route, 
                            $project_discussion->id,
                            $authId, 
                        );
                    }
                }

                
            // ./ notify the project creator 

        // ./ send notification



 
        // dispatch notifications to the project discussion mentions
        if(!empty($project_discussion->project_discussion_mentions)){

            foreach($project_discussion->project_discussion_mentions as $project_discussion_mention){

                $project_discussion =  $project_discussion_mention->project_discussion;
            
                $authId = Auth::id() ?? null;

                // Success message from the activity log project helper 
                $message =  ProjectDiscussionLogHelper::getActivityMessage('mentioned-you',$project_discussion->id,$authId);

                // get the route 
                $route = ProjectDiscussionLogHelper::getRoute('mentioned-you', $project_discussion->id);
                
                // log to the user that is mentioned
                $user_id = $project_discussion_mention->user_id;

                // // log the event 
                event(new ProjectDiscussionLogEvent(
                    $message ,
                    $user_id, 
                    $project_discussion->id,
                    $project_discussion->project_id ?? null,
                    $project_discussion->project_document_id ?? null,

                ));

                // send notification
                    ProjectDiscussionMentionNotificationHelper::sendSystemNotificationForMentions(
                        $message,
                        $route,  
                        $project_discussion_mention->id,
                    );
 
                // ./ send notification

            } 


        }




        
        // $this->dispatch('projectDiscussionAdded');

        $this->reset(['title', 'body', 'is_private']);



    }

    public array $selected_users = [];

    public function addUser(int $user_id, string $name): void
    {

        // dd($user_id);
        if (isset($this->selected_users[$user_id])) {
            return; // Already selected
        }

        $this->selected_users[$user_id] = [
            'user_id' => $user_id,
            'name'    => $name,
        ];

        $this->search = '';
    }

    public function removeUser(int $user_id): void
    {
        unset($this->selected_users[$user_id]);
    }

    public string $search = '';
     public function getUsersProperty(){

        $users = User::query()
            ->select('id','name');

            if(!empty($this->search)){

            $users = $users->where('name','LIKE','%'.$this->search.'%');
            }

        return $users->limit(10)
            ->get();    

     }

     
 

    public function render()
    {
        return view('livewire.admin.project-discussion.project-discussion-create',[
            'users' => $this->users
        ]);
    }
}
