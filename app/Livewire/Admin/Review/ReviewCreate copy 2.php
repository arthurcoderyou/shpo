<?php

namespace App\Livewire\Admin\Review;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;

use App\Models\DocumentType;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Validation\Rule;
use App\Models\ProjectReferences;
use App\Models\ReviewAttachments;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\ReviewRequireDocumentUpdates;
use Illuminate\Support\Facades\Notification;
use App\Models\ReviewRequireAttachmentUpdates;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ReviewerReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ReviewerReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;

class ReviewCreate extends Component
{

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

    public $review_status;
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
        public $selectedProjects = []; // Selected project references

    
    public function mount($id,$project_document_id){
        $this->project = Project::findOrFail($id);
        $this->project_document = ProjectDocument::findOrFail($project_document_id);

        // Generate the project number
        if(empty($this->project->rc_number)){ 
            $this->rc_number = ProjectDocument::generateProjectNumber(rand(10, 99));
        }


        // dd($this->project->project_documents);

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



        if(!empty( $this->project->project_references) && count($this->project->project_references) > 0){
            // add to the project references
            foreach($this->project->project_references as $project_reference){
                $project = Project::find($project_reference->referenced_project_id);
                $this->selectedProjects[] = [
                    'id' => $project->id,
                    'name' => $project->name,
                    'rc_number' => $project->rc_number,
                    'project_number' => $project->project_number,
                    'location' => $project->location,
                    'type' => $project->type,
                    'federal_agency' => $project->federal_agency,
                ];

            }


        }

         

        // dd( $this->selectedProjects );


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

    public function updatedReviewStatus(){
        if(!empty($this->review_status)){
            $this->project_status = false;
        }
    }

    

    public function updateDueDate(){
              
        $this->submitter_due_date = Project::calculateDueDate($this->project->updated_at,$this->submitter_response_duration_type, $this->submitter_response_duration );
        $this->reviewer_due_date = Project::calculateDueDate($this->project->updated_at,$this->reviewer_response_duration_type, $this->reviewer_response_duration );
    }

    // full approval of the project
    public function approve_project($project_id){
        
        $project = Project::find($project_id);
        

       
        // reset all roject reviewers
        $reviewers = ProjectReviewer::where('project_id', $project->id) 
                ->orderBy('order', 'asc')
                ->get();

        // make all reviewers approved
        foreach($reviewers as $rev){
            $rev->status = false; // make all reviewers as none active
            $rev->review_status = "approved";
            $rev->save();
        }

         
                
        // while status is true for project reviewer, this means that the project reviewer is the active/current reviewer o
        $reviewer = ProjectReviewer::where('project_id', $project->id)
            // ->where('review_status', 'pending') 
            ->orderBy('order', 'desc') // the order is backwards making the last as first
            ->first();


        // update the first reviewer as the current reviewer
        $reviewer->status = true;
        $reviewer->save();


        //create an approval review
        $review  = new Review();
        $review->viewed = false;
        $review->project_review = "Project is approved";
        $review->project_id = $project->id;
        $review->reviewer_id =  $reviewer->user_id;
        $review->review_status = "approved";
        // # ['pending','approved','rejected']
        $review->created_by = $reviewer->user_id;
        $review->updated_by = $reviewer->user_id;
        $review->save();


        // Send notification email to the creator of the project
        $user = User::where('id', $project->created_by)->first();

        $project = Project::where('id', $project->id)->first();
        if ($user) {

            // Notification::send($user, new ReviewerReviewNotification($project, $review));

            ProjectHelper::sendForProjectCreatorReviewerReviewNotification($user,$project,$review);

        }

 
        
        
        $project->status = "approved"; // aprove the project
        $project->allow_project_submission = false; // do not allow double submission until it is reviewed
        $project->save();


        // send project approval updates for creators and project subscribers if the project is approved 
        if($project->status == "approved"){
            ProjectHelper::sendCompleteProjectApprovalNotification($project);
        }



        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" approved ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
            'project_id' => $project->id,
        ]);

        Alert::success('Success','Project approved ');
        return redirect()->route('project.show',['project' => $project->id]);


    }


    // full rejection of the project
    public function reject_project($project_id){
        
        $project = Project::find($project_id);
        

       
        // reset all roject reviewers
        $reviewers = ProjectReviewer::where('project_id', $project->id) 
                ->orderBy('order', 'asc')
                ->get();

        // make all reviewers approved
        foreach($reviewers as $rev){
            $rev->status = false; // make all reviewers as none active
            $rev->review_status = "rejected";
            $rev->save();
        }

         
                
        // while status is true for project reviewer, this means that the project reviewer is the active/current reviewer o
        $reviewer = ProjectReviewer::where('project_id', $project->id)
            // ->where('review_status', 'pending') 
            ->orderBy('order', 'desc') // the order is backwards making the last as first
            ->first();


        // update the first reviewer as the current reviewer
        $reviewer->status = true;
        $reviewer->save();


        //create an approval review
        $review  = new Review();
        $review->viewed = false;
        $review->project_review = $this->project_review;
        $review->project_id = $project->id;
        $review->reviewer_id =  $reviewer->user_id ?? Auth::user()->id;
        $review->review_status = "rejected";
        // # ['pending','approved','rejected']
        $review->project_status = "rejected";
        $review->created_by = $reviewer->user_id ?? Auth::user()->id;
        $review->updated_by = $reviewer->user_id ?? Auth::user()->id;
        $review->save();


        // Send notification email to the creator of the project
        $user = User::where('id', $project->created_by)->first();

        $project = Project::where('id', $project->id)->first();
        if ($user) {

            // Notification::send($user, new ReviewerReviewNotification($project, $review));

            ProjectHelper::sendForProjectCreatorReviewerReviewNotification($user,$project,$review);

        }

 
        
        
        $project->status = "rejected"; // reject the project
        $project->allow_project_submission = false; // do not allow double submission until it is reviewed
        $project->save();


        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" rejected ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
            'project_id' => $project->id,
        ]);

        Alert::success('Success','Project rejected ');
        return redirect()->route('project.show',['project' => $project->id]);


    }




    public function update_project(){
        // dd($this->selectedProjects);


        $this->validate([
            'rc_number' => [
                'required',
                'string',
                Rule::unique('projects', 'rc_number'), // Ensure rc_number is unique
            ]
        ],[
            'The shpo number has already been taken. Please enter other combinations of shpo number '
        ]);

        $project = Project::findOrFail($this->project->id);

        $project->rc_number = $this->rc_number;
        $project->allotted_review_time_hours = $this->allotted_review_time_hours;
        


        $project->reviewer_response_duration = $this->reviewer_response_duration;
        $project->reviewer_response_duration_type = $this->reviewer_response_duration_type;
        // after updating the project, update the due date timers
        $project->reviewer_due_date = Project::calculateDueDate(now(),$this->reviewer_response_duration_type, $this->reviewer_response_duration );


        $project->submitter_response_duration = $this->submitter_response_duration;
        $project->submitter_response_duration_type = $this->submitter_response_duration_type;  
        // $project->submitter_due_date = Project::calculateDueDate(now(),$project->submitter_response_duration_type, $project->submitter_response_duration );
        $project->submitter_due_date = Project::calculateDueDate(now(),$this->submitter_response_duration_type, $this->submitter_response_duration );
 
        $project->save();



        // delete existing project references 
        if(!empty($project->project_references)){
            // delete project_references
            if(!empty($project->project_references)){
                foreach($project->project_references as $project_reference){
                    $project_reference->delete();
                } 
            }
        }



        // Save Project References (if any)
        if (!empty($this->selectedProjects)) {
            foreach ($this->selectedProjects as $selectedProject) {
                ProjectReferences::create([
                    'project_id' => $project->id,
                    'referenced_project_id' => $selectedProject['id'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }


 

        ActivityLog::create([
            'log_action' => "Project SHPO number on \"".$project->name."\" updated ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
            'project_id' => $project->id,
        ]);

        Alert::success('Success', "Project SHPO number on \"".$project->name."\" updated. You can now submit a review");
        return redirect()->route('project.review',['project' => $this->project->id]);

    }


    // For the Search Project Functionality
        

        public function updatedQuery()
        {
            if (!empty($this->query)) {
                $user = Auth::user();   

                // Extract selected project IDs to exclude from results
                $excludedIds = array_column($this->selectedProjects, 'id');


                $this->projects = Project::whereNotNull('rc_number')
                    ->whereNotNull('project_number')
                    ->whereNotIn('id', $excludedIds) // 👈 Exclude selected projects
                    ->where(function ($mainQuery) use ($user) {
                    $mainQuery->where('name', 'like', '%' . $this->query . '%')
                            ->orWhere('rc_number', 'like', '%' . $this->query . '%');

                    // Optional: Apply access control if needed
                    if (!$user->can('system access global admin') && !$user->can('system access admin')) {
                        // Example: only show projects the user created or is assigned to
                        $mainQuery->where(function ($q) use ($user) {
                            $q->where('created_by', $user->id);
                            // Add more project-level access rules here if needed
                        });
                    }

                })
                ->limit(10)
                ->get();

            } else {
                $this->projects = [];
            }
        }

        public function addProjectReference($projectId)
        {
            if (!in_array($projectId, array_column($this->selectedProjects, 'id'))) {
                $project = Project::find($projectId);
                $this->selectedProjects[] = [
                    'id' => $project->id,
                    'name' => $project->name,
                    'rc_number' => $project->rc_number,
                    'project_number' => $project->project_number,
                    'location' => $project->location,
                    'type' => $project->type,
                    'federal_agency' => $project->federal_agency,
                ];
            }

            $this->query = '';
            $this->projects = [];
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
            'project_review' => [
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

        ]);



        // dd($this->all());

        // dd($this->project_status);

        if(!empty($this->project_status)){
            if($this->project_status == "approved"){

                // dd("project is approved");
                $this->approve_project($this->project->id);


            }elseif($this->project_status == "rejected"){
                // dd("project is rejected");
                $this->reject_project($this->project->id);




            }
        }

        // dd("Project status approved");

        // dd($this->review_status);



        

        // get the project reviewer instance of the current user reviewer
        // $project_reviewer = $this->project->getReview(Auth::user()->id);
        // $project_reviewer = $this->project->getUserReview(Auth::user()->id);

        $project_reviewer = ProjectReviewer::getProjectReviewer($this->project->id,Auth::user()->id);


        if(empty($project_reviewer)){
            Alert::error('Error','You are not the current reviewer for the project');
            return redirect()->route('project.review',['project' => $this->project->id]);

        }

        // dd($this->project->project_documents);
        // dd($project_reviewer);


        // dd($project_reviewer);


        // dd($status);


        // update project reviewer
            // dd($project_reviewer);

            $project_reviewer->review_status = $this->review_status;

            if($this->review_status == "approved"){
                $project_reviewer->status = false; 
            }

            $project_reviewer->requires_project_update = $this->requires_project_update; 
            $project_reviewer->requires_document_update = $this->requires_document_update; 
            $project_reviewer->requires_attachment_update = $this->requires_attachment_update; 

            $project_reviewer->updated_at = now();
            $project_reviewer->updated_by = Auth::user()->id;
            $project_reviewer->save();
        // ./ update project reviewer
 

        //add review
            $review = new Review();
            $review->project_review = $this->project_review;
            $review->project_id = $this->project->id;
            $review->project_status = $this->project->status;

            // check if the reviewer type is document, to add project_document_id
            if($project_reviewer->reviewer_type == "document"){
                $review->project_document_id = $project_reviewer->project_document_id; // the project reviewer reviewing project document will be associated to its review
            }
            
            $review->reviewer_id = Auth::user()->id;


            /** Update the review time */
                /**
                 * Review Time is now based on last_submitted_at of the project
                 * 
                 * last_reviewed_at
                 * 
                 */

                // Ensure updated_at is after created_at
                if ($this->project->updated_at && now()->greaterThan($this->project->updated_at)) {
                    // Calculate time difference in hours
                    // $review->review_time_hours = $this->project->updated_at->diffInHours(now()); 
                    $review->review_time_hours = $this->project->updated_at->diffInSeconds(now()) / 3600; // shows hours in decimal
                }
    
            /** ./ Update the review time */
    
            $review->review_status = $this->review_status; 
            $review->requires_project_update = $this->requires_project_update; 
            $review->requires_document_update = $this->requires_document_update; 
            $review->requires_attachment_update = $this->requires_attachment_update; 


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

        // ./ add review

         


       
        
    

        // update project
            $project = Project::where('id', $this->project->id)->first();

            $project->allow_project_submission = true; 

            //update the next reviewer
            if($this->review_status == "approved"){
                $project->allow_project_submission = false;
            }

            $project->updated_at = now(); 

            $project->last_reviewed_at = now();
            $project->last_reviewed_by = Auth::user()->id;
 

            $project->reviewer_response_duration = $this->reviewer_response_duration;
            $project->reviewer_response_duration_type = $this->reviewer_response_duration_type;
            // after updating the project, update the due date timers
            $project->reviewer_due_date = Project::calculateDueDate(now(),$this->reviewer_response_duration_type, $this->reviewer_response_duration );


            $project->submitter_response_duration = $this->submitter_response_duration;
            $project->submitter_response_duration_type = $this->submitter_response_duration_type;  
            // $project->submitter_due_date = Project::calculateDueDate(now(),$project->submitter_response_duration_type, $project->submitter_response_duration );
            $project->submitter_due_date = Project::calculateDueDate(now(),$this->submitter_response_duration_type, $this->submitter_response_duration );
            

            $project->save();
        // ./ update project

        
        // add review required project document updates 
            if($this->requires_document_update){
                if(!empty($this->required_document_updates)){
                    foreach($this->required_document_updates as $key => $document_id){
                        ReviewRequireDocumentUpdates::create([
                            'review_id'  => $review->id,
                            'project_id' => $project->id,
                            'document_type_id' => $document_id, // document that is required to be added the next project submission
                            'project_reviewer_id'  => $project_reviewer->id,
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                        ]);
                    }

                }

            }
        // ./ add review required project document updates 


        // add review required project document attachment updates 
            if($this->requires_attachment_update){
                if(!empty($this->required_attachment_updates)){
                    foreach($this->required_attachment_updates as $key => $project_document_id){

                        $project_document = ProjectDocument::find($project_document_id);


                        ReviewRequireAttachmentUpdates::create([
                            'review_id'  => $review->id,
                            'project_id' => $project->id,
                            'project_document_id' => $project_document_id,
                            'document_type_id' => $project_document->document_type_id, // document that is required to be added the next project submission
                            'project_reviewer_id' => $project_reviewer->id,
                            'created_by'  => Auth::user()->id,
                            'updated_by'  => Auth::user()->id,
                        ]);
                    }

                }

            }
        // ./ add review required project document attachment updates




 
        // Send notification email to reviewer 
            $user = User::findOrFail($this->project->creator->id);// insert the project submitter and creator
            if ($user) {

                // notification to send to creator of the project about the reviewer review had been submitted . It is also an eamil notification
                $creator = User::where('id',$this->project->creator->id)->first();
                ProjectHelper::sendForProjectCreatorReviewerReviewNotification($creator, $project, $review);

            }
        // ./ Send notification email to reviewer



        // reset the project document reviewers 
        $project->resetCurrentProjectDocumentReviewers();
 
        //update the next reviewer
        if($this->review_status == "approved"){


            // the reviewers had been reset, we can just get the current reviewer now 
            // $next_project_reviewer = $project->getNextReviewer();
            $next_project_reviewer = $project->getCurrentReviewer();

            if(!empty($next_project_reviewer)){ // check if there are next reviewers
                // $next_project_reviewer->status = true;
                // $next_project_reviewer->save();
                
                // check if the next reviewer has a user value , if not, then it is an open review
                // NORMAL REVIEW NEXT
                if(!empty($next_project_reviewer->user_id) ){
                     // add to the review 
                    $review->next_reviewer_name = $next_project_reviewer->user->name;
                    $review->save();


                    // notify that reviewer that he is the next in line
                    // Send notification email to the next reviewer
                    $next_project_reviewer_user = User::where('id', $next_project_reviewer->user_id)->first();
                    if ($next_project_reviewer_user) {

                        // notification to send to the reviewer about project review notification
                        ProjectHelper::sendForReviewersProjectReviewNotification($next_project_reviewer_user,$project,  $next_project_reviewer);

                    }
                
                // OPEN REVIEW NEXT 
                }else{


                    // Determine users based on reviewer type
                    $reviewerType = $next_project_reviewer->reviewer_type; // assuming $reviewer is available

                    if (in_array($reviewerType, ['initial', 'final'])) {
                        $users = \Spatie\Permission\Models\Permission::whereIn('name', [
                            'system access admin',
                            'system access global admin',
                        ])
                        ->with('roles.users')
                        ->get()
                        ->flatMap(function ($permission) {
                            return $permission->roles->flatMap(function ($role) {
                                return $role->users;
                            });
                        })->unique('id')->values();
                    } elseif ($reviewerType === 'document') {
                        $users = \Spatie\Permission\Models\Permission::whereIn('name', [
                            'system access reviewer',
                            'system access admin',
                            'system access global admin',
                        ])
                        ->with('roles.users')
                        ->get()
                        ->flatMap(function ($permission) {
                            return $permission->roles->flatMap(function ($role) {
                                return $role->users;
                            });
                        })->unique('id')->values();
                    } else {
                        $users = collect(); // fallback to empty if reviewer_type is unknown
                    }




                    
                    foreach ($users as $user) {
                        try {
                            Notification::send($user, new ProjectOpenReviewNotification($project,$next_project_reviewer));
                        } catch (\Throwable $e) {
                            Log::error('Failed to send ProjectOpenReviewNotification notification: ' . $e->getMessage(), [
                                'project_id' => $project->id,
                                'user_id' => $user->id,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }

                        try {
                            Notification::send($user, new ProjectOpenReviewNotificationDB($project,$next_project_reviewer));
                        } catch (\Throwable $e) {
                            Log::error('Failed to send ProjectOpenReviewNotificationDB notification: ' . $e->getMessage(), [
                                'project_id' => $project->id,
                                'user_id' => $user->id,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }
                    }




                }

               


            




            }else{ // if there are no more reviewers, meaning the project is completed

                $project->status = "approved";
                $project->save();

 


            }

           


        }


        ActivityLog::create([
            'log_action' => "Project review on \"".$project->name."\" submitted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);


 

        // update the subscribers 

            //message for the subscribers 
            $message = "The project '".$project->name."' had been rejected by reviewer '".Auth::user()->name."'";
    
            if($this->review_status == "approved"){
                
                $message = "The project '".$project->name."' had been approved by reviewer '".Auth::user()->name."'";
            }

            ProjectHelper::sendForProjectSubscribersProjectSubscribersNotification($project,$message,"project_reviewed");
            
 
        // ./ update the subscribers 




        // send project approval updates for creators and project subscribers if the project is approved 
        if($project->status == "approved"){
            ProjectHelper::sendCompleteProjectApprovalNotification($project);
        }

        

        Alert::success('Success','Project review submitted successfully');
        return redirect()->route('project.project_document_review',['project' => $this->project->id]);



    }



    public function render()
    {
        return view('livewire.admin.review.review-create');
    }
}
