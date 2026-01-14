<?php

namespace App\Listeners\System\DBBackup;

use App\Models\User;
use App\Models\DBBackups;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Events\System\DBBackup\DbBackupEvent;
use App\Mail\System\DBBackup\SendDbBackupMail;
use App\Helpers\SystemNotificationHelpers\SystemNotificationHelper;


class DbBackupEventListener implements ShouldQueue
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
    public function handle(DbBackupEvent $event): void
    {
        $dbBackup = DBBackups::find($event->dbBackupId); 
        $user = User::find($event->targetUserId);

        // get the users shpo global admin 
 
 
    
        //  $roles = [
        //     'global_admin',
        //     'admin'
        // ];
        // $userIds = SystemNotificationHelper::getUserIdsBasedOnRoles($roles); 


        // // dd($userIds);

        // foreach($userIds as $userId){

        //     $user = User::find($userId);


        try {
            Mail::to($user->email)->queue(
                new SendDbBackupMail( $dbBackup, $user)
            );
         } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to dispatch SendDbBackupMail mail: ' . $e->getMessage(), [
                'dbBackup_id' => $dbBackup->id, 
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // project log
        ActivityLog::create([
            'created_by' =>  $user->id,
            'log_username' => $user->name,
            'log_action' => "Database backup sent to ".$user->name, 
        ]);    

 



       

         
 
        
    }
}
