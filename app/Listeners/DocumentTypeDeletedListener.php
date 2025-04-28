<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Events\DocumentTypeDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocumentTypeDeletedListener
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
    public function handle(DocumentTypeDeleted $event): void
    {
        $document_type = $event->document_type;

        ActivityLog::create([
            'created_by' => auth()->user()->id,
            'log_action' => "Document type '".$document_type->name."' had been deleted",
            'log_username' => auth()->user()->name,
        ]);
    }
}
