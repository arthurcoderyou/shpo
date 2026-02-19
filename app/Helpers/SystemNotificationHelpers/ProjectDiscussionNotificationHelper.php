<?php 
namespace App\Helpers\SystemNotificationHelpers;
use Carbon\Carbon;
use App\Models\User; 
use App\Models\Project;
use App\Models\ActivityLog;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Models\ProjectDiscussion;
use App\Events\System\SystemEvent;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Void_;
use Illuminate\Support\Facades\Auth;
use App\Events\TargetedNotification; // or whatever event you use
 
class ProjectDiscussionNotificationHelper
{
    /**
     * Send system notification 
     * @param  string       $message      message 
     * @param  string       $route        route to redirect
     * @param  int          $authId       auth id 
     * @return void         void          Not required to return value
     */
    public static function sendSystemNotification(
        string $message,
        string $route, 
    ){

        $users_roles_to_notify = [
            'admin',
            'global_admin',
            // 'reviewer',
            // 'user'
        ];  
 

        // set custom users that will not be notified 
        $excluded_users = []; 
        if(Auth::user()->can('system access admin') || Auth::user()->can('system access admin')){
            // do nothing
        }else{
            $excluded_users[] = Auth::user()->id ?? null; // exclude the current user to the notified user list 
        }


        // $excluded_users[] = 72; // for testing only
        // dd($excluded_users);




        // notified users without hte popup notification | ideal in notifying the user that triggered the event without the popu
        $customIdsNotifiedWithoutPopup = [];
        $customIdsNotifiedWithoutPopup[] = Auth::user()->id ?? null; // exclude the current user to the notified user list 
 
        $customIds = [];
        

        


 

        // dd("good ");
        SystemNotificationHelper::sendSystemNotificationEvent(
            $users_roles_to_notify,
            $message,
            $customIds,
            'info', // use info, for information type notifications
            [$excluded_users],
            $route, // nullable
            $customIdsNotifiedWithoutPopup
        );

    }

 
    /**
     * notify project creator if the connected project or project document is for him and he is not the person who initiated the project discussion
     * @param  string       $message      message 
     * @param  string       $route        route to redirect 
     * @param int           $projectDiscussionId project discussion id
     * @param int           $authId     auth id
     * @return void         void          Not required to return value
     */
    public static function sendSystemNotificationForConnectedProjectCreator(
        string $message,
        string $route,
        int $projectDiscussionId,
        int $authId,
    ){
 
        $projectDiscussion = ProjectDiscussion::find($projectDiscussionId);

 

        $customIds = [];

        
        // check if this is a reply meaning there is a parent id 
        if($projectDiscussion->project_id){ 

            $project = Project::find($projectDiscussion->project_id);

            if($project->created_by !== $authId){
                // notify the discussion reciepient to notify him that there is a reply
                $customIds[] = $project->created_by;
            }    
        }elseif($projectDiscussion->project_document_id){
            $project_document = ProjectDocument::find($projectDiscussion->project_document_id);

            if($project_document->created_by !== $authId){
                // notify the discussion reciepient to notify him that there is a reply
                $customIds[] = $project_document->created_by;
            } 
        }
 

        // dd("good ");
        SystemNotificationHelper::sendSystemNotificationEvent(
            [],
            $message,
            $customIds,
            'info', // use info, for information type notifications
            [],
            $route, // nullable
            
        );

    }
    
    
    

    /**
     * Send system notification only for the project discussion reply reciepient
     * @param  string       $message      message 
     * @param  string       $route        route to redirect 
     * @return void         void          Not required to return value
     */
    public static function sendSystemNotificationForReply(
        string $message,
        string $route,
        int $projectDiscussionId,
        int $authId,
    ){
 
        $projectDiscussion = ProjectDiscussion::find($projectDiscussionId);

 

        $customIds = [];

        // check if this is a reply meaning there is a parent id 
        if($projectDiscussion->parent_id){

            $replyToProjectDiscussion = ProjectDiscussion::find($projectDiscussion->parent_id);

            // notify the discussion reciepient to notify him that there is a reply
             $customIds[] = $replyToProjectDiscussion->created_by;

        }
        // $customIds[] = $projectDiscussion->created_by;

        $customIdsNotifiedWithoutPopup = [];

        if($projectDiscussion->created_by == $authId){

            
            $customIdsNotifiedWithoutPopup[] = $projectDiscussion->created_by;

        }else{
            $customIds = [];
            $customIds[] = $projectDiscussion->created_by;
        }

        



        // dd("good ");
        SystemNotificationHelper::sendSystemNotificationEvent(
            [],
            $message,
            $customIds,
            'info', // use info, for information type notifications
            [],
            $route, // nullable
            $customIdsNotifiedWithoutPopup
            
        );

        
    }

 
 
}
