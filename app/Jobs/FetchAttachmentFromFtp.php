<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\ProjectAttachments; 
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class FetchAttachmentFromFtp implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $attachmentId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $attachment = ProjectAttachment::findOrFail($this->attachmentId);

        $localPath = trim($attachment->path, '/').'/'.$attachment->stored_name;

        // If already present, do nothing
        if (Storage::disk('public')->exists($localPath)) {
            return;
        }

        // Example: pull from FTP disk into local public disk
        $ftpPath = trim($attachment->ftp_path, '/'); // adjust to your schema

        $stream = Storage::disk('ftp')->readStream($ftpPath);
        if (!$stream) {
            throw new \RuntimeException("FTP file not found or unreadable: {$ftpPath}");
        }

        Storage::disk('public')->put($localPath, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }
    }
}
