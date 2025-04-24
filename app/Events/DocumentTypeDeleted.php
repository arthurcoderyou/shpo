<?php

namespace App\Events;

use App\Models\DocumentType;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DocumentTypeDeleted implements ShouldBroadcastNow
{ 
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DocumentType $document_type;
    /**
     * Create a new event instance.
     */
    public function __construct(DocumentType $document_type)
    {
        $this->document_type = $document_type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('document.type'),
        ];
    }

    public function broadcastAs(){
        return "deleted";
    }


    public function broadcastWith(){
         

        return [
            'message' => "Document type '{$this->document_type->name}' had been deleted",
            'document_type_url' => route('document_type.index'),
        ];
    }
}
