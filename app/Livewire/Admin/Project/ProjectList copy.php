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
use App\Helpers\ProjectHelper;
 
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

    protected $listeners = [
        'projectCreated' => '$refresh',
        'projectUpdated' => '$refresh',
        'projectDeleted' => '$refresh',
        'projectSubmitted' => '$refresh',
        'projectQueued' => '$refresh',
        'projectDocumentCreated' => '$refresh',
        'projectDocumentUpdated' => '$refresh',
        'projectDocumentDeleted' => '$refresh',
    ];


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
    
    public $routeIsMyProjects;
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
        'Local',
        'Federal',
        'Private'
    ];


    public $route;
    public $myProjects;

    public $home_route;
 
    public function mount($route = 'project.index' ){
        // dd($route);

        $this->route = $route;
        /**
         * options for status
         * projects
         * pending_update_projects
         * in_review_projects
         */


         

        // get the current route
        // dd(request()->routeIs('project.index'));



        // function to update title and subtitle
        $this->updateTitleAndSub();

    
        //set the project status array
        $this->setProjectStatusArray();

        //set the project review status array
        $this->setReviewStatusArray();


 


        $this->project_status = request()->query('project_status', ''); // Default to empty string if not set

        $this->review_status = request()->query('review_status', ''); // Default to empty string if not set
        
        $this->projects_count = 0;
 
        $this->home_route = route($route);

        // dd($this->home_route);



    }


    // public function setHomeRoute(){

    //     $user = Auth::user();

    //     switch ($this->status) {
    //         case 'projects':
    //             if ($this->myProjects) {
    //                 $this->home_route = route('project.index');
    //             } else {
    //                 $this->home_route = route('project.index');
                     
    //             }
    //             break;

    //         case 'pending_update_projects':
    //             if ($this->myProjects) {
    //                 $this->home_route = route('project.pending_project_update.my-projects');
    //             } else {
    //                 $this->home_route = route('project.pending_project_update');
                     
    //             }
    //             break;

    //         case 'in_review_projects':
    //             if ($this->myProjects) {
    //                 $this->home_route = route('project.in_review.my-projects');
    //             } else {
    //                 $this->home_route = route('project.in_review'); 
    //             }
    //             break;

    //         default:
    //             $this->home_route = route('project.index');
    //             break;
    //     }
    // }


    public function setProjectStatusArray()
    { 

        $this->project_status_options = [
            "submitted" => "Submitted",
            "in_review" => "In Review",
            "pending" => "Pending",
            "approved" => "Approved",
            "rejected" => "Rejected",
            

        ];

        if ( (auth()->user()->can('system access global admin') || auth()->user()->can('project list view all')) || request()->routeIs('project.index')  ) {
           $this->project_status_options = [
                "draft" => "Draft",
                "on_que" => "On Que",
            ] + $this->project_status_options; // Add "Draft" and "On Que at the beginning 


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


    public function updateTitleAndSub()
    {
        switch ($this->route) {
            case 'project.index':
                $this->title = 'My Projects';
                $this->subtitle = 'Listing of my projects';
                break;

            case 'project.index.update-pending':
                $this->title = 'My Projects - Update Pending';
                $this->subtitle = 'Projects you own that have pending updates';
                break;

            case 'project.index.review-pending':
                $this->title = 'My Projects - Review Pending';
                $this->subtitle = 'Projects you own that are pending review';
                break;

            case 'project.index.all':
                $this->title = 'All Projects';
                $this->subtitle = 'Listing of all submitted projects';
                break;

            case 'project.index.update-pending.all':
                $this->title = 'All Projects - Update Pending';
                $this->subtitle = 'All projects in the system that have pending updates';
                break;

            case 'project.index.review-pending.all':
                $this->title = 'All Projects - Review Pending';
                $this->subtitle = 'All projects in the system that are pending review';
                break;



            case 'project.index.all.no-drafts':
                $this->title = 'All Projects (No Drafts)';
                $this->subtitle = 'Listing of all non-draft projects';
                break;

            case 'project.index.update-pending.all-linked':
                $this->title = 'Linked Projects - Update Pending';
                $this->subtitle = 'Projects you are reviewing that require updates';
                break;

            case 'project.index.review-pending.all-linked':
                $this->title = 'Linked Projects - Review Pending';
                $this->subtitle = 'Projects you are reviewing that are pending review';
                break;

            case 'project.index.open-review':
                $this->title = 'Open Review projects';
                $this->subtitle = 'Projects without a confirmed reviewer';
                break;  

            

            default:
                $this->title = 'Projects';
                $this->subtitle = 'General project listing';
                break;
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
            if(Auth::user()->can('system access user')){
                 
                if($project->created_by !== Auth::id()){
                    Alert::error('Error', 'Selected projects are not yours');
                    return redirect()->route('project.index');
                }

                if($project->status !== "draft"){
                    Alert::error('Error', 'Selected projects are not drafts');
                    return redirect()->route('project.index');
                }
                


            }elseif(Auth::user()->can('system access reviewer')){
                if($project->created_by !== Auth::id()){
                    Alert::error('Error', 'Selected projects are not yours');
                    return redirect()->route('project.index');
                }

                if($project->status !== "draft"){
                    Alert::error('Error', 'Selected projects are not drafts');
                    return redirect()->route('project.index');
                }


            }elseif(Auth::user()->can('system access admin')){

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

        // if($project->created_by == auth()->user()->id){
        //     return redirect()->route('project.index');

        // }else{

        return redirect()->back();
        // }

        
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
            if(Auth::user()->can('system access user')){
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


            }elseif(Auth::user()->can('system access reviewer')){
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


            }elseif(Auth::user()->can('system access admin') || Auth::user()->can('system access global admin')){
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

        if( Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('project delete override')  ){
        }else{
 
            if($project->status !== "draft"  ){
                Alert::error('Error','Project is not draft. It cannot be deleted. Please contact administrator if you want to delete the project ');

                if($project->created_by == auth()->user()->id){
                    return redirect()->route('project.index');
                }else{
                    return redirect()->route($this->home_route);
                }

                
            }

            
        }
            


        // $project->status == "draft" && Auth::user()->id == $project->created_by




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


        // ActivityLog::create([
        //     'log_action' => "Project \"".$project->name."\" deleted ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);




        Alert::success('Success','Project deleted successfully');
        // return redirect()->route('project.index');

        // if($project->created_by == auth()->user()->id){
        //     return redirect()->route('project.index');

        // }else{

        //     return redirect()->route('project.index');
        // }

        // return ProjectHelper::returnHomeRouteBasedOnProject($project);
        if($project->created_by == auth()->user()->id){
            return redirect()->route('project.index');
        }else{
            return redirect($this->home_route);
        }



    }


    

    public function submit_project($project_id){

        ProjectHelper::submit_project($project_id);

    }

    public function force_submit_project($project_id){

        ProjectHelper::submit_project($project_id, true); // override on que submission will submit projects that are on que and start the review process

    }


    public function open_review_project($project_id){

        ProjectHelper::open_review_project($project_id);

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

         
                
        // // while status is true for project reviewer, this means that the project reviewer is the active/current reviewer o
        // $reviewer = ProjectReviewer::where('project_id', $project->id)
        //     ->where('review_status', 'pending') 
        //     ->orderBy('order', 'asc')
        //     ->first();


        // // update the first reviewer as the current reviewer
        // $reviewer->status = true;
        // $reviewer->save();

        // ✅ Get the first project document
        $firstProjectDocument = $project->project_documents->first();

        if ($firstProjectDocument) {
            // ✅ Get the first reviewer for this document (lowest order)
            $firstReviewer = $firstProjectDocument->project_reviewers()
                ->orderBy('order', 'asc')
                ->first();

            if ($firstReviewer) {
                $firstReviewer->status = true; // mark as current/active
                $firstReviewer->save();
            }


            $submission_type = "submission";

            // dd($firstReviewer->user->name);
            ProjectHelper::notifyReviewersAndSubscribers($project, $firstReviewer, $submission_type);
            


        }





        // Send notification email to reviewer
        $user = User::find( $firstProjectDocument->user_id);
        if ($user) {
            Notification::send($user, new ProjectReviewNotification($project, $firstProjectDocument));
        }

 
        
        
        $project->status = "submitted"; // back to default
        $project->allow_project_submission = false; // do not allow double submission until it is reviewed
        $project->save();


        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" review restarted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
            'project_id' => $project->id,
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
        $user = User::where('id', $project->created_by)->first();

        $project = Project::where('id', $project->id)->first();
        if ($user) {

            // Notification::send($user, new ReviewerReviewNotification($project, $review));

            ProjectHelper::sendForProjectCreatorReviewerReviewNotification($user,$project,$review);

        }

 
        
        
        $project->status = "approved"; // aprove the project
        $project->allow_project_submission = false; // do not allow double submission until it is reviewed
        $project->save();


        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" approved ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
            'project_id' => $project->id,
        ]);

        Alert::success('Success','Project approved ');
        return redirect()->route('project.index');


    }
 
 


    protected function applyRouteBasedFilters($query)
    {
        $user = Auth::user();
        $userId = $user->id;

        if ($this->route) {
            switch ($this->route) {
                case 'project.index':
                    // Owned projects
                    $query->ownedBy($userId);
                    break;

                case 'project.index.update-pending':
                    // Owned, update pending, not draft
                    $query->ownedBy($userId)
                        ->pendingUpdate($query)
                        ->notDraft($query);
                    break;

                case 'project.index.review-pending':
                    // Owned, review pending, not draft
                    $query->ownedBy($userId)
                        ->inReview($query)
                        ->notDraft($query);
                    break;

                case 'project.index.all':
                    break;

                case 'project.index.all.no-drafts':
                    // All, no draft
                    $query->notDraft($query);
                    break;

                case 'project.index.update-pending.all-linked':
                    // User is reviewer; project is update pending and linked to user
                    $query->pendingUpdate($query)
                        ->notDraft($query)
                        ->whereHas('project_reviewers', function ($q) use ($userId) {
                            $q->where('user_id', $userId)->where('status', true);
                        });
                    break;

                case 'project.index.review-pending.all-linked':
                    // User is reviewer; project is review pending and linked to user
                    $query->inReview($query)
                        ->notDraft($query)
                        ->whereHas('project_reviewers', function ($q) use ($userId) {
                            $q->where('user_id', $userId)
                                ->where('status', true)
                                // ->where('review_status','pending')
                                ;
                        });
                    break;

                case 'project.index.update-pending.all':
                    // All update-pending projects
                    $query->pendingUpdate($query)->notDraft($query);
                    break;

                case 'project.index.review-pending.all':
                    // All review-pending projects
                    $query->inReview($query)->notDraft($query);
                    break;

                case 'project.index.open-review':
                    // User is reviewer; project is review pending and linked to user
                    $query->inReview($query)
                        ->notDraft($query)
                        ->whereHas('project_reviewers', function ($q) use ($userId) {
                            $q->whereNull('user_id')
                                ->where('status', true)
                                // ->where('review_status','pending')
                                ;
                        });
                    break;

                default:
                    // Default to owned
                    $query->ownedBy($userId);
                    break;
            }
        }

        return $query;
    }



    protected function applySorting($query)
    {
        switch ($this->sort_by) {
            case "Name A - Z":
                return $query->orderBy('projects.name', 'ASC');
            case "Name Z - A":
                return $query->orderBy('projects.name', 'DESC');
            case "Description A - Z":
                return $query->orderBy('projects.description', 'ASC');
            case "Description Z - A":
                return $query->orderBy('projects.description', 'DESC');
            case "Federal Agency A - Z":
                return $query->orderBy('projects.federal_agency', 'ASC');
            case "Federal Agency Z - A":
                return $query->orderBy('projects.federal_agency', 'DESC');
            case "Nearest Submission Due Date":
                return $query->withCount([
                    'project_reviewers as pending_submission_count' => fn($q) => $q->where('status', true)->where('review_status', 'rejected'),
                    'project_reviewers as not_fully_approved_count' => fn($q) => $q->where('status', true)->whereNot('review_status', 'approved')
                ])->orderByDesc('pending_submission_count')
                ->orderByDesc('not_fully_approved_count')
                ->orderBy('submitter_due_date', 'ASC');
            case "Farthest Submission Due Date":
                return $query->withCount([
                    'project_reviewers as pending_submission_count' => fn($q) => $q->where('status', true)->where('review_status', 'rejected'),
                    'project_reviewers as not_fully_approved_count' => fn($q) => $q->where('status', true)->whereNot('review_status', 'approved')
                ])->orderByDesc('pending_submission_count')
                ->orderByDesc('not_fully_approved_count')
                ->orderBy('submitter_due_date', 'DESC');
            case "Nearest Reviewer Due Date":
                return $query->withCount([
                    'project_reviewers as pending_review_count' => fn($q) => $q->where('status', true)->where('review_status', 'pending'),
                    'project_reviewers as not_fully_approved_count' => fn($q) => $q->where('status', true)->whereNot('review_status', 'approved')
                ])->orderByDesc('pending_review_count')
                ->orderByDesc('not_fully_approved_count')
                ->orderBy('reviewer_due_date', 'ASC');
            case "Farthest Reviewer Due Date":
                return $query->withCount([
                    'project_reviewers as pending_review_count' => fn($q) => $q->where('status', true)->where('review_status', 'pending'),
                    'project_reviewers as not_fully_approved_count' => fn($q) => $q->where('status', true)->whereNot('review_status', 'approved')
                ])->orderByDesc('pending_review_count')
                ->orderByDesc('not_fully_approved_count')
                ->orderBy('reviewer_due_date', 'DESC');
            case "Latest Added":
                return $query->orderBy('projects.created_at', 'DESC');
            case "Oldest Added":
                return $query->orderBy('projects.created_at', 'ASC');
            case "Latest Updated":
                return $query->orderBy('projects.updated_at', 'DESC');
            case "Oldest Updated":
                return $query->orderBy('projects.updated_at', 'ASC');
            default:
                // Default route-based sorting
                if (request()->routeIs('project.pending_project_update')) {
                    return $query->orderBy('projects.submitter_due_date', 'ASC');
                } elseif (request()->routeIs('project.in_review')) {
                    return $query->withCount([
                        'project_reviewers as pending_review_count' => fn($q) => $q->where('review_status', 'pending')
                    ])->orderByDesc('pending_review_count')
                    ->orderBy('reviewer_due_date', 'ASC');
                } else {
                    return $query->orderBy('projects.updated_at', 'DESC');
                }
        }
    }


    public function getProjectsProperty()
    {
        $query = Project::query();

        $query = $this->applyRouteBasedFilters($query);

        if (!empty($this->search)) {
            $query->withSearch($this->search);
        }

        if (!empty($this->location)) {
            $locations = array_map('trim', explode(',', $this->location));
            $query->withLocationFilter($locations);
        }

        if (!empty($this->type)) {
            $query->where('type', $this->type);
        }

        if (!empty($this->project_status)) {
            // dd( $this->project_status);

            $query->where('status', $this->project_status);
        }

        if (!empty($this->review_status)) {
            $query->withReviewStatus($this->review_status);
        }

        // Add your sorting logic here (moved to another method for clarity if needed)
        $query = $this->applySorting($query);

        $paginated = $query->paginate($this->record_count);
        $this->projects_count = $paginated->total();

        return $paginated;
    }




    public function render()
    {   

        
 
        return view('livewire.admin.project.project-list',[
            'projects' => $this->projects
        ]);
    }
}
