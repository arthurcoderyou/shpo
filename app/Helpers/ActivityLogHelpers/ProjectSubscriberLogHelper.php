<?php 
namespace App\Helpers\ActivityLogHelpers;
use Carbon\Carbon;
use App\Models\User;  
use App\Models\Project;
use App\Models\ActivityLog;  
use App\Models\ProjectSubscriber;
 
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
     * @param  int          $projectId         projectSubscriber id
     */
    public static function getActivityMessage(string $event, int $projectSubscriberId = null, int $authId, int $projectId = null,): string{

         

        // get the record
        $projectSubscriber = ProjectSubscriber::find($projectSubscriberId); 
        $project = Project::find($projectId);

        // get the user that initiated the event
        $authUser = User::find($authId); 
 

        $subscriberName =  $projectSubscriber->user->name ?? 'Subsriber unnamed';

        $subscribedProjectName = $project->name ?? 'Project unnamed'; 



        $authName = $authUser->name ?? 'Auth unnamed'; 
 
        // return message based on the event
        return match($event) {

            'updated' => "Project subscribers updated for '{$subscribedProjectName}' by '{$authName}' successfully.",

            // message for admin
            'added' => "'{$subscriberName}' has been added as a subscriber to '{$subscribedProjectName}' by '{$authName}' successfully.",
            // message for users that are added to the subsribers
            'added-usr-msg' => "You had been added as a subscriber project '{$subscribedProjectName}' by '{$authName}' successfully.",  
            default => "Action completed for '{$subscriberName}' on '{$subscribedProjectName}' ."
        };
 
    } 

    
     /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $projectSubscriberId         project_timer id optional 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event,  int  $projectSubscriberId = null  ,   int  $projectGivenId = null): string{
        $projectSubscriber = ProjectSubscriber::find($projectSubscriberId);

        $projectId = $projectSubscriber->project_id ?? null;

        $projectId = $projectGivenId;

        // dd($projectId);
        $projectDocumentId = $projectSubscriber->project_document_id ?? null;

        // return message based on the event
        return match($event) { 
              
            // only two options on route
            // if there is a project document id, then go to the project document page
            // if there is project only, then go to the project page
            default =>  
                empty($projectDocumentId) ?  
                    // if there is no project document id -> go to project page
                    route('project.show',['project' => $projectId]) :
                    // if there is project document id -> go to project document page
                    route('project.project-document.show',['project' => $projectId, 'project_document' =>  $projectDocumentId]),
        };

    }

}
