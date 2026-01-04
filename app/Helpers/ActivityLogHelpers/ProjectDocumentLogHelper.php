<?php 
namespace App\Helpers\ActivityLogHelpers;
use App\Models\DocumentType;
use Carbon\Carbon;
use App\Models\User; 
use App\Models\Project;
use App\Models\ActivityLog;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Events\System\SystemEvent;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Void_;
use App\Events\TargetedNotification; // or whatever event you use
 
class ProjectDocumentLogHelper
{
 
    
    /**
     * Generate a ActivityLog for Project document based on event
     * @param  string       $event        'created', 'updated', 'deleted', 'reviewed'
     * @param  int          $projectDocumentId    project id
     * @param  int          $authId       auth id 
     * @return void         void           Not required to return value
     */
    public static function logProjectActivity(string $event = null,int $projectDocumentId, int $authId){

         // get the project
        $projectDocument = ProjectDocument::find($projectDocumentId);
        $project = $projectDocument->project;
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = ProjectDocumentLogHelper::getActivityMessage($event,$projectDocumentId,$authId);
        
        // save the activity log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' =>  $message,
            'project_id' => $project->id,
            'project_document_id' => $projectDocument->id,
        ]);

    }

    /**
     * Generate a ActivityLog message || This is also used in messages returned 
     * @param  string       $event        'created', 'updated', 'deleted', 'reviewed'
     * @param  int          $projectDocumentId    project document id
     * @param  int          $authId       auth id 
     * @return void         void           Not required to return value
     */
    public static function getActivityMessage(string $event, int $projectDocumentId, int $authId): string{

        $projectDocument = ProjectDocument::find($projectDocumentId);

        // get the project
        $project = Project::find($projectDocument->project_id);
        $document = DocumentType::find($projectDocument->document_type_id);

        // get the user that initiated the event
        $authUser = User::find($authId); 

        $projectName = $project->name ?? 'Project unnamed';
        $documentName = $document->name ?? 'Document unnamed';
        $userName = $authUser->name ?? 'User unnamed';
        

        // return message based on the event
        return match($event) {
            'created' => "Document '{$documentName}' on Project '{$projectName}' has been created by '{$userName}' successfully.",
            'updated' => "Document '{$documentName}' on Project '{$projectName}' has been updated by '{$userName}' successfully.",
            'deleted' => "Document '{$documentName}' on Project '{$projectName}' has been deleted by '{$userName}' successfully.",
            'reviewed' => "Document '{$documentName}' on Project '{$projectName}' has been reviewed by '{$userName}' successfully.",
            'submitted' => "Document '{$documentName}' on Project '{$projectName}' has been submitted by '{$userName}' successfully.",
            'on-que' => "Document '{$documentName}' on Project '{$projectName}' has been queued and will be automatically submitted by the system on the next working day.",
            'auto-submit' => "Document '{$documentName}' on Project '{$projectName}' has been automatically submitted by the system successfully.",
            'force-submit' => "Document '{$documentName}' on Project '{$projectName}' has been force submitted by '{$userName}' successfully.",
            'open-review-claimed' => "Document '{$documentName}' on Project '{$projectName}' open-review request has been claimed by '{$userName}' successfully.",
            'rc-reviewed' => "Document '{$documentName}' on Project '{$projectName}' has been reviewed by '{$userName}' successfully.", 
            'ref-updated' => "Document '{$documentName}' on Project '{$projectName}' references has been updated by '{$userName}' successfully.",
            default => "Action completed for Document '{$documentName}' on project '{$projectName}'."
        };
 
    }


    /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $projectDocumentId         role id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event, int $projectDocumentId, int $authId = null): string{

        // get the record
        $project_document = ProjectDocument::find($projectDocumentId);
            // dd($project_document);
        
        $project = $project_document->project;


        // dd($project);

        $authUser = User::find($authId);

        // dd($authId);
        $params = [];
        $routeName = 'dashboard';
        // Decide which route name to use
        // if($authUser){
        //     if ($project) {

        //         // dd($project);

        //         if ($project->created_by == $authUser->id && $authUser->can('project list view')) {
        //             // dd(1);
        //             $routeName = 'project.index';

        //         } elseif ($authUser->can('project list view all') || $authUser->can('system access global admin')) {
        //             // dd(2);
        //             $routeName = 'project.index.all';

        //         } elseif ($authUser->can('project list view all no drafts')) {
        //             // dd(3);
        //             $routeName = 'project.index.all.no-drafts';

        //         } else {
        //             // dd(vars: 4);
        //             $routeName = 'dashboard';
        //         }

        //     } else {

        //         if ($authUser->can('project list view all') || $authUser->can('system access global admin')) {

        //             $routeName = 'project.index.all';

        //         } elseif ($authUser->can('project list view all no drafts')) {

        //             $routeName = 'project.index.all.no-drafts';

        //         } elseif ($authUser->can('project list view')) { // for pure users only

        //             $routeName = 'project.index';

        //         } else {

        //             $routeName = 'dashboard';
        //         }
        //     }
        // }





        $homeRoute = route($routeName, $params);

        // return message based on the event
        return match($event) { 
            'created' => route('project.project-document.show',['project' => $project->id, 'project_document' =>  $project_document->id]),
            'updated' =>  route('project.project-document.show',['project' => $project->id,'project_document' =>  $project_document->id]),
            'reviewed' => route('project.project-document.show',['project' => $project->id,'project_document' =>  $project_document->id]),
            'submitted' => route('project.project-document.show',['project' => $project->id,'project_document' =>  $project_document->id]),  
            'auto-submit' => route('project.project-document.show',['project' => $project->id,'project_document' =>  $project_document->id]),  
            'force-submit' => route('project.project-document.show',['project' => $project->id,'project_document' =>  $project_document->id]),  
            'on-que' => route('project.project-document.show',['project' => $project->id,'project_document' =>  $project_document->id]),  
            'open-review-claimed' => route('project.review',['project' => $project->id,'project_document' =>  $project_document->id]),
            'rc-reviewed' => route('project.project-document.show',['project' => $project->id,'project_document' =>  $project_document->id]),
            'deleted' =>  route('project-document.index'),  

            default =>  route('project-document.index')
        };

    }


 
}
