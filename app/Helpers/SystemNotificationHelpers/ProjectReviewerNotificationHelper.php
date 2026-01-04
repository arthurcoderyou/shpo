<?php 
namespace App\Helpers\SystemNotificationHelpers;
use Carbon\Carbon;
use App\Models\User; 
use App\Models\Project;
use App\Models\ActivityLog;
use App\Helpers\ProjectHelper;
use App\Events\System\SystemEvent;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Void_;
use Illuminate\Support\Facades\Auth;
use App\Events\TargetedNotification; // or whatever event you use
 
class ProjectReviewerNotificationHelper
{
    /**
     * Send system notification 
     * @param  string       $message      message 
     * @param  string       $route        route to redirect 
     * @param  int          $projectReviewerId       project reviewer id 
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

        $customIdUsers = [];

         

        


        // notified users without hte popup notification | ideal in notifying the user that triggered the event without the popu
        $customIdsNotifiedWithoutPopup = [];
        $customIdsNotifiedWithoutPopup[] = Auth::user()->id ?? null; // exclude the current user to the notified user list 

        // dd("good ");
        SystemNotificationHelper::sendSystemNotificationEvent(
            $users_roles_to_notify,
            $message,
            $customIdUsers,
            'info', // use info, for information type notifications
            [$excluded_users],
            $route, // nullable
            $customIdsNotifiedWithoutPopup
        );

    }
    
    

 
}
