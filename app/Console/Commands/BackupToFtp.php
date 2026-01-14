<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;  
use Illuminate\Support\Facades\Storage;

class BackupToFtp extends Command
{
    protected $signature = 'backup:ftp {file}';
    protected $description = 'Send a private backup file to FTP server';

    public function handle()
    {
        $file = $this->argument('file');
 

        $localPath = "backups/2026-01-14_02-14-52/{$file}";

        if (!Storage::disk('local')->exists($localPath)) {
            $this->error("File does not exist: {$localPath}");
            return Command::FAILURE;
        }

        $contents = Storage::disk('local')->get($localPath);

        $dir = "backups/2026-01-14_02-14-52/{$file}";

        Storage::disk('ftp')->put($dir, $contents);

        $this->info("Backup sent to FTP successfully.");
        return Command::SUCCESS;
    }
}
