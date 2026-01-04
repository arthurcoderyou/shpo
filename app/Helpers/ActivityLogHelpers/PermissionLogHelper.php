<?php 
namespace App\Helpers\ActivityLogHelpers;
use Carbon\Carbon;
use App\Models\User;  
use App\Models\ActivityLog;
use App\Events\System\SystemEvent;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Void_;
use App\Events\TargetedNotification; 
use Spatie\Permission\Models\Permission;// or whatever event you use
 
class PermissionLogHelper
{
 
    /**
     * Generate a ActivityLog based on event
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $permissionId         permission id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function logActivity(string $event = null,int $permissionId, int $authId){

         // get the record
        $permission = Permission::find($permissionId);
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = PermissionLogHelper::getActivityMessage($event,$permission->id,$authId);
        
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
     * @param  int          $permissionId         permission id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function getActivityMessage(string $event, int $permissionId = null, int $authId): string{

         

        // get the record
        $permission = Permission::find($permissionId);
        // get the user that initiated the event
        $authUser = User::find($authId); 

        $permissionName = $permission->name ?? 'Permission unnamed';
        $authName = $authUser->name ?? 'Auth unnamed';
 

        // return message based on the event
        return match($event) {
            'created' => "Permission '{$permissionName}' has been created by '{$authName}' successfully.",
            'updated' => "Permission '{$permissionName}' has been updated by '{$authName}' successfully.",
            'deleted' => "Permission '{$permissionName}' has been deleted by '{$authName}' successfully.",  
            default => "Action completed for project '{$permissionName}'."
        };
 
    } 
    /**
     * Generate a event message that is custom and not for activity logs
     * @param  string       $event          'created', 'updated', 'deleted',  
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function getEventMessage(string $event, int $authId): string{

          
        // get the user that initiated the event
        $authUser = User::find($authId);  
        $authName = $authUser->name ?? 'Auth unnamed';
 

        // return message based on the event
        return match($event) { 
            'does-not-exists' => "Permission does not exists.",  
            default => "Action '{$event}' completed for permission ."
        };
 
    } 

 
     /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $permissionId         permission id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event, int $permissionId): string{

        // get the record
        $permission = Permission::find($permissionId);
         
        // return message based on the event
        return match($event) { 
            'created' => route('permission.index'),
            'updated' =>  route('permission.edit',['permission' => $permission->id]),
            'deleted' =>  route('permission.index'), 
            default =>  route('permission.index')
        };

    }


 


}
