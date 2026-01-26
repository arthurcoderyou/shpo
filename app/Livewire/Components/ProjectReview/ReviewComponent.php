<?php

namespace App\Livewire\Components\ProjectReview;

use Carbon\Carbon;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use App\Models\ReviewAttachments;
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

class ReviewComponent extends Component
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
    public $submitter_response_duration;
    public $submitter_response_duration_type;
    public $submitter_due_date;


    public $reviewer_response_duration;
    public $reviewer_response_duration_type;  

    public $reviewer_due_date;
 
 

    // enables this sections: 
        public $requires_project_update = false;
        public $requires_document_update  = false;
        public $requires_attachment_update  = false;
 


 
        
    public function mount($project_reviewer_id){


        $this->project_reviewer = ProjectReviewer::find($project_reviewer_id);

        $this->project = Project::find( $this->project_reviewer->project_id);

        $this->project_document = ProjectDocument::find( $this->project_reviewer->project_document_id );



    }


    public function checkIfLastInReviewerList(){
        $isLastReviewer = ProjectReviewer::where('project_document_id',  $this->project_document->id)
            ->max('order') ===  $this->project_reviewer->order;

        return $isLastReviewer;
    }

      // saving and submission of review 
    public function save(){

        // dd($this->requires_document_update);
 

        $this->validate([
            'review_status' => [
                'required',
            ],

            // 'requires_project_update'   => ['boolean'],
            // 'requires_document_update'  => ['boolean'],
            // 'requires_attachment_update'=> ['boolean'],

            // Conditional: required_document_updates is required if requires_document_update is true
            // 'required_document_updates' => [
            //     'array',
            //     function ($attribute, $value, $fail) {
            //         if ($this->requires_document_update && count($value) < 1) {
            //             $fail('You must select at least one document as a requirements for the projects next update request for information.');
            //         }
            //     },
            // ],
            // 'required_document_updates.*' => ['integer', 'exists:document_types,id'],

             
            // // Conditional: required_attachment_updates is required if requires_attachment_update is true
            // 'required_attachment_updates' => [
            //     'array',
            //     function ($attribute, $value, $fail) {
            //         if ($this->requires_attachment_update && count($value) < 1) {
            //             $fail('You must select at least one document update.');
            //         }
            //     },
            // ],
            // 'required_attachment_updates.*' => ['integer'],
 


        ] 
        
        );



        // dd($this->review_status);


        if($this->review_status == "changes_requested"){
            $this->requires_project_update = true; 
            // $this->requires_document_update = $this->requires_document_update; 
            $this->requires_attachment_update = true;
        }

        // dd($this->all());
        
  
        // get the project document
        $project_document = ProjectDocument::find($this->project_document->id);

        // dd($project_document->hasUnapprovedReviewers());
 

        $project_reviewer = ProjectReviewer::find($this->project_reviewer->id);
        
        //  dd($this->all());

        // update current reviewer
            // dd($project_reviewer);

            // update review status 
            $project_reviewer->review_status = $this->review_status;

            // if the review is reviewed / set the reviewer to not be the current reviewer by setting status to false 
            if($this->review_status == "reviewed" || $this->review_status == "approved"){
                $project_reviewer->status = false; 
            }

            // if review status is changes_requested, require project and attachment update for the project document 
            if($this->review_status == "changes_requested"){
                $project_reviewer->requires_project_update = true; 
                // $project_reviewer->requires_document_update = $this->requires_document_update; 
                $project_reviewer->requires_attachment_update = true;
            }
             
            // add update date time and updater 
            $project_reviewer->updated_at = now();
            $project_reviewer->updated_by = Auth::user()->id;
            $project_reviewer->save();
        // ./ update project reviewer
 

 
        // Add review

            //add review
            $review = new Review(); 
            $review->project_review = $this->project_review;
            $review->project_id = $project_document->project_id;
            $review->project_document_id = $project_document->id;
            $review->project_document_status = $project_document->status; 
            $review->reviewer_id = Auth::user()->id; // this is considered the user 
 


            $review->project_reviewer_id = $project_reviewer->id;

            /** Update the review time */
                /**
                 * Review Time is now based on last_submitted_at of the project
                 * 
                 * last_reviewed_at
                 * 
                 */

                // Ensure updated_at is after created_at
                if ($project_document->updated_at && now()->greaterThan($project_document->updated_at)) {
                    // Calculate time difference in hours
                    // $review->review_time_hours = $project_document->updated_at->diffInHours(now()); 
                    $review->review_time_hours = $project_document->updated_at->diffInSeconds(now()) / 3600; // shows hours in decimal
                }
    
            /** ./ Update the review time */
    
            // update review status
            $review->review_status = $this->review_status;   

            // if changes are requested, add project and attachment update requirement to the review
            if($this->review_status == "changes_requested"){ 

                $review->requires_project_update = true; 
                // $review->requires_document_update = $this->requires_document_update; 
                $review->requires_attachment_update = true; 

            }
 
            // add create & update datetime and the current user  
            $review->created_by = Auth::user()->id;
            $review->updated_by = Auth::user()->id;
            $review->created_at = now();
            $review->updated_at = now();
            $review->save();

            // add review attachments 
            if (!empty($this->attachments)) {

                $now = Carbon::now(); 
                $disk = 'public';


                foreach ($this->attachments as $file) {
                        // Generate a unique file name
                    $fileName = Carbon::now()->timestamp
                        . '-' . $review->id
                        . '-' . uniqid()
                        . '.' . $file['extension'];

                    // Source and destination
                    $sourcePath = $file['path']; // full temp path
                    $destinationPath = "uploads/review_attachments/{$review->id}";

                    $dir = "uploads/review_attachments/{$review->id}";


                        // Case 1: Livewire/HTTP-uploaded file objects
                    if ($file instanceof TemporaryUploadedFile || $file instanceof \Illuminate\Http\UploadedFile) {
                        $originalName = $file->getClientOriginalName();
                        $ext   = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
                        $mime  = $file->getMimeType();
                        $size  = $file->getSize() ?? 0;
                        $tmpPath = $file->getRealPath();

                        $storedName = $now->format('Ymd_His').'-'.$originalName;
                        // $file->storeAs($dir, $storedName, $disk);


                        /*
                        |------------------------------------------------------------
                        | Move file into storage/app/public/uploads/review_attachments
                        |------------------------------------------------------------
                        */
                        Storage::disk('public')->put(
                            $destinationPath,
                            file_get_contents($sourcePath)
                        );



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




                    

                    // Make sure the source exists
                    if (!file_exists($sourcePath)) {
                        continue;
                    }

                    

                    // Optional: delete temp file after successful move
                    @unlink($sourcePath);

                    // Save to the database
                    ReviewAttachments::create([
                        'attachment'  => $originalName,
                        'review_id'   => $review->id,
                        'created_by'  => Auth::id(),
                        'updated_by'  => Auth::id(),

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

                        'size_bytes'           => $size ,  


                    ]);
                }
            }
            // ./ add review attachments 



        // ./ Add review


         

        // notifications
            // reset the project document reviewers 
            // sets all reviewers into status = false and makes the first reviewer as active
            ProjectDocument::resetCurrentProjectDocumentReviewersByDocument($project_document->id); 
            
            

            //update the next reviewer
            if($this->review_status == "reviewed" || $this->review_status == "approved"){
                
                // update project document  
                $project_document->allow_project_submission = false;
                $project_document->save();

                // the reviewers had been reset, we can just get the current reviewer now 
                // $next_project_reviewer = $project->getNextReviewer();
                $next_project_reviewer = $project_document->getCurrentReviewerByProjectDocument();

                if(!empty($next_project_reviewer)){ // check if there are next reviewers
                     

                    // // send notification to the next reviewer
                    ProjectReviewerHelpers::sendReviewNotificationToReviewer($project_document);
 

                }else{ // if there are no more reviewers, meaning the project is completed

                    // update project document  
                    $project_document->status = "approved";
                    $project_document->save();
 
                }

            


            }else{ // if the review is not approved and either the project is rejected or changes_requested 
                // update project document  
                $project_document->allow_project_submission = true;
                $project_document->save();

            }


            // notify submitter about the review update 
            // get the submitter user id 
            $submitter_id = $project_document->created_by;
            // submit
            ProjectDocumentHelpers::notifySubmitter(
                $review->id, 
                $submitter_id
            );

            // notify the reviewer about his submitted review 
             ProjectDocumentHelpers::notifyReviewSubmitter(
                $review->id, 
                $submitter_id
            ); 


            // send project approval updates for creators and project subscribers if the project is approved 
            if($project_document->status == "approved"){
                ProjectDocumentHelpers::sendCompleteProjectDocumentApprovalNotification($project_document);
            }



        // ./ notifications

 

        $authId = Auth::id() ?? null;
        $project = Project::find($this->project->id);

        // Success message from the activity log project helper 
        $message =  ProjectDocumentLogHelper::getActivityMessage('reviewed',$project_document->id,$authId);
 
        // get the route 
        $route = ProjectDocumentLogHelper::getRoute('reviewed', $project_document->id);
        

        // // log the event 
        event(new ProjectDocumentLogEvent(
            $message ,
            $authId, 
            $project->id,
            $project_document->id,

        ));


        // update the reviewer list  
        event(new ProjectReviewerLogEvent(
            $message ,
            $authId, 
            $project->id,
            $project_document->id,

        ));

           

        /** send system notifications to users */
                
            ProjectDocumentNotificationHelper::sendSystemNotification(
                message: $message,
                route: $route 
            );

        /** ./ send system notifications to users */

 

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
        return view('livewire.components.project-review.review-component');
    }
}
