<?php 
namespace App\Helpers\ActivityLogHelpers; 
use App\Models\User;  
use App\Models\ActivityLog; 
use Spatie\Permission\Models\Role; 
 
class RoleLogHelper
{
  
    /**
     * Generate a ActivityLog for Role based on event
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $roleId         role id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function logActivity(string $event = null,int $roleId, int $authId){

        // get the record
        $role = Role::find($roleId);
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = RoleLogHelper::getActivityMessage($event,$role->id,$authId);
        
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
     * @param  int          $roleId         role id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function getActivityMessage(string $event, int $roleId, int $authId): string{ 
        
        // get the record
        $role = Role::find($roleId);
        // get the user that initiated the event
        $authUser = User::find($authId); 

        $roleName = $role->name ?? 'Role unnamed';
        $authName = $authUser->name ?? 'Auth unnamed'; 
 
        // return message based on the event
        return match($event) {
            'created' => "Role '{$roleName}' has been created by '{$authName}' successfully.",
            'updated' => "Role '{$roleName}' has been updated by '{$authName}' successfully.",
            'deleted' => "Role '{$roleName}' has been deleted by '{$authName}' successfully.", 
            'role-permissions-updated' => "Role '{$roleName}' permissions has been updated by '{$authName}' successfully.", 
            default => "Action completed for project '{$roleName}'."
        };
 
    } 

 
     /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $roleId         role id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event, int $roleId): string{

        // get the record
        $role = Role::find($roleId);
        
        

        // return message based on the event
        return match($event) { 
            'created' => route('role.edit',['role' => $role->id]),
            'updated' =>  route('role.edit',['role' => $role->id]),
            'deleted' =>  route('role.index'), 
            'role-permissions-updated' =>  route('role.add_permissions', ['role' => $role->id]), 
            
            default =>  route('user.index')
        };

    }


}
