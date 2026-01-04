<?php 
namespace App\Helpers\ActivityLogHelpers;
use App\Models\ProjectSubscriber;
use Carbon\Carbon;
use App\Models\User;  
use App\Models\ActivityLog;  
 
class ProjectSubscriberLogHelper
{



    
  
    /**
     * Generate a ActivityLog for Role based on event
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $projectSubscriberId         role id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function logActivity(string $event = null,int $projectSubscriberId, int $authId){

        // get the record
        $role = ProjectSubscriber::find($projectSubscriberId);
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = ProjectSubscriberLogHelper::getActivityMessage($event,$role->id,$authId);
        
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
     * @param  int          $projectSubscriberId         projectSubscriber id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function getActivityMessage(string $event, int $projectSubscriberId, int $authId): string{

         

        // get the record
        $projectSubscriber = ProjectSubscriber::find($projectSubscriberId); 

        // get the user that initiated the event
        $authUser = User::find($authId); 



        $subscriberName =  $projectSubscriber->user->name ?? 'Subsriber unnamed';

        $subscribedProjectName = $projectSubscriber->project->name ?? 'Project unnamed';
        $authName = $authUser->name ?? 'Auth unnamed'; 
 
        // return message based on the event
        return match($event) {
            // message for admin
            'added' => "Project subscriber '{$subscriberName}' has been added to '{$subscribedProjectName}' by '{$authName}' successfully.",
            // message for users that are added to the subsribers
            'added-usr-msg' => "You had been added as a subscriber project '{$subscribedProjectName}' by '{$authName}' successfully.",  
            default => "Action completed for '{$subscriberName}' on '{$subscribedProjectName}' ."
        };
 
    } 

  

}
