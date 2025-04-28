<?php

namespace App\Livewire\Admin\Reviewer;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ProjectReviewer;
use App\Models\DocumentTypeReviewer;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectReviewerUpdatedNotification;
use App\Notifications\ProjectReviewerUpdatedNotificationDB;

class ReviewerList extends Component
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

    // public $lastOrder;
    public $document_types;

    public $document_type_id;

    public $documentTypesWithoutReviewers;
    public $allDocumentTypesHaveReviewers;
    
    protected $listeners = [
        'reviewerCreated' => '$refresh', 
        'reviewerUpdated' => '$refresh',
        'reviewerDeleted' => '$refresh',
        
    ];
     


    public function mount(){

        // DocumentTypes that don't have any reviewers
        $this->documentTypesWithoutReviewers = DocumentType::whereDoesntHave('reviewers')->pluck('name')->toArray();

        // Check if all document types have at least one reviewer
        $this->allDocumentTypesHaveReviewers = empty($this->documentTypesWithoutReviewers);

        $this->errors = [
            'document_types_missing_reviewers' => !$this->allDocumentTypesHaveReviewers,
        ];

        // Get the last order number
        // $this->lastOrder = Reviewer::max('order') ?? 0;

        $this->document_types = DocumentType::all();
        

        // check the first document type
        $this->document_type_id = DocumentType::first()->id ?? null;
        
        // check the get request if it has one 
        $this->document_type_id = request('document_type_id') ?? $this->document_type_id;

        

        // dd($this->lastOrder);

    }


    /**
     * Computed (live) property for last order
     */
    public function getLastOrderProperty()
    {
        return Reviewer::where('document_type_id', $this->document_type_id)->count();
    }

    public function updateOrder($reviewer_id, $order, $direction, $document_type_id)
    {
        if ($direction == "move_up") {
            // Find the reviewer with the closest order that is less than the current order
            $prev_reviewer = Reviewer::where('document_type_id', $document_type_id)
                ->where('order', '<', $order)
                ->orderBy('order', 'DESC')
                ->first();

            if ($prev_reviewer) {
                // Swap the orders
                $prev_reviewer->order = $order;
                $prev_reviewer->save();

                $reviewer = Reviewer::find($reviewer_id);
                $reviewer->order = $prev_reviewer->order - 1;
                $reviewer->save();
            }
        } elseif ($direction == "move_down") {
            // Find the reviewer with the closest order that is greater than the current order
            $next_reviewer = Reviewer::where('document_type_id', $document_type_id)
                ->where('order', '>', $order)
                ->orderBy('order', 'ASC')
                ->first();

                // dd($next_reviewer);

            if ($next_reviewer) {
                // Swap the orders
                $next_reviewer->order = $order;
                $next_reviewer->save();

                $reviewer = Reviewer::find($reviewer_id);
                $reviewer->order = $next_reviewer->order + 1;
                $reviewer->save();
            }
        }


        $this->resetOrder($document_type_id);


    }


    public function resetOrder($document_type_id)
    {
        $reviewers = Reviewer::where('document_type_id', $document_type_id)
            ->orderBy('order', 'ASC')
            ->get();

        foreach ($reviewers as $index => $reviewer) {
            $reviewer->order = $index + 1;
            $reviewer->save();
        }
    }


    public function updateDocumentTypeReviewerOrder($reviewer_id, $direction)
    {
        // $reviewer = DocumentTypeReviewer::find($document_type_reviewer_id);


        $reviewer = Reviewer::find($reviewer_id);


        if (!$reviewer) return;

        $reviewer_id = $reviewer->reviewer_id;
        $current_order = $reviewer->order;

        $new_order = $direction === 'move_up' 
            ? $current_order - 1 
            : $current_order + 1;

        // Find the reviewer at the new order in the same document type
        $swapReviewer = DocumentTypeReviewer::where('document_type_id', $document_type_id)
            ->where('review_order', $new_order)
            ->first();

        if ($swapReviewer) {
            // Swap their orders
            $swapReviewer->review_order = $current_order;
            $swapReviewer->save();
        }

        $reviewer->review_order = $new_order;
        $reviewer->save();
    }

 
   

    public function apply_to_all(){


        // Get all reviewers from the reviewers table
        $reviewers = Reviewer::orderBy('order')->get();

        // Get all projects where status is not 'approved'
        $projects = Project::where('status', '!=', 'approved')->get();

        foreach ($projects as $project) {
            // Get existing project reviewers for this project
            $existingReviewers = $project->project_reviewers()->pluck('user_id', 'id')->toArray();


            
            // dd($existingReviewers);
            


            // Store reviewer IDs from reviewers table
            $newReviewerIds = $reviewers->pluck('user_id')->toArray();

            // **1. Remove project reviewers that are not in the reviewers table**
            $toRemove = array_diff($existingReviewers, $newReviewerIds);
            if (!empty($toRemove)) {
                ProjectReviewer::where('project_id', $project->id)
                    ->whereIn('user_id', $toRemove)
                    ->delete();
            }

            // **2. Update order for existing reviewers in project reviewers**
            foreach ($reviewers as $reviewer) {
                ProjectReviewer::where('project_id', $project->id)
                    ->where('user_id', $reviewer->user_id)
                    ->update(['order' => $reviewer->order]);
            }

            // **3. Add new reviewers that are not already in project_reviewers**
            foreach ($reviewers as $reviewer) {
                if (!in_array($reviewer->user_id, $existingReviewers)) {
                    ProjectReviewer::create([
                        'project_id' => $project->id,
                        'user_id' => $reviewer->user_id,
                        'order' => $reviewer->order,
                        'status' => false, // Assuming new reviewers are inactive by default
                        'review_status' => 'pending', // Set default review status
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                }
            }


            // **4. Reset all project_reviewers' status to false**
            ProjectReviewer::where('project_id', $project->id)->update(['status' => false]);

            // **5. Find the first project_reviewer that is NOT approved and set it to active**
            $nextReviewer = ProjectReviewer::where('project_id', $project->id)
                ->where('review_status', '!=', 'approved')
                ->orderBy('order', 'asc')
                ->first();

            if ($nextReviewer) {
                $nextReviewer->update(['status' => true]);
            }

 
            // dd($existingReviewers);

            // notify the existing reviewers that are not on the reviewers list 
            foreach($toRemove as $key => $toRemove_user_id){

                $user = User::where('id',$toRemove_user_id)->first();
                $project = Project::where('id',$project->id)->first();

                /**Do not include drafts */
                if($project->status != "draft"){
                    // dd($project);



                    $title = "";
                    $title = $project->title ?? "";
    
                    // dd($user);
                    if ($user) {
                        //this is to notify the user that the reviewer list had been updated 
                        Notification::send($user, new ProjectReviewerUpdatedNotification($project,$user));

                        //this is to add to reviewer notifications
                        Notification::send($user, new ProjectReviewerUpdatedNotificationDB($project,$user));


 
             
                    }

                    // if user is also the current reviewer, send a review request nofication to that user 
                    $current_reviewer = $project->getCurrentReviewer();
                    if($user->id == $current_reviewer->user->id){

                        Notification::send($user, new ProjectReviewNotification($project, $current_reviewer));

                        //send notification to the database
                        Notification::send($user, new ProjectReviewNotificationDB($project, $current_reviewer));
                    }

 
                }

            }    




            //  and the project creator for the update on the project
            foreach($newReviewerIds as $key => $user_id){

                // dd($user_id);

                $user = User::where('id',$user_id)->first();
                $project = Project::where('id',$project->id)->first();

                /**Do not include drafts */
                if($project->status != "draft"){
                    // dd($project);



                    $title = "";
                    $title = $project->title ?? "";
    
                    // dd($user);
                    if ($user) {
                        //this is to notify the user that the reviewer list had been updated 
                        Notification::send($user, new ProjectReviewerUpdatedNotification($project,$user));

                        //this is to add to reviewer notifications
                        Notification::send($user, new ProjectReviewerUpdatedNotificationDB($project,$user));

 
             
                    }

                    // if user is also the current reviewer, send a review request nofication to that user 
                    $current_reviewer = $project->getCurrentReviewer();
                    if($user->id == $current_reviewer->user->id){

                        Notification::send($user, new ProjectReviewNotification($project, $current_reviewer));

                        //send notification to the database
                        Notification::send($user, new ProjectReviewNotificationDB($project, $current_reviewer));
                    }

 
                }
               

            }
            
            

            // update the creator of the project

            $creator = User::where('id',$project->created_by)->first();


            // dd($user);
            if ($creator) {
                //this is to notify the creator of the project that his project had been reviewed
                Notification::send($creator, new ProjectReviewerUpdatedNotification($project,$creator));
     
                //send notification to the database
                Notification::send($creator, new ProjectReviewerUpdatedNotificationDB($project,$creator));
                // ProjectReviewerUpdatedNotificationDB
            }



            $admin = User::where('id',Auth::user()->id)->first();

            if ($admin) {
                //this is to notify the creator of the project that his project had been reviewed
                Notification::send($admin, new ProjectReviewerUpdatedNotification($project,$admin));
     
                //send notification to the database
                Notification::send($admin, new ProjectReviewerUpdatedNotificationDB($project,$admin));
                // ProjectReviewerUpdatedNotificationDB
            }





            // add a review to the user
            //add to the review model
            $review = new Review();
            $review->project_review = "The project reviewers list had been updated";
            $review->admin_review = true;
            $review->project_id = $project->id;
            $review->reviewer_id = Auth::user()->id;
            $review->review_status = "Approved";
            $review->created_by = Auth::user()->id;
            $review->updated_by = Auth::user()->id;
            $review->created_at = now();
            $review->updated_at = now();
            $review->save();


            ActivityLog::create([
                'log_action' => "Project \"".$project->name."\" reviewer list updated ",
                'log_username' => Auth::user()->name,
                'created_by' => Auth::user()->id,
            ]);





        }

        Alert::success('Success','Reviewer applied to all successfully');
        return redirect()->route('reviewer.index');

    }



    public function delete($id){
        $reviewer = Reviewer::find($id);

        $document_type_id = $reviewer->document_type_id;


        $reviewer->delete();

        // $order_start_number = $reviewer->order;

        $this->resetOrder($document_type_id);
 
       
        // Alert::success('Success','Reviewer deleted successfully');
        // return redirect()->route('reviewer.index');

    }



    // public function render()
    // {
    //     $query = DocumentTypeReviewer::with('reviewer.user');


    //     // dd($query);


    //     // Filter by document_type_id or show default reviewers (where null)
    //     if (!empty($this->document_type_id)) {
    //         $query = $query->where(function ($q) {
    //             $q->where('document_type_id', $this->document_type_id)
    //             ->orWhereNull('document_type_id');
    //         });
    //     } else {
    //         $query = $query->whereNull('document_type_id');
    //     }

    //     // Filter by search (on user.name or user.email)
    //     if (!empty($this->search)) {
    //         $query = $query->whereHas('reviewer.user', function ($q) {
    //             $q->where('name', 'like', "%{$this->search}%")
    //             ->orWhere('email', 'like', "%{$this->search}%");
    //         });
    //     }

    //     // Role-based filtering (example kept, optional)
    //     if (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {
    //         $query = $query->whereHas('reviewer', function ($q) {
    //             $q->where('created_by', Auth::id());
    //         });
    //     }

    //     // Sorting logic
    //     if (!empty($this->sort_by)) {
    //         switch ($this->sort_by) {
    //             case "Name A - Z":
    //                 $query = $query->orderBy(User::select('name')
    //                     ->whereColumn('users.id', 'reviewers.user_id'), 'ASC');
    //                 break;

    //             case "Name Z - A":
    //                 $query = $query->orderBy(User::select('name')
    //                     ->whereColumn('users.id', 'reviewers.user_id'), 'DESC');
    //                 break;

    //             case "Order Ascending":
    //                 $query = $query->orderBy('review_order', 'ASC');
    //                 break;

    //             case "Order Descending":
    //                 $query = $query->orderBy('review_order', 'DESC');
    //                 break;

    //             case "Latest Added":
    //                 $query = $query->orderBy('created_at', 'DESC');
    //                 break;

    //             case "Oldest Added":
    //                 $query = $query->orderBy('created_at', 'ASC');
    //                 break;

    //             case "Latest Updated":
    //                 $query = $query->orderBy('updated_at', 'DESC');
    //                 break;

    //             case "Oldest Updated":
    //                 $query = $query->orderBy('updated_at', 'ASC');
    //                 break;

    //             default:
    //                 $query = $query->orderBy('updated_at', 'DESC');
    //         }
    //     } else {
    //         $query = $query->orderBy('review_order', 'ASC');
    //     }

    //     $document_type_reviewers = $query->paginate($this->record_count);

    //     return view('livewire.admin.reviewer.reviewer-list', [
    //         'document_type_reviewers' => $document_type_reviewers,
    //     ]);
    // }



    public function getReviewersProperty(){

        $query = Reviewer::select('reviewers.*');


        if (!empty($this->search)) {
            $search = $this->search;


            // $query = $query->where(function($query) use ($search){
            //     $query =  $query->where('reviewers.name','LIKE','%'.$search.'%');
            // });

            $query = $query->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            });


        }

        // Filter by document_type_id or show default reviewers (where null)
        if (!empty($this->document_type_id)) {
            $query = $query->where(function ($q) {
                $q->where('document_type_id', $this->document_type_id);
            });
        }  

        /*
            // Find the role
            $role = Role::where('name', 'DSI God Admin')->first();

            if ($role) {
                // Get user IDs only if role exists
                $dsiGodAdminUserIds = $role->reviewers()->pluck('id');
            } else {
                // Set empty array if role doesn't exist
                $dsiGodAdminUserIds = [];
            }


            // if(!Auth::user()->hasRole('DSI God Admin')){
            //     $query =  $query->where('reviewers.created_by','=',Auth::user()->id);
            // }

            // Adjust the query
            if (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {
                $query = $query->where('reviewers.created_by', '=', Auth::user()->id);
            }elseif(Auth::user()->hasRole('Admin')){
                $query = $query->whereNotIn('reviewers.created_by', $dsiGodAdminUserIds);
            } else {

            }
        */


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Name A - Z":
                    $query = Reviewer::with('user')
                        ->whereHas('user') // Ensures the reviewer has a related user
                        ->orderBy(User::select('name')->whereColumn('users.id', 'reviewers.user_id'), 'ASC');
                    break;
            
                case "Name Z - A":
                    $query = Reviewer::with('user')
                        ->whereHas('user') 
                        ->orderBy(User::select('name')->whereColumn('users.id', 'reviewers.user_id'), 'DESC');
                    break;

                case "Order Ascending":
                    $query =  $query->orderBy('reviewers.order','ASC');
                    break;

                case "Order Descending":
                    $query =  $query->orderBy('reviewers.order','DESC');
                    break;


                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $query =  $query->orderBy('reviewers.created_at','DESC');
                    break;

                case "Oldest Added":
                    $query =  $query->orderBy('reviewers.created_at','ASC');
                    break;

                case "Latest Updated":
                    $query =  $query->orderBy('reviewers.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $query =  $query->orderBy('reviewers.updated_at','ASC');
                    break;
                default:
                    $query =  $query->orderBy('reviewers.updated_at','DESC');
                    break;

            }


        }else{
            $query =  $query->orderBy('reviewers.order','ASC');

        }





        return $query->paginate($this->record_count);



    }


    public function render()
    {

        // $reviewers = Reviewer::select('reviewers.*');


        // if (!empty($this->search)) {
        //     $search = $this->search;


        //     // $reviewers = $reviewers->where(function($query) use ($search){
        //     //     $query =  $query->where('reviewers.name','LIKE','%'.$search.'%');
        //     // });

        //     $reviewers = $reviewers->where(function ($query) {
        //         $query->whereHas('user', function ($q) {
        //             $q->where('name', 'like', "%{$this->search}%")
        //                 ->orWhere('email', 'like', "%{$this->search}%");
        //         });
        //     });


        // }

        // // Filter by document_type_id or show default reviewers (where null)
        // if (!empty($this->document_type_id)) {
        //     $reviewers = $reviewers->where(function ($q) {
        //         $q->where('document_type_id', $this->document_type_id);
        //     });
        // }  

        // /*
        //     // Find the role
        //     $role = Role::where('name', 'DSI God Admin')->first();

        //     if ($role) {
        //         // Get user IDs only if role exists
        //         $dsiGodAdminUserIds = $role->reviewers()->pluck('id');
        //     } else {
        //         // Set empty array if role doesn't exist
        //         $dsiGodAdminUserIds = [];
        //     }


        //     // if(!Auth::user()->hasRole('DSI God Admin')){
        //     //     $reviewers =  $reviewers->where('reviewers.created_by','=',Auth::user()->id);
        //     // }

        //     // Adjust the query
        //     if (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {
        //         $reviewers = $reviewers->where('reviewers.created_by', '=', Auth::user()->id);
        //     }elseif(Auth::user()->hasRole('Admin')){
        //         $reviewers = $reviewers->whereNotIn('reviewers.created_by', $dsiGodAdminUserIds);
        //     } else {

        //     }
        // */


        // // dd($this->sort_by);
        // if(!empty($this->sort_by) && $this->sort_by != ""){
        //     // dd($this->sort_by);
        //     switch($this->sort_by){

        //         case "Name A - Z":
        //             $reviewers = Reviewer::with('user')
        //                 ->whereHas('user') // Ensures the reviewer has a related user
        //                 ->orderBy(User::select('name')->whereColumn('users.id', 'reviewers.user_id'), 'ASC');
        //             break;
            
        //         case "Name Z - A":
        //             $reviewers = Reviewer::with('user')
        //                 ->whereHas('user') 
        //                 ->orderBy(User::select('name')->whereColumn('users.id', 'reviewers.user_id'), 'DESC');
        //             break;

        //         case "Order Ascending":
        //             $reviewers =  $reviewers->orderBy('reviewers.order','ASC');
        //             break;

        //         case "Order Descending":
        //             $reviewers =  $reviewers->orderBy('reviewers.order','DESC');
        //             break;


        //         /**
        //          * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
        //          * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
        //          */

        //         case "Latest Added":
        //             $reviewers =  $reviewers->orderBy('reviewers.created_at','DESC');
        //             break;

        //         case "Oldest Added":
        //             $reviewers =  $reviewers->orderBy('reviewers.created_at','ASC');
        //             break;

        //         case "Latest Updated":
        //             $reviewers =  $reviewers->orderBy('reviewers.updated_at','DESC');
        //             break;

        //         case "Oldest Updated":
        //             $reviewers =  $reviewers->orderBy('reviewers.updated_at','ASC');
        //             break;
        //         default:
        //             $reviewers =  $reviewers->orderBy('reviewers.updated_at','DESC');
        //             break;

        //     }


        // }else{
        //     $reviewers =  $reviewers->orderBy('reviewers.order','ASC');

        // }





        // $reviewers = $reviewers->paginate($this->record_count);
 

        return view('livewire.admin.reviewer.reviewer-list',[
            'reviewers' => $this->reviewers ,
            'lastOrder' => $this->lastOrder,
        ]);
    }


}
