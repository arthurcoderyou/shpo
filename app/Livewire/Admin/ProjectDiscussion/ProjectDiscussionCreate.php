<?php

namespace App\Livewire\Admin\ProjectDiscussion;

use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\ProjectDocument;
use App\Models\ProjectDiscussion;
use Illuminate\Support\Facades\Auth;
use App\Events\Project\ProjectLogEvent;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Events\ProjectDiscussion\ProjectDiscussionLogEvent;
use App\Helpers\ActivityLogHelpers\ProjectDiscussionLogHelper;
use App\Helpers\SystemNotificationHelpers\ProjectDiscussionNotificationHelper;

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
        // dd("here");
        $this->validate([
            'body' => 'required|string',
            'title' => 'nullable|string',
        ]);

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


        
        // $this->dispatch('projectDiscussionAdded');

        $this->reset(['title', 'body', 'is_private']);



    }

    public function render()
    {
        return view('livewire.admin.project-discussion.project-discussion-create');
    }
}
