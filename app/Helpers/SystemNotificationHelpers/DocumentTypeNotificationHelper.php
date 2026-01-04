<?php 
namespace App\Helpers\SystemNotificationHelpers;   
use Illuminate\Support\Facades\Auth; 
 
class DocumentTypeNotificationHelper
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
            string $route
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

        // dd("good ");
        SystemNotificationHelper::sendSystemNotificationEvent(
            $users_roles_to_notify,
            $message,
            [],
            'info', // use info, for information type notifications
            [$excluded_users],
            $route, // nullable
            $customIdsNotifiedWithoutPopup
        );

    }
    
    

 
}
