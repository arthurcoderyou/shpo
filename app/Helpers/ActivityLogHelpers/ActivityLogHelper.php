<?php 
namespace App\Helpers\ActivityLogHelpers;
use Carbon\Carbon;
use App\Models\User; 
use App\Models\Project;
use App\Models\ActivityLog;
use App\Events\System\SystemEvent;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Void_;
use App\Events\TargetedNotification; // or whatever event you use
 
class ActivityLogHelper
{
 
  




    // Project:  Activity log helper
    /**
     * Generate a ActivityLog for Project based on event
     * @param  string       $event        'created', 'updated', 'deleted', 
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
        $message = ActivityLogHelper::getActivityMessage($event,$projectId,$authId);
        
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
     * @param  string       $event        'created', 'updated', 'deleted', 
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
            default => "Action completed for project '{$projectName}'."
        };
 
    }


    // ./ Project:  Activity log helper










}
