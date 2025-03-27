<?php

namespace App\Livewire\Admin\Reviewer;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ProjectReviewer;
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

    public $lastOrder;


    public function mount(){
        // Get the last order number
        $this->lastOrder = Reviewer::max('order') ?? 0;
    }


    public function updateOrder($reviewer_id, $order, $direction){

        if($direction == "move_up"){


            $new_order = $order - 1; // minus because the direction is 1 to 100

            //Updated
            $prev_reviewer = Reviewer::where('order', $new_order)->first();
            $prev_reviewer->order = $order;
            $prev_reviewer->save();

            /**update the value  */

            $reviewer = Reviewer::find($reviewer_id);
            $reviewer->order = $new_order;
            $reviewer->save();





        }else if($direction == "move_down"){


            $new_order = $order + 1;  // add because the direction is 1 to 100

            //Updated
            $prev_reviewer = Reviewer::where('order', $new_order)->first();
            $prev_reviewer->order = $order;
            $prev_reviewer->save();

            /**update the value  */

            $reviewer = Reviewer::find($reviewer_id);
            $reviewer->order = $new_order;
            $reviewer->save();



        }
    }


    // Method to delete selected records
    public function deleteSelected()
    {
        Reviewer::whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records


        ActivityLog::create([
            'log_action' => "Global Project reviewer list deleted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Selected reviewers deleted successfully');
        return redirect()->route('reviewer.index');
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
            $this->selected_records = Reviewer::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
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


        $reviewer->delete();

        $order_start_number = $reviewer->order;

        $reviewers = Reviewer::orderBy('order','ASC')->get();

        $index = 1;
        foreach($reviewers as $reviewer_user){


            

            $reviewer_user->order =  $index;
            $reviewer_user->save();

            $index++;
        }


        ActivityLog::create([
            'log_action' => "Global Project reviewer '".$reviewer->user->name."' on list deleted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Reviewer deleted successfully');
        return redirect()->route('reviewer.index');

    }



    public function render()
    {

        $reviewers = Reviewer::select('reviewers.*');


        if (!empty($this->search)) {
            $search = $this->search;


            // $reviewers = $reviewers->where(function($query) use ($search){
            //     $query =  $query->where('reviewers.name','LIKE','%'.$search.'%');
            // });

            $reviewers = $reviewers->where(function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
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
            //     $reviewers =  $reviewers->where('reviewers.created_by','=',Auth::user()->id);
            // }

            // Adjust the query
            if (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {
                $reviewers = $reviewers->where('reviewers.created_by', '=', Auth::user()->id);
            }elseif(Auth::user()->hasRole('Admin')){
                $reviewers = $reviewers->whereNotIn('reviewers.created_by', $dsiGodAdminUserIds);
            } else {

            }
        */


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Name A - Z":
                    $reviewers = Reviewer::with('user')
                        ->whereHas('user') // Ensures the reviewer has a related user
                        ->orderBy(User::select('name')->whereColumn('users.id', 'reviewers.user_id'), 'ASC');
                    break;
            
                case "Name Z - A":
                    $reviewers = Reviewer::with('user')
                        ->whereHas('user') 
                        ->orderBy(User::select('name')->whereColumn('users.id', 'reviewers.user_id'), 'DESC');
                    break;

                case "Order Ascending":
                    $reviewers =  $reviewers->orderBy('reviewers.order','ASC');
                    break;

                case "Order Descending":
                    $reviewers =  $reviewers->orderBy('reviewers.order','DESC');
                    break;


                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $reviewers =  $reviewers->orderBy('reviewers.created_at','DESC');
                    break;

                case "Oldest Added":
                    $reviewers =  $reviewers->orderBy('reviewers.created_at','ASC');
                    break;

                case "Latest Updated":
                    $reviewers =  $reviewers->orderBy('reviewers.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $reviewers =  $reviewers->orderBy('reviewers.updated_at','ASC');
                    break;
                default:
                    $reviewers =  $reviewers->orderBy('reviewers.updated_at','DESC');
                    break;

            }


        }else{
            $reviewers =  $reviewers->orderBy('reviewers.order','ASC');

        }





        $reviewers = $reviewers->paginate($this->record_count);




        return view('livewire.admin.reviewer.reviewer-list',[
            'reviewers' => $reviewers 
        ]);
    }
}
