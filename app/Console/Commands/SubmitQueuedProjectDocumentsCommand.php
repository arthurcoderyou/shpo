<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Project;
use App\Models\ActiveDays;
use App\Models\ProjectTimer;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ProjectDocumentHelpers;
use App\Helpers\ProjectReviewerHelpers;

class SubmitQueuedProjectDocumentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:submit-queued-project-documents-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Submits project documents with queued status when time and day are valid';

    /**
     * Execute the console command.
     */
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




            $errors = ProjectDocumentHelpers::checkProjectDocumentRequirements();
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

            }else{
                $queuedProjectDocuments = ProjectDocument::where('status', 'on_que')->get();

                foreach ($queuedProjectDocuments as $project_document) { 


                    

    
                    $submission_type = "initial_submission";
            
                    // update project document
                    ProjectDocumentHelpers::updateDocumentAndAttachments(
                        $project_document,
                        "submitted", 
                        false
                    );
                            
                      
                    $project = Project::find($project_document->project_id);
                    $project->status = "submitted"; 
                    $project->updated_at = now();
                    $project->last_submitted_at = now(); 
                    $project->save();

 

                    // set the project document reviewers
                    ProjectReviewerHelpers::setProjectDocumentReviewers($project_document, $submission_type);


                    try {
                        event(new \App\Events\ProjectDocument\Submitted(
                            $project_document->id, 
                            Auth::user()->id ?? $project->created_by,
                            true,
                            true));
                    } catch (\Throwable $e) {
                        // Log the error without interrupting the flow
                        Log::error('Failed to dispatch Submitted event: ' . $e->getMessage(), [
                            'project_id' => $project->id,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }


                    // set the project document reviewers
                    ProjectReviewerHelpers::sendNotificationOnReviewerListUpdate($project_document);

                    // send notification to the current reviewer
                    ProjectReviewerHelpers::sendReviewNotificationToReviewer($project_document, $submission_type);


                     

                }

                $this->info('Submitted ' . $queuedProjectDocuments->count() . ' queued project(s).');
            }











            
        } else {
            $this->info('Current time/day not valid for submission.');
        }
    }
}
