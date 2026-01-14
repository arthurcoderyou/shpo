<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\DBBackups;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Events\System\DBBackup\DbBackupEvent;
use App\Mail\System\DBBackup\SendDbBackupMail;
use App\Helpers\SystemNotificationHelpers\SystemNotificationHelper;
use Illuminate\Support\Facades\Log;

class BackupDBMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:send-mail {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is to send email notifications about the database backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         $id = $this->argument('id');

        $dbBackup = DBBackups::find($id);

        if (empty( $dbBackup )) {
            $this->error("DB Backup does not exists for id: {$id}");
            return Command::FAILURE;
        }
  
        // dd($dbBackup);
         $roles = [
            'global_admin',
            'admin'
        ];
        $userIds = SystemNotificationHelper::getUserIdsBasedOnRoles($roles); 


        // dd($userIds);

        foreach($userIds as  $i => $userId ){

            

            
 
            try {
                event(new DbBackupEvent( 
                    $dbBackup->id,  
                    $userId
                ));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch DbBackupEvent event: ' . $e->getMessage(), [
                    'dbBackup_id' => $dbBackup->id,
                     'userId' => $userId,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            // $user = User::where('id',$userId)->first();
            //  $this->info("Successful email: {$user->email}");
            //     Mail::to($user->email)
            //     ->later(now()->addSeconds($i * 5), new SendDbBackupMail($dbBackup, $user));



        }


        


        // update the database record 
        $dbBackup->emailed_status = true;
        $dbBackup->save();
        $this->info("Backup database record emailed_status updated to yes. ID: {$dbBackup->id}");

       
        return Command::SUCCESS;
    }
}
