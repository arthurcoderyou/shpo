<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\DocumentTypeCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocumentTypeCreatedListener
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
        $document_type = $event->document_type;

       
        ActivityLog::create([
            'created_by' => auth()->user()->id,
            'log_action' => "Document type '".$document_type->name."' had been created",
            'log_username' => auth()->user()->name,
        ]);
    }
}
