<?php 
namespace App\Helpers\SystemNotificationHelpers;
use App\Models\ProjectSubscriber;
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
 
class ProjectSubscriberNotificationHelper
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
        $excluded_users[] = Auth::user()->id ?? null; // exclude the current user to the notified user list 
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
     * notify project creator if the connected project or project document is for him and he is not the person who initiated the project subscriber
     * @param  string       $message      message 
     * @param  string       $route        route to redirect 
     * @param int           $projectSubsciberId project subscriber id
     * @param int           $authId     auth id
     * @return void         void          Not required to return value
     */
    public static function sendSystemNotificationForConnectedProjectCreator(
        string $message,
        string $route,
        int $projectSubsciberId = null,
        int $authId,
        int $projectGivenId,

    ){
 
        $projectSubsciber = ProjectSubscriber::find($projectSubsciberId); 
        $customIds = [];

        $projectId = !empty($projectSubsciber->project_id) ? $projectSubsciber->project_id : null;
        $projectId = $projectGivenId ? $projectGivenId : null;


        // check if this is project
        if($projectId){ 

            $project = Project::find($projectId);

            if($project->created_by !== $authId){
                // notify the project creator about the new project subscriber
                $customIds[] = $project->created_by;
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
    
     
    
 
}
