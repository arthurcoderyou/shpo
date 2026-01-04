<?php 
namespace App\Helpers\ActivityLogHelpers;
use App\Models\DocumentType;
use Carbon\Carbon;

use App\Models\User;  
use App\Models\ActivityLog;  
use App\Models\Reviewer;  
 
class ReviewerLogHelper
{
  
    /**
     * Generate a ActivityLog for Role based on event
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $reviewerId         reviewer id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function logActivity(string $event = null, int $authId){
 
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = ReviewerLogHelper::getActivityMessage($event,$authId);
        
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
     * @param  int          $authId         auth id  
     * @return void         void            Not required to return value
     */
    public static function getActivityMessage(string $event, int $authId): string{
 
        // get the user that initiated the event
        $authUser = User::find($authId);  
        $authName = $authUser->name ?? 'Auth unnamed';  
 
        // return message based on the event
        return match($event) {  
            "updated" => "Reviewer list updated successfully by '{$authName}'",
            default => "Project reviewer list updated successfully by '{$authName}'."
        };
 
    } 

 
     /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
   
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event): string{
 

        // return message based on the event
        return match($event) {   
            'updated' => route('reviewer.index'),
            default =>  route('reviewer.index')
        };

    }


}
