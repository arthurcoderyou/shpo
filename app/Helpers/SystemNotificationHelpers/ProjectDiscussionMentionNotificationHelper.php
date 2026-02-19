<?php 
namespace App\Helpers\SystemNotificationHelpers;
use App\Models\ProjectDiscussionMentions;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth; 
 
class ProjectDiscussionMentionNotificationHelper
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
     * Send system notification only for the project discussion mention reciepient
     * @param  string       $message      message 
     * @param  string       $route        route to redirect 
     * @return void         void          Not required to return value
     */
    public static function sendSystemNotificationForMentions(
        string $message,
        string $route,
        int $projectDiscussionMentionId, 
    ){
 
        $projectDiscussionMention = ProjectDiscussionMentions::find($projectDiscussionMentionId); 
 
        $customIds = [];
        // notify the discussion mentioned users to notify him that there is a mention to him
        $customIds[] = $projectDiscussionMention->user_id; 
 
 
        // dd("good ");
        SystemNotificationHelper::sendSystemNotificationEvent(
            [],
            $message,
            $customIds,
            'info', // use info, for information type notifications
            [],
            $route, // nullable
            []
            
        );

        
    }

 
 
}
  