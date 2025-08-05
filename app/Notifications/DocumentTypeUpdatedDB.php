<?php

namespace App\Notifications;

use App\Models\DocumentType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentTypeUpdatedDB extends Notification implements ShouldQueue
{
    use Queueable;

    protected $document_type;

    /**
     * Create a new notification instance.
     */
    public function __construct(DocumentType $document_type)
    {
        $this->document_type = $document_type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

     
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'document_type_id' => $this->document_type->id,
            'name' => $this->document_type->name, 
            'message' => "Document type '".$this->document_type->name."' had been updated",
            'url' => route('document_type.index'),
        ];
    }
}
