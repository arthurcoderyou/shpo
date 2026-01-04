<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\ActiveDays;
use App\Models\ProjectTimer;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use Illuminate\Console\Command; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\Project\ProjectLogEvent;
use App\Helpers\ProjectDocumentHelpers;
use App\Helpers\ProjectReviewerHelpers;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\SystemNotificationHelpers\SystemNotificationHelper;
use App\Helpers\SystemNotificationHelpers\ProjectNotificationHelper;

class SubmitQueuedProjectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    
    protected $signature = 'submit:queued-projects';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Submits projects with queued status when time and day are valid';


    public function handle()
    {
        $projectTimer = ProjectTimer::first();

        if (!$projectTimer || !$projectTimer->project_submission_restrict_by_time) {
            $this->info('Submission not restricted, skipping...');
            return;
        }

        $currentTime = now();
        $currentDay = $currentTime->format('l');
        $openTime = Carbon::parse($projectTimer->project_submission_open_time);
        $closeTime = Carbon::parse($projectTimer->project_submission_close_time);

        $isDayActive = ActiveDays::where('day', $currentDay)
            ->where('is_active', true)
            ->exists();

        $isWithinTime = $currentTime->between($openTime, $closeTime);

        if ($isDayActive && $isWithinTime) {




            $errors = ProjectHelper::checkProjectDocumentRequirements();
            $errorMessages = [];

            foreach ($errors as $key => $error) {
                if ($error) {
                    switch ($key) {
                        case 'response_duration':
                            $errorMessages[] = 'Response duration settings are not yet configured.';
                            break;
                        case 'project_submission_times':
                            $errorMessages[] = 'Project submission times are not set.';
                            break;
                        case 'no_reviewers':
                            $errorMessages[] = 'No reviewers have been set.';
                            break;
                        case 'no_document_types':
                            $errorMessages[] = 'Document types have not been added.';
                            break;
                        case 'document_types_missing_reviewers':
                            // $message = 'Some document types have no project reviewers assigned. Please wait for the administrator to set them up. ';
                            $message = "Missing reviewers for document/s: ";
                            $message .= implode(', ', $errors['documentTypesWithoutReviewers']);
                            $errorMessages[] = $message;
                            break; 
                    }
                }
            }
    
 


            if (!empty($errorMessages)) {
                $message = 'The project cannot be submitted because: ';
                $message .= implode(', ', $errorMessages);
                $message .= '. Please wait for the admin to configure these settings.';
                
                $this->info($message);
                // dd($message);

            }else{
                $queuedProjects = Project::where('status', 'on_que')->get();

 


                $this->info($queuedProjects);

                foreach ($queuedProjects as $project) { 

 
                    $project = Project::find($project->id);
                    $project->status = "submitted"; 
                    $project->updated_at = now();
                    $project->last_submitted_at = now(); 
                    $project->save();


                    

                    // logging and system notifications
                        $authId = $project->created_by; // get the project creator

                        // get the message from the helper 
                        $message = ProjectLogHelper::getActivityMessage('auto-submit', $project->id, $authId);

                        // get the route 
                        $route = ProjectLogHelper::getRoute('auto-submit', $project->id);

                        // log the event 
                        event(new ProjectLogEvent(
                            $message ,
                            $authId, 
                            $project->id,

                        ));
                
                        /** send system notifications to users */
                            
                            $users_roles_to_notify = [
                                'admin',
                                'global_admin',
                                // 'reviewer',
                                // 'user'
                            ];  

                            


                            // set custom users that will not be notified 
                            $excluded_users = [];  
                            // $excluded_users[] = 72; // for testing only
                            // dd($excluded_users);

                            $customIds = []; 
                            $customIds[] = $authId; // includes the project creator


                            // notified users without hte popup notification | ideal in notifying the user that triggered the event without the popu
                            $customIdsNotifiedWithoutPopup = []; 

                            // dd("good ");
                            SystemNotificationHelper::sendSystemNotificationEvent(
                                $users_roles_to_notify,
                                $message,
                                $customIds,
                                'info', // use info, for information type notifications
                                [$excluded_users],
                                $route, // nullable
                                $customIdsNotifiedWithoutPopup
                            );

                        /** ./ send system notifications to users */
                    // ./ logging and system notifications



 
                    $submission_type = "initial_submission";
                    ProjectReviewerHelpers::setProjectReviewer($project,$submission_type);

 
                    // if($submission_type = "submission")
                    try {
                        event(new \App\Events\Project\Submitted($project->id,auth()->user()->id ,true,true));
                    } catch (\Throwable $e) {
                        // Log the error without interrupting the flow
                        Log::error('Failed to dispatch Submitted event: ' . $e->getMessage(), [
                            'project_id' => $project->id,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                        


                    // send notification to the current reviewer
                    ProjectReviewerHelpers::sendProjectReviewNotificationToReviewer($project,$submission_type);



                    
                    
 
                    



                }

                $this->info('Submitted ' . $queuedProjects->count() . ' queued project(s).');
            }











            
        } else {
            $this->info('Current time/day not valid for submission.');
        }

    }
}
