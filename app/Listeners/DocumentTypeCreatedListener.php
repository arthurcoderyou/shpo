<?php

namespace App\Listeners;

use App\Models\DocumentType;
use App\Models\User;
use App\Models\ActivityLog;
use App\Events\DocumentTypeCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\DocumentTypeCreatedDB;
use Illuminate\Support\Facades\Notification;

class DocumentTypeCreatedListener  implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DocumentTypeCreated $event): void
    { 
        $document_type_id = $event->document_type_id;
        $document_type =  DocumentType::find($document_type_id);
        $authId = $event->authId;
        $auth_user = User::find($authId);
       
        ActivityLog::create([
            'created_by' => $authId,
            'log_action' => "Document type '".$document_type->name."' had been created",
            'log_username' => $auth_user->name,
        ]);



        

        // add notification about document to all users with permission
        // add db notification 
        $permissionNames = [
            'system access global admin',
            'system access admin',
            'document type list view',
        ];

        $users = User::whereHas('permissions', function ($query) use ($permissionNames) {
            $query->whereIn('name', $permissionNames);
        })->orWhereHas('roles.permissions', function ($query) use ($permissionNames) {
            $query->whereIn('name', $permissionNames);
        })->get();


        if(!empty($users)){
            foreach($users as $user){
                 try {
                    // Send email and DB notification to reviewer
                    Notification::send($user, new DocumentTypeCreatedDB($document_type));
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to send DocumentTypeCreatedDB notification: ' . $e->getMessage(), [
                        'document_type_id' => $document_type->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

            }

        }
           



    }
}
