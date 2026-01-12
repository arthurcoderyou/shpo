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

class ProjectDiscussionList extends Component
{

    public Project $project;
    public ?ProjectDocument $project_document;

    public int $project_id;
    public ?int $project_document_id;

    // public bool $onlyPrivate = false;

    public string $discussionVisibility = 'all'; // Options: all, private, public

    // public $discussions;

    public $record_count = 10;

    // dynamic listener 
        protected $listeners = [
            // add custom listeners as well
            // 'systemUpdate'       => 'handleSystemUpdate',
            // 'SystemNotification' => 'handleSystemNotification',
        ];

        protected function getListeners(): array
        {

            $listeners = [
                "projectDiscussionEvent.{$this->project_id}" => '$refresh',
            ];

            if(!empty($this->project_document_id)){
                $listeners = array_merge($listeners, [
                    "projectDocumentDiscussionEvent.{$this->project_document_id}" => '$refresh',
                ]);

            }


            return array_merge($this->listeners, $listeners);
        }
    // ./ dynamic listener 

    /**
     * Summary of Reply Function
     * @var 
     */
        public ?int $replyToId = null;

        public function startReply(int $id)
        {
            $this->replyToId = $id;
        }


        public string $replyBody = '';

        public function submitReply()
        {
            $this->validate([
                'replyBody' => 'required|string|min:1',
            ]);

            $parent = ProjectDiscussion::find($this->replyToId);

            if (!$parent) {
                return; // Or optionally show an error/toast
            }

            $reply = ProjectDiscussion::create([
                'project_id' => $this->project->id,
                'parent_id' => $this->replyToId,
                'body' => $this->replyBody,
                'is_private' => $parent->is_private, // inherit privacy from parent
                'project_document_id' => $parent->project_document_id, // inherit project document id
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);


            // broadcast(new \App\Events\ProjectDiscussionReplied($parent->project, $parent ,$reply));


            // // temporary || should be on ProjectDiscussion events
            //     $authId = Auth::id() ?? null;

            //     // Success message from the activity log project helper 
            //     $message =  ProjectLogHelper::getProjectActivityMessage('updated', $this->project->id,$authId);
        
            //     // get the route 
            //     $route = ProjectLogHelper::getRoute('updated',  $this->project->id);
                

            //     // // log the event 
            //     event(new ProjectLogEvent(
            //         $message ,
            //         $authId, 

            //     ));

            
            // // Refresh  
            // // $this->dispatch('projectDiscussionAdded');

            // set the reply as the project discussion 
            $reply_project_discussion = ProjectDiscussion::find($reply->id);

            $authId = Auth::id() ?? null; 

            // Success message from the activity log project helper 
            $message =  ProjectDiscussionLogHelper::getActivityMessage('reply',$reply_project_discussion->id,$authId);

            // get the route 
            $route = ProjectDiscussionLogHelper::getRoute('reply', $reply_project_discussion->id);
            

            // // log the event 
            event(new ProjectDiscussionLogEvent(
                $message ,
                $authId, 
                $reply_project_discussion->id,
                $reply_project_discussion->project_id ?? null,
                $reply_project_discussion->project_document_id ?? null,

            ));


            // send notification



                
                // for admins and reviewers 
                ProjectDiscussionNotificationHelper::sendSystemNotification(
                    $message,
                    $route,  
                );

                // notify the project creator 

                    // check first it the authenticated user is the same as the project creator
                    $project = Project::find($reply_project_discussion->project_id);

                    // if the current auth user is the project creator, then no need to notify then
                    if($project->created_by !== $authId){
                        //get creator id
                        $creatorId = $project->created_by;

                        // check if its private 
                        if($reply_project_discussion->is_private == true){

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
                                    $reply_project_discussion->id,
                                    $authId, 
                                );
                                
                            }


                        }elseif($reply_project_discussion->is_private == false){
                            // dd("false");
                            ProjectDiscussionNotificationHelper::sendSystemNotificationForConnectedProjectCreator(
                                $message,
                                $route, 
                                $reply_project_discussion->id,
                                $authId, 
                            );
                        }
                    }

                    
                // ./ notify the project creator 

                // notify the parent project discussion in which this is being replied to

                    $parent_project_discussion =  ProjectDiscussion::find($parent->id);
 
 
                        //get creator id
                        $repliedToUserId = $parent_project_discussion->created_by;


                        // Success message from the activity log project helper 
                        $message =  ProjectDiscussionLogHelper::getActivityMessage('replied-on-you',$reply_project_discussion->id,$authId);

                        if($repliedToUserId !== $authId){


                            // check if its private 
                            if($parent_project_discussion->is_private == true){

                                // dd("true");
                                // check if the creator is allowed to recieve private messages
                                $repliedToUser = User::find($repliedToUserId);
                                
                                $canSeePrivate = $repliedToUser->hasAnyPermission([
                                    'system access global admin',
                                    'system access admin',
                                    'system access reviewer',
                                ]);

                                if($canSeePrivate){
                                    // dd($canSeePrivate);
                                    ProjectDiscussionNotificationHelper::sendSystemNotificationForReply(
                                        $message,
                                        $route, 
                                        $parent_project_discussion->id, 
                                        $authId,
                                    );
                                    
                                }


                            }elseif($reply_project_discussion->is_private == false){
                                // dd("false");
                                ProjectDiscussionNotificationHelper::sendSystemNotificationForReply(
                                    $message,
                                    $route, 
                                    $parent_project_discussion->id, 
                                    $authId,
                                );
                            } 
                            
                        }

                    
                // ./ notify the parent project discussion in which this is being replied to



            // ./ send notification

            $this->reset(['replyBody', 'replyToId']);
        }


        public function cancelReply()
        {
            $this->reset(['replyBody', 'replyToId']);
        }
    /**
    * ./ Reply Function 
    */



    /** Edit Function  */
        public ?int $editId = null;

        public ?string $editTitle = '';
        public ?string $editBody = '';
        public ?int $editParentId = null;

      

        public function startEdit(int $id)
        {
            $this->editId = $id;
            $discussion = ProjectDiscussion::find($this->editId); 

            $this->editTitle = $discussion->title;
            $this->editBody = $discussion->body;
            $this->editParentId = $discussion->parent_id;

        }

        

        public function submitUpdate()
        {
            $this->validate([
                'editTitle' => 'nullable|string|min:1',
                'editBody' => 'required|string|min:1',
            ]);

            $project_discussion = ProjectDiscussion::find($this->editId); 
            $project_discussion->project_id = $this->project->id; 
            $project_discussion->title = $this->editTitle;
            $project_discussion->body = $this->editBody;  
            $project_discussion->updated_by = auth()->id(); 
            $project_discussion->updated_at = now();
            $project_discussion->save();
 

            $authId = Auth::id() ?? null;

            // Success message from the activity log project helper 
            $message =  ProjectDiscussionLogHelper::getActivityMessage('updated',$project_discussion->id,$authId);

            // get the route 
            $route = ProjectDiscussionLogHelper::getRoute('updated', $project_discussion->id);
            

            // // log the event 
            event(new ProjectDiscussionLogEvent(
                $message ,
                $authId, 
                $project_discussion->id,
                $project_discussion->project_id ?? null,
                $project_discussion->project_document_id ?? null,

            ));


            // send notification
                // for admins and reviewers 
                ProjectDiscussionNotificationHelper::sendSystemNotification(
                    $message,
                    $route,  
                );

                // notify the project creator 

                    // check first it the authenticated user is the same as the project creator
                    $project = Project::find($project_discussion->project_id);

                    // if the current auth user is the project creator, then no need to send a notification then 
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


                        }elseif($project_discussion->is_private == false){
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




            $this->reset(['editTitle', 'editBody','editParentId']);
            $this->editId = null;
        }


        public function cancelUpdate()
        {
            $this->reset(['editTitle', 'editBody','editParentId','editId']);
        }

    /** ./ Edit Function  */



    // delete
    public function delete_discussion($id){

        $discussion = ProjectDiscussion::with('replies')->findOrFail($id);

        broadcast(new \App\Events\ProjectDiscussionDeleted($discussion->project, $discussion));


        if (
            $discussion->created_by !== auth()->id() &&
            !auth()->user()->hasAnyPermission(['system access admin', 'system access global admin'])
        ) {
            abort(403);
        }
    
        $discussion->deleteWithReplies();

        
        

        $this->dispatch('projectDiscussionDeleted');


    }


    public function mount(Project $project , ProjectDocument $project_document = null)
    {
        $this->project = $project;
        $this->project_document = $project_document;

        $this->project_id = $project->id ?? null;
        $this->project_document_id = $project_document->id ?? null;

            // dd($projec)

        // // $this->discussions = ProjectDiscussion::with(['creator', 'replies.creator'])
        // $this->discussions =  ProjectDiscussion::with(['creator', 'parent.creator', 'replies.creator', 'replies.parent.creator'])
        //     ->where('project_id', $this->project->id)
        //     ->whereNull('parent_id')
        //     ->when(!auth()->user()->hasRole(['DSI God Admin','Admin', 'Reviewer']), function ($query) {
        //         $query->where('is_private', false);
        //     })
        //     ->latest()
        //     ->get();
    }

    public function getDiscussionsProperty()
    {
 

        $query = ProjectDiscussion::with([
            'creator',
            'parent.creator',
            'replies.creator',
            'replies.parent.creator'
        ]);

        if(!empty($this->project_document->id) && $this->project_document->id !== null){ 
            $query = $query->where('project_document_id','=',$this->project_document->id);
            // dd("Here");
        }

        $query =    $query->where('project_id', $this->project->id)
            ->whereNull('parent_id')
            ->when($this->discussionVisibility === 'all', function ($query) {
                $canSeePrivate = auth()->user()->hasAnyPermission([
                    'system access global admin',
                    'system access admin',
                    'system access reviewer',
                ]);

                if (! $canSeePrivate) {
                    $query->where('is_private', false);
                }
            })
            ->when($this->discussionVisibility === 'private', function ($query) {
                $query->where('is_private', true);
            })
            ->when($this->discussionVisibility === 'public', function ($query) {
                $query->where('is_private', false);
            })
            ->latest();

        return $query->paginate($this->record_count);
    }


    public function render()
    {

        // dd($this->discussions);
        return view('livewire.admin.project-discussion.project-discussion-list',[
            'discussions' => $this->discussions
        ]);
    }
}
