<?php 
namespace App\Helpers\ActivityLogHelpers;
use Carbon\Carbon;
use App\Models\User;  
use App\Models\ActivityLog;  
use App\Models\DocumentType;  
 
class DocumentTypeLogHelper
{
  
    /**
     * Generate a ActivityLog for Role based on event
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $documentTypeId         documentType id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function logActivity(string $event = null,int $documentTypeId, int $authId){
 
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = DocumentTypeLogHelper::getActivityMessage($event,$documentTypeId,$authId);

 
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
     * @param  int          $documentTypeId         documentType id || nullable || special for this model
     * @param  int          $authId         auth id  
     * @return void         void            Not required to return value
     */
    public static function getActivityMessage(string $event,int $documentTypeId = null, int $authId): string{
 
        // get the user that initiated the event
        $authUser = User::find($authId);  
        $authName = $authUser->name ?? 'Auth unnamed'; 

        $documentType = DocumentType::find($documentTypeId);
        $documentTypeName = $documentType->name ?? 'Document unnamed'; 

 
        // return message based on the event
        return match($event) {   
            'created' => "Document type '{$documentTypeName}' has been added to the list by '{$authName}' successfully.",
            'updated' => "Document type '{$documentTypeName}' has been updated in the list by '{$authName}' successfully.",
            'deleted' => "Document type '{$documentTypeName}' has been deleted in the list by '{$authName}' successfully.",  
            'list-updated' => "Document type list updated successfully by '{$authName}'.",

            // special message
            'admin-missing-error' => 'Save cannot proceed because the system detected that there are no users with the required administrator permissions.',
            'document-type-locked' => 'Document type list cannot be updated because one of the removed document types is already used by a project.',
            default => "Action completed for project '{$documentTypeName}'."
        };
 
    } 

 
     /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $documentTypeId         documentType id optional
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event): string{
 

        // return message based on the event
        return match($event) {   
            'created' =>  route('document_type.index'),
            'updated' =>  route('document_type.index'),
            'deleted' =>   route('document_type.index'),  
            'list-updated' => route('document_type.index'),
            'document-type-locked' => route('document_type.index'),

            default =>  route('document_type.index')
        };

    }


 


}
