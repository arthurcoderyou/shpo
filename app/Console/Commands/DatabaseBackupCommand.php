<?php

namespace App\Console\Commands;

use App\Models\DBBackups;
use Carbon\Carbon;
use Illuminate\Console\Command; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 
use Symfony\Component\Process\Process;


class DatabaseBackupCommand extends Command
{

    protected $signature = 'backup:database {--storage : Include storage files}';
    protected $description = 'Backup database and optionally storage files to local storage';

    public function handle(): int
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $backupDir = "backups/{$timestamp}";

        Storage::disk('local')->makeDirectory($backupDir);

        $this->backupDatabase($backupDir);

        // if ($this->option('storage')) {
        //     $this->backupStorage($backupDir);
        // }

        
        return Command::SUCCESS;
    }

    protected function backupDatabase(string $backupDir): void
    {
        $connection = config('database.default');
        $config = config("database.connections.$connection");

        $filename = $config['database'].".sql"; // database.sql originally replaced with our database
        $path = Storage::disk('local')->path("$backupDir/$filename");

        if ($config['driver'] !== 'mysql') {
            $this->error('Only MySQL is supported in this command.');
            return;
        }

        $command = sprintf(
            'mysqldump -h%s -u%s %s %s > %s',
            escapeshellarg($config['host']),
            escapeshellarg($config['username']),
            $config['password'] ? '-p' . escapeshellarg($config['password']) : '',
            escapeshellarg($config['database']),
            escapeshellarg($path)
        );

        exec($command, $output, $result);
        $this->info("Backup generated successfully at {$path}.");

        // save into database 
        $db_backup = DBBackups::create([
            'file' =>  $filename ,    
            'folder' => $backupDir,
            'emailed_status' => false,
            'ftp_copied_status' => false,
        ]);
        $this->info("Backup database record saved successfully. ID: {$db_backup->id}");


        // send backup to ftp
        $this->call('backup:ftp-via-id', ['id' => $db_backup->id]);
        
         
        if ($result !== 0) {
            $this->error('Database backup failed.');
        } else {
            $this->info('Database backup completed.');
        }
    }

    // protected function backupStorage(string $backupDir): void
    // {
    //     $zipPath = storage_path("app/$backupDir/storage.zip");
    //     $storagePath = storage_path('app/public');

    //     $command = sprintf(
    //         'zip -r %s %s',
    //         escapeshellarg($zipPath),
    //         escapeshellarg($storagePath)
    //     );

    //     exec($command, $output, $result);

    //     if ($result !== 0) {
    //         $this->error('Storage backup failed.');
    //     } else {
    //         $this->info('Storage backup created.');
    //     }
    // }

 
    // HOW TO USE 
    /**
        ### Database only

        ```bash
        php artisan backup:project
        ```

        ### Database + storage files

        ```bash
        php artisan backup:project --storage
```
     * 
     */


}
