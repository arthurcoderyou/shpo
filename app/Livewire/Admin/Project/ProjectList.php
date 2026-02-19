<?php

namespace App\Livewire\Admin\Project;

use App\Exports\ProjectsExport;
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
use App\Events\Project\ProjectLogEvent;
use App\Helpers\ProjectDocumentHelpers;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert; 
use Illuminate\Support\Facades\Notification; 
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ReviewerReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;
use App\Helpers\SystemNotificationHelpers\ProjectNotificationHelper;

class ProjectList extends Component
{

    use WithFileUploads;
    use WithPagination;

    protected $listeners = [
        'systemEvent' => '$refresh',

        'projectEvent' => '$refresh',
        // 'projectCreated' => '$refresh',
        // 'projectUpdated' => '$refresh',
        // 'projectDeleted' => '$refresh',
        // 'projectSubmitted' => '$refresh',
        // 'projectQueued' => '$refresh',
        // 'projectDocumentCreated' => '$refresh',
        // 'projectDocumentUpdated' => '$refresh',
        // 'projectDocumentDeleted' => '$refresh',
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
        'Local Government',
        'Federal Government',
        'Private'
    ];


    public $route;
    public $myProjects;

    public $home_route;

    
    /**Count */
    public $count_all;
    public $count_on_que;
    public $count_open_review;
    public $count_changes_requested;
    public $count_pending;
    public $count_pending_rc_number;
     

    public $pending_rc_number = false; 


    public function resetFilters(){

        $this->search = '';
        $this->sort_by = ''; 
   

        $this->project_status = null; 
  
 

        $this->type = null;
  

        $this->pending_rc_number = false; 

    }


 
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

       $this->pending_rc_number = request()->query('pending_rc_number') == '1';


        
        $this->projects_count = 0;
 
        $this->home_route = route($route);

        // dd($this->home_route);

        $this->setCountForReviewStatus();

        // dd($this->review_status);

    }



     public function setCountForReviewStatus( ){

         

        $this->count_all = Project::countBasedOnReviewStatus('all', $this->pending_rc_number) ?? 0;
        $this->count_changes_requested = Project::countBasedOnReviewStatus('changes_requested', $this->pending_rc_number) ?? 0;
        $this->count_pending = Project::countBasedOnReviewStatus('pending', $this->pending_rc_number) ?? 0;
        $this->count_open_review = Project::countBasedOnReviewStatus('open_review', $this->pending_rc_number) ?? 0; 
        $this->count_on_que = Project::where('status','on_que')->count();
        $this->count_pending_rc_number = Project::whereNull('rc_number')
            ->whereNot('status','draft')
            ->where(function ($q) {
                $q->doesntHave('project_documents');
            })
            ->count();



    }


    public function updatedPendingRcNumber(){
        $this->setCountForReviewStatus();
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
 






    public function delete($id){ 
   
        ProjectHelper::delete($id); 

    }


    

    public function submit_project_for_rc_evaluation($project_id){ 
        
        // dd($project_id);
        ProjectHelper::submit_project_for_rc_evaluation($project_id);

    }

    public function force_submit_project($project_id){

        ProjectHelper::submit_project($project_id, true); // override on que submission will submit projects that are on que and start the review process

    }


    public function open_review_project($project_id){

        ProjectHelper::open_review_project($project_id);

    }
    
    public function force_submit_project_for_rc_evaluation($id){
        ProjectHelper::submit_project_for_rc_evaluation($id,true);
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

         $route = ProjectHelper::returnHomeProjectRoute($project);

        return redirect($route); 


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
        $route = ProjectHelper::returnHomeProjectRoute($project);

        return redirect($route); 


    }
 
 

    public function returnStatusConfig($status){
        return ProjectDocumentHelpers::returnStatusConfig($status);
    }

    public function returnFormattedLabel($status){
        return ProjectDocumentHelpers::returnFormattedLabel($status);
    }

    public function updateReviewStatus(){

        

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
            // $query->withReviewStatus($this->review_status);

            $query = $query->applyReviewStatusBasedFilters($this->review_status);

        }



        if($this->pending_rc_number == true){
            // dd($this->pending_rc_number);
            $query->whereNull('rc_number')
                // ->where(function ($q) {
                //     $q->doesntHave('project_documents');
                // })
                ->whereNot('status','draft');

        }

        // Add your sorting logic here (moved to another method for clarity if needed)
        $query = $this->applySorting($query);

        $paginated = $query->paginate($this->record_count);
        $this->projects_count = $paginated->total();

        return $paginated;
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
                // $query->withReviewStatus($this->review_status);

                $query = $query->applyReviewStatusBasedFilters($this->review_status);

            }



            if($this->pending_rc_number == true){
                // dd($this->pending_rc_number);
                $query->whereNull('rc_number')
                    // ->where(function ($q) {
                    //     $q->doesntHave('project_documents');
                    // })
                    ->whereNot('status','draft');

            }

            // Add your sorting logic here (moved to another method for clarity if needed)
            $query = $this->applySorting($query);
 



            $this->selected_records = $query
                ->pluck('id')
                ->toArray();
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }


    public array $export_table_columns = [
        'name' => false,
        'description' => false,
        'agency' => false, 
        'type' => false,  
        'status' => false,   
        'allow_project_submission' => false,  
        'created_by' => false,
        'updated_by' => false,
        'created_at' => false,
        'updated_at' => false, 
        'project_number' => false, 
        'rc_number' => false,
        'street' => false,
        'area' => false,
        'lot_number' => false,
 
        'submitter_response_duration_type' => false,
        'submitter_response_duration' => false,
        'submitter_due_date' => false,
        'reviewer_response_duration' => false,
        'reviewer_response_duration_type' => false,
        'reviewer_due_date' => false, 
 
        'latitude' => false, 'longitude' => false, 'location' => false,

        'last_submitted_at' => false,
        'last_submitted_by' => false,
        'last_reviewed_at' => false,
        'last_reviewed_by' => false,


        'allotted_review_time_hours' => false,


        'staff_engineering_data' => false,
        'staff_initials' => false,
        'lot_size' => false,
        'unit_of_size' => false,
        'site_area_inspection' => false,
        'burials_discovered_onsite' => false,
        'certificate_of_approval' => false,
        'notice_of_violation' => false,


        'installation' => false,
        'sub_area' => false,
        'project_size' => false,
    ];


    public function export()
    {
        // return Excel::download(new CustomersExport, 'customers.xlsx');
        // return (new CustomersExport($this->selected_records))->download('customers.xlsx');

        if(empty($this->sort_by)){
            $this->sort_by = "Latest Updated";
        }


        // ActivityLog::create([
        //     'log_action' => 'Customer Export generated ',
        //     'log_user' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);


        return (new ProjectsExport())->forExportSorting($this->selected_records,$this->sort_by)->download('projects.xlsx');

    }


    //for the import part
    public function import(){

        ini_set('max_execution_time',3600);

        $this->validate([
            'file' => 'mimes:xlsx|required'
        ]);

        Excel::import(new CustomerImport, $this->file);


        ActivityLog::create([
            'log_action' => 'Customer Import generated ',
            'log_user' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Customers imported successfully');
        return redirect()->route('customer.index');

    }




    public function render()
    {   

        
 
        return view('livewire.admin.project.project-list',[
            'projects' => $this->projects
        ]);
    }
}
