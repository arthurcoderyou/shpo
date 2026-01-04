<?php 
namespace App\Helpers\ActivityLogHelpers;
use App\Events\System\SystemEvent;
use App\Events\TargetedNotification; // or whatever event you use
use App\Helpers\ActivityLogHelpers\ProjectReferenceLogHelper;
use App\Helpers\ProjectHelper;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectReferences;
use App\Models\User; 
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Void_;
 
class ProjectReferenceLogHelper
{
 
    
    // /**
    //  * Generate a ActivityLog for Project document based on event
    //  * @param  string       $event        'created', 'updated', 'deleted', 'reviewed'
    //  * @param  int          $projectReferenceId    project id
    //  * @param  int          $authId       auth id 
    //  * @return void         void           Not required to return value
    //  */
    // public static function logProjectActivity(string $event = null,int $projectReferenceId, int $authId){

    //     // get the instance
    //     $projectReference = ProjectReferences::find($projectReferenceId);
    //     $project = Project::find($projectReference->project_id);
    //     $referenced_project = Project::find($projectReference->referenced_project_id);

    //     // get the user that initiated the event
    //     $authUser = User::find($authId); 
 
    //     // get the message
    //     $message = ProjectReferenceLogHelper::getActivityMessage($event,$projectDocumentId,$authId);
        
    //     // save the activity log
    //     ActivityLog::create([
    //         'created_by' => $authUser->id,
    //         'log_username' => $authUser->name,
    //         'log_action' =>  $message,
    //         'project_id' => $project->id,
    //         'project_document_id' => $projectDocument->id,
    //     ]);

    // }

    /**
     * Generate a ActivityLog message || This is also used in messages returned 
     * @param  string       $event        'created', 'updated', 'deleted', 'reviewed'
     * @param  int          $projectReferenceId    project reference id
     * @param  int          $authId       auth id 
     * @return void         void           Not required to return value
     */
    public static function getActivityMessage(string $event, int $projectReferenceId, int $authId): string{

        $projectReference = ProjectReferences::find($projectReferenceId);

        $project = Project::find($projectReference->project_id);
        $referenced_project = Project::find($projectReference->referenced_project_id);

        // get the user that initiated the event
        $authUser = User::find($authId); 

        $projectName = $project->name ?? 'Project unnamed';
        $referencedProjectName = $referenced_project->name ?? 'Referened Project unnamed';
        $userName = $authUser->name ?? 'User unnamed';
        

        // return message based on the event
        return match($event) {
            'referenced' => "Project '{$referencedProjectName}' had been added as a reference on Project '{$projectName}' by '{$userName}' successfully.", 
            default => "Action completed for Project '{$projectName}' referencing the project '{$referencedProjectName}'."
        };
 
    }


    /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $projectDocumentId         role id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event, int $projectReferenceId, int $authId = null): string{

        // get the record
        $projectReference = ProjectReferences::find($projectReferenceId);
            // dd($project_document);
        
        $project = Project::find($projectReference->project_id);


        // dd($project);

        $authUser = User::find($authId);

          
        // return message based on the event
        return match($event) { 
            'referenced' => route('project.show',['project' => $project->id]),  

            default =>  route('project-document.index')
        };

    }


 
}
