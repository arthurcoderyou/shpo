<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NewUserRegisteredNotificationDB;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth; 
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Support\Facades\Notification;

class DashboardController extends Controller
{
    //dashboard
    public function dashboard(){
 
        $user = User::where('id',Auth::user()->id)->first();
 
        if(Auth::user()->roles->isEmpty()){
            // dd("true");
            // notify the admin about the new user that had registered on the website
            // Get all users with the "Admin" role
            $admin_users = User::role('Admin')->get();

            // Send notification to all admins
            if ($admin_users->isNotEmpty()) {

                // foreach($admin_users as $admin_user){
                //     Notification::send($admin_user, new NewUserRegisteredNotification($user ));
                // }



                foreach ($admin_users as $admin) {
                    $alreadyNotified = $admin->notifications()
                        ->where('type', NewUserRegisteredNotificationDB::class)
                        // ->where('notifiable_id', $admin->id)
                        ->whereJsonContains('data->user_id', $user->id)
                        ->exists();
                
                    if (!$alreadyNotified) {
                        // email notification
                        Notification::send($admin, new NewUserRegisteredNotification($user));

                        // db notification
                        Notification::send($admin, new NewUserRegisteredNotificationDB($user));

                        // auth()->user()->notify(new NewUserRegisteredNotification($user));


                    }
                }


                
            }

 

        }

        
            


        return view('dashboard');

    }
}
