<?php

namespace App\Livewire\Admin\ProjectDocument;

use App\Exports\ProjectDocumentExport;
use App\Helpers\ProjectDocumentHelpers;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ProjectDocument;


use App\Models\User;
use App\Models\Review; 
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use App\Models\ProjectTimer; 
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


class ProjectDocumentList extends Component
{

 
    use WithFileUploads;
    use WithPagination;

    protected $listeners = [
        'systemEvent' => '$refresh',  

        'projectEvent' => '$refresh',
        'projectDocumentEvent' => '$refresh',


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

    public $document_status;
    public $document_status_options = [];

    public $review_status = 'all';

    public $document_type_id;
    public $document_type_options = [];


    // Route verifiers
    
    public $routeIsMyProjects;
    public $routeIsReview;
    public $routeIsPendingProject;

    public $project_documents_count;

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

    public $project_search;
    public $project_id;


    public $count_all = 0;
    public $count_changes_requested = 0;
    public $count_pending = 0;

    public $count_open_review = 0;

    public $count_on_que = 0;

    public $single_project_page = false; 

    public function resetFilters($project_id = null){
        $this->search = '';
        $this->sort_by = ''; 

        $this->document_status = null; 
        $this->review_status = null;

        $this->document_type_id = null; 

        if($this->single_project_page == false){
            $this->project_id = null;
            $this->project = null;
            $this->project_search = null; 
        }
           

  
 
    }
 
    public function mount($route = 'project.index', $project_id = null, $project_document_id = null ){


        // dd($route);

        
        // if(request()->routeIs('project.project-document.index')){
        //     dd(request()->routeIs('project.project-document.index'));
        // }   
        

        if($route == "project.project_documents" || $route == "project.show"){

            $this->single_project_page = true;

            // dd("Here");

            // $project_id = request()
            // $this->project_id = request()->query('project_id', ''); // Default to empty string if not set


            $this->project_id = request()->query('project_id', '');

            if(!empty( $this->project_id)){
                    
                $project = Project::find($this->project_id);
                $this->project =  $project;
            }
            
            if(!empty($project_id)){
                // dd($this->project_id);
                $project = Project::find($project_id);
                $this->project =  $project;
                
            }

            $this->project_id = $this->project->id ?? null;



            if(!empty($project_document_id)){
                $project_document = ProjectDocument::find($project_document_id);
                $this->document_type_id = $project_document->document_type_id;

            }



        }else{

            


            $project = Project::find($project_id);
            $this->project =  $project;
            $this->project_id = $project_id;

            $this->project_id = request()->query('project_id', '');

            // dd("Here");

        }



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
        $this->setDocumentStatusArray();
 

        // set the document types options array  
        $this->document_type_options = DocumentType::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->document_status = request()->query('document_status', ''); // Default to empty string if not set

        $this->review_status = request()->query('review_status', ''); // Default to empty string if not set
        
        $this->projects_count = 0;
 


        if($route == "project.project-document.index"){
            $this->home_route = route('project.project-document.index',['project' => $this->project_id]);
        }elseif($route == "project.show"){
            $this->home_route = route('project.show',['project' => $this->project_id]);
        }else{
            $this->home_route = route($route);
        }
         
        // dd($this->home_route);

        $this->setCountForReviewStatus();


        // dd($this->project_id);

    }





    public function setCountForReviewStatus( ){

        $project_id = $this->project_id ?? null;

        $this->count_all = ProjectDocument::countBasedOnReviewStatus('all', $project_id) ?? 0;
        $this->count_changes_requested = ProjectDocument::countBasedOnReviewStatus('changes_requested', $project_id) ?? 0;
        $this->count_pending = ProjectDocument::countBasedOnReviewStatus('pending', $project_id) ?? 0;
        $this->count_open_review = ProjectDocument::countBasedOnReviewStatus('open_review', $project_id) ?? 0; 
        $this->count_on_que = ProjectDocument::where('status','on_que')->count();




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

            case 'project-document.index.changes-requested':
                $this->title = 'My Projects - Changes Requested';
                $this->subtitle = 'Projects you own that needs changes';
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

            case 'project-document.index.review-pending':
                $this->title = 'Review Pending';
                $this->subtitle = 'Project documents that are pending review';
                break;

            case 'project-document.index.open-review':
                $this->title = 'Open Review Documents';
                $this->subtitle = 'Project documents without a confirmed reviewer';
                break;  

            case 'project.project_documents':
                $this->title = 'Project Documents';
                $this->subtitle = 'Listing of Project Documents';
                break;
                
            case 'project.project-document.index':
                $this->title = 'Project Documents';
                $this->subtitle = 'Listing of Project Documents';
                break;
            case 'project.project_documents.open':
                $this->title = 'Open Review Project Documents';
                $this->subtitle = 'Listing of Open Review Project Documents';
                break;  

            default:
                $this->title = 'Project Documents';
                $this->subtitle = 'Listing of Project Documents';
                break;
        }
    }

 
    public function setDocumentStatusArray()
    { 

        $this->document_status_options = [
            "draft" => "Draft",
            "on_que" => "On Que",
            "submitted" => "Submitted",
            "in_review" => "In Review",
            // "requires_update" => "Requires Update",
            "approved" => "Approved",
            "rejected" => "Rejected", 
             
        ];

        // if ( (auth()->user()->can('system access global admin') || auth()->user()->can('project list view all')) || request()->routeIs('project.index')  ) {
        //    $this->document_status_options = [
        //         "draft" => "Draft",
        //         "on_que" => "On Que",
        //     ] + $this->document_status_options; // Add "Draft" and "On Que at the beginning 


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
            

            $user = Auth::user();
            $userId = $user->id;

            // $projectIdsSub = $this->buildProjectBaseQuery();

                // dd($projectIdsSub );


            $docs = ProjectDocument::query()
                // Only docs for the filtered projects
                // ->whereIn('project_id', $projectIdsSub)
                // Avoid N+1
                ->with([
                    'project:id,name,project_number,status,submitter_due_date,reviewer_due_date',
                    'document_type:id,name' // adjust columns to your table
                ]);

    
            // $docs  = $this->applyRouteBasedFilters($docs );



            // (Optional) additional filters just for documents:
            if (!empty($this->document_type_id)) {
                $docs->where('document_type_id', $this->document_type_id);
            }

            // (Optional) additional filters just for documents:
            if (!empty($this->project_id)) {
                $docs->where('project_id', $this->project_id);
            }

            $search = $this->search ?? null; // or request('search');


            $docs = $docs->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    // Search in ProjectDocument fields
                    $q->where('rc_number', 'like', "%{$search}%") ;      // adjust column names
                    // ->orWhere('description', 'like', "%{$search}%");

                // Also search in related Project fields
                })->orWhereHas('project', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('rc_number', 'like', "%{$search}%");
                });
            });


            if(Auth::user()->hasPermissionTo('system access global admin')){

            }else if(Auth::user()->hasPermissionTo('system access admin')){

                if($this->route == "project-document.index.open-review"){
                    // dd($this->route );
                    $docs = $docs->applyRouteBasedFilters($this->route);
                }elseif($this->route == "project-document.index.review-pending"){

                    $docs = $docs->applyRouteBasedFilters($this->route);
                }

                // dd($docs);

            }elseif(Auth::user()->hasPermissionTo('system access reviewer')){
    

                $userId = Auth::user()->id;
                // only documents where THIS user is an active reviewer
                // $docs->whereHas('project_reviewers', function ($q) use ($userId) {
                //     $q->where('user_id', $userId);
                //     // ->where('status', true);
                // });

                if($this->route == "project-document.index.open-review"){
                    // dd($this->route );
                    $docs = $docs->applyRouteBasedFilters($this->route);
                }elseif($this->route == "project-document.index.review-pending"){

                    $docs = $docs->applyRouteBasedFilters($this->route);
                }



            }
            
            
            if(Auth::user()->hasPermissionTo('system access user') && request()->routeIs('project-document.index')){

                if($this->route == "project-document.index.changes-requested"){
                    $docs = $docs->applyRouteBasedFilters($this->route);
                    // dd($this->route);
                }


                $docs = $docs->ownedBy($userId);

            }





            // if (!empty($this->uploaded_from) && !empty($this->uploaded_to)) {
            //     $docs->whereBetween('created_at', [$this->uploaded_from, $this->uploaded_to]);
            // }

            if(!empty($this->sort_by)){
                // dd("Here")
                $docs = $docs->applySortingUsingWhereHas($this->sort_by)  // sorrting with aggregate to other model relationships
                    ->applySorting($this->sort_by); // sorrting directly to the project document model
            }else{

                $cods = $docs->applySorting($this->sort_by);
                
            }


            // (Optional) additional filters just for documents:
            if (!empty($this->document_status)) {
                $docs->where('status', $this->document_status);
            }
    
            if (!empty($this->review_status) && $this->review_status !== "all") {

                // dd($this->review_status);
                $docs = $docs->ApplyReviewStatusBasedFilters($this->review_status);
            }
 



            
         
            $this->selected_records = $docs->pluck('id')->toArray();

        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){
        $project_document = ProjectDocument::find($id);

        if( Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('project delete override')  ){
        }else{
 
            if($project_document->status !== "draft"  ){
                Alert::error('Error','Project document is not draft. It cannot be deleted. Please contact administrator if you want to delete the project document');

                return redirect()->route('project-document.index');

                
            }

            
        }
            


        // $project_document->status == "draft" && Auth::user()->id == $project_document->created_by




        // delete project_document connected records 
        
            //delete project_document reviewers 
            if(!empty($project_document->project_reviewers)){
                foreach($project_document->project_reviewers as $reviewer){
                    $reviewer->delete();
                } 
            }

            //delete project reviews 
            if(!empty($project_document->project_reviews)){
                foreach($project_document->project_reviews as $review){
                    $review->delete();
                } 
            }


           
           


            //delete project document attachments
             
            // delete project attachments for each project document 
            if(!empty($project_document->project_attachments)){
                foreach($project_document->project_attachments as $attachment){
                    // Construct the full file path
                    $dir = "{$project_document->path}/{$project_document->stored_name}";

                    // dd($dir);
 

                     // check if the file is completely uploaded on the ftp server
                    if (Storage::disk('ftp')->exists($dir)) { 

                        // delete the file on the ftp server
                        Storage::disk('ftp')->delete($dir); 

                        // delete the file on the public server
                        // Storage::disk('public')->delete($dir); 

 

                    }


                    // Delete the record from the database
                    $attachment->delete();
                }


            }
 
        
        // ./ delete project connected records 



        $project_document->delete();


        // ActivityLog::create([
        //     'log_action' => "Project \"".$project->name."\" deleted ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);




        
        // return redirect()->route('project.index');

        // if($project->created_by == auth()->user()->id){
        //     return redirect()->route('project.index');

        // }else{

        //     return redirect()->route('project.index');
        // }
        Alert::success('Success','Project document deleted successfully');  

        

        // project -> project documents page
        if(request()->routeIs('project.project-document.index')){
            return redirect()->route('project.project-document.index',[ 
                'project_id' => $this->project_id,
            ]); 


        // project documents page
        }else{

            return redirect()->route('project-document.index',[
                'document_status' => $this->document_status,
                'document_type_id' => $this->document_type_id,
                'project_id' => $this->project_id,
            ]); 
        }


        




    }


    
    public function search_project($project_id){

        // return redirect()->route('project.project_documents',['project' =>  $project_id]);
        $this->project_id = $project_id;
        $this->project = Project::findOrFail($project_id);
        $this->project_search = null;

    }



    public function submit_project_document($project_document_id){

        ProjectDocumentHelpers::submit_project_document($project_document_id);

    }

    public function force_submit_project_document($project_document_id){

        ProjectDocumentHelpers::submit_project_document($project_document_id, true); // override on que submission will submit project documents that are on que and start the review process

    }


    public function open_review_project($project_id){

        ProjectHelper::open_review_project($project_id);

    }

    public function open_review_project_document($project_document_id){
        ProjectDocumentHelpers::open_review_project_document($project_document_id);

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


    
    public function returnFormattedDatetime($datetime){

        return ProjectDocumentHelpers::returnFormattedDatetime($datetime);

    }

    public function returnFormattedUser($userId){

        return ProjectDocumentHelpers::returnFormattedUser($userId);

    }
 

    public function returnStatusConfig($status){
        return ProjectDocumentHelpers::returnStatusConfig($status);
    }

    public function returnReviewerName(ProjectDocument $project_document){
        return ProjectDocumentHelpers::returnReviewerName($project_document);
    }

    public function returnSlotData(ProjectDocument $project_document, $type){
        return ProjectDocumentHelpers::returnSlotData($project_document, $type); 
    }

    public function returnReviewStatus($project_document){
        return ProjectDocumentHelpers::returnReviewStatus($project_document); 
    }

    public function returnDueDate(ProjectDocument $project_document, $return_type){
        return ProjectDocumentHelpers::returnDueDate($project_document,$return_type); 

    }

    public function getUserRoles($user_id)
    {

        // dd($user_id);
        // Find the user by ID
        $user = User::find($user_id);

        // Return an empty array if not found
        if (!$user) {
            return [];
        }

        // Return the roles as an array (e.g. ['Admin', 'Reviewer'])
        return $user->getRoleNames()->toArray();
    }


    public function returnReviewFlagsStatus(ProjectDocument $project_document){
         return ProjectDocumentHelpers::returnReviewFlagsStatus($project_document); 
    }


    // protected function applyRouteBasedFilters($query)
    // {

    //     // dd($this->route);

    //     $user = Auth::user();
    //     $userId = $user->id;

    //     if ($this->route) {
    //         switch ($this->route) {
    //             // case 'project.index':
    //             //     // Owned projects
    //             //     $query->ownedBy($userId);
    //             //     break;

    //             // case 'project.index.update-pending':
    //             //     // Owned, update pending, not draft
    //             //     $query->ownedBy($userId)
    //             //         ->pendingUpdate($query)
    //             //         ->notDraft($query);
    //             //     break;

    //             // case 'project.index.review-pending':
    //             //     // Owned, review pending, not draft
    //             //     $query->ownedBy($userId)
    //             //         ->inReview($query)
    //             //         ->notDraft($query);
    //             //     break;

    //             // case 'project.index.all':
    //             //     break;

    //             // case 'project.index.all.no-drafts':
    //             //     // All, no draft
    //             //     $query->notDraft($query);
    //             //     break;

    //             // case 'project.index.update-pending.all-linked':
    //             //     // User is reviewer; project is update pending and linked to user
    //             //     $query->pendingUpdate($query)
    //             //         ->notDraft($query)
    //             //         ->whereHas('project_reviewers', function ($q) use ($userId) {
    //             //             $q->where('user_id', $userId)->where('status', true);
    //             //         });
    //             //     break;

    //             // case 'project.index.review-pending.all-linked':
    //             //     // User is reviewer; project is review pending and linked to user
    //             //     $query->inReview($query)
    //             //         ->notDraft($query)
    //             //         ->whereHas('project_reviewers', function ($q) use ($userId) {
    //             //             $q->where('user_id', $userId)
    //             //                 ->where('status', true)
    //             //                 // ->where('review_status','pending')
    //             //                 ;
    //             //         });
    //             //     break;

    //             // case 'project.index.update-pending.all':
    //             //     // All update-pending projects
    //             //     $query->pendingUpdate($query)->notDraft($query);
    //             //     break;

    //             // case 'project.index.review-pending.all':
    //             //     // All review-pending projects
    //             //     $query->inReview($query)->notDraft($query);
    //             //     break;

    //             case 'project-document.index.open-review':
    //                 // User is reviewer; project is review pending and linked to user
    //                 $query
    //                 // ->inReview($query)
    //                     ->notDraft($query)
    //                     ->whereHas('project_reviewers', function ($q) use ($userId) {
    //                         $q->whereNull('user_id')
    //                             // ->where('status', true)
    //                             ->where('status', true)
    //                             ->where('review_status', 'pending')
    //                              ->where('slot_type', 'open')
                                
    //                             // ->where('review_status','pending')
    //                             ;
    //                     });
    //                 break;
    //             // case 'project.project_documents.open':
    //             //     // User is reviewer; project is review pending and linked to user
    //             //     $query->inReview($query)
    //             //         ->notDraft($query)
    //             //         ->whereHas('project_reviewers', function ($q) use ($userId) {
    //             //             $q->whereNull('user_id')
    //             //                 ->where('status', true)
    //             //                 ->where('review_status', 'pending')
    //             //                  ->where('slot_type', 'open')
    //             //                 // ->where('review_status','pending')
    //             //                 ;
    //             //         });
    //             //     break;


    //             default:
    //                 // Default to owned
    //                 $query->ownedBy($userId);
    //                 break;
    //         }
    //     }

    //     return $query;
    // }


 
    // public function getProjectsProperty()
    // {
    //     $query = Project::query();
       
        

    //     if (!empty($this->search)) {
    //         $query->withSearch($this->search);
    //     }

    //     if (!empty($this->location)) {
    //         $locations = array_map('trim', explode(',', $this->location));
    //         $query->withLocationFilter($locations);
    //     }

    //     if (!empty($this->type)) {
    //         $query->where('type', $this->type);
    //     }

    //     if (!empty($this->document_status)) {
    //         // dd( $this->document_status);

    //         $query->where('status', $this->document_status);
    //     }

    //     if (!empty($this->review_status)) {
    //         $query->withReviewStatus($this->review_status);
    //     }

    //     // Add your sorting logic here (moved to another method for clarity if needed)
    //     $query = $this->applySorting($query);

    //     $paginated = $query->paginate($this->record_count);
    //     $this->projects_count = $paginated->total();

    //     return $paginated;
    // }



    // protected function buildProjectBaseQuery()
    // {
    //     $query = Project::query();

    //     if(Auth::user()->hasPermissionTo('system access global admin')){
    //         // dd($query);
    //     }elseif(Auth::user()->hasPermissionTo('system access admin')){
    //          $query = $this->applyRouteBasedFilters($query);


    //     }elseif(Auth::user()->hasPermissionTo('system access reviewer')){
    //         $query = $this->applyRouteBasedFilters($query);


    //     }elseif(Auth::user()->hasPermissionTo('system access user')){
    //         $query = $this->applyRouteBasedFilters($query);
    //     }

    //     if (!empty($this->project_id)) {
    //         $query->where('projects.id', $this->project_id);
    //         // dd( $this->project_id);
    //     }

    //     if (!empty($this->search)) {
    //         $query->withSearch($this->search);
    //     }

    //     if (!empty($this->location)) {
    //         $locations = array_map('trim', explode(',', $this->location));
    //         $query->withLocationFilter($locations);
    //     }   




    //     if (!empty($this->type)) {
    //         $query->where('type', $this->type);
    //     }

    //     if (!empty($this->document_status)) {
    //         $query->where('status', $this->document_status);
    //     }

    //     if (!empty($this->review_status)) {
    //         $query->withReviewStatus($this->review_status);
    //     }

    //     // Keep your project sorting if it matters for documents (optional)
    //     $query = $this->applySorting($query);

    //     return $query->select('projects.id'); // keep it lean for subquery
    // }


    public function getProjectDocumentsProperty()
    {

        $user = Auth::user();
        $userId = $user->id;

        // $projectIdsSub = $this->buildProjectBaseQuery();

            // dd($projectIdsSub );


        $docs = ProjectDocument::query()
            // Only docs for the filtered projects
            // ->whereIn('project_id', $projectIdsSub)
            // Avoid N+1
            ->with([
                'project:id,name,project_number,status,submitter_due_date,reviewer_due_date',
                'document_type:id,name' // adjust columns to your table
            ]);

 
        // $docs  = $this->applyRouteBasedFilters($docs );



        // (Optional) additional filters just for documents:
        if (!empty($this->document_type_id)) {
            $docs->where('document_type_id', $this->document_type_id);
        }

        // (Optional) additional filters just for documents:
        if (!empty($this->project_id)) {
            $docs->where('project_id', $this->project_id);
        }

        $search = $this->search ?? null; // or request('search');


        $docs = $docs->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                // Search in ProjectDocument fields
                $q->where('rc_number', 'like', "%{$search}%") ;      // adjust column names
                // ->orWhere('description', 'like', "%{$search}%");

            // Also search in related Project fields
            })->orWhereHas('project', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
                ->orWhere('rc_number', 'like', "%{$search}%");
            });
        });


        if(Auth::user()->hasPermissionTo('system access global admin')){

        }else if(Auth::user()->hasPermissionTo('system access admin')){

            if($this->route == "project-document.index.open-review"){
                // dd($this->route );
                $docs = $docs->applyRouteBasedFilters($this->route);
            }elseif($this->route == "project-document.index.review-pending"){

                $docs = $docs->applyRouteBasedFilters($this->route);
            }

            // dd($docs);

        }elseif(Auth::user()->hasPermissionTo('system access reviewer')){
 

            $userId = Auth::user()->id;
            // only documents where THIS user is an active reviewer
            // $docs->whereHas('project_reviewers', function ($q) use ($userId) {
            //     $q->where('user_id', $userId);
            //     // ->where('status', true);
            // });

            if($this->route == "project-document.index.open-review"){
                // dd($this->route );
                $docs = $docs->applyRouteBasedFilters($this->route);
            }elseif($this->route == "project-document.index.review-pending"){

                $docs = $docs->applyRouteBasedFilters($this->route);
            }



        }
        
        
        if(Auth::user()->hasPermissionTo('system access user') && request()->routeIs('project-document.index')){

            if($this->route == "project-document.index.changes-requested"){
                $docs = $docs->applyRouteBasedFilters($this->route);
                // dd($this->route);
            }


            $docs = $docs->ownedBy($userId);

        }





        // if (!empty($this->uploaded_from) && !empty($this->uploaded_to)) {
        //     $docs->whereBetween('created_at', [$this->uploaded_from, $this->uploaded_to]);
        // }

        if(!empty($this->sort_by)){
            // dd("Here")
            $docs = $docs->applySortingUsingWhereHas($this->sort_by)  // sorrting with aggregate to other model relationships
                ->applySorting($this->sort_by); // sorrting directly to the project document model
        }else{

            $cods = $docs->applySorting($this->sort_by);
            
        }


        // (Optional) additional filters just for documents:
        if (!empty($this->document_status)) {
            $docs->where('status', $this->document_status);
        }
 
        if (!empty($this->review_status) && $this->review_status !== "all") {

            // dd($this->review_status);
            $docs = $docs->ApplyReviewStatusBasedFilters($this->review_status);
        }


        // // (Optional) sorting for documents:
        // $docs = match ($this->sort_by ?? null) {
        //     'Document Type A - Z'   => $docs->leftJoin('document_types as dt','dt.id','=','project_documents.document_type_id')
        //                                     ->orderBy('dt.name','ASC')->select('project_documents.*'),
        //     'Document Type Z - A'   => $docs->leftJoin('document_types as dt','dt.id','=','project_documents.document_type_id')
        //                                     ->orderBy('dt.name','DESC')->select('project_documents.*'),
        //     'Project A - Z'         => $docs->leftJoin('projects as p','p.id','=','project_documents.project_id')
        //                                     ->orderBy('p.name','ASC')->select('project_documents.*'),
        //     'Latest Uploaded'       => $docs->orderBy('project_documents.created_at','DESC'),
        //     'Oldest Uploaded'       => $docs->orderBy('project_documents.created_at','ASC'),
        //     default                 => $docs->orderBy('project_documents.created_at','DESC'),
        // };

        // Paginate documents
        $paginated = $docs->paginate($this->record_count);
        $this->project_documents_count = $paginated->total();

        return $paginated;
    }




    public function getProjectProperty(){


        return Project::where('id',$this->project_id)->first();

    }



    public array $export_table_columns = [
        'project_id' => false,
        'document_type_id' => false,
        'created_by' => false,
        'updated_by' => false,
        'status' => false, 
        'last_submitted_at' => false,
        'last_submitted_by' => false,

        'allow_project_submission' => false,

        'rc_number' => false,

        'submitter_response_duration_type' => false,
        'submitter_response_duration' => false,
        'submitter_due_date' => false,

        'reviewer_response_duration' => false,
        'reviewer_response_duration_type' => false,
        'reviewer_due_date' => false,


        'type' => false,


        'permit_number' => false,
        'application_type' => false, 


        'applicant' => false,
        'document_from' => false,
        'company' => false,
        'comments' => false,
        'findings' => false,

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


        return (new ProjectDocumentExport())->forExportSorting($this->selected_records,$this->sort_by)->download('project_documents.xlsx');

    }


    public function render()
    {   
        // dd($this->projectDocuments);



        
        $results = Project::select('projects.*');
        if (!empty($this->project_search) && strlen($this->project_search) > 0) {
            $search = $this->project_search;

            // $results = $results->where(function ($query) use ($search) {
            //     $query->where('projects.name', 'LIKE', '%' . $search . '%')
            //     ->where('projects.name', 'LIKE', '%' . $search . '%')
            //         ;
            // });


            $results = $results->where(function($query) use ($search) {
                $query->where('projects.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.federal_agency', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.description', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.rc_number', 'LIKE', '%' . $search . '%')
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


        if(Auth::user()->hasPermissionTo('system access user')){

            $results = $results->where('created_by',Auth::user()->id);

        }

        $results =  $results->orderBy('created_at','DESC')
            ->limit(10)->get();



        
        return view('livewire.admin.project-document.project-document-list',[
            'project_documents' => $this->projectDocuments,
            'results' => $results,
            'project' => $this->project
        ]); 
    }
}
