<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Project;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('project.discussions.global', function ($user) {
    // return $user->hasRole(['Admin', 'DSI God Admin']) ||
    //        $user->reviewed_projects()->exists() ||
    //        $user->created_projects()->exists();


    return $user->hasRole(['Admin', 'DSI God Admin','Reviewer','User']);

});



Broadcast::channel('project.timer', function ($user) {
    // return $user->hasRole(['Admin', 'DSI God Admin']) ||
    //        $user->reviewed_projects()->exists() ||
    //        $user->created_projects()->exists();


    return $user->hasRole(['Admin', 'DSI God Admin','Reviewer','User']);

});


Broadcast::channel('activitylog', function ($user) { 
    
    return Auth::check();

});

Broadcast::channel('document.type', function ($user) { 
    
    return Auth::check();

});

Broadcast::channel('reviewer', function ($user) { 
    
    return Auth::check();

});