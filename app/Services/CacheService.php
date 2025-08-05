<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\User;

class CacheService
{



    // remember to check the authenticated user if the users is an Admin or DSI God Admin



    // User Model Cache Updates
    public static function updateUserStats()
    {

        /** usersAllTime */
            Cache::forever('usersAllTime', 
                User::countUsers()
            ); 
            Cache::forever('admin-usersAllTime',
                User::countUsers(null,null,null, true)
            ); 

        /** usersUpdatePending */
            Cache::forever('usersUpdatePending',  
                User::countUsers(null, 'user', true)
            );  

            Cache::forever('admin-usersUpdatePending',  
                User::countUsers(null, 'user', true)
            ); 
            
        /** reviewersUpdatePending */
            Cache::forever('reviewersUpdatePending',  
                User::countUsers(null, 'reviewer', true)
            );  

            Cache::forever('admin-reviewersUpdatePending',  
                User::countUsers(null, 'reviewer', true)
            ); 

 
    }   



    // Project Model Stats 
    public static function updateProjectStats(){
        /** projectAllTime */
            Cache::forever('projectsAllTime', 
                User::countUsers()
            ); 
            Cache::forever('admin-usersAllTime',
                User::countUsers(null,null,null, true)
            ); 
    }





}
