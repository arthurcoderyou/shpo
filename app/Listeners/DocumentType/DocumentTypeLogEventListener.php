<?php

namespace App\Listeners\DocumentType;

use App\Events\DocumentType\DocumentTypeLogEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Models\ActivityLog;

class DocumentTypeLogEventListener implements ShouldQueue
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
    public function handle(DocumentTypeLogEvent $event): void
    {
        $message = $event->message;
        $authUser = User::find($event->authId); 

   
        // project log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' => $message ,  
        ]);
    }
}
