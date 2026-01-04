<?php

namespace App\Livewire\Admin\Review;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;

use App\Models\DocumentType;
use App\Models\ProjectTimer;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use App\Models\ReReviewRequest;
use Illuminate\Validation\Rule;
use App\Models\ProjectReferences;
use App\Models\ReviewAttachments;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ProjectDocumentHelpers;
use App\Helpers\ProjectReviewerHelpers;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\ProjectDocumentReferences;
use App\Models\ReviewRequireDocumentUpdates;
use Illuminate\Support\Facades\Notification;
use App\Models\ReviewRequireAttachmentUpdates;
use App\Events\ProjectDocument\Review\Reviewed;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ReviewerReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ReviewerReviewNotificationDB;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Notifications\ProjectSubscribersNotification;
use App\Events\ProjectDocument\ProjectDocumentLogEvent;
use App\Helpers\ActivityLogHelpers\ProjectDocumentLogHelper;
use App\Helpers\SystemNotificationHelpers\ProjectDocumentNotificationHelper;

class ReviewCreate extends Component
{   

     protected $listeners = [
        'systemEvent' => '$refresh',  

        'projectEvent' => '$refresh',
        'projectDocumentEvent' => '$refresh',

        // 'projectReviewCreated' => '$refresh', 
        // 'reviewerUpdated' => '$refresh',
        // 'reviewerDeleted' => '$refresh',
        
    ];
    /** Actions with Password Confirmation panel */
        public $passwordConfirm = '';
        public $passwordError = null;
         
        public $review_status;

        /** Delete Confirmation  */
            public $confirmingReview = false; // closes the confirmation delete panel
            
            public function confirmReview($review_status = "approved")
            {
                $this->confirmingReview = true;
                $this->review_status = $review_status;
                $this->passwordConfirm = '';
                $this->passwordError = null;
            }

            public function saveReview()
            {
                if (!Hash::check($this->passwordConfirm, auth()->user()->password)) {
                    $this->passwordError = 'Incorrect password.';
                    return;
                }
 

                // save the review
                 

                $this->reset(['confirmingReview', 'passwordConfirm', 'recordId', 'passwordError','selected_record']); 
            }

        /** ./ Delete Confirmation */

    /** ./ Actions with Password Confirmation panel */



    use WithFileUploads;

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

    public $documentTypes = [];
    public $usedProjectDocuments = [];

    
    public $project_status;

    public $rfi  = false; // request for additional information

    // enables this sections: 
        public $requires_project_update = false;
        public $requires_document_update  = false;
        public $requires_attachment_update  = false;

    public $required_document_updates = [];
    public $required_attachment_updates = [];


    // For the Search Project Functionality
        public $query = ''; // Search input
        public $projects = []; // Search results
        public $project_documents = [];
        public $selectedProjectDocuments = []; // Selected project references




    // for the signature

        public $signableType;   // e.g., App\Models\ProjectDocument::class
        public $signableId;
        public $signer_name = '';
       
        public $signatureData = null; // data:image/png;base64,...

        
    
    public function mount($id,$project_document_id){

        // :signable-type="\App\Models\ProjectDocument::class" :signable-id="249"

        $this->signableType = ProjectDocument::class;
        $this->signableId   = $project_document_id;
        $this->signer_name  = $signerName ?? Auth::user()->name;



        $this->project = Project::findOrFail($id);
        $this->project_document = ProjectDocument::findOrFail($project_document_id);

        // // Generate the project number
        // if(empty($this->project->rc_number)){ 
        //     $this->rc_number = ProjectDocument::generateProjectNumber(rand(10, 99));
        // }


        // dd($this->project->project_documents);

        $this->rc_number = $this->project_document->rc_number;

 
        $this->submitter_due_date = $this->project_document->submitter_due_date;
        $this->submitter_response_duration_type = $this->project_document->submitter_response_duration_type;
        $this->submitter_response_duration = $this->project_document->submitter_response_duration;

        $this->reviewer_due_date = $this->project_document->reviewer_due_date;
        $this->reviewer_response_duration = $this->project_document->reviewer_response_duration;
        $this->reviewer_response_duration_type = $this->project_document->reviewer_response_duration_type; 
 

        // $this->allotted_review_time_hours = $this->project->allotted_review_time_hours;
        $value = (float) $this->project->allotted_review_time_hours;
        $this->allotted_review_time_hours = rtrim(rtrim(number_format($value, 4, '.', ''), '0'), '.');


        // Get used document_type_ids from the project's documents
        $usedDocumentTypeIds = $this->project->project_documents->pluck('document_type_id')->toArray();

        // Get only document types that are NOT used yet
        $this->documentTypes = DocumentType::whereNotIn('id', $usedDocumentTypeIds)->orderBy('order','ASC')->get();

        // Get only used project documents
        $this->usedProjectDocuments = $this->project->project_documents;

        // Get only document types that are used yet
        // $this->usedDocumentTypes = DocumentType::whereIn('id', $usedDocumentTypeIds)->orderBy('order','ASC')->get();



        if(!empty( $this->project_document->document_references) && count($this->project_document->document_references) > 0){
            // add to the project references
            foreach($this->project_document->document_references as $document_reference){
                $project_document = ProjectDocument::find($document_reference->referenced_project_document_id);

                $project = Project::find($project_document->project_id);

                $this->selectedProjectDocuments[] = [
                    'id' => $project_document->id,
                    'project_name' => $project->name,
                    'document_name' => $project_document->document_type->name,
                    'rc_number' => $project_document->rc_number,
                    'project_number' => $project_document->project_number,
                    'location' => $project->location,
                    'type' => $project->type,
                    'agency' => $project->agency,
                ];

            }


        }


        

        $this->loadProjectReferencesBasedOnLotNumber();

        // dd( $this->selectedProjectDocuments );


        // load last review
        $this->loadLastReview();


    }




    public function loadProjectReferencesBasedOnLotNumber(){
        // check for project documents that has connections to project based on lot number 

        $current_reviewer = $this->project_document->getCurrentReviewerByProjectDocument();
        $project = Project::find($this->project_document->project_id);
        $projects = Project::where('lot_number',$project->lot_number)->get();


        // check if it is the first in order 
        if(!empty($current_reviewer ) && $current_reviewer->order == 1 && !empty($project ) && !empty($projects )){

            foreach($projects as $proj){

                foreach($proj->project_documents as $project_docs){

                    if(!empty($project_docs->rc_number) && $project_docs->status !== "draft"){
                        $this->selectedProjectDocuments[] = [
                            'id' => $project_docs->id,
                            'project_name' => $proj->name,
                            'lot_number' => $proj->lot_number,
                            'document_name' => $project_docs->document_type->name,
                            'rc_number' => $project_docs->rc_number,
                            'project_number' => $project_docs->project_number,
                            'location' => $proj->location,
                            'type' => $proj->type,
                            'agency' => $proj->agency,
                        ];
                    }

                    

                }
            }

            




        }


    }














    public function updated(){
        $this->updateDueDate();

        // if(!empty($this->project_status)){

        //     // return to default
        //     $this->review_status = null;
        //     $this->rfi  = false; // request for additional information

        //     // enables this sections: 
        //         $this->requires_project_update = false;
        //         $this->requires_document_update  = false;
        //         $this->requires_attachment_update  = false;

        // }elseif(!empty($this->review_status)){
        //     $this->project_status = false;
        // }

    }

    public function updatedProjectStatus(){

        if(!empty($this->project_status)){

            // return to default
            $this->review_status = null;
            $this->rfi  = false; // request for additional information

            // enables this sections: 
                $this->requires_project_update = false;
                $this->requires_document_update  = false;
                $this->requires_attachment_update  = false;

        }
    }

    // public function updatedReviewStatus(){
    //     if(!empty($this->review_status)){
    //         $this->project_status = false;
    //     }
    // }

    

    public function updateDueDate(){
              
        $this->submitter_due_date = Project::calculateDueDate($this->project->updated_at,$this->submitter_response_duration_type, $this->submitter_response_duration );
        $this->reviewer_due_date = Project::calculateDueDate($this->project->updated_at,$this->reviewer_response_duration_type, $this->reviewer_response_duration );
    }

    

    public function open_review_project_document($project_document_id){
        ProjectDocumentHelpers::open_review_project_document($project_document_id);

    }

    

    public function update_project(){
        // dd("Here");
        $project = Project::find($this->project->id);

        $project_document = ProjectDocument::find($this->project_document->id);  // get the current project document 
        
        $project_reviewer = $project_document->getCurrentReviewerByProjectDocument(); // get the current reviewer


        // dd($project_document);
 

        $this->validate([
            'rc_number' => [
                'required',
                'string',
                Rule::unique('project_documents', 'rc_number'), // Ensure rc_number is unique
            ]
        ],[
            'The rc number has already been taken. Please enter other combinations of shpo number '
        ]);

        if(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id && $project_reviewer->order == 1){
            // the review is automatically approved if the first reviewer had save and confirmed that the project is approved in the initial review
            $this->review_status = "reviewed";

        }

        // dd(empty($project_reviewer) ||  (!empty($project_reviewer) && $project_reviewer->user_id !== Auth::user()->id) ); 
        // dd(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id && $project_reviewer->order == 1);
        // dd($this->all());
         

        $project_document->rc_number = $this->rc_number;
        // $project_document->allotted_review_time_hours = $this->allotted_review_time_hours;


        $project->rc_number = $this->rc_number;
        $project->save();


        $project_timer = ProjectTimer::first();

        if(!empty($project_timer)){
            $this->reviewer_response_duration = $this->reviewer_response_duration ?? $project_timer->reviewer_response_duration ?? null;
            $this->reviewer_response_duration_type = $this->reviewer_response_duration_type ?? $project_timer->reviewer_response_duration_type ?? null;
            $this->submitter_response_duration = $this->submitter_response_duration ?? $project_timer->submitter_response_duration ?? null;
            $this->submitter_response_duration_type = $this->submitter_response_duration_type ?? $project_timer->submitter_response_duration_type ?? null;


        }

        $project_document->reviewer_response_duration = $this->reviewer_response_duration;
        $project_document->reviewer_response_duration_type = $this->reviewer_response_duration_type;
        // after updating the project, update the due date timers
        $project_document->reviewer_due_date = Project::calculateDueDate(now(),$this->reviewer_response_duration_type, $this->reviewer_response_duration );


        $project_document->submitter_response_duration = $this->submitter_response_duration;
        $project_document->submitter_response_duration_type = $this->submitter_response_duration_type;  
        // $project_document->submitter_due_date = Project::calculateDueDate(now(),$project_document->submitter_response_duration_type, $project_document->submitter_response_duration );
        $project_document->submitter_due_date = Project::calculateDueDate(now(),$this->submitter_response_duration_type, $this->submitter_response_duration );
 
        $project_document->save();



        // delete existing project document references 
        if(!empty($project_document->document_references)){
            // delete document_references
            if(!empty($project_document->document_references)){
                foreach($project_document->document_references as $document_reference){
                    $document_reference->delete();
                } 
            }
        }



        // Save Project References (if any)
        if (!empty($this->selectedProjectDocuments)) {
            foreach ($this->selectedProjectDocuments as $selectedProjectDocument) {
                ProjectDocumentReferences::create([
                    'project_document_id' => $project_document->id,
                    'referenced_project_document_id' => $selectedProjectDocument['id'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }


        $message = "";
 
        
        // if project reviewer is not empty and the user is not the current reviewer, just save it 
        if( empty($project_reviewer) || 
            (!empty($project_reviewer) && $project_reviewer->user_id !== Auth::user()->id )  
            ){ 

            $message = 'Project Document Information Saved Successfully';

            Alert::success('Success',$message);
            return redirect()->route('project-document.review',[
                'project' => $this->project->id,
                'project_document' => $this->project_document->id,
            
            ]);


        // if project reviewer is not empty and the user is current reviewer and he is the first reviewer, then add a review as well  
        }elseif(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id && $project_reviewer->order == 1){

 
            $message = "Project Document \"".$project_document->document_type->name."\" on \"".$project_document->project->name."\" reviewed successfully ";

            // update project reviewer
                // dd($project_reviewer);

                $project_reviewer->review_status = $this->review_status;

                if($this->review_status == "reviewed"){
                    $project_reviewer->status = false; 
                }

                if($this->review_status == "changes_requested"){
                    $project_reviewer->requires_project_update = true; 
                    // $project_reviewer->requires_document_update = $this->requires_document_update; 
                    $project_reviewer->requires_attachment_update = true;
                }
                

                $project_reviewer->updated_at = now();
                $project_reviewer->updated_by = Auth::user()->id;
                $project_reviewer->save();
            // ./ update project reviewer





            // Add review

                //add review
                $review = new Review();
                // $review->project_review = $this->project_review;
                $review->project_review = $message;
                $review->project_id = $project_document->project_id;
                $review->project_document_id = $project_document->id;
                $review->project_document_status = $project_document->status;
                $review->project_reviewer_id = $project_reviewer->id ;
                $review->reviewer_id = Auth::user()->id;


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
        
                

                if($this->review_status == "changes_requested"){ 

                    $review->requires_project_update = true; 
                    // $review->requires_document_update = $this->requires_document_update; 
                    $review->requires_attachment_update = true; 

                }else{ // if it is not changes request, then it must be approved 

                    $this->review_status == "reviewed";
                }

                $review->review_status = $this->review_status; 


                $review->created_by = Auth::user()->id;
                $review->updated_by = Auth::user()->id;
                $review->created_at = now();
                $review->updated_at = now();
                $review->save();

                // add review attachments
                if (!empty($this->attachments)) {
                    foreach ($this->attachments as $file) {
                
                        // Generate a unique file name
                        $fileName = Carbon::now()->timestamp . '-' . $review->id . '-' . uniqid() . '.' . $file['extension'];
                
                        // Move the file manually from temporary storage
                        $sourcePath = $file['path'];
                        $destinationPath = storage_path("app/public/uploads/review_attachments/{$fileName}");
                
                        // Ensure the directory exists
                        if (!file_exists(dirname($destinationPath))) {
                            mkdir(dirname($destinationPath), 0777, true);
                        }
                
                        // Move the file to the destination
                        if (file_exists($sourcePath)) {
                            rename($sourcePath, $destinationPath);
                        } else {
                            // Log or handle the error (file might not exist at the temporary path)
                            continue;
                        }
                
                        // Save to the database
                        ReviewAttachments::create([
                            'attachment' => $fileName,
                            'review_id' => $review->id,
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
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
                if($this->review_status == "reviewed"){
                    
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



                // send project approval updates for creators and project subscribers if the project is approved 
                if($project_document->status == "approved"){
                    ProjectDocumentHelpers::sendCompleteProjectDocumentApprovalNotification($project_document);
                }




            // ./ notifications


            
 



            ActivityLog::create([
                'log_action' => $message,
                'log_username' => Auth::user()->name,
                'created_by' => Auth::user()->id,
                'project_id' => $project_document->project->id,
                'project_document_id' => $project_document->id,
            ]);

            Alert::success('Success', $message);
            return redirect()->route('project-document.review',[
                'project' => $this->project->id,
                'project_document' => $this->project_document->id,
            
            ]);

 
        }

         
 
        Alert::success('Success', $message);
        return redirect()->route('project-document.review',[
            'project' => $this->project->id,
            'project_document' => $this->project_document->id,
        
        ]);

    }


    // For the Search Project Functionality
        

        public function updatedQuery()
        {
            if (!empty($this->query)) {
                $user = Auth::user();   

                // Extract selected project IDs to exclude from results
                $excludedIds = array_column($this->selectedProjectDocuments, 'id');


                $this->project_documents = ProjectDocument::query()
                    ->with('document_type','project') // optional: for display without extra queries
                    ->whereNotNull('rc_number')
                    // ->whereNotNull('project_number')
                    ->when(!empty($excludedIds), fn($q) => $q->whereNotIn('id', $excludedIds)) // safe guard
                    ->where(function ($mainQuery) use ($user) {
                        $search = trim((string) $this->query);

                        // group all OR conditions
                        $mainQuery->where(function ($q) use ($search) {
                            $q
                            // ->where('name', 'like', "%{$search}%")
                                ->orWhere('rc_number', 'like', "%{$search}%")
                                ->orWhere('applicant', 'like', "%{$search}%")
                                ->orWhere('document_from', 'like', "%{$search}%")
                                ->orWhere('company', 'like', "%{$search}%")
                                ->orWhere('findings', 'like', "%{$search}%")
                                ->orWhere('comments', 'like', "%{$search}%") 
                                ->orWhereHas('document_type', function ($dq) use ($search) {
                                    $dq->where('name', 'like', "%{$search}%");
                                })
                                ->orWhereHas('project', function ($dq) use ($search) {
                                    $dq->where('name', 'like', "%{$search}%")
                                        ->orWhere('agency', 'like', "%{$search}%")
                                        ->orWhere('lot_number', 'like', "%{$search}%")
                                        ->orWhere('location', 'like', "%{$search}%")
                                        ->orWhere('street', 'like', "%{$search}%")
                                        ->orWhere('description', 'like', "%{$search}%");
                                }) ;
                        });

                        // Optional: access control
                        if (!$user->can('system access global admin') && !$user->can('system access admin')) {
                            $mainQuery->where(function ($q) use ($user) {
                                $q->where('created_by', $user->id);
                                // add more rules if needed
                            });
                        }
                    })
                    ->limit(10)
                    ->get();

            } else {
                $this->project_documents = [];
            }


            // dd($this->project_documents);
        }

        public function addProjectDocumentReference($projectDocumentId)
        {
            if (!in_array($projectDocumentId, array_column($this->selectedProjectDocuments, 'id'))) {
                $project_document = ProjectDocument::find($projectDocumentId);
                $project = Project::find($project_document->project_id);
                $this->selectedProjectDocuments[] = [


                    'id' => $project_document->id,
                    'project_name' => $project->name,
                    'document_name' => $project_document->document_type->name,
                    'rc_number' => $project_document->rc_number,
                    'project_number' => $project_document->project_number,
                    'location' => $project->location,
                    'type' => $project->type,
                    'agency' => $project->agency,



                    // 'id' => $project->id,
                    // 'name' => $project->name,
                    // 'rc_number' => $project->rc_number,
                    // 'project_number' => $project->project_number,
                    // 'location' => $project->location,
                    // 'type' => $project->type,
                    // 'agency' => $project->agency,
                ];
            }

            $this->query = '';
            $this->project_documents = [];
        }

        public function removeProjectReference($index)
        {
            unset($this->selectedProjectDocuments[$index]);
            $this->selectedProjectDocuments = array_values($this->selectedProjectDocuments); // Re-index array
        }

    // ./// For the Search Project Functionality

        

    public function saveImage() {
        $this->validate([
            'signatureData' => 'required|string',
            'signer_name'   => 'required|string|max:255',
        ],[
            'signatureData.required' => 'Please enter your signature'
        ]);

        // Strip prefix and decode
        $raw = preg_replace('~^data:image/\w+;base64,~', '', $this->signatureData);
        $bytes = base64_decode($raw, true);
        if ($bytes === false || strlen($bytes) < 200) {
            $this->addError('signatureData', 'Invalid signature data.');
            return;
        }

        $uuid = (string) Str::uuid();
        $path = "signatures/{$uuid}.png";
        Storage::disk('public')->put($path, $bytes);

        // Build tamper-evident HMAC over critical fields
        $payload = implode('|', [
            $this->signableType,
            $this->signableId,
            Auth::id(),
            $this->signer_name,
            $path,
            now()->toIso8601String(),
            request()->ip(),
            request()->userAgent()
        ]);

        $appKey = config('app.key');
        $key = str_starts_with($appKey, 'base64:') ? base64_decode(substr($appKey, 7)) : $appKey;
        $hash = hash_hmac('sha256', $payload, $key);

        Signature::create([
            'signable_type' => $this->signableType,
            'signable_id'   => $this->signableId,
            'user_id'       => Auth::id(),
            'signer_name'   => $this->signer_name,
            'signature_path'=> $path,
            'signed_at'     => now(),
            'ua'            => request()->userAgent(),
            'ip'            => request()->ip(),
            'hash'          => $hash,
            'meta'          => ['reason' => $this->reason],
        ]);

        $this->dispatch('signature:saved');
        $this->reset('signatureData');
    }


    // saving and submission of review 
    public function save(){

        // dd($this->requires_document_update);
        
       

        $this->validate([
            'review_status' => [
                'required',
            ],

            'requires_project_update'   => ['boolean'],
            'requires_document_update'  => ['boolean'],
            'requires_attachment_update'=> ['boolean'],

            // Conditional: required_document_updates is required if requires_document_update is true
            'required_document_updates' => [
                'array',
                function ($attribute, $value, $fail) {
                    if ($this->requires_document_update && count($value) < 1) {
                        $fail('You must select at least one document as a requirements for the projects next update request for information.');
                    }
                },
            ],
            'required_document_updates.*' => ['integer', 'exists:document_types,id'],

             
            // Conditional: required_attachment_updates is required if requires_attachment_update is true
            'required_attachment_updates' => [
                'array',
                function ($attribute, $value, $fail) {
                    if ($this->requires_attachment_update && count($value) < 1) {
                        $fail('You must select at least one document update.');
                    }
                },
            ],
            'required_attachment_updates.*' => ['integer'],

            // 'signatureData' => 'required',
            // 'signer_name'   => 'required',


        ]
            // ,[
            //     'signatureData.required' => 'Please enter your signature'
            // ]
        
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
 

        // dd($project_document);

        //get the current reviewer
        $current_reviewer = $project_document->getCurrentReviewerByProjectDocument();
         
        // check if the current user is the current reviewer
        if(empty($current_reviewer) && $current_reviewer->user_id == Auth::user()->id){
            $message = 'You are not the current reviewer for the project';
            // Alert::error('Error','You are not the current reviewer for the project');
            return redirect()->route('project-document.review',[
                'project' => $this->project->id,
                'project_document' => $this->project_document->id,
            
            ])
            ->with('alert.error' , $message );
            ;

        }

        
        //  dd($this->all());

        // update current reviewer
            // dd($project_reviewer);

            // update review status 
            $current_reviewer->review_status = $this->review_status;

            // if the review is reviewed / set the reviewer to not be the current reviewer by setting status to false 
            if($this->review_status == "reviewed" || $this->review_status == "approved"){
                $current_reviewer->status = false; 
            }

            // if review status is changes_requested, require project and attachment update for the project document 
            if($this->review_status == "changes_requested"){
                $current_reviewer->requires_project_update = true; 
                // $project_reviewer->requires_document_update = $this->requires_document_update; 
                $current_reviewer->requires_attachment_update = true;
            }
             
            // add update date time and updater 
            $current_reviewer->updated_at = now();
            $current_reviewer->updated_by = Auth::user()->id;
            $current_reviewer->save();
        // ./ update project reviewer
 

 
        // Add review

            //add review
            $review = new Review(); 
            $review->project_review = $this->project_review;
            $review->project_id = $project_document->project_id;
            $review->project_document_id = $project_document->id;
            $review->project_document_status = $project_document->status; 
            $review->reviewer_id = Auth::user()->id; // this is considered the user 
 


            $review->project_reviewer_id = $current_reviewer->id;

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

            // add review attachments (optional)
            if (!empty($this->attachments)) {
                foreach ($this->attachments as $file) {
            
                    // Generate a unique file name
                    $fileName = Carbon::now()->timestamp . '-' . $review->id . '-' . uniqid() . '.' . $file['extension'];
            
                    // Move the file manually from temporary storage
                    $sourcePath = $file['path'];
                    $destinationPath = storage_path("app/public/uploads/review_attachments/{$fileName}");
            
                    // Ensure the directory exists
                    if (!file_exists(dirname($destinationPath))) {
                        mkdir(dirname($destinationPath), 0777, true);
                    }
            
                    // Move the file to the destination
                    if (file_exists($sourcePath)) {
                        rename($sourcePath, $destinationPath);
                    } else {
                        // Log or handle the error (file might not exist at the temporary path)
                        continue;
                    }
            
                    // Save to the database
                    ReviewAttachments::create([
                        'attachment' => $fileName,
                        'review_id' => $review->id,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
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
        $project = Project::find($project_document->id);

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

           

        /** send system notifications to users */
                
            ProjectDocumentNotificationHelper::sendSystemNotification(
                message: $message,
                route: $route 
            );

        /** ./ send system notifications to users */



 

        // Alert::success('Success','Project review submitted successfully');
        return redirect()->route('project-document.review',[
            'project' => $this->project->id,
            'project_document' => $this->project_document->id,
        
        ])
        ->with('alert.success',$message)
        ;



    }


    // for the signature pad 

    public function clearPad() {
        $this->dispatch('signature:clear'); // front-end will clear canvas
    }

    public function with() {
        return [];
    }


    public function getSignaturesProperty(){

        $query = Signature::query();


        return $query->where('user_id',Auth::user()->id)
            ->orderBy('created_at','DESC')
            ->paginate(10);
    }

     // used for the table formatting tools on datetime
    public static function returnFormattedDatetime($datetime){
        $formatted = $datetime
            ? ( $datetime instanceof Carbon
                ? $datetime
                : Carbon::parse($datetime)
              )->format('M d, Y • H:i')
            : null;

        return $formatted;
    }










    // Re- Review Request
    public bool $showRereviewModal = false; // can be kept if you want future server toggle
     public $reason = '';


    public array $previous_reviewers = [
        ['id' => 1, 'name' => 'Jane Reviewer', 'iteration' => 1],
        ['id' => 2, 'name' => 'Mark Analyst', 'iteration' => 2],
    ];

    public array $rereview = [
        'to_reviewer_id' => null,
        'reason' => '',
        'issues' => [],
    ];

    public array $common_issues = ['Incomplete data', 'Wrong format', 'Missing signatures', 'Outdated info'];

    public $last_review = null;

    public function loadLastReview( )
    {
        // if (!$id) {
        //     $this->last_review = null;
        //     return;
        // }

        $this->last_review = $this->project_document->getLastReview();
        // dd($last_review );
        // $reviewer = $this->project_document->getCurrentReviewerUser();

        // $this->last_review = $last_review ? [
        //     'review_status'        => $last_review->review_status,
        //     'reviewer_name' => $reviewer?->name,
        //     'reviewed_at'   => optional($r->created_at ?? $last_review->created_at)->format('Y-m-d H:i'),
        //     'iteration'     => $last_review->iteration,
        //     // 'role'          => $last_review->role,
        //     'review'          => $last_review->project_review,
        //     // 'issues'        => $last_review->issues ?? [],
        // ] : null;

 
    }

    public function toggleRereviewIssue($tag)
    {
        $i = array_search($tag, $this->rereview['issues'], true);
        if ($i === false) {
            $this->rereview['issues'][] = $tag;
        } else {
            unset($this->rereview['issues'][$i]);
            $this->rereview['issues'] = array_values($this->rereview['issues']);
        }
    }

    public function submitRereview()
    {   
        // check if there is a last review instance
        if(empty($this->last_review)){
            Alert::error('Error','Re-review request failed. There are no previuos reviewers');
            return redirect()->route('project-document.review',[
                'project' => $this->project->id,
                'project_document' => $this->project_document->id,
            
            ]); 
        }   






        // dd($this->all());
        $this->validate([
            'reason' => 'required', 
        ]);

        // dd("All Goods");

        // Save logic here
        // ProjectReReviewRequest::create([...])

        // $this->dispatch('toast', type:'success', message:'Re-review request sent successfully!');
        // $this->rereview = ['to_reviewer_id' => null, 'reason' => '', 'issues' => []];

        // get the requested project reviewer by the last review 
        $reviewer_requested_to_id = $this->last_review->project_reviewer_id;

        // get the current reviewer that initiated the previous reviewer re-review request 
        $current_reviewer =     $this->project_document->getCurrentReviewerByProjectDocument();
        $reviewer_requested_by_id = $current_reviewer->id;

        $re_review_requests = ReReviewRequest::create([   
            'reason' => $this->reason,   
            'status' => 'submitted', // true or false
            'response_notes' => null,
            'requested_to' => $reviewer_requested_to_id,
            'requested_by' => $reviewer_requested_by_id,
            'project_id' => $this->project->id,
            'project_document_id' => $this->project_document->id,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'created_at' => now(),
            'updated_at' => now(), 
        ]);
        

        // create the review 
        $review = Review::create([
            'iteration'  => $current_reviewer->iteration++, // iteration 
            'project_id' => $current_reviewer->project_id,
            'project_document_id' => $current_reviewer->project_document_id,
            'reviewer_id' => $current_reviewer->user_id, // user id of hte user that made the review
            'project_reviewer_id' => $current_reviewer->id, // project reviewer 
            'review_status' => 're_review_requested',
            
            # ['pending','approved','rejected','submitted','re_submitted','changes_requested','reviewed','re_review_requested']
            # 'submitted' is the special review status for users 
            # re_submitted for resubmission

            'project_document_status' => 're_review_requested', 
            # 'draft','submitted','in_review','approved','rejected','completed','cancelled',','changes_requested','reviewed', 're_review_requested' 
 
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
            'created_at' => now(),
            'updated_at' => now(),
     

            're_review_requests_id' => $re_review_requests->id
        ]);  

        

        ProjectReviewerHelpers::sendReReviewRequest($re_review_requests);


        Alert::success('Success','Re-review request submitted successfully');
        return redirect()->route('project-document.review',[
            'project' => $this->project->id,
            'project_document' => $this->project_document->id,
        
        ]);


    }



    public array $eligible_reviewers = [
        ['id' => 1, 'name' => 'Alex Cruz', 'dept' => 'Technical'],
        ['id' => 2, 'name' => 'Maria Lopez', 'dept' => 'Legal'],
        ['id' => 3, 'name' => 'John Tan', 'dept' => 'Environmental'],
    ];

    public array $pre = [
        'reviewers' => [],
        'scope' => '',
        'due_on' => '',
        'instructions' => '',
        'block_my_review' => true,
        'notify_all' => true,
    ];

    public function submitPreReview()
    {
        $this->validate([
            'pre.reviewers' => 'required|array|min:1',
            'pre.scope' => 'required|string',
            'pre.due_on' => 'nullable|date',
            'pre.instructions' => 'required|string|min:5',
        ]);

        // Example save logic
        // ProjectPreReviewRequest::create([
        //     'project_document_id' => $this->docId,
        //     'requested_by' => auth()->id(),
        //     'reviewers' => $this->pre['reviewers'],
        //     'scope' => $this->pre['scope'],
        //     'instructions' => $this->pre['instructions'],
        //     'due_on' => $this->pre['due_on'],
        //     'block_my_review' => $this->pre['block_my_review'],
        //     'notify_all' => $this->pre['notify_all'],
        // ]);

        // Reset
        $this->pre = [
            'reviewers' => [],
            'scope' => '',
            'due_on' => '',
            'instructions' => '',
            'block_my_review' => true,
            'notify_all' => true,
        ];

        $this->dispatch('toast', type:'success', message:'Review request sent to additional reviewers.');
    }






    public function checkIfProjectReviewerHasSubmittedReReview($pending_check = "re_review"){
        $project_document = ProjectDocument::find($this->project_document->id);  // get the current project document 
        $project_reviewer = $project_document->getCurrentReviewerByProjectDocument(); // get the current reviewer
        $user = Auth::user();

        // check if the auth user is the current project reviewer
        if(empty($user) || empty( $project_reviewer)  ||  ( $user->id !==  $project_reviewer->user_id) ){

            return false;
        }

        // check if there are existing pending re_review request by the user 
        if($pending_check == "re_review"){
            $has_pending_request = ReReviewRequest::checkIfProjectReviewerHasSubmittedReReview(
                $project_reviewer->id,
                $project_document->project_id,
                $project_document->id,
            );


        }

        return $has_pending_request;


    }





    public function render()
    {   





        // dd($this->last_review);
        // dd($this->project_document->getCurrentReviewerByProjectDocument());
        
        return view('livewire.admin.review.review-create',[
            'has_pending_request' => $this->checkIfProjectReviewerHasSubmittedReReview(),
            'project_reviewer' => $this->project_document->getCurrentReviewerByProjectDocument(),
            'last_review' => $this->last_review,
                               

        ]);
    }
}
