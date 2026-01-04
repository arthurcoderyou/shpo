<?php 
namespace App\Helpers\ActivityLogHelpers;
use App\Helpers\ProjectHelper;
use Carbon\Carbon;
use App\Models\User; 
use App\Models\Project;
use App\Models\ActivityLog;
use App\Events\System\SystemEvent;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Void_;
use App\Events\TargetedNotification; // or whatever event you use
 
class ProjectLogHelper
{
    
    
    /**
     * Generate a ActivityLog for Project based on event
     * @param  string       $event        'created', 'updated', 'deleted', 'reviewed'
     * @param  int          $projectId    project id
     * @param  int          $authId       auth id 
     * @return void         void           Not required to return value
     */
    public static function logProjectActivity(string $event = null,int $projectId, int $authId){

         // get the project
        $project = Project::find($projectId);
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = ProjectLogHelper::getActivityMessage($event,$projectId,$authId);
        
        // save the activity log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' =>  $message,
            'project_id' => $project->id,
        ]);

    }

    /**
     * Generate a ActivityLog message || This is also used in messages returned 
     * @param  string       $event        'created', 'updated', 'deleted', 'reviewed'
     * @param  int          $projectId    project id
     * @param  int          $authId       auth id 
     * @return void         void           Not required to return value
     */
    public static function getActivityMessage(string $event, int $projectId, int $authId): string{ 

        // get the project
        $project = Project::find($projectId);
        // get the user that initiated the event
        $authUser = User::find($authId); 

        $projectName = $project->name ?? 'Project unnamed';
        $userName = $authUser->name ?? 'User unnamed';
        

        // return message based on the event
        return match($event) {
            'created' => "Project '{$projectName}' has been created by '{$userName}' successfully.",
            'updated' => "Project '{$projectName}' has been updated by '{$userName}' successfully.",
            'deleted' => "Project '{$projectName}' has been deleted by '{$userName}' successfully.",
            'reviewed' => "Project '{$projectName}' has been reviewed by '{$userName}' successfully.",
            'submitted' => "Project '{$projectName}' has been submitted by '{$userName}' successfully.",
            'on-que' => "Project '{$projectName}' has been queued and will be automatically submitted by the system on the next working day.",
            'auto-submit' => "Project '{$projectName}' has been automatically submitted by the system successfully.",
            'force-submit' => "Project '{$projectName}' has been force submitted by '{$userName}' successfully.",
            'open-review-claimed' => "Project '{$projectName}' open-review request has been claimed by '{$userName}' successfully.",
            'your-open-review-claimed' => "Your project '{$projectName}' has entered the review stage and is currently under evaluation.", 
            'rc-reviewed' => "Project '{$projectName}' has been reviewed by '{$userName}' successfully.",
            'ref-updated' => "Project '{$projectName}' references has been updated by '{$userName}' successfully.",
            default => "Action completed for project '{$projectName}'."
        };
 
    }


    /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $projectId         role id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event, int $projectId, int $authId = null): string{

        // get the record
        $project = Project::find($projectId);

        // dd($project);

        $authUser = User::find($authId);

        // dd($authId);
        $params = [];
        $routeName = 'dashboard';
        // Decide which route name to use
        if($authUser){
            if ($project) {

                // dd($project);

                if ($project->created_by == $authUser->id && $authUser->can('project list view')) {
                    // dd(1);
                    $routeName = 'project.index';

                } elseif ($authUser->can('project list view all') || $authUser->can('system access global admin')) {
                    // dd(2);
                    $routeName = 'project.index.all';

                } elseif ($authUser->can('project list view all no drafts')) {
                    // dd(3);
                    $routeName = 'project.index.all.no-drafts';

                } else {
                    // dd(vars: 4);
                    $routeName = 'dashboard';
                }

            } else {

                if ($authUser->can('project list view all') || $authUser->can('system access global admin')) {

                    $routeName = 'project.index.all';

                } elseif ($authUser->can('project list view all no drafts')) {

                    $routeName = 'project.index.all.no-drafts';

                } elseif ($authUser->can('project list view')) { // for pure users only

                    $routeName = 'project.index';

                } else {

                    $routeName = 'dashboard';
                }
            }
        }





        $homeRoute = route($routeName, $params);

        // return message based on the event
        return match($event) { 
            'created' => route('project.show',['project' => $project->id]),
            'updated' =>  route('project.show',['project' => $project->id]),
            'reviewed' => route('project.show',['project' => $project->id]),
            'submitted' => route('project.show',['project' => $project->id]),  
            'auto-submit' => route('project.show',['project' => $project->id]),  
            'force-submit' => route('project.show',['project' => $project->id]),  
            'on-que' => route('project.show',['project' => $project->id]),  
            'open-review-claimed' => route('project.review',['project' => $project->id]),
            'your-open-review-claimed' => route('project.show',['project' => $project->id]),
            'rc-reviewed' => route('project.show',['project' => $project->id]),
            'deleted' =>  $homeRoute,  

            default =>  route('project.index')
        };

    }


 
}
