<?php 
namespace App\Helpers\ActivityLogHelpers;
use App\Models\ProjectReferences;
use Carbon\Carbon;
use App\Models\User;  
use App\Models\Project;
use App\Models\ActivityLog;  
use App\Models\ProjectSubscriber;
 
class ProjectReferenceLogHelper
{



    
  
    /**
     * Generate a ActivityLog for Role based on event
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $projectReferenceId         role id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function logActivity(string $event = null,int $projectReferenceId, int $authId){

        // get the record
        $projectReference = ProjectReferences::find($projectReferenceId);
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = ProjectReferenceLogHelper::getActivityMessage($event,$projectReference->project_id,$authId);
        
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
     * @param  int          $projectReferenceId         projectSubscriber id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * @param  int          $projectId         projectSubscriber id
     */
    public static function getActivityMessage(string $event, int $projectReferenceId = null, int $authId, int $projectId = null,): string{

         

        // get the record
        $projectReference = ProjectReferences::find($projectReferenceId); 
        $project = Project::find($projectId);

        // get the user that initiated the event
        $authUser = User::find($authId); 
 

        $referencedProjectName =  $projectReference->referenced_project->name ?? 'Reference project unnamed';

        $projectName = $project->name ?? 'Project unnamed'; 



        $authName = $authUser->name ?? 'Auth unnamed'; 
 
        // return message based on the event
        return match($event) {

            'updated' => "Project references updated for '{$projectName}' by '{$authName}' successfully.",

            // message for admin
            'added' => "'{$referencedProjectName}' has been added as a subscriber to '{$projectName}' by '{$authName}' successfully.", 
            default => "Action completed for '{$projectName}' by '{$authName}' ."
        };
 
    } 

    
     /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $projectSubscriberId         project_timer id optional 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event,   int  $projectId = null): string{ 
 

        $project = Project::find($projectId);

       
        // return message based on the event
        return match($event) { 
              
            // only two options on route
            // if there is a project document id, then go to the project document page
            // if there is project only, then go to the project page
            default =>   
                    route('project.show',['project' => $project->id]),
        };

    }

}
