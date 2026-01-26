<?php

namespace App\Console\Commands;

use App\Models\ProjectAttachments;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AttachmentToFtp extends Command
{
    protected $signature = 'backup:attachment-to-ftp {id}';
    protected $description = 'Send a private backup file to FTP server';

    public function handle()
    {
        $id = $this->argument('id');
 
        $project_attachment = ProjectAttachments::find($id);

        // dd($project_attachment->path);


        $file = $project_attachment->stored_name;
        $path = $project_attachment->path;



        $localPath = "{$path}/{$file}";

        if (!Storage::disk('public')->exists($localPath)) {
            $this->error("File does not exist: {$localPath}");
            return Command::FAILURE;
        }else{

            // if the file is found , proceed 
            $contents = Storage::disk('public')->get($localPath);


            // check if the file is already on ftp, to not make double file upload 
            if (Storage::disk('ftp')->exists($localPath)) {
                $this->error("File in ftp server already exist: {$localPath}");
                return Command::FAILURE;
            }else{
                // if it does not exists continue uploading it 
                Storage::disk('ftp')->put($localPath, $contents);

                $this->info("Backup sent to FTP successfully.");

            }
 
        }

        

        
        return Command::SUCCESS;
    }
}
