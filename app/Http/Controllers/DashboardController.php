<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Notification;
use App\Events\User\NewUserVerificationRequest;
use App\Helpers\ActivityLogHelpers\UserLogHelper;
use App\Notifications\NewUserRegisteredNotification;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Notifications\System\SystemEventNotification;
use App\Notifications\NewUserRegisteredNotificationDB;
use App\Helpers\SystemNotificationHelpers\SystemNotificationHelper;

class DashboardController extends Controller
{
    //dashboard
    public function dashboard(){
 
        // abort(429);


        $user = User::where('id',Auth::user()->id)->first();
        
        // For new database
         
        if(Auth::user()->roles->isEmpty()){
            // dd("true");
            // notify the admin about the new user that had registered on the website
            // Get all users with the "Admin" role
            $admin_users = User::permission([
                'system access global admin',
                'system access admin',
            ])->get();

            // dd($admin_users );

            // Send notification to all admins
            if ($admin_users->isNotEmpty()) {

                // foreach($admin_users as $admin_user){
                //     Notification::send($admin_user, new NewUserRegisteredNotification($user ));
                // }

                // dd("Here");

                $authId = Auth::check() ? Auth::id() : $user->id;

                // get the message from the helper 
                $message = UserLogHelper::getActivityMessage('new-user-verification-request', $user->id, $authId);

                // get the route 
                $route =  UserLogHelper::getRoute('new-user-verification-request', $user->id); 

                /** send system notifications to users */
                    // check  ActivityLogHelper::sendSystemNotificationEvent() function to understand how to user this.

                    $users_roles_to_notify = [
                        'admin',
                        'global_admin',
                        // 'reviewer',
                        // 'user'
                    ];  

                    // set custom users that will not be notified 
                    $excluded_users = []; 

                    foreach ($admin_users as $admin) {
                        $alreadyNotified = $admin->notifications()
                            ->where('type', SystemEventNotification::class)
                            // ->where('notifiable_id', $admin->id)
                            ->where('data->message', $message)           // same message
                            ->exists();
                    
                        if ($alreadyNotified) {
                            $excluded_users[] = $admin->id; 
                        }else{

                            // send email notification 
                            $userId = $user->id;
                            $userIdToNotify = $admin->id;


                            // You can adapt this to your actual event
                            event(new NewUserVerificationRequest(
                                $userId ,
                                $userIdToNotify,
                                true

                            ));


                        }
                    }


                    // dd($excluded_users);
                    // set the custom ids 
                    // none at this point

                   
                    
                    // $excluded_users[] = 72; // for testing only
                    // dd($excluded_users);

                    // dd("good ");
                    SystemNotificationHelper::sendSystemNotificationEvent(
                        $users_roles_to_notify,
                        $message,
                        [],
                        'info', // use info, for information type notifications
                        [$excluded_users],
                        $route// nullable
                        
                    ); 


                /** ./ send system notifications to users */







                
            }

 

        } 


        // for old database
        /*
        if (Auth::user()->roles->isEmpty()) {
            // Get all users with the "Admin" role
            $admin_users = User::role('Admin')->get();

            if ($admin_users->isNotEmpty()) {
                foreach ($admin_users as $admin) {
                    // Manual LIKE query because JSON_CONTAINS doesn't work in MariaDB 10.1
                    $alreadyNotified = $admin->notifications()
                        ->where('type', NewUserRegisteredNotificationDB::class)
                        ->where('data', 'like', '%"user_id":' . $user->id . '%')
                        ->exists();

                    if (!$alreadyNotified) {
                        // email notification
                        Notification::send($admin, new NewUserRegisteredNotification($user));

                        // db notification
                        Notification::send($admin, new NewUserRegisteredNotificationDB($user));
                    }
                }
            }
        }
            */

        
            


        return view('dashboard');

    }
}
