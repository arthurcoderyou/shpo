<?php

namespace App\Console\Commands;

use App\Models\DBBackups;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupToFtpViaID extends Command
{
    protected $signature = 'backup:ftp-via-id {id}';
    protected $description = 'Send a private backup file to FTP server via ID';

    public function handle()
    {
        $id = $this->argument('id');

        $dbBackup = DBBackups::find($id);

        if (empty( $dbBackup )) {
            $this->error("DB Backup does not exists for id: {$id}");
            return Command::FAILURE;
        }
 
        $file = $dbBackup->file;
        $folder = $dbBackup->folder;
        

        $localPath = "{$folder}/{$file}";

        if (!Storage::disk('local')->exists($localPath)) {
            $this->error("File does not exist: {$localPath}");
            return Command::FAILURE;
        }

        $contents = Storage::disk('local')->get($localPath);

        // we passed it to ftp having the same name 

        Storage::disk('ftp')->put($localPath, $contents);
        $this->info("Backup sent to FTP successfully at path {$localPath}. ");

        // update the database record 
        $dbBackup->ftp_copied_status = true;
        $dbBackup->save();
        $this->info("Backup database record ftp_copied_status updated to yes. ID: {$dbBackup->id}");

        
        // send email notifications
        // send backup to ftp
        $this->call('backup:send-mail', ['id' => $dbBackup->id]);


        return Command::SUCCESS;
    }
}
