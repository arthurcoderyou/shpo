<?php

namespace App\Listeners\User;

use App\Models\User;
use App\Models\ActivityLog;
use App\Events\User\NewUserVerified;
use Illuminate\Support\Facades\Mail;
use App\Mail\User\NewUserVerifiedMail; 
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\ActivityLogHelpers\UserLogHelper;

class NewUserVerifiedListener  implements ShouldQueue
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
    public function handle(NewUserVerified $event): void
    {
        $user = User::find($event->userId);
        $userToNotify = User::find($event->userIdToNotify);


        $viewUrl = UserLogHelper::getRoute('your-account-verified',$user->id);
        $message =  UserLogHelper::getActivityMessage('your-account-verified',$user->id, $user->id);

        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($userToNotify->email)->queue(
                   new NewUserVerifiedMail($user, $userToNotify, $viewUrl )
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch NewUserVerifiedMail mail: ' . $e->getMessage(), [ 
                    'user_id' => $user->id,
                    'userToNotify_id' => $userToNotify->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        }

         
        // activity log
        ActivityLog::create([
            'created_by' => $user->id,
            'log_username' => $user->name,
            'log_action' => $message ,  
        ]);
    }
}
