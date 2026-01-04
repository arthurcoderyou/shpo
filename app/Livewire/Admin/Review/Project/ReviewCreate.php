<?php

namespace App\Livewire\Admin\Review\Project;

use Carbon\Carbon;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use App\Models\ProjectTimer;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use Illuminate\Validation\Rule;
use App\Models\ProjectReferences;
use App\Models\ReviewAttachments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Events\Project\ProjectLogEvent;
use App\Helpers\ProjectDocumentHelpers;
use App\Helpers\ProjectReviewerHelpers;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\ProjectDocumentReferences;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\ActivityLogHelpers\ProjectReferenceLogHelper;
use App\Helpers\SystemNotificationHelpers\ProjectNotificationHelper;

class ReviewCreate extends Component
{


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

    public $project_reviewer;

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


        public $selectedProjects = []; // Selected project references

    // for the signature    

        public $signableType;   // e.g., App\Models\ProjectDocument::class
        public $signableId;
        public $signer_name = '';
       
        public $signatureData = null; // data:image/png;base64,...
 
    
    public function mount($id){

     

        $this->project = Project::findOrFail($id); 

        // // Generate the project number
        // if(empty($this->project->rc_number)){ 
        //     $this->rc_number = ProjectDocument::generateProjectNumber(rand(10, 99));
        // }

        // dd($this->project);

        $this->rc_number = $this->project->rc_number;

 
        $this->submitter_due_date = $this->project->submitter_due_date;
        $this->submitter_response_duration_type = $this->project->submitter_response_duration_type;
        $this->submitter_response_duration = $this->project->submitter_response_duration;

        $this->reviewer_due_date = $this->project->reviewer_due_date;
        $this->reviewer_response_duration = $this->project->reviewer_response_duration;
        $this->reviewer_response_duration_type = $this->project->reviewer_response_duration_type; 
 

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



        // if(!empty( $this->project->project_references) && count($this->project->project_references) > 0){
        //     // add to the project references
        //     foreach($this->project->project_references as $project_reference){
        //         $project_document = ProjectDocument::find($document_reference->referenced_project_document_id);

        //         $project = Project::find($project_document->project_id);

        //         $this->selectedProjectDocuments[] = [
        //             'id' => $project_document->id,
        //             'project_name' => $project->name,
        //             'document_name' => $project_document->document_type->name,
        //             'rc_number' => $project_document->rc_number,
        //             'project_number' => $project_document->project_number,
        //             'location' => $project->location,
        //             'type' => $project->type,
        //             'agency' => $project->agency,
        //         ];

        //     }


        // }


        

        $this->loadProjectReferencesBasedOnLotNumber();

        // dd( $this->selectedProjectDocuments );

        $this->project_reviewer = $this->project->getCurrentReviewer() ?? null;
 


    }


    public $options;

    public function updatedRcNumber($value)
    {

        // $this->validate([
        //     'rc_number' => [
        //         'required',
        //         'string',
        //         // Rule::unique('project_documents', 'rc_number'), // Ensure rc_number is unique
        //          Rule::unique('projects')
        //             ->where(fn ($query) => $query
        //                 ->where('name', $this->project->name)
        //                 ->where('lot_number', $this->project->lot_number)
        //                 ->where('rc_number', $this->rc_number)
        //             ), 

        //     ]
        // ],[
        //     'The rc number has already been taken. Please enter other combinations of rc number '
        // ]);

        $search = trim($value);
        $limit  = 20;

        $query = Project::query()
            ->whereNotNull('rc_number')
            ->select('id', 'name', 'location', 'lot_number', 'rc_number');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
                ->orWhere('lot_number', 'like', "%{$search}%")
                ->orWhere('rc_number', 'like', "%{$search}%");
            });
        }

        $projects = $query
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();

        // Store full Eloquent collection (if you still need it elsewhere)
        $this->projects = $projects;

        // Map to options for the dropdown (id, name, rc, lot, location)
        $this->options = $projects->map(function ($project) {
            return [
                'id'         => $project->id,
                'name'       => $project->name,
                'location'   => $project->location,
                'lot_number' => $project->lot_number,
                'rc_number'  => $project->rc_number,
            ];
        })->toArray();


        

    }





    public function loadProjectReferencesBasedOnLotNumber(){
        // check for project documents that has connections to project based on lot number 

        $current_reviewer = $this->project->getCurrentReviewer();
        $project = Project::find($this->project->id); 

        $project = $this->project;

        // Grab existing values safely
        $lotNumber = $project?->lot_number;
        $projectName = $project?->name;
        $rcNumber = $this->rc_number;

        // If nothing to search with, return empty or skip
        if (empty($lotNumber) && empty($projectName) && empty($rcNumber)) {
            $this->projects = [];
            return;
        }

        $projects = Project::query()
            // Optional: exclude the current project itself
            ->when($project?->id, fn ($q) => $q->where('id', '!=', $project->id))

            ->where(function ($q) use ($lotNumber, $projectName, $rcNumber) {

                // LOT NUMBER (exact OR contains)
                if (!empty($lotNumber)) {
                    $q->orWhere(function ($sub) use ($lotNumber) {
                        $sub->where('lot_number', $lotNumber)
                            ->orWhere('lot_number', 'LIKE', "%{$lotNumber}%");
                    });
                }

                // RC NUMBER (exact OR contains)
                if (!empty($rcNumber)) {
                    $q->orWhere(function ($sub) use ($rcNumber) {
                        $sub->where('rc_number', $rcNumber)
                            ->orWhere('rc_number', 'LIKE', "%{$rcNumber}%");
                    });
                }

                // PROJECT NAME (exact OR contains)
                if (!empty($projectName)) {
                    $q->orWhere(function ($sub) use ($projectName) {
                        $sub->where('name', $projectName)
                            ->orWhere('name', 'LIKE', "%{$projectName}%");
                    });
                }
            })
            ->limit(20)
            ->get()
            ->toArray();

        $this->projects = $projects;

        // dd($current_reviewer);


        // check if it is the first in order 
        // if(!empty($current_reviewer ) && $current_reviewer->order == 1 && !empty($project ) && !empty($projects )){

           


            //  dd($this->selectedProjects);
            foreach($projects as $proj){ 
                if(!empty($proj['rc_number']) && $proj['status'] !== "draft"){

                    // dd($proj['name']);

                    $this->selectedProjects[] = [
                        'id' => $proj['id'],
                        'name' => $proj['name'],
                        'lot_number' => $proj['lot_number'], 
                        'rc_number' => $proj['rc_number'], 
                        'location' => $proj['location'],
                        'type' => $proj['type'],
                        'agency' => $proj['agency'],
                    ];
                } 
            }

            




        // }



       

    }





    public function open_review_project($project_id){

        ProjectHelper::open_review_project($project_id);

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

        $project = Project::find($this->project->id);  // get the current project document 
        $project_reviewer = $project->getCurrentReviewer(); // get the current reviewer


        // dd($project_reviewer);
 

        $this->validate([
            'rc_number' => [
                'required',
                'string',
                // Rule::unique('project_documents', 'rc_number'), // Ensure rc_number is unique
                //  Rule::unique('projects')
                //     ->where(fn ($query) => $query
                //         ->where('name', $this->project->name)
                //         ->where('lot_number', $this->project->lot_number)
                //         ->where('rc_number', $this->rc_number)
                //     ), 

            ]
        ],[
            'The rc number has already been taken. Please enter other combinations of rc number '
        ]);


        // dd("All goods");
        if(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id && $project_reviewer->order == 1){
            // the review is automatically approved if the first reviewer had save and confirmed that the project is approved in the initial review
            $this->review_status = "reviewed";

        }

        // dd(empty($project_reviewer) ||  (!empty($project_reviewer) && $project_reviewer->user_id !== Auth::user()->id) ); 
        // dd(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id && $project_reviewer->order == 1);
        // dd($this->all());
         

        $project->rc_number = $this->rc_number;
        $project->updated_at = now();
        $project->updated_by = Auth::user()->id; 
        $project->status = "reviewed";
        // $project->allotted_review_time_hours = $this->allotted_review_time_hours;
        


        $project_timer = ProjectTimer::first();

        if(!empty($project_timer)){
            $this->reviewer_response_duration = $this->reviewer_response_duration ?? $project_timer->reviewer_response_duration ?? null;
            $this->reviewer_response_duration_type = $this->reviewer_response_duration_type ?? $project_timer->reviewer_response_duration_type ?? null;
            $this->submitter_response_duration = $this->submitter_response_duration ?? $project_timer->submitter_response_duration ?? null;
            $this->submitter_response_duration_type = $this->submitter_response_duration_type ?? $project_timer->submitter_response_duration_type ?? null;


        }

        $project->reviewer_response_duration = $this->reviewer_response_duration;
        $project->reviewer_response_duration_type = $this->reviewer_response_duration_type;
        // after updating the project, update the due date timers
        $project->reviewer_due_date = Project::calculateDueDate(now(),$this->reviewer_response_duration_type, $this->reviewer_response_duration );


        $project->submitter_response_duration = $this->submitter_response_duration;
        $project->submitter_response_duration_type = $this->submitter_response_duration_type;  
        // $project->submitter_due_date = Project::calculateDueDate(now(),$project->submitter_response_duration_type, $project->submitter_response_duration );
        $project->submitter_due_date = Project::calculateDueDate(now(),$this->submitter_response_duration_type, $this->submitter_response_duration );
 
        $project->save();



        // delete existing project document references 
        if(!empty($project->project_references)){
            // delete project_references
            if(!empty($project->project_references)){
                foreach($project->project_references as $reference){
                    $reference->delete();
                } 
            }
        }


        // get the user
        $authId = Auth::check() ? Auth::id() : null;


        // Save Project References (if any)
        if (!empty($this->selectedProjects)) {
            foreach ($this->selectedProjects as $selectedProject) {
                $project_reference = ProjectReferences::create([
                    'project_id' => $project->id,
                    'referenced_project_id' => $selectedProject['id'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                // log the instance to the project logs
                    // we need to log it onto hte project because this is generally the parent model
                    // // get the message from the helper 
                    $message = ProjectReferenceLogHelper::getActivityMessage('referenced', $project_reference->id, $authId);

                    // // get the route
                    $route = ProjectReferenceLogHelper::getRoute('referenced', $project_reference->id);

                    // log the event 
                    event(new ProjectLogEvent(
                        $message ,
                        $authId, 
                        $project->id,

                    ));
                    
                // ./ log the instance


            }
        }


        $message = "";
 
        
        // if project reviewer is not empty and the user is not the current reviewer, just save it 
        if( empty($project_reviewer) || 
            (!empty($project_reviewer) && $project_reviewer->user_id !== Auth::user()->id )  
            ){ 

            $message = 'Project Information Saved Successfully';

            // Alert::success('Success',$message);
            return redirect()->route('project.review',[
                'project' => $this->project->id, 
            
            ])
            ->with('alert.success',$message)
            ;


        // if project reviewer is not empty and the user is current reviewer and he is the first reviewer, then add a review as well  
        }elseif(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id && $project_reviewer->order == 1){

 
            

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
                $review->project_id = $project->id; 
                $review->review_status = "reviewed";
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
                    if ($project->updated_at && now()->greaterThan($project->updated_at)) {
                        // Calculate time difference in hours
                        // $review->review_time_hours = $project->updated_at->diffInHours(now()); 
                        $review->review_time_hours = $project->updated_at->diffInSeconds(now()) / 3600; // shows hours in decimal
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

 
            
 

            // logging and system notifications
                $authId = Auth::check() ? Auth::id() : null;

                event(new \App\Events\Project\Reviewed($project->id,$authId , true, true));

                // get the message from the helper 
                $message = ProjectLogHelper::getActivityMessage('rc-reviewed', $project->id, $authId);

                // get the route
                $route = ProjectLogHelper::getRoute('rc-reviewed', $project->id);

                // log the event  
                // no need to log hte event because it is already logged in the Reviewed event 
                // event(new ProjectLogEvent(
                //     $message ,
                //     $authId, 
                //     $project->id,

                // ));
        
                /** send system notifications to users */
                    
                    ProjectNotificationHelper::sendSystemNotification(
                        message: $message,
                        route: $route 
                    );

                    // send system notification to the project creator 

                        $customIds = [];
                        $customIds[] = $project->created_by;

                        ProjectNotificationHelper::sendSystemNotificationCustomIds(
                            message: $message,
                            route: $route ,
                            customIds: $customIds
                        );
                    // ./ send system notification to the project creator 

                /** ./ send system notifications to users */



            // ./ logging and system notifications



            





            // Alert::success('Success', $message);
            return redirect()->route('project.show',[
                'project' => $this->project->id, 
            
            ])
            ->with('alert.success',$message)
            ;

 
        }

         
 
        // Alert::success('Success', $message);
        return redirect()->route('project.show',[
            'project' => $this->project->id, 
        
        ])
        ->with('alert.success',$message)
        ;

    }


    // For the Search Project Functionality
        

        // public function updatedQuery()
        // {
        //     if (!empty($this->query)) {
        //         $user = Auth::user();   

        //         // Extract selected project IDs to exclude from results
        //         $excludedIds = array_column($this->selectedProjectDocuments, 'id');


        //         $this->project_documents = ProjectDocument::query()
        //             ->with('document_type','project') // optional: for display without extra queries
        //             ->whereNotNull('rc_number')
        //             // ->whereNotNull('project_number')
        //             ->when(!empty($excludedIds), fn($q) => $q->whereNotIn('id', $excludedIds)) // safe guard
        //             ->where(function ($mainQuery) use ($user) {
        //                 $search = trim((string) $this->query);

        //                 // group all OR conditions
        //                 $mainQuery->where(function ($q) use ($search) {
        //                     $q
        //                     // ->where('name', 'like', "%{$search}%")
        //                         ->orWhere('rc_number', 'like', "%{$search}%")
        //                         ->orWhere('applicant', 'like', "%{$search}%")
        //                         ->orWhere('document_from', 'like', "%{$search}%")
        //                         ->orWhere('company', 'like', "%{$search}%")
        //                         ->orWhere('findings', 'like', "%{$search}%")
        //                         ->orWhere('comments', 'like', "%{$search}%") 
        //                         ->orWhereHas('document_type', function ($dq) use ($search) {
        //                             $dq->where('name', 'like', "%{$search}%");
        //                         })
        //                         ->orWhereHas('project', function ($dq) use ($search) {
        //                             $dq->where('name', 'like', "%{$search}%")
        //                                 ->orWhere('agency', 'like', "%{$search}%")
        //                                 ->orWhere('lot_number', 'like', "%{$search}%")
        //                                 ->orWhere('location', 'like', "%{$search}%")
        //                                 ->orWhere('street', 'like', "%{$search}%")
        //                                 ->orWhere('description', 'like', "%{$search}%");
        //                         }) ;
        //                 });

        //                 // Optional: access control
        //                 if (!$user->can('system access global admin') && !$user->can('system access admin')) {
        //                     $mainQuery->where(function ($q) use ($user) {
        //                         $q->where('created_by', $user->id);
        //                         // add more rules if needed
        //                     });
        //                 }
        //             })
        //             ->limit(10)
        //             ->get();

        //     } else {
        //         $this->project_documents = [];
        //     }


        //     // dd($this->project_documents);
        // }

        // public function addProjectDocumentReference($projectId)
        // {
        //     if (!in_array($projectId, array_column($this->selectedProjects, 'id'))) { 
        //         $project = Project::find($projectId);
        //         $this->selectedProjects[] = [


        //             // 'id' => $project->id,
        //             // 'project_name' => $project->name, 
        //             // 'rc_number' => $project->rc_number, 
        //             // 'location' => $project->location,
        //             // 'type' => $project->type,
        //             // 'agency' => $project->agency,



        //             'id' => $project->id,
        //             'name' => $project->name,
        //             'rc_number' => $project->rc_number,
        //             'project_number' => $project->project_number,
        //             'location' => $project->location,
        //             'type' => $project->type,
        //             'agency' => $project->agency,
        //         ];
        //     }

        //     $this->query = '';
        //     $this->project_documents = [];
        // }




        public function updatedQuery()
        {
            if (empty($this->query)) {
                $this->projects = null;
                return;
            }

            $user = Auth::user();

            // If somehow no authenticated user, return empty results safely
            if (!$user) {
                $this->projects = [];
                return;
            }

            // Extract selected project IDs to exclude from results
            $excludedIds = array_column($this->selectedProjects, 'id');

            $search = $this->query;

            $query = Project::query()
                ->select('id', 'name', 'lot_number',  'location', 'rc_number')
                ->whereNotNull('rc_number')
                // ->whereNotNull('project_number')
                ->when(!empty($excludedIds), function ($q) use ($excludedIds) {
                    $q->whereNotIn('id', $excludedIds);
                });

            // Optional: access control
            if (
                !$user->can('system access global admin')
                && !$user->can('system access admin')
            ) {
                $query->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                    // Add more rules if needed, e.g. shared projects, assignments, etc.
                });
            }

            // Search conditions
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('agency', 'like', "%{$search}%")
                    ->orWhere('lot_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('rc_number', 'like', "%{$search}%");
            });

            $this->projects = $query
                ->limit(10)
                ->get()
                ->toArray();
        }


 
        public function addProjectReference($projectId)
        {
            if (!in_array($projectId, array_column($this->selectedProjects, 'id'))) { 
                $project = Project::find($projectId);
                $this->selectedProjects[] = [


                    // 'id' => $project->id,
                    // 'project_name' => $project->name, 
                    // 'rc_number' => $project->rc_number, 
                    // 'location' => $project->location,
                    // 'type' => $project->type,
                    // 'agency' => $project->agency,



                    'id' => $project->id,
                    'name' => $project->name,
                    'rc_number' => $project->rc_number,
                    'project_number' => $project->project_number,
                    'location' => $project->location,
                    'type' => $project->type,
                    'agency' => $project->agency,
                ];
            }

            $this->query = '';
            $this->project_documents = [];
        }

        
        public function removeProjectReference($index)
        {
            unset($this->selectedProjects[$index]);
            $this->selectedProjects = array_values($this->selectedProjects); // Re-index array
        }

    // ./// For the Search Project Functionality

        
 

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
            Alert::error('Error','You are not the current reviewer for the project');
            return redirect()->route('project-document.review',[
                'project' => $this->project->id,
                'project_document' => $this->project_document->id,
            
            ]);

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

 
 

        Alert::success('Success','Project review submitted successfully');
        return redirect()->route('project-document.review',[
            'project' => $this->project->id,
            'project_document' => $this->project_document->id,
        
        ]);



    }

 
 
 





    public function render()
    {
        return view('livewire.admin.review.project.review-create');
    }
}
