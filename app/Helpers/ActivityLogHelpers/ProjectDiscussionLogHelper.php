<?php 
namespace App\Helpers\ActivityLogHelpers;
use App\Models\ProjectDiscussion;
use Carbon\Carbon;
use App\Models\User;  
use Illuminate\Support\Str;
use App\Models\ActivityLog;   
 
class ProjectDiscussionLogHelper
{
 
 
    /**
     * Generate a ActivityLog for Role based on event
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $project_timerId         project_timer id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function logActivity(string $event = null, int $authId){
 
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = ProjectTimerLogHelper::getActivityMessage($event,$authId);
        
        // save the activity log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' =>  $message, 
        ]); 
    }

    /**
     * Generate a ActivityLog message || This is also used in messages returned 
     * @param  string       $event          'new-discussion', 'reply', 'updated','deleted', 
     * @param  int          $projectDiscussionId         project discussion id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function getActivityMessage(string $event, int  $projectDiscussionId  , int $authId): string{
 
        $projectDiscussion = ProjectDiscussion::find($projectDiscussionId);

        $projectName = $projectDiscussion->project->name ?? 'Unnamed project';

        // get the user that initiated the event
        $authUser = User::find($authId);  
        $userName = $authUser->name ?? 'Auth unnamed';  

        // $replyUser = 


        // return message based on the event
        return match($event) {  
            'new-discussion' => "New Discussion added to '{$projectName}' has been added by '{$userName}'.",
            'updated' => "Discussion on '{$projectName}' by '{$authUser}' has been updated.",
            'reply' => "Message by '{$userName}' on '{$projectName}' has been replied by  uccessfully.",
            'replied-on-you' => "Discussion on  '{$projectName}' has been updated by '{$userName}' successfully.",
            'deleted' => "Project '{$projectName}' has been deleted by '{$userName}' successfully.",
            default => "Project time settings updated successfully by '{$authName}'."
        };
 
    } 

 
     /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $project_timerId         project_timer id optional
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event): string{
 

        // return message based on the event
        return match($event) {   
            default =>  route('project_timer.index')
        };

    }


}
