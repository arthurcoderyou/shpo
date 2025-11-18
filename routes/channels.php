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


    return $user->hasAnyPermission(['system access global admin', 'system access user', 'system access reviewer','system access user']);

});

// users channel
Broadcast::channel('users', function ($user) {
    return $user->hasPermissionTo('user list view') || $user->hasPermissionTo('system access global admin');
});

// roles channel
Broadcast::channel('roles', function ($user) {
    return $user->hasPermissionTo('role list view') || $user->hasPermissionTo('system access global admin');
});

// permissions channel
Broadcast::channel('permissions', function ($user) {
    return $user->hasPermissionTo('permission list view') || $user->hasPermissionTo('system access global admin');
});



Broadcast::channel('project.timer', function ($user) {
    // return $user->hasAnyPermission(['system access global admin']) ||
    //        $user->reviewed_projects()->exists() ||
    //        $user->created_projects()->exists();


    return $user->hasAnyPermission(['system access global admin', 'timer list view']);

});


Broadcast::channel('activitylog', function ($user) { 
    
    return Auth::check();

});

Broadcast::channel('document.type', function ($user) {
    return $user && $user->hasAnyPermission([
        'system access global admin',
        'document type list view'
    ]);
});

Broadcast::channel('reviewer', function ($user) { 
    
    return $user->hasAnyPermission(['system access global admin', 'reviewer list view']);

});

Broadcast::channel('project', function ($project) { 
    
    return Auth::check();

});

Broadcast::channel('project_document', function ($project) { 
    
    return Auth::check();

});
Broadcast::channel('project_attachment', function ($project) { 
    
    return Auth::check();

});

Broadcast::channel('project_reviewer', function ($project) { 
    
    return Auth::check();

});

Broadcast::channel('project_subscriber', function ($review) { 
    
    return Auth::check();

});

Broadcast::channel('review', function ($review) { 
    
    return Auth::check();

});


// Broadcast::channel('notifications', function ($review) { 
    
//     return Auth::check();

// });
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});



Broadcast::channel('project-document-submitted.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});



Broadcast::channel('project-document', function ($review) { 
    
    return Auth::check();

});

Broadcast::channel('attachment.{authId}', function ($user, int $authId) {
    return (int) $user->id === (int) $authId;
});


Broadcast::channel('attachments', function ($attachment) { 
    
    return Auth::check();

});

