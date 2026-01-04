<?php

namespace App\Listeners\User;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\User\NewUserVerificationRequest;
use App\Mail\User\NewUserVerificationRequestMail;
use App\Helpers\ActivityLogHelpers\UserLogHelper; 

class NewUserVerificationRequestListener implements ShouldQueue
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
    public function handle(NewUserVerificationRequest $event): void
    {
 
        $user = User::find($event->userId);
        $userToNotify = User::find($event->userIdToNotify);


        $viewUrl = UserLogHelper::getRoute('new-user-verification-request',$user->id);
        $message =  UserLogHelper::getActivityMessage('new-user-verification-request',$user->id, $user->id);

        // if mail is true 
        if($event->sendMail){
            
            try {
                Mail::to($userToNotify->email)->queue(
                   new NewUserVerificationRequestMail($user, $userToNotify, $viewUrl )
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch NewUserVerificationRequestMail mail: ' . $e->getMessage(), [ 
                    'user_id' => $user->id,
                    'userToNotify_id' => $userToNotify->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        }

         
        // project log
        ActivityLog::create([
            'created_by' => $userToNotify->id,
            'log_username' => $userToNotify->name,
            'log_action' => $message ,  
        ]);
    }
}
