<?php 
namespace App\Helpers\ActivityLogHelpers;
use Carbon\Carbon;
use App\Models\User; 
use App\Models\Project;
use App\Models\ActivityLog;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use App\Events\System\SystemEvent;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Void_;
use App\Events\TargetedNotification; // or whatever event you use
 
class ProjectReviewerLogHelper
{
 
    
    /**
     * Generate a ActivityLog for Project based on event
     * @param  string       $event        'created', 'updated', 'deleted', 'reviewed'
     * @param  int          $projectId    project id
     * @param  int          $authId       auth id 
     * @return void         void           Not required to return value
     */
    public static function logActivity(string $event = null,int $projectReviewerId, int $authId){

        // get the project reviewer
        $project_reviewer = ProjectReviewer::find($projectReviewerId) ;
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = ProjectLogHelper::getActivityMessage($event,$projectReviewerId,$authId);
         
        
        
        // save the activity log
        ActivityLog::create([
            'created_by' => $project_reviewer->created_by,
            'log_username' => $authUser->name,
            'log_action' => $message,
            'project_id' =>  $project_reviewer->project_id,   
            'project_document_id' => $project_reviewer->project_document_id ?? null, 
            'project_reviewer_id' => $project_reviewer->id ,
        ]);




    }

    /**
     * Generate a ActivityLog message || This is also used in messages returned 
     * @param  string       $event        'created', 'updated', 'deleted', 'reviewed'
     * @param  int          $projectReviewerId    project reviewer id
     * @param  int          $authId       auth id 
     * @return void         void           Not required to return value
     */
    public static function getActivityMessage(string $event, int $projectReviewerId, int $authId): string
    {
        $project_reviewer = ProjectReviewer::find($projectReviewerId);
        $authUser = User::find($authId);

        $actorName = $authUser->name ?? 'User unnamed';

        if (!$project_reviewer) {
            return "Action completed.";
        }

        // Project
        $project = Project::find($project_reviewer->project_id);
        $projectName = $project->name ?? 'Project unnamed';

        // Optional Project Document context
        $documentName = null;
        if (!empty($project_reviewer->project_document_id)) {
            // Adjust the model name if yours is different
            $projectDocument = ProjectDocument::find($project_reviewer->project_document_id);
            $documentName = $projectDocument->document_type->name ?? null;
        }

        // Build a context phrase we can reuse everywhere
        // Example:
        // - "project 'ABC'" OR
        // - "project 'ABC' (Document: 'Site Plan')"
        $context = "project '{$projectName}'";
        if (!empty($documentName)) {
            $context .= " (Document: '{$documentName}')";
        }

        // Normalize slot fields
        $slotType = strtolower((string) ($project_reviewer->slot_type ?? '')); // open|person
        $slotRole = strtolower((string) ($project_reviewer->slot_role ?? '')); // admin|reviewer

        // Resolve assigned user if "person" slot
        $assignedUserName = null;
        if ($slotType === 'person' && !empty($project_reviewer->user_id)) {
            $assignedUserName = optional(User::find($project_reviewer->user_id))->name;
        }

        // Helper labels
        $roleLabel = match ($slotRole) {
            'admin' => 'admin reviewer',
            'reviewer' => 'reviewer',
            default => 'reviewer',
        };

        // Special handling for "created" (adding reviewer slot)
        if ($event === 'created') {
            // OPEN SLOT
            if ($slotType === 'open') {
                if ($slotRole === 'admin') {
                    return "An open review request for an admin has been added to {$context}.";
                }
                return "An open review request has been added to {$context}.";
            }

            // PERSON SLOT
            if ($slotType === 'person') {
                if (!empty($assignedUserName)) {
                    return ucfirst($roleLabel) . " '{$assignedUserName}' has been added to {$context}.";
                }
                return "A {$roleLabel} has been added to {$context}.";
            }

            return "A review assignment has been added to {$context}.";
        }

        // Default events (kept consistent with the new context + tone)
        return match ($event) {
            'updated' => ucfirst($context) . " has been updated by '{$actorName}'.",
            'deleted' => ucfirst($context) . " has been deleted by '{$actorName}'.", 

            default => "Action completed for {$context}.",
        };
    }


    /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $projectReviewerId         role id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event, int $projectReviewerId, int $authId = null): string{

        // get the record
        $project_reviewer = ProjectReviewer::find($projectReviewerId);

        // dd($project);

        $authUser = User::find($authId);

        if (!$project_reviewer) {
            return "Action completed.";
        }


        // Project 
        $project = Project::find($project_reviewer->project_id); 



        // Project Document || if there is a project document
        if($project_reviewer->project_document_id){ 

            // return message based on the event
            return match($event) { 
                'created' => route('project.project-document.show',[
                    'project' => $project->id,
                    'project_document' => $project_reviewer->project_document_id,
                ]),
                'updated' =>  route('project.project-document.show',[
                    'project' => $project->id,
                    'project_document' => $project_reviewer->project_document_id,
                ]),  

                default =>  route('project-document.index')
            };
        }

        
        // if it is only project
         
        // return message based on the event
        return match($event) { 
            'created' => route('project.show',['project' => $project->id]),
            'updated' =>  route('project.show',['project' => $project->id]),  

            default =>  route('project.index')
        };

    }

    






 
}
