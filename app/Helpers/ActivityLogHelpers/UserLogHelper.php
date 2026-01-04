<?php 
namespace App\Helpers\ActivityLogHelpers;
use Carbon\Carbon;
use App\Models\User; 
use App\Models\Project;
use App\Models\ActivityLog;
use App\Events\System\SystemEvent;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Void_;
use App\Events\TargetedNotification; // or whatever event you use
 
class UserLogHelper
{
  
    /**
     * Generate a ActivityLog for Project based on event
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $userId         user id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function logActivity(string $event = null,int $userId, int $authId){

         // get the record
        $user = User::find($userId);
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = UserLogHelper::getActivityMessage($event,$userId,$authId);
        
        // save the activity log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' =>  $message, 
        ]);

    }

    /**
     * Generate a ActivityLog message || This is also used in messages returned 
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $userId         user id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function getActivityMessage(string $event, int $userId, int $authId): string{

         

        // get the record
        $user = User::find($userId);
        // get the user that initiated the event
        $authUser = User::find($authId); 

        $userName = $user->name ?? 'User unnamed';
        $authName = $authUser->name ?? 'Auth unnamed';

        $roleRequest = $user->role_request ?? 'No request';

        

        // return message based on the event
        return match($event) {
            'created' => "User '{$userName}' has been created by '{$authName}' successfully.",
            'updated' => "User '{$userName}' has been updated by '{$authName}' successfully.",
            'deleted' => "User '{$userName}' has been deleted by '{$authName}' successfully.",
            'new-user-verification-request' => "New User Verification Request: {$userName} for the role {$roleRequest}",
            'role-updated' => "User role '{$userName}' has been updated by '{$authName}' successfully.",
            'your-role-updated' => "Your role has been updated successfully.", 
            'account-verified' => "User '{$userName}' Account has been verified",
            'your-account-verified' => "Your Account has been verified",
            default => "Action completed for project '{$userName}'."
        };
 
    } 

 
     /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $userId         user id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event, int $userId): string{

        // get the record
        $user = User::find($userId);
        
        

        // return message based on the event
        return match($event) { 
            'created' => route('user.edit',['user' => $user->id]),
            'updated' =>  route('user.edit',['user' => $user->id]),
            'deleted' =>  route('user.index'),
            'new-user-verification-request' =>  route('user.edit_role', ['user' => $user->id]), 
            'role-updated' => route('user.edit_role', ['user' => $user->id]), 
            'your-account-verified' =>  route('dashboard'), 
            
            default =>  route('user.index')
        };

    }


}
