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
     * @param  int          $projectDiscussionId         project_discussion id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function logActivity(string $event = null, int $projectDiscussionId, int $authId){
 
        // get the user that initiated the event
        $authUser = User::find($authId); 

        $projectDiscussion = ProjectDiscussion::find($projectDiscussionId);
 
        // get the message
        $message = ProjectDiscussionLogHelper::getActivityMessage($event, $projectDiscussion->id, $authId);
        
        // save the activity log
        ActivityLog::create([
            'created_by' => $projectDiscussion->created_by,
            'log_username' => $authUser->name,
            'log_action' =>  $message,
            'project_id' =>  $projectDiscussion->project_id,   
            'project_discussion_id' => $projectDiscussion->id, 
            'project_document_id' => $project_discussion->project_document_id ?? null,
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

        $projectDocument = $projectDiscussion->project_document->document_type->name ?? 'Unnamed project document';

        // get the user that initiated the event
        $authUser = User::find($authId);  
        $authName = $authUser->name ?? 'Auth unnamed';  


        // reply message
        $reply_message = "'{$authName}' had a reply on the discussion on project '{$projectName}'.";
        // replied on you message
        $replied_on_you_messsage = "'{$authName}' had a reply on the discussion on project '{$projectName}'.";
         

        if($projectDiscussion->parent_id){
            // it means there is a source and this is a reply
            $parentProjectDiscussion = ProjectDiscussion::find($projectDiscussion->parent_id);

            $replyToUser = User::find( $parentProjectDiscussion->created_by ); // get the creator of that discussion made

            $replyToUserName = $replyToUser->name ?? "Unnamed user";

            // adjust the messasge 
            $reply_message = "Message by '{$replyToUserName }' on '{$projectName}' has been replied by '{$authName}'.";
 

            // replied on you message
            $replied_on_you_messsage = "Your message on discussion for '{$projectName}' has been replied by '{$authName}'.";


            // if there is a project document connected
            if(!empty($projectDocument) && $projectDiscussion->project_document_id){

               // adjust the messasge 
                $reply_message = "Message by '{$replyToUserName }' on document '{$projectDocument}' for project '{$projectName}' has been replied by '{$authName}'.";
    

                // replied on you message
                $replied_on_you_messsage = "Your message for document '{$projectDocument}' on project '{$projectName}' has been replied by '{$authName}'.";


            }

            
        }
 
        // if there is a project document connected
        if(!empty($projectDocument) && $projectDiscussion->project_document_id){

            // return message based on the event
            return match($event) {  
                'created' => "New Discussion created by '{$authName}' on '{$projectName}'.",
                'updated' => "Discussion message updated by '{$authName}' on '{$projectName}'.",
                'reply' => $reply_message,
                'replied-on-you' => $replied_on_you_messsage,
                'deleted' => "Message on discussion for '{$projectName}' has been deleted by '{$authName}'.",
                'mentioned-you' => "'{$authName}' had mentioned you on the discussion on project '{$projectName}'.",
                default => "Discussion '{$event}' action successfully by '{$authName}'."
            };

        }


        // project only
        // return message based on the event
        return match($event) {  
            'created' => "New Discussion created by '{$authName}' on '{$projectName}'.",
            'updated' => "Discussion message updated by '{$authName}' on '{$projectName}'.",
            'reply' => $reply_message,
            'replied-on-you' => $replied_on_you_messsage,
            'deleted' => "Message on discussion for '{$projectName}' has been deleted by '{$authName}'.",
            'mentioned-you' => "'{$authName}' had mentioned you on the discussion on project '{$projectName}'.",
            default => "Discussion '{$event}' action successfully by '{$authName}'."
        };
 
    } 

 
     /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $projectDiscussionId         project_timer id optional 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event,  int  $projectDiscussionId  ): string{
        $projectDiscussion = ProjectDiscussion::find($projectDiscussionId);

        $projectId = $projectDiscussion->project_id ?? null;
        $projectDocumentId = $projectDiscussion->project_document_id ?? null;

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
