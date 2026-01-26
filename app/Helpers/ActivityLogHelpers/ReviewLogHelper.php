<?php 
namespace App\Helpers\ActivityLogHelpers;
use App\Helpers\ProjectHelper;
use App\Models\Review;
use Carbon\Carbon;
use App\Models\User; 
use App\Models\Project;
use App\Models\ActivityLog;
use App\Events\System\SystemEvent;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Void_;
use App\Events\TargetedNotification; // or whatever event you use
 
class ReviewLogHelper
{
    
    
    /**
    //  * Generate a ActivityLog for Project based on event
    //  * @param  string       $event        'created', 'updated', 'deleted', 'reviewed'
    //  * @param  int          $projectId    project id
    //  * @param  int          $authId       auth id 
    //  * @return void         void           Not required to return value
    //  */
    // public static function logProjectActivity(string $event = null,int $projectId, int $authId){

    //      // get the project
    //     $project = Project::find($projectId);
    //     // get the user that initiated the event
    //     $authUser = User::find($authId); 
 
    //     // get the message
    //     $message = ProjectLogHelper::getActivityMessage($event,$projectId,$authId);
        
    //     // save the activity log
    //     ActivityLog::create([
    //         'created_by' => $authUser->id,
    //         'log_username' => $authUser->name,
    //         'log_action' =>  $message,
    //         'project_id' => $project->id,
    //     ]);

    // }

    /**
     * Generate a ActivityLog message || This is also used in messages returned 
     * @param  string       $event        'created', 'updated', 'deleted', 'reviewed' 
     * @param  int          $reviewId    review id
     * @param  int          $authId       auth id 
     * @param  string       $target       target proponent on the message
     * @return void         void           Not required to return value
     */
    public static function getActivityMessage(string $event,  int $reviewId, int $authId,string $target): string{ 

        // get the review
        $review = Review::find($reviewId);

        

        // get the project
        $project = Project::find($review->project_id);


        // get the user that initiated the event
        $authUser = User::find($authId); 

        $projectName = $project->name ?? 'Project unnamed';
        $userName = $authUser->name ?? 'User unnamed';
        $review_status =    $review->review_status;



        $rc_reviewed_message = "RC {$review_status} for {$projectName}";
         

        $target = $target ?? 'general';

        if ($target === 'general') {

            if ($review_status === 'reviewed') {

                $rc_reviewed_message =
                    "RC review completed for {$projectName}. An RC Number has been issued and the project is approved.";

            } elseif ($review_status === 'changes_requested') {

                $rc_reviewed_message =
                    "RC review completed for {$projectName}. Changes have been requested.";

            } elseif ($review_status === 'approved') {

                $rc_reviewed_message =
                    "RC review completed for {$projectName}. The project has been approved.";

            } else {

                $rc_reviewed_message =
                    "RC review update recorded for {$projectName}.";
            }

        } elseif ($target === 'submitter') {

            if ($review_status === 'reviewed') {

                $rc_reviewed_message =
                    "Your project {$projectName} has completed RC review. An RC Number has been issued and your project is approved.";

            } elseif ($review_status === 'changes_requested') {

                $rc_reviewed_message =
                    "Your project {$projectName} has completed RC review. Please review and address the requested changes.";

            } elseif ($review_status === 'approved') {

                $rc_reviewed_message =
                    "Your project {$projectName} has been approved following RC review.";

            } else {

                $rc_reviewed_message =
                    "There is an update regarding the RC review of your project {$projectName}.";
            }
        }


        // return message based on the event
        return match($event) {
            'rc-reviewed' => $rc_reviewed_message, 
            default => "Action completed for project '{$projectName}'."
        };
 
    }

 

 
}
