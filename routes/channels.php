<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Project;

Broadcast::channel('project.discussions.global', function ($user) {
    // return $user->hasRole(['Admin', 'DSI God Admin']) ||
    //        $user->reviewed_projects()->exists() ||
    //        $user->created_projects()->exists();


    return $user->hasAnyPermission(['system access global admin', 'system access admin', 'system access reviewer','system access user']);

});


// Broadcast::channel('projects', function ($project) {
  
//     // return $user->hasAnyPermission(['system access global admin', 'system access admin', 'system access reviewer','system access user']);
//     return Auth::check();
// });





 
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});



Broadcast::channel('project-document-submitted.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

 

Broadcast::channel('attachment.{authId}', function ($user, int $authId) {
    return (int) $user->id === (int) $authId;
});
 


Broadcast::channel('system.{id}', function ($user, $id) {
    // Only allow the user with this id to listen on this channel
    return (int) $user->id === (int) $id;
});


 











/** @channel permission channel */
    // model wide
    Broadcast::channel('permission', function ($user) {
        return Auth::check();
    });

    // instance specific
    Broadcast::channel('permission.{modelId}', function ($user, $modelId) {
        
        return Auth::check();
    }); 
/** @channel ./ permission channel */
 


/** @channel user channel */
    // model wide
    Broadcast::channel('user', function ($user) {
        return Auth::check();
    });

    // instance specific
    Broadcast::channel('user.{modelId}', function ($user, $modelId) {
        return Auth::check();
    });
/** @channel ./ user channel */

/** @channel role channel */
    // model wide
    Broadcast::channel('role', function ($user) {
        return Auth::check();
    });

    // instance specific
    Broadcast::channel('role.{modelId}', function ($user, $modelId) {
        return Auth::check();
    });
    
/** @channel ./ role channel */

/** @channel document_type */
    // model wide
    Broadcast::channel('document_type', function ($userr) { 
        return Auth::check();
    });

/** @channel ./ document_type */



/** @channel project channel */
    // model wide
    Broadcast::channel('project', function ($user) {
        return Auth::check();
    });

    // instance specific
    Broadcast::channel('project.{modelId}', function ($user, $modelId) {
        return Auth::check();
    });
    
/** @channel ./ project channel */

/** @channel project_document channel */
    // model wide
    Broadcast::channel('project_document', function ($user) {
        return Auth::check();
    });

    // instance specific
    Broadcast::channel('project_document.{modelId}', function ($user, $modelId) {
        return Auth::check();
    });
    
/** @channel ./ project_document channel */


/** @channel project_timer channel */
    // model wide
    Broadcast::channel('project_timer', function ($user) {
        return Auth::check();
    });

    // instance specific
    Broadcast::channel('project_timer.{modelId}', function ($user, $modelId) {
        return Auth::check();
    });
    
/** @channel ./ project_timer channel */

 
/** @channel reviewer channel */
    // model wide
    Broadcast::channel('reviewer', function ($user) {
        return Auth::check();
    });

    // instance specific
    Broadcast::channel('reviewer.{modelId}', function ($user, $modelId) {
        return Auth::check();
    });
    
/** @channel ./ reviewer channel */
 
/** @channel project.project_discussion channel */
    /** @channel project.project_discussion channel */
        // model wide
        Broadcast::channel('project.project_discussion', function ($user) {
            return Auth::check();
        });

        // instance specific
        Broadcast::channel('project.project_discussion.{modelId}', function ($user, $modelId) {
            return Auth::check();
        });
        
    /** @channel ./ project.project_discussion channel */



    /** @channel project.project_document.project_discussion channel */
        // model wide
        Broadcast::channel('project.project_document.project_discussion', function ($user) {
            return Auth::check();
        });

        // instance specific
        Broadcast::channel('project.project_document.project_discussion.{modelId}', function ($user, $modelId) {
            return Auth::check();
        });
        
    /** @channel ./ project.project_document.project_discussion channel */

/** @channel ./  project.project_discussion channel */




/** @channel project.project_subscriber channel */
    /** @channel project.project_subscriber channel */
        // model wide
        Broadcast::channel('project.project_subscriber', function ($user) {
            return Auth::check();
        });

        // instance specific
        Broadcast::channel('project.project_subscriber.{modelId}', function ($user, $modelId) {
            return Auth::check();
        });
        
    /** @channel ./ project.project_subscriber channel */



    /** @channel project.project_document.project_subscriber channel */
        // model wide
        Broadcast::channel('project.project_document.project_subscriber', function ($user) {
            return Auth::check();
        });

        // instance specific
        Broadcast::channel('project.project_document.project_subscriber.{modelId}', function ($user, $modelId) {
            return Auth::check();
        });
        
    /** @channel ./ project.project_document.project_subscriber channel */

/** @channel ./  project.project_subscriber channel */





/** @channel project.project_reference channel */
    /** @channel project.project_reference channel */
        // model wide
        Broadcast::channel('project.project_reference', function ($user) {
            return Auth::check();
        });

        // instance specific
        Broadcast::channel('project.project_reference.{modelId}', function ($user, $modelId) {
            return Auth::check();
        });
        
    /** @channel ./ project.project_reference channel */

 
/** @channel project.project_reference channel */
