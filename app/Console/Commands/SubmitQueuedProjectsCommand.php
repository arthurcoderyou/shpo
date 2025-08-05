<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\ActiveDays;
use App\Models\ProjectTimer;
use App\Helpers\ProjectHelper;
use Illuminate\Console\Command; 
use Illuminate\Support\Facades\Log;

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




            $errors = ProjectHelper::checkProjectRequirements();
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
                    }
                }
            }
 


            if (!empty($errorMessages)) {
                $message = 'The project cannot be submitted because: ';
                $message .= implode(', ', $errorMessages);
                $message .= '. Please wait for the admin to configure these settings.';
                
                $this->info($message);

            }else{
                $queuedProjects = Project::where('status', 'on_que')->get();

                foreach ($queuedProjects as $project) { 
                    $project->status = "submitted"; 
                    $project->updated_at = now();
                    $project->last_submitted_at = now(); 
                    $project->save();


                    ProjectHelper::updateDocumentsAndAttachments($project);


                    $submission_type = "submission";


                    ProjectHelper::setProjectReviewers($project,$submission_type);


                    $reviewer = $project->getCurrentReviewer(); // get the current reviewer


                    ProjectHelper::notifyReviewersAndSubscribers($project, $reviewer, $submission_type);

                    
                    
                    

                    // if($submission_type = "submission")
                    try {
                        event(new \App\Events\ProjectSubmitted($project, $submission_type,$project->created_by));
                    } catch (\Throwable $e) {
                        // Log the error without interrupting the flow
                        Log::error('Failed to dispatch ProjectSubmitted event: ' . $e->getMessage(), [
                            'project_id' => $project->id,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                }

                $this->info('Submitted ' . $queuedProjects->count() . ' queued project(s).');
            }











            
        } else {
            $this->info('Current time/day not valid for submission.');
        }

    }
}
