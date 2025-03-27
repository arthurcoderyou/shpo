<?php

namespace App\Livewire\Admin\ProjectReviewer;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ProjectReviewer;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectReviewerUpdatedNotification;
use App\Notifications\ProjectReviewerUpdatedNotificationDB;

class ProjectReviewerList extends Component
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

    public $project;


    public function mount($id){

        $this->project = Project::findOrFail($id);

        // Get the last order number
        $this->lastOrder = ProjectReviewer::where('project_id',$id)->max('order') ?? 0;
    }


    public function updateOrder($reviewer_id, $order, $direction){

        if($direction == "move_up"){


            $new_order = $order - 1; // minus because the direction is 1 to 100

            //Updated
            $prev_reviewer = ProjectReviewer::where('project_id',$this->project->id)->where('order', $new_order)->first();
            $prev_reviewer->order = $order;
            $prev_reviewer->save();

            /**update the value  */

            $reviewer = ProjectReviewer::find($reviewer_id);
            $reviewer->order = $new_order;
            $reviewer->save();





        }else if($direction == "move_down"){


            $new_order = $order + 1;  // add because the direction is 1 to 100

            //Updated
            $prev_reviewer = ProjectReviewer::where('project_id',$this->project->id)->where('order', $new_order)->first();
            $prev_reviewer->order = $order;
            $prev_reviewer->save();

            /**update the value  */

            $reviewer = ProjectReviewer::find($reviewer_id);
            $reviewer->order = $new_order;
            $reviewer->save();



        }
    }


    // Method to delete selected records
    public function deleteSelected()
    {
        ProjectReviewer::where('project_id',$this->project->id)->whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records

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
            $this->selected_records = ProjectReviewer::where('project_id',$this->project->id)->pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

 

    public function delete($id){
        $reviewer = ProjectReviewer::find($id);


        $reviewer->delete();

        $order_start_number = $reviewer->order;

        $reviewers = ProjectReviewer::where('project_id',$this->project->id)->orderBy('order','ASC')->get();

        $index = 1;
        foreach($reviewers as $reviewer_user){

 

            $reviewer_user->order =  $index;
            $reviewer_user->save();

            $index++;
        }


        // **5. Find the first project_reviewer that is NOT approved and set it to active**
        $nextReviewer = ProjectReviewer::where('project_id', $this->project->id)
            ->where('review_status', '!=', 'approved')
            ->orderBy('order', 'asc')
            ->first();

        if ($nextReviewer) {
            $nextReviewer->update(['status' => true]);
        }




        ActivityLog::create([
            'log_action' => "Project '".$this->project->name."' reviewer '".$reviewer->user->name."' on list deleted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project reviewer deleted successfully');
        return redirect()->route('project.reviewer.index',['project' => $this->project->id]);

    }



    public function notify_to_all(){

 

        // Get all projects where status is not 'approved'
        $project  = Project::find($this->project->id);
 
            // Get existing project reviewers for this project
            $existingReviewers = $project->project_reviewers()->pluck('user_id', 'id')->toArray();


            
            // dd($existingReviewers);
            
 

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

            // notify the existing reviewers about the reviewer update order list
            foreach($existingReviewers as $key => $user_id){

                $user = User::where('id',$user_id)->first();
                $project = Project::where('id',$project->id)->first();

                /**Do not include drafts */
                if($project->status != "draft"){
                    // dd($project);

 
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



            //notify the admin updating it
            // dd($user);

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
            $review->project_review = "The project reviewers list had been updated for '".$project->name."'";
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



        // update the subscribers 

            //message for the subscribers 
            $message =  "The project reviewers list had been updated for '".$project->name."' by '".Auth::user()->name."'";
    

            if(!empty($project->project_subscribers)){

                $sub_project = Project::where('id',$project->id)->first(); // get the project to be used for notification

                foreach($project->project_subscribers as $subcriber){

                    // subscriber user 
                    $sub_user = User::where('id',$subcriber->user_id)->first();

                    if(!empty($sub_user)){
                        // notify the next reviewer
                        Notification::send($sub_user, new ProjectSubscribersNotification($sub_user, $sub_project,'project_reviewers_updated',$message ));
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


    

        Alert::success('Success','Project reviewer update notified to all successfully');
        return redirect()->route('project.reviewer.index',['project' => $project->id]);

    }


    public function render()
    {

        $reviewers = ProjectReviewer::select('project_reviewers.*')
            ;

        if (!empty($this->search)) {
            $search = $this->search;
        
            $reviewers = $reviewers->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
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
            //     $reviewers =  $reviewers->where('project_reviewers.created_by','=',Auth::user()->id);
            // }

            // Adjust the query
            if (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {
                $reviewers = $reviewers->where('project_reviewers.created_by', '=', Auth::user()->id);
            }elseif(Auth::user()->hasRole('Admin')){
                $reviewers = $reviewers->whereNotIn('project_reviewers.created_by', $dsiGodAdminUserIds);
            } else {

            }
        */


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Name A - Z":
                    $reviewers = ProjectReviewer::with('user')
                        ->whereHas('user') // Ensures the reviewer has a related user
                        ->orderBy(User::select('name')->whereColumn('users.id', 'project_reviewers.user_id'), 'ASC');
                    break;
            
                case "Name Z - A":
                    $reviewers = ProjectReviewer::with('user')
                        ->whereHas('user') 
                        ->orderBy(User::select('name')->whereColumn('users.id', 'project_reviewers.user_id'), 'DESC');
                    break;

                case "Order Ascending":
                    $reviewers =  $reviewers->orderBy('project_reviewers.order','ASC');
                    break;

                case "Order Descending":
                    $reviewers =  $reviewers->orderBy('project_reviewers.order','DESC');
                    break;


                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $reviewers =  $reviewers->orderBy('project_reviewers.created_at','DESC');
                    break;

                case "Oldest Added":
                    $reviewers =  $reviewers->orderBy('project_reviewers.created_at','ASC');
                    break;

                case "Latest Updated":
                    $reviewers =  $reviewers->orderBy('project_reviewers.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $reviewers =  $reviewers->orderBy('project_reviewers.updated_at','ASC');
                    break;
                default:
                    $reviewers =  $reviewers->orderBy('project_reviewers.updated_at','DESC');
                    break;

            }


        }else{
            $reviewers =  $reviewers->orderBy('project_reviewers.order','ASC');

        }





        $reviewers = $reviewers->where('project_reviewers.project_id',$this->project->id)
        ->paginate($this->record_count);

        
        return view('livewire.admin.project-reviewer.project-reviewer-list',[
            'reviewers' => $reviewers
        ]);
    }
}
