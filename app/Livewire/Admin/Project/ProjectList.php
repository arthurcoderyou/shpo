<?php

namespace App\Livewire\Admin\Project;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use App\Models\ProjectTimer;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ProjectReviewer;
 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert; 
use Illuminate\Support\Facades\Notification; 
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ReviewerReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;

class ProjectList extends Component
{

    use WithFileUploads;
    use WithPagination;

    public $search = '';
    public $sort_by = '';
    public $record_count = 10;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;

    public $file;

    
    public $title = "Projects";
    public $subtitle = "Listing of projects";

    public $project_status;
    public $project_status_options = [];

    public $review_status;
    public $review_status_options = [];

    // Route verifiers
    public $routeIsReview;
    public $routeIsPendingProject;

    public $projects_count;

    // location search
    public $location_search;


    public $latitude;
    public $longitude;
    public $location;

    public $type;

    public $project_types = [
        'Company',
        'Federal Agency'
    ];

 
    public function mount(){


        


        // get the current route
        // dd(request()->routeIs('project.index'));



        // function to update title and subtitle
        $this->updateTitleAndSub();

    
        //set the project status array
        $this->setProjectStatusArray();

        //set the project review status array
        $this->setReviewStatusArray();

        $this->routeIsReview = request()->routeIs('project.in_review');
        $this->routeIsPendingProject = request()->routeIs('project.pending_project_update');

        $this->project_status = request()->query('project_status', ''); // Default to empty string if not set

        $this->review_status = request()->query('review_status', ''); // Default to empty string if not set
        
        $this->projects_count = 0;





    }


    public function setProjectStatusArray()
    { 

        $this->project_status_options = [
            "in_review" => "In Review",
            "pending" => "Pending",
            "approved" => "Approved",
            "rejected" => "Rejected",

        ];

        if (auth()->user()->hasRole('DSI God Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('User')) {
           $this->project_status_options = ["draft" => "Draft"] + $this->project_status_options; // Add "Draft" at the beginning
        }
 
    }


    public function setReviewStatusArray()
    { 

        $this->review_status_options = [ 
            "pending" => "Pending",
            "approved" => "Approved",
            "rejected" => "Rejected",
        ];

         
    }


    public function updateTitleAndSub(){


        if(request()->routeIs('project.index')){
            $this->title = "Project"; 
            $this->subtitle = "Listing of projects";
        }elseif(request()->routeIs('project.in_review')){
            $this->title = "Project for Review"; 
            $this->subtitle = "Listing of projects that needs to be reviewed";
        }elseif(request()->routeIs('project.pending_project_update')){
            $this->title = "Project for Update"; 
            $this->subtitle = "Listing of projects that needs to be updated ";
        }

    }


    public function deleteSelected()
    {
        // Get selected projects before deleting
        $projects = Project::whereIn('id', $this->selected_records)->get(); 

        // Delete the selected records
        // Project::whereIn('id', $this->selected_records)->delete();

 

        // Log each deleted project
        foreach ($projects as $project) {


            // check if project is not created by the user or the project is not draft
            /**Identify the records to disply by roles  */
            if(Auth::user()->hasRole('User')){
                 
                if($project->created_by !== Auth::id()){
                    Alert::error('Error', 'Selected projects are not yours');
                    return redirect()->route('project.index');
                }

                if($project->status !== "draft"){
                    Alert::error('Error', 'Selected projects are not drafts');
                    return redirect()->route('project.index');
                }
                


            }elseif(Auth::user()->hasRole('Reviewer')){
                if($project->created_by !== Auth::id()){
                    Alert::error('Error', 'Selected projects are not yours');
                    return redirect()->route('project.index');
                }

                if($project->status !== "draft"){
                    Alert::error('Error', 'Selected projects are not drafts');
                    return redirect()->route('project.index');
                }


            }elseif(Auth::user()->hasRole('Admin')){

            }




            ActivityLog::create([
                'log_action' => "Project \"{$project->name}\" deleted",
                'log_username' => Auth::user()->name,
                'created_by' => Auth::user()->id,
            ]);


            // delete project connected records 
        
                //delete project reviewers 
                if(!empty($project->project_reviewers)){
                    foreach($project->project_reviewers as $reviewer){
                        $reviewer->delete();
                    } 
                }

                //delete project reviews 
                if(!empty($project->project_reviews)){
                    foreach($project->project_reviews as $review){
                        $review->delete();
                    } 
                }

                //delete project documents 
                if(!empty($project->project_documents)){
                    foreach($project->project_documents as $document){

                        // delete project attachments for each project document 
                        if(!empty($document->project_attachments)){
                            foreach($document->project_attachments as $attachment){
                                // Construct the full file path
                                $filePath = "public/uploads/project_attachments/{$attachment->attachment}";

                                // Check if the file exists in storage and delete it
                                if (Storage::exists($filePath)) {
                                    Storage::delete($filePath);
                                }

                                // Delete the record from the database
                                $attachment->delete();
                            }


                        }



                        $document->delete();
                    } 
                }

                // delete project subscribers
                if(!empty($project->project_subscribers)){
                    foreach($project->project_subscribers as $subcriber){
                        $subcriber->delete();
                    } 
                }
            
            // ./ delete project connected records 



            $project->delete();



        }

        // Clear selected records
        $this->selected_records = [];

        Alert::success('Success', 'Selected projects deleted successfully');
        return redirect()->route('project.index');
    }

    // This method is called automatically when selected_records is updated
    public function updateSelectedCount()
    {
        // Update the count when checkboxes are checked or unchecked
        $this->count = count($this->selected_records);
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selected_records = Project::select('projects.*'); // Select all records


            /**Identify the records to disply by roles  */
            if(Auth::user()->hasRole('User')){
                // $projects = $projects->where('projects.created_by', '=', Auth::user()->id);
            

                /**Identify the records to display based on the current route */
                /** User route for pending project updates for resubmission */
                if($this->routeIsPendingProject){
                    $this->selected_records = $this->selected_records->whereNot('status','approved') // show projects that are pending update because the project needs to be updated after being reviewed
                        ->whereNot('status','draft')
                        ->where('allow_project_submission',true)
                        
                        ->where('created_by',Auth::user()->id);
                } 

                // for other routes, user will display all of his projects
                $this->selected_records = $this->selected_records->where('projects.created_by', '=', Auth::user()->id);


            }elseif(Auth::user()->hasRole('Reviewer')){
                // $this->selected_records = $this->selected_records->where('projects.created_by', '=', Auth::user()->id);
                
                if($this->routeIsReview){
                    $this->selected_records = $this->selected_records->whereNot('status','approved')->whereNot('status','draft')
                        ->whereHas('project_reviewers', function ($query) {
                            $query->where('user_id', Auth::id())
                                ->where('status', true)
                                ; // Filter by the logged-in user's ID
                        });

                }

                // do not show drafts to reviewers
                $this->selected_records = $this->selected_records->whereNot('status','draft');


            }elseif(Auth::user()->hasRole('Admin')){
                // $this->selected_records = $this->selected_records->where('projects.created_by', '=', Auth::user()->id);
                
                if($this->routeIsReview){
                    $this->selected_records = $this->selected_records->whereNot('status','approved')->whereNot('status','draft')
                        ->whereHas('project_reviewers', function ($query) {
                            $query->where('status', true)
                                ; // Filter by the logged-in user's ID
                        })
                        ->where('allow_project_submission',false);

                }elseif($this->routeIsPendingProject){
                    $this->selected_records = $this->selected_records->whereNot('status','approved') // show projects that are pending update because the project needs to be updated after being reviewed
                        ->whereNot('status','draft')
                        ->where('allow_project_submission',true);
                        
                        // ->where('created_by',Auth::user()->id);
                } 

                // do not show drafts to reviewers
                $this->selected_records = $this->selected_records->whereNot('status','draft');
            }
         
            $this->selected_records = $this->selected_records->pluck('id')->toArray();

        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){
        $project = Project::find($id);


        // delete project connected records 
        
            //delete project reviewers 
            if(!empty($project->project_reviewers)){
                foreach($project->project_reviewers as $reviewer){
                    $reviewer->delete();
                } 
            }

            //delete project reviews 
            if(!empty($project->project_reviews)){
                foreach($project->project_reviews as $review){
                    $review->delete();
                } 
            }

            //delete project documents 
            if(!empty($project->project_documents)){
                foreach($project->project_documents as $document){

                    // delete project attachments for each project document 
                    if(!empty($document->project_attachments)){
                        foreach($document->project_attachments as $attachment){
                            // Construct the full file path
                            $filePath = "public/uploads/project_attachments/{$attachment->attachment}";

                            // Check if the file exists in storage and delete it
                            if (Storage::exists($filePath)) {
                                Storage::delete($filePath);
                            }

                            // Delete the record from the database
                            $attachment->delete();
                        }


                    }



                    $document->delete();
                } 
            }

            // delete project subscribers
            if(!empty($project->project_subscribers)){
                foreach($project->project_subscribers as $subcriber){
                    $subcriber->delete();
                } 
            }
        
        // ./ delete project connected records 



        $project->delete();


        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" deleted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);




        Alert::success('Success','Project deleted successfully');
        return redirect()->route('project.index');

    }


    /** Project Submission restriction  */
    private function checkProjectRequirements()
    {
        $projectTimer = ProjectTimer::first();
    
        return [
            'response_duration' => !$projectTimer || (
                !$projectTimer->submitter_response_duration_type ||
                !$projectTimer->submitter_response_duration ||
                !$projectTimer->reviewer_response_duration ||
                !$projectTimer->reviewer_response_duration_type
            ),
            'project_submission_times' => !$projectTimer || (
                !$projectTimer->project_submission_open_time ||
                !$projectTimer->project_submission_close_time ||
                !$projectTimer->message_on_open_close_time
            ),
            'no_reviewers' => Reviewer::count() === 0,
            'no_document_types' => DocumentType::count() === 0,
        ];
    }


    /**Check if project is within open and close hours */
    private function isProjectSubmissionAllowed()
    {
        $projectTimer = ProjectTimer::first();

        if ($projectTimer->project_submission_restrict_by_time) {
            $currentTime = now();
            $openTime = $projectTimer->project_submission_open_time;
            $closeTime = $projectTimer->project_submission_close_time;

            if ($currentTime < $openTime || $currentTime > $closeTime) {
                return false;
            }
        }

        return true;
    }


    public function submit_project($project_id){


        $errors = $this->checkProjectRequirements();
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


        if (!$this->isProjectSubmissionAllowed()) {
            $openTime = ProjectTimer::first()->project_submission_open_time;
            $closeTime = ProjectTimer::first()->project_submission_close_time;

            $errorMessages[] = 'Project submission is currently restricted. Please try again between ' . $openTime->format('h:i A') . ' and ' . $closeTime->format('h:i A');
        }

        if (!empty($errorMessages)) {
            $message = 'The project cannot be submitted because: ';
            $message .= implode(', ', $errorMessages);
            $message .= '. Please wait for the admin to configure these settings.';
            Alert::error('Error', $message);
            return redirect()->route('project.index');
        }



        // // check if there are existing project reviewers 
        // if(Reviewer::count() == 0){
        //     Alert::error('Error','Project reviewers are not added yet to the system');
        //     return redirect()->route('project.index');

        // }


        
        $project = Project::find($project_id);
        $response_time_hours = 0;

        /** Update the response time */

            // Ensure updated_at is after created_at
            if ( $project->updated_at && now()->greaterThan( $project->updated_at)) {
                // Calculate time difference in hours
                // $response_time_hours = $this->project->updated_at->diffInHours(now()); 
                $response_time_hours = $project->updated_at->diffInSeconds(now()) / 3600; // shows hours in decimal
            }
 
        /** ./ Update the response time */


        // dd($project);


        // if the project is a draft, create the default values
        if($project->status == "draft"){
            // Fetch all reviewers in order
            $reviewers = Reviewer::orderBy('order')->get();

            foreach ($reviewers as $reviewer) {
                $projectReviewer = ProjectReviewer::create([
                    'order' => $reviewer->order,
                    'review_status' => 'pending',
                    'project_id' => $project->id,
                    'user_id' => $reviewer->user_id,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                
            }
            
            // while status is true for project reviewer, this means that the project reviewer is the active/current reviewer o
            $reviewer = ProjectReviewer::where('project_id', $project->id)
                ->where('review_status', 'pending') 
                ->orderBy('order', 'asc')
                ->first();


            // update the first reviewer as the current reviewer
            $reviewer->status = true;
            $reviewer->save();



            // submitting a project creates a review that the user had submitted the project
            // the condition is that the project creator id must be hte same to the reviewer id
            Review::create([
                'viewed' => true,
                'project_review' => 'The project had been submitted', // message for draft projects
                'project_id' => $project->id,
                'reviewer_id' =>  $project->created_by,
                'review_status' => 'submitted',
                'created_by' => $project->created_by,
                'updated_by' => $project->created_by,
                'response_time_hours' => $response_time_hours,
                
            ]);


            // Send notification email to reviewer
            $user = User::find( $reviewer->user_id);
            if ($user) {
                //send email notification
                Notification::send($user, new ProjectReviewNotification($project, $reviewer));
                //send notification to the database
                Notification::send($user, new ProjectReviewNotificationDB($project, $reviewer));



                // update the subscribers 

                    //message for the subscribers 
                    $message = "The project '".$project->name."' had been submitted by '".Auth::user()->name."'";
            

                    if(!empty($project->project_subscribers)){

                        $sub_project = Project::where('id',$project->id)->first(); // get the project to be used for notification

                        foreach($project->project_subscribers as $subcriber){

                            // subscriber user 
                            $sub_user = User::where('id',$subcriber->user_id)->first();

                            if(!empty($sub_user)){
                                // notify the next reviewer
                                Notification::send($sub_user, new ProjectSubscribersNotification($sub_user, $sub_project,'project_submitted',$message ));
                                /**
                                 * Message type : 
                                 * @case('project_submitted')
                                        @php $message = "A new project, <strong>{$project->name}</strong>, has been submitted for review. Stay tuned for updates."; @endphp
                                        @break

                                    @case('project_reviewed')
                                        @php $message = "The project <strong>{$project->name}</strong> has been reviewed. Check out the latest status."; @endphp
                                        @break

                                    @case('project_resubmitted')
                                        @php $message = "The project <strong>{$project->name}</strong> has been updated and resubmitted for review."; @endphp
                                        @break

                                    @case('project_reviewers_updated')
                                        @php $message = "The list of reviewers for the project <strong>{$project->name}</strong> has been updated."; @endphp
                                        @break

                                    @default
                                        @php $message = "There is an important update regarding the project <strong>{$project->name}</strong>."; @endphp
                                */


                            }
                            


                        }
                    } 
                // ./ update the subscribers 



            }





        }else{ // if not, get the current reviewer

            $reviewer = $project->getCurrentReviewer();
            $reviewer->review_status = "pending";
            $reviewer->save();


            
            // submitting a project creates a review that the user had submitted the project
            // the condition is that the project creator id must be hte same to the reviewer id
            Review::create([
                'viewed' => true,
                'project_review' => 'The project had been re-submitted', // message for not-draft projects
                'project_id' => $project->id,
                'reviewer_id' =>  $project->created_by,
                'review_status' => 're_submitted',
                'created_by' => $project->created_by,
                'updated_by' => $project->created_by,
                'response_time_hours' => $response_time_hours,
                
            ]);

            // Send notification email to reviewer
            $user = User::find( $reviewer->user_id);
            if ($user) {
                //send email notification
                Notification::send($user, new ProjectReviewFollowupNotification($project, $reviewer));
                //send notification to the database
                Notification::send($user, new ProjectReviewFollowupNotificationDB($project, $reviewer));



                // update the subscribers 

                    //message for the subscribers 
                    $message = "The project '".$project->name."' had been re-submitted by '".Auth::user()->name."'";
            

                    if(!empty($project->project_subscribers)){

                        $sub_project = Project::where('id',$project->id)->first(); // get the project to be used for notification

                        foreach($project->project_subscribers as $subcriber){

                            // subscriber user 
                            $sub_user = User::where('id',$subcriber->user_id)->first();

                            if(!empty($sub_user)){
                                // notify the next reviewer
                                Notification::send($sub_user, new ProjectSubscribersNotification($sub_user, $sub_project,'project_resubmitted',$message ));
                                /**
                                 * Message type : 
                                 * @case('project_submitted')
                                        @php $message = "A new project, <strong>{$project->name}</strong>, has been submitted for review. Stay tuned for updates."; @endphp
                                        @break

                                    @case('project_reviewed')
                                        @php $message = "The project <strong>{$project->name}</strong> has been reviewed. Check out the latest status."; @endphp
                                        @break

                                    @case('project_resubmitted')
                                        @php $message = "The project <strong>{$project->name}</strong> has been updated and resubmitted for review."; @endphp
                                        @break

                                    @case('project_reviewers_updated')
                                        @php $message = "The list of reviewers for the project <strong>{$project->name}</strong> has been updated."; @endphp
                                        @break

                                    @default
                                        @php $message = "There is an important update regarding the project <strong>{$project->name}</strong>."; @endphp
                                */


                            }
                            


                        }
                    } 
                // ./ update the subscribers



            }




        }
        
        
        $project->status = "submitted";
        $project->allow_project_submission = false; // do not allow double submission until it is reviewed
        $project->updated_at = now();
        $project->save();



        


        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" submitted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project submitted successfully');
        return redirect()->route('project.index');


    }


 
    public function restart_review_project($project_id){
        
        $project = Project::find($project_id);
        

       
        // reset all roject reviewers
        $reviewers = ProjectReviewer::where('project_id', $project->id) 
                ->orderBy('order', 'asc')
                ->get();

        // make all reviewers restart
        foreach($reviewers as $rev){
            $rev->review_status = "pending";
            $rev->save();
        }

         
                
        // while status is true for project reviewer, this means that the project reviewer is the active/current reviewer o
        $reviewer = ProjectReviewer::where('project_id', $project->id)
            ->where('review_status', 'pending') 
            ->orderBy('order', 'asc')
            ->first();


        // update the first reviewer as the current reviewer
        $reviewer->status = true;
        $reviewer->save();


        // Send notification email to reviewer
        $user = User::find( $reviewer->user_id);
        if ($user) {
            Notification::send($user, new ProjectReviewNotification($project, $reviewer));
        }

 
        
        
        $project->status = "submitted"; // back to default
        $project->allow_project_submission = false; // do not allow double submission until it is reviewed
        $project->save();


        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" review restarted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project review restarted ');
        return redirect()->route('project.index');


    }


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
        $review->review_status = "pending";
        // # ['pending','approved','rejected']
        $review->created_by = $reviewer->user_id;
        $review->updated_by = $reviewer->user_id;
        $review->save();


        // Send notification email to the creator of the project
        $user = User::find( $project->created_by);
        if ($user) {

            Notification::send($user, new ReviewerReviewNotification($project, $review));
        }

 
        
        
        $project->status = "approved"; // aprove the project
        $project->allow_project_submission = false; // do not allow double submission until it is reviewed
        $project->save();


        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" approved ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project approved ');
        return redirect()->route('project.index');


    }
 



    public function render()
    {   

        /**
         * 3 Kinds of users
         * Reviewer
         * Admin
         * User
         * 
         * Non-role users
         * 
         * 
         * Adjust the display based on the current route
         */


        $projects = Project::select('projects.*');


        if (!empty($this->search)) {
            $search = $this->search;
        
            $projects = $projects->where(function($query) use ($search) {
                $query->where('projects.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.federal_agency', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.type', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.description', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.location', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.latitude', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.longitude', 'LIKE', '%' . $search . '%')
                    // ->orWhereHas('creator', function ($query) use ($search) {
                    //     $query->where('users.name', 'LIKE', '%' . $search . '%')
                    //         ->where('users.email', 'LIKE', '%' . $search . '%');
                    // })
                    // ->orWhereHas('updator', function ($query) use ($search) {
                    //     $query->where('users.name', 'LIKE', '%' . $search . '%')
                    //         ->where('users.email', 'LIKE', '%' . $search . '%');
                    // })
                    ->orWhereHas('project_reviewers.user', function ($query) use ($search) {
                        $query->where('users.name', 'LIKE', '%' . $search . '%')
                        ->where('users.email', 'LIKE', '%' . $search . '%');
                    });
            });
        }


        if (!empty($this->location)) {
            $locations = explode(',', $this->location); // Convert location string to an array
            $locations = array_map('trim', $locations); // Remove extra spaces
        
            $projects = $projects->where(function($query) use ($locations) {
                foreach ($locations as $location) {
                    $query->where('projects.location', 'LIKE', '%' . $location . '%');
                }
            });
        }
        

        if (!empty($this->type)) {
            $type = $this->type;
         

            $projects = $projects->where('projects.type', $type );
        }

        

        // filter for project status
        if(!empty($this->project_status) && $this->project_status !== ""){
            // dd($this->routeIsReview);
            $projects = $projects->where('status',$this->project_status);
        }

        // Filter projects based on review_status in project_reviewers
        if (!empty($this->review_status) && $this->review_status !== "") {

            if($this->review_status == "approved"){
                $projects = $projects->where('status', "approved");
            }else{
                $projects = $projects->whereHas('project_reviewers', function ($query) {
                    $query->where('status', true)
                        ->where('review_status', $this->review_status);
                });
            }
            


        }


        /**Identify the records to disply by roles  */
        if(Auth::user()->hasRole('User')){
            // $projects = $projects->where('projects.created_by', '=', Auth::user()->id);
        

            /**Identify the records to display based on the current route */
            /** User route for pending project updates for resubmission */
            if($this->routeIsPendingProject){
                $projects = $projects->whereNot('status','approved') // show projects that are pending update because the project needs to be updated after being reviewed
                    ->whereNot('status','draft')
                    ->where('allow_project_submission',true)
                    
                    ->where('created_by',Auth::user()->id);
            } 

            // for other routes, user will display all of his projects
            $projects = $projects->where('projects.created_by', '=', Auth::user()->id);


        }elseif(Auth::user()->hasRole('Reviewer')){
            // $projects = $projects->where('projects.created_by', '=', Auth::user()->id);
            
            if($this->routeIsReview){
                $projects = $projects->whereNot('status','approved')->whereNot('status','draft')
                    ->whereHas('project_reviewers', function ($query) {
                        $query->where('user_id', Auth::id())
                            ->where('status', true)
                            ; // Filter by the logged-in user's ID
                    });

            }

            // do not show drafts to reviewers
            $projects = $projects->whereNot('status','draft');


        }elseif(Auth::user()->hasRole('Admin')){
            // $projects = $projects->where('projects.created_by', '=', Auth::user()->id);
            
            if($this->routeIsReview){
                $projects = $projects->whereNot('status','approved')->whereNot('status','draft')
                    ->whereHas('project_reviewers', function ($query) {
                        $query->where('status', true)
                            ; // Filter by the logged-in user's ID
                    })
                    ->where('allow_project_submission',false);

            }elseif($this->routeIsPendingProject){
                $projects = $projects->whereNot('status','approved') // show projects that are pending update because the project needs to be updated after being reviewed
                    ->whereNot('status','draft')
                    ->where('allow_project_submission',true);
                    
                    // ->where('created_by',Auth::user()->id);
            } 

            // do not show drafts to reviewers
            $projects = $projects->whereNot('status','draft');
        }
         


        


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Name A - Z":
                    $projects =  $projects->orderBy('projects.name','ASC');
                    break;

                case "Name Z - A":
                    $projects =  $projects->orderBy('projects.name','DESC');
                    break;

                case "Description A - Z":
                    $projects =  $projects->orderBy('projects.description','ASC');
                    break;
 
                case "Description Z - A":
                    $projects =  $projects->orderBy('projects.description','DESC');
                    break;

                case "Federal Agency A - Z":
                    $projects =  $projects->orderBy('projects.federal_agency','ASC');
                    break;
    
                case "Federal Agency Z - A":
                    $projects =  $projects->orderBy('projects.federal_agency','DESC');
                    break;

                case "Nearest Submission Due Date":
                    /** For submission, look for projects first that are rejected and needs resubmission */

                    $projects = $projects->withCount([
                        'project_reviewers as pending_submission_count' => function ($query) {
                            $query->where('status', true)
                                ->where('review_status', 'rejected');
                        },
                        'project_reviewers as not_fully_approved_count' => function ($query) {
                            $query->where('status', true)
                                ->whereNot('review_status', 'approved'); // Ensures at least one reviewer is not approved
                        }
                    ])
                    ->orderByDesc('pending_submission_count') // Prioritize projects needing resubmission
                    ->orderByDesc('not_fully_approved_count') // Prioritize projects where not all reviewers have approved
                    ->orderBy('submitter_due_date', 'ASC'); // Then sort by due date
                

                    // $projects = $projects->orderBy('projects.submitter_due_date', 'ASC');
                    break;
                    
                case "Farthest Submission Due Date":
                    $projects = $projects->withCount([
                        'project_reviewers as pending_submission_count' => function ($query) {
                            $query->where('status', true)
                                ->where('review_status', 'rejected');
                        },
                        'project_reviewers as not_fully_approved_count' => function ($query) {
                            $query->where('status', true)
                                ->whereNot('review_status', 'approved'); // Ensures at least one reviewer is not approved
                        }
                    ])
                    ->orderByDesc('pending_submission_count') // Prioritize projects needing resubmission
                    ->orderByDesc('not_fully_approved_count') // Prioritize projects where not all reviewers have approved
                    ->orderBy('submitter_due_date', 'DESC'); // Then sort by due date
                


                    // $projects = $projects->orderBy('projects.submitter_due_date', 'DESC');
                    break;
                    
                case "Nearest Reviewer Due Date":
                    $projects = $projects->withCount([
                        'project_reviewers as pending_review_count' => function ($query) {
                            $query->where('status', true)
                                ->where('review_status', 'pending');
                        },
                        'project_reviewers as not_fully_approved_count' => function ($query) {
                            $query->where('status', true)
                                ->whereNot('review_status', 'approved'); // Ensures at least one reviewer is not approved
                        }
                    ])
                    ->orderByDesc('pending_review_count') // Prioritize projects needing review
                    ->orderByDesc('not_fully_approved_count') // Prioritize projects where not all reviewers have approved
                    ->orderBy('reviewer_due_date', 'ASC'); // Then sort by due date



                    // $projects = $projects->orderBy('projects.reviewer_due_date', 'ASC');
                    break;
                    
                case "Farthest Reviewer Due Date":
                    $projects = $projects->withCount([
                        'project_reviewers as pending_review_count' => function ($query) {
                            $query->where('status', true)
                                ->where('review_status', 'pending');
                        },
                        'project_reviewers as not_fully_approved_count' => function ($query) {
                            $query->where('status', true)
                                ->whereNot('review_status', 'approved'); // Ensures at least one reviewer is not approved
                        }
                    ])
                    ->orderByDesc('pending_review_count') // Prioritize projects needing review
                    ->orderByDesc('not_fully_approved_count') // Prioritize projects where not all reviewers have approved
                    ->orderBy('reviewer_due_date', 'DESC'); // Then sort by due date

                    // $projects = $projects->orderBy('projects.reviewer_due_date', 'DESC');
                    break;

                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $projects =  $projects->orderBy('projects.created_at','DESC');
                    break;

                case "Oldest Added":
                    $projects =  $projects->orderBy('projects.created_at','ASC');
                    break;

                case "Latest Updated":
                    $projects =  $projects->orderBy('projects.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $projects =  $projects->orderBy('projects.updated_at','ASC');
                    break;
                default:
                    $projects =  $projects->orderBy('projects.updated_at','DESC');
                    break;

            }


        }else{

            /** Adjust the default sorting based on the user role and route */
            /**Identify the records to disply by the current route  */
            if(request()->routeIs('project.pending_project_update')){ // showing user projects pending update

                /**
                 * prioritized due date based on submitter_due_date
                 * @var mixed
                 */
                $projects =  $projects->orderBy('projects.submitter_due_date','ASC');

            }elseif(request()->routeIs('project.in_review')){

                /**
                 * prioritized due date based on reviewer_due_date
                 * @var mixed
                 */
                // $projects =  $projects->orderBy('projects.reviewer_due_date','ASC');

                $projects =  $projects->withCount([
                    'project_reviewers as pending_review_count' => function ($query) {
                        $query->where('review_status', 'pending');
                    }
                ])
                ->orderByDesc('pending_review_count') // Prioritize projects with pending reviews
                ->orderBy('reviewer_due_date', 'ASC'); // Then sort by due date


            }else{
                $projects =  $projects->orderBy('projects.updated_at','DESC');
            }
 

            

        }


        $this->projects_count = $projects->count();


        $projects = $projects->paginate($this->record_count);
 
        return view('livewire.admin.project.project-list',[
            'projects' => $projects
        ]);
    }
}
