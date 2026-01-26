<?php

namespace App\Livewire\Components\ProjectReview;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Setting;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\ProjectTimer;
use Livewire\WithFileUploads;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use App\Models\ProjectAttachments;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ProjectDocumentHelpers;
use App\Helpers\ProjectReviewerHelpers;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use App\Events\ProjectDocument\ProjectDocumentLogEvent;
use App\Events\ProjectReviewer\ProjectReviewerLogEvent;
use App\Helpers\ActivityLogHelpers\ProjectDocumentLogHelper;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Helpers\SystemNotificationHelpers\ProjectDocumentNotificationHelper;

class ResubmitComponent extends Component
{

    public ProjectReviewer $project_reviewer;


    use WithFileUploads;
    public $review_status;
    public $project_review;
    public $project;

    public $project_document;

    public $attachments = []; // Initialize with one phone field 

    public $rc_number;
    public $allotted_review_time_hours; 
 
    public $project_id;
    public $project_document_id;
 


    public $counts; 
     public $existingFiles;
 
        
    public function mount($project_reviewer_id){


        $this->project_reviewer = ProjectReviewer::find($project_reviewer_id);

        $this->project = Project::find( $this->project_reviewer->project_id);

        $this->project_document = ProjectDocument::find( $this->project_reviewer->project_document_id );

        $this->project_id = $this->project->id;
        $this->project_document_id = $this->project_document->id; 
        $this->loadData();
    }

    public function loadData(){
 
        $project_document = ProjectDocument::findOrFail($this->project_document_id);

        // dd($this->project_document );

        $project_document->refresh();
        

        $this->counts = $project_document->attachmentTypeCounts();
        $this->existingFiles = $this->getExistingFilesProperty();
    }   


 



    public function getExistingFilesProperty()
    {
        if (empty($this->project_document) || $this->project_document->project_attachments->isEmpty()) {
            return [];
        }

       return $this->project_document->project_attachments
            ->sortByDesc('created_at')
            ->groupBy(function ($document) {
                return optional($document->last_submitted_at)->format('M d, Y h:i A') ?? 'Unsubmitted';
            })
            ->toArray();

    }



    public function save(){
        // dd("Here");

        // $document_upload_location = Setting::getOrCreateWithDefaults('document_upload_location');

        // if(empty($document_upload_location) && empty($document_upload_location->value) ){

        //     Alert::error('Error','The Document Upload location had not been set by the administrator');
        //     return redirect()->route('project.project_document.edit_attachments',[
        //         'project' => $this->project->id,
        //         'project_document' => $this->project_document->id
        //     ] );

        // } 


        

        // // Check FTP connection before processing
        // try {
        //     Storage::disk($document_upload_location->value)->exists('/'); // Basic check
        //     // dd($document_upload_location->value." works");

        // } catch (\Exception $e) {
        //     // Handle failed connection
        //     // logger()->error("FTP connection failed: " . $e->getMessage());
        //     // return; // Exit or show error as needed

        //     Alert::error('Error','Connection cannot be stablished with the '.$document_upload_location->value.' server');
        //     return redirect()->route('project.project_document.edit_attachments',[
        //         'project' => $this->project->id,
        //         'project_document' => $this->project_document->id
        //     ] );

        // }



        // document_type_id

        $this->validate([
            
            'attachments' => [
                function ($attribute, $value, $fail) {
                    if (!empty($this->document_type_id)) {
                        if (empty($value) || !is_array($value) || count($value) < 1) {
                            $fail('The attachments field is required and must contain at least one item when a document type is selected.');
                        }
                    }
                },
            ],


        ]);


 
        $project_document = ProjectDocument::findOrFail($this->project_document_id);
        $project_document->updated_by = Auth::user()->id;
        $project_document->updated_at = now();
        $project_document->save();


        $project = Project::findOrFail($this->project_id);
        $project->updated_by = Auth::user()->id;
        $project->updated_at = now();
        $project->save();
 



        if (!empty($this->attachments)) {
            $now = Carbon::now();
            $dir = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}/".$now->format('Ymd_His');
            $disk = 'public';

            // Flatten in case you have nested arrays from the UI
            foreach ($this->attachments as $file) {

                // Case 1: Livewire/HTTP-uploaded file objects
                if ($file instanceof TemporaryUploadedFile || $file instanceof \Illuminate\Http\UploadedFile) {
                    $originalName = $file->getClientOriginalName();
                    $ext   = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
                    $mime  = $file->getMimeType();
                    $size  = $file->getSize() ?? 0;
                    $tmpPath = $file->getRealPath();

                    $storedName = $now->format('Ymd_His').'-'.$originalName;
                    $file->storeAs($dir, $storedName, $disk);

                // Case 2: Your code supplied an array (e.g., from a custom uploader)
                } elseif (is_array($file)) {
                    // Try to read conventional keys; adjust as needed
                    $originalName = $file['name'] ?? 'attachment';
                    $tmpPath      = $file['tmp_path'] ?? $file['path'] ?? null;

                    if (!$tmpPath || !is_readable($tmpPath)) {
                        Log::warning('Skipping attachment with missing/unreadable tmp path', ['file' => $file]);
                        continue;
                    }

                    $ext  = strtolower($file['extension'] ?? pathinfo($originalName, PATHINFO_EXTENSION));
                    $mime = $file['mime'] ?? @mime_content_type($tmpPath) ?: null;
                    $size = $file['size'] ?? @filesize($tmpPath) ?: 0;

                    $storedName = $now->format('Ymd_His').'-'.$originalName;
                    // Copy the file into your target disk/folder
                    Storage::disk($disk)->putFileAs($dir, new \Illuminate\Http\File($tmpPath), $storedName);

                } else {
                    // Unknown type; skip
                    Log::warning('Skipping unsupported attachment type', ['type' => gettype($file)]);
                    continue;
                }

                // Optional integrity/dimensions
                $sha256 = (isset($tmpPath) && is_readable($tmpPath)) ? @hash_file('sha256', $tmpPath) : null;
                $width = $height = null;
                if ($mime && str_starts_with(strtolower($mime), 'image/') && isset($tmpPath) && is_readable($tmpPath)) {
                    try { [$width, $height] = getimagesize($tmpPath) ?: [null, null]; } catch (\Throwable $e) {}
                }

                // Save DB row (NOTE: use your actual column names; if it's snake_case, change 'storedName' -> 'stored_name')
                ProjectAttachments::create([
                    'attachment'           => $originalName,
                    'project_id'           => $project->id,
                    'project_document_id'  => $project_document->id,
                    'filesystem'           => $disk,
                    'original_name'        => $originalName,
                    'stored_name'           => $storedName,      // or 'stored_name' if that’s your column
                    'disk'                 => $disk,
                    'path'                 => $dir,
                    'mime_type'            => $mime,
                    'extension'            => $ext,
                    'width'                => $width,
                    'height'               => $height,
                    'duration_seconds'     => null,
                    'sha256'               => $sha256,
                    'created_by'           => Auth::id(),
                    'updated_by'           => Auth::id(),
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ]);
            }
        }


        // ActivityLog::create([
        //     'log_action' => "Project attachments on \"".$project_document->document_type->name."\" updated ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
         // ]);

        // Alert::success('Success',"Project attachments on \"".$project_document->document_type->name."\" updated ");


        // logging and system notifications
            $authId = Auth::id() ?? null;
            $projectId = $project_document->project_id; 

            // logging for the project document 
                // Success message from the activity log project helper
                $message =  ProjectDocumentLogHelper::getActivityMessage('updated',$project_document->id,$authId);
        
                // get the route 
                $route = ProjectDocumentLogHelper::getRoute('updated', $project_document->id);
                

                // // log the event 
                event(new ProjectDocumentLogEvent(
                    $message ,
                    $authId, 
                    $projectId,
                    $project_document->id,

                ));
            // ./ logging for the project document  
            

            /** send system notifications to users */
                /** send system notifications to users */
                    
                    ProjectDocumentNotificationHelper::sendSystemNotification(
                        message: $message,
                        route: $route 
                    );

                /** ./ send system notifications to users */
            /** ./ send system notifications to users */
        // ./ logging and system notifications


        // return redirect()->route('project.project-document.show',[
        //     'project' => $this->project->id,
        //     'project_document' => $this->project_document->id,
        // ])
        // ->with('alert.success',$message)
        // ;







        // submission 

        $submission_type = "supplemental_submission";
        
        // update project document
        ProjectDocumentHelpers::updateDocumentAndAttachments(
            $project_document,
            "submitted", 
            false
        );

 
         
            // set the project document reviewers
            // ProjectReviewerHelpers::setProjectDocumentReviewers($project_document, $submission_type); 

            $project_reviewer = ProjectReviewer::find($this->project_reviewer->id);
            $project_reviewer->review_status = "pending";
            $project_reviewer->updated_at = now();
            $project_reviewer->updated_by = Auth::user()->id;
            $project_reviewer->save();


            
            try {
                event(new \App\Events\ProjectDocument\Submitted(
                    $project_document->id, 
                    Auth::user()->id,
                    true,
                    true));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch Submitted event: ' . $e->getMessage(), [
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

 
            // send notification to the current reviewer
            ProjectReviewerHelpers::sendReviewNotificationToReviewer($project_document,$submission_type);
 

        // update project submission and review timers 
        $project_timer = ProjectTimer::first();

        if(!empty($project_timer)){
            $reviewer_response_duration =  $project_timer->reviewer_response_duration ?? null;
            $reviewer_response_duration_type =  $project_timer->reviewer_response_duration_type ?? null;
            $submitter_response_duration =   $project_timer->submitter_response_duration ?? null;
            $submitter_response_duration_type = $project_timer->submitter_response_duration_type ?? null;


            $project_document->reviewer_response_duration = $reviewer_response_duration;
            $project_document->reviewer_response_duration_type = $reviewer_response_duration_type;
            // after updating the project, update the due date timers
            $project_document->reviewer_due_date = Project::calculateDueDate(now(),$reviewer_response_duration_type, $reviewer_response_duration );


            $project_document->submitter_response_duration = $submitter_response_duration;
            $project_document->submitter_response_duration_type = $submitter_response_duration_type;  
            // $project_document->submitter_due_date = Project::calculateDueDate(now(),$project_document->submitter_response_duration_type, $project_document->submitter_response_duration );
            $project_document->submitter_due_date = Project::calculateDueDate(now(),$submitter_response_duration_type, $submitter_response_duration );
    
            $project_document->save();


        }



         // logging and system notifications
            $authId = Auth::id() ?? null;
            $projectId = $project_document->project_id; 

            // logging for the project document 
                // Success message from the activity log project helper
                $message =  ProjectDocumentLogHelper::getActivityMessage('submitted',$project_document->id,$authId);
        
                // get the route 
                $route = ProjectDocumentLogHelper::getRoute('submitted', $project_document->id);
                

                // // log the event 
                event(new ProjectDocumentLogEvent(
                    $message ,
                    $authId, 
                    $projectId,
                    $project_document->id,

                ));

                // update the reviewer list  
                event(new ProjectReviewerLogEvent(
                    $message ,
                    $authId, 
                    $project->id,
                    $project_document->id,

                ));

            // ./ logging for the project document  
            

            /** send system notifications to users */
                /** send system notifications to users */
                    
                    ProjectDocumentNotificationHelper::sendSystemNotification(
                        message: $message,
                        route: $route 
                    );

                /** ./ send system notifications to users */
            /** ./ send system notifications to users */
        // ./ logging and system notifications













        // Alert::success('Success','Project review submitted successfully');
        return redirect()->route('project.document.reviewer.index',[
            // 'project' => $this->project->id,
            'project_document' => $this->project_document->id,
        
        ])
        ->with('alert.success',$message)
        ;



    }


    public function render()
    {
        return view('livewire.components.project-review.resubmit-component');
    }
}
