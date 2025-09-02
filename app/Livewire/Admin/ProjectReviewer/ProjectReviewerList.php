<?php

namespace App\Livewire\Admin\ProjectReviewer;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectReviewer;
use Illuminate\Validation\Rule;
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
    public $record_count = 200;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;

    public $file;
 

    public $project;

    public $document_type_id;

    public $project_documents; 

    public $project_document_id;

    public $reviewer_type = "";




    public $user_id;
    public $order;
    public $status = 0;
    public $review_status = "pending";
    
    
    public $users;


    protected $listeners = [
        'projectReviewerCreated' => '$refresh', 
        'projectReviewerUpdated' => '$refresh',
        'projectReviewerDeleted' => '$refresh',
        
    ];


    public $reviewers = [];



    public function mount($id){

        


        $this->project = Project::findOrFail($id);

        // Get the last order number
        // $this->lastOrder = ProjectReviewer::where('project_id',$id)->max('order') ?? 0;


        // dd($this->project->project_documents);
        
        // document type adjustment
        $this->project_documents = $this->project->project_documents;
            

        // dd($this->document_types);

        // check the first document type
            $this->project_document_id = $this->project->project_documents->first()->id ?? null;
            

            
            // check the get request if it has one 
            $this->project_document_id = request('project_document_id') ?? $this->project_document_id;
        // ./  document type adjustment
        
        // reviewer_type adjustment
            $this->reviewer_type = "document";
            
            // check the get request if it has reviewer_type 
            $this->reviewer_type = request('reviewer_type') ?? $this->reviewer_type;


            if($this->reviewer_type != "document"){
                $this->project_document_id = null;
            }
            
        // ./ reviewer_type adjustment




        // dd($this->reviewer_type);
        $this->reviewers = $this->getReviewersProperty();



        // Refresh users list based on the same rules
        $this->users = $this->eligibleUsersQuery() 
            ->orderBy('name', 'asc')
            ->pluck('id', 'name')->toArray();


    }


     


    public function updatedReviewerType(){
        
        if ($this->reviewer_type !== 'document') {
            $this->project_document_id = null;
        }

        if ($this->reviewer_type == 'document') { 
            $this->project_document_id = $this->project->project_documents->first()->id ?? null;
        }


        // Refresh users list based on the same rules
        $this->users = $this->eligibleUsersQuery() 
            ->orderBy('name', 'asc')
            ->pluck('id', 'name')->toArray();

      
        $this->reviewers = $this->getReviewersProperty();

    }   


    /**
     * Build a query for eligible users based on reviewer_type.
     * Rules:
     *  - initial/final: must have BOTH 'system access admin' AND 'system access reviewer'
     *  - document: must have 'system access reviewer'
     *  - other/empty: returns no users (guardrail)
     */
    protected function eligibleUsersQuery()
    {
        $q = User::query();

        switch ($this->reviewer_type) {
            case 'initial':
            case 'final':
                // Chain permission() to enforce AND
                $q->permission('system access admin')
                ->permission('system access reviewer');
                break;

            case 'document':
                $q->permission('system access reviewer')
                    ->whereDoesntHave('permissions', function ($sub) {
                        $sub->where('name', 'system access admin');
                    })
                    ->whereDoesntHave('roles.permissions', function ($sub) {
                        $sub->where('name', 'system access admin');
                    });
                break;

            default:
                // Unknown type: return an empty set
                $q->whereRaw('1=0');
                break;
        }

        return $q;
    }


    public function updatedProjectDocumentId(){
       $this->reviewers = $this->getReviewersProperty();
    }


     
     /**
     * Computed (live) property for last order
     */
    public function getLastOrderProperty()
    {
        // return Reviewer::where('project_document_id', $this->project_document_id)->count();
        return collect($this->reviewers)
            ->where('reviewer_type', $this->reviewer_type)
            ->when($this->reviewer_type === 'document', function ($collection) {
                return $collection->where('project_document_id', $this->project_document_id);
            }, function ($collection) {
                return $collection->whereNull('project_document_id');
            })
            ->max('order');
    }

   

    public function updateOrder($index, $order, $direction, $project_document_id, $reviewer_type)
    {
        if (!isset($this->reviewers[$index])) {
            return;
        }

        // Filter reviewers by type and (optional) project_document_id
        $filtered = collect($this->reviewers)
            ->where('reviewer_type', $reviewer_type)
            ->when($reviewer_type === 'document', function ($collection) use ($project_document_id) {
                return $collection->where('project_document_id', $project_document_id);
            }, function ($collection) {
                return $collection->whereNull('project_document_id');
            })
            ->sortBy('order')
            ->values(); // Re-index the array

        // Find the position of the current reviewer in the filtered list
        $currentReviewer = $this->reviewers[$index];
        $position = $filtered->search(function ($item) use ($currentReviewer) {
            return $item['user_id'] == $currentReviewer['user_id']
                && $item['reviewer_type'] == $currentReviewer['reviewer_type']
                && ($item['project_document_id'] ?? null) == ($currentReviewer['project_document_id'] ?? null);
        });

        if ($position === false) {
            return;
        }

        // Move up
        if ($direction === 'move_up' && $position > 0) {
            $temp = $filtered[$position - 1];
            $filtered[$position - 1] = $filtered[$position];
            $filtered[$position] = $temp;
        }

        // Move down
        if ($direction === 'move_down' && $position < $filtered->count() - 1) {
            $temp = $filtered[$position + 1];
            $filtered[$position + 1] = $filtered[$position];
            $filtered[$position] = $temp;
        }

        // Reassign order based on new positions
        foreach ($filtered->values() as $i => $reviewer) {
            $reviewer['order'] = $i + 1;

            // Find matching reviewer in main array and update
            foreach ($this->reviewers as &$mainReviewer) {
                if (
                    $mainReviewer['user_id'] == $reviewer['user_id'] &&
                    $mainReviewer['reviewer_type'] == $reviewer['reviewer_type'] &&
                    ($mainReviewer['project_document_id'] ?? null) == ($reviewer['project_document_id'] ?? null)
                ) {
                    $mainReviewer['order'] = $reviewer['order'];
                    break;
                }
            }
        }



        // Optional: sort the reviewers array by `order` again
        $this->reviewers = collect($this->reviewers)->sortBy('order')->values()->toArray();


    }

     

    public function resetOrder()
    {
        $project_document_id = $this->project_document_id;
        $reviewer_type = $this->reviewer_type;

        $filtered = collect($this->reviewers)
            ->where('reviewer_type', $reviewer_type)
            ->when($reviewer_type === 'document', function ($collection) use ($project_document_id) {
                return $collection->where('project_document_id', $project_document_id);
            }, function ($collection) {
                return $collection->whereNull('project_document_id');
            })
            ->sortBy('order')
            ->values(); // Reset index

        foreach ($filtered as $i => $reviewer) {
            $reviewer['order'] = $i + 1;

            foreach ($this->reviewers as &$mainReviewer) {
                if (
                    $mainReviewer['user_id'] == $reviewer['user_id'] &&
                    $mainReviewer['reviewer_type'] == $reviewer['reviewer_type'] &&
                    ($mainReviewer['project_document_id'] ?? null) == ($reviewer['project_document_id'] ?? null)
                ) {
                    $mainReviewer['order'] = $reviewer['order'];
                    break;
                }
            }
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

 
   
    public function delete($index)
    {
        // Remove the reviewer from the array using the index
        unset($this->reviewers[$index]);

        // Reindex the array to avoid gaps in keys
        $this->reviewers = array_values($this->reviewers);

        // // Optional: sort the reviewers array by `order` again
        // $this->reviewers = collect($this->reviewers)->sortBy('order')->values()->toArray();

        $this->resetOrder();

        // ✅ Ensure there's at least one reviewer with status = true
        $this->enforceSingleActiveReviewer();


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

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'user_id' => [
                'required',
                Rule::unique('project_reviewers', 'user_id')
                    ->where(function ($query) {
                    // Reviewer type is initial or final
                    if (in_array($this->reviewer_type, ['initial', 'final'])) {
                        return $query->where('reviewer_type', $this->reviewer_type);
                    }

                    // Reviewer type is document — must match both type and project_document_id
                    if ($this->reviewer_type === 'document') {
                        return $query
                            ->where('reviewer_type', 'document')
                            ->where('project_document_id', $this->project_document_id);
                    }

                    // fallback to avoid error
                    return $query->whereNull('id'); // guarantees no match
                }),

            ],
             
            'reviewer_type' => [
                'required',
            ],

            'project_document_id' => [
                function ($attribute, $value, $fail) {
                    if (!empty($this->reviewer_type) &&  $this->reviewer_type == "document") {
                        if (empty($value)) {
                            $fail('The project document field is required');
                        }
                    }
                },
            ],


            'order' => [
                'required',
            ],
            // 'status' => [
            //     'required',
            // ],
        ], [
            'user_id.required' => 'User is required',
            'user_id.unique' => 'User is already added for this document type',
            'project_document_id.required' => 'Project document is required',
        ]);






    }

 

    public function validateReviewer()
    {
        $this->validate([
            'user_id' => ['required'],
            'reviewer_type' => ['required'],
            'project_document_id' => [
                function ($attribute, $value, $fail) {
                    if (!empty($this->reviewer_type) &&  $this->reviewer_type === "document") {
                        if (empty($value)) {
                            $fail('The project document field is required.');
                        }
                    }
                },
            ],
            'order' => ['required'],
        ], [
            'user_id.required' => 'User is required',
            'project_document_id.required' => 'Document type is required',
        ]);

        // Manual uniqueness check within the array
        foreach ($this->reviewers as $reviewer) {
            if (
                $reviewer['user_id'] == $this->user_id &&
                $reviewer['reviewer_type'] == $this->reviewer_type &&
                (
                    $this->reviewer_type !== 'document' ||
                    ($reviewer['project_document_id'] ?? null) == $this->project_document_id
                )
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'user_id' => 'User is already added for this reviewer type' .
                                ($this->reviewer_type === 'document' ? ' and document type.' : '.'),
                ]);
            }
        }
    }


    /**
     * Handle save
     */
    // public function save()
    public function add()
    {
        
        // dd($this->status);

        $this->validateReviewer();

        // the the status of the new reviewer is true, turn all reviewers into false
        if((bool) $this->status){
            $this->reviewers = collect($this->reviewers)->map(function ($reviewer) use (&$hasTrue) {
                if ($reviewer['status'] ) { 
                    $reviewer['status'] = false;
                }  
                return $reviewer;
            })->toArray();
        }



        $newReviewer = [
            'order' => 1, // Temporary default
            'status' => (bool) $this->status,
            'user_id' => $this->user_id,
            'project_id' => $this->project->id,
            'project_document_id' => $this->project_document_id,
            'reviewer_type' => $this->reviewer_type, 
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'updated_at' => now(),
            'review_status' => $this->review_status,
            'user' => User::find($this->user_id)?->only(['id', 'name', 'email']),
        ];


        



        // Initialize array if not already
        if (!is_array($this->reviewers)) {
            $this->reviewers = [];
        }

        // Filter reviewers of the same type for order adjustment
        $filteredReviewers = collect($this->reviewers)->filter(function ($reviewer) {
            return $reviewer['reviewer_type'] === $this->reviewer_type &&
                ($this->reviewer_type === 'document'
                    ? $reviewer['project_document_id'] == $this->project_document_id
                    : is_null($reviewer['project_document_id']));
        });

        if ($this->order === 'top') {
            // Increment order for matching reviewers
            $this->reviewers = collect($this->reviewers)->map(function ($rev) {
                if (
                    $rev['reviewer_type'] === $this->reviewer_type &&
                    ($this->reviewer_type === 'document'
                        ? $rev['project_document_id'] == $this->project_document_id
                        : is_null($rev['project_document_id']))
                ) {
                    $rev['order'] += 1;
                }
                return $rev;
            })->toArray();

            $newReviewer['order'] = 1;
            array_unshift($this->reviewers, $newReviewer); // Add to start of array
        } elseif ($this->order === 'end') {
            $lastOrder = $filteredReviewers->max('order') ?? 0;
            $newReviewer['order'] = $lastOrder + 1;

            $this->reviewers[] = $newReviewer; // Add to end of array
        }

        // Optional: sort the reviewers array by `order` again
        $this->reviewers = collect($this->reviewers)->sortBy('order')->values()->toArray();


        // dd($this->reviewers);


        // ✅ Ensure there's at least one reviewer with status = true
        $this->enforceSingleActiveReviewer();
         
 
     
        // Alert::success('Success','Reviewer added successfully');
        // return redirect()->route('reviewer.index',[
        //     'document_type_id' => $this->document_type_id,
        //     'reviewer_type' => $this->reviewer_type
        // ]);
    }







    public function save()
    {
        
        // dd($this->reviewers);


        $project_document_id = $this->project_document_id;
        $reviewer_type = $this->reviewer_type;


        //delete all existing reviewers 
        ProjectReviewer::where('project_document_id', $project_document_id)
            ->where('reviewer_type', $reviewer_type)->delete();


        // Filter and sort the reviewers array
        $filtered = collect($this->reviewers)
            ->where('reviewer_type', $reviewer_type)
            ->when($reviewer_type === 'document', function ($collection) use ($project_document_id) {
                return $collection->where('project_document_id', $project_document_id);
            }, function ($collection) {
                return $collection->filter(function ($item) {
                    return empty($item['project_document_id']);
                });
            })
            ->sortBy('order')
            ->values(); // Reset index

        foreach ($filtered as $i => $reviewer) {
            $newOrder = $i + 1;

            // Try to find an existing reviewer
            $query = ProjectReviewer::where('user_id', $reviewer['user_id'])
                
                ->where('reviewer_type', $reviewer_type);

            if ($reviewer_type === 'document') {
                $query->where('project_document_id', $project_document_id);
            } else {
                $query->whereNull('project_document_id');
            }

            $existingReviewer = $query->first();

            if ($existingReviewer) {
                // Update the order
                $existingReviewer->order = $newOrder;
                $existingReviewer->updated_by = Auth::user()->id;
                $existingReviewer->updated_at = now();
                $existingReviewer->save();
            } else {
                // Create a new record
                // ProjectReviewer::create([
                //     'user_id' => $reviewer['user_id'],
                //     'reviewer_type' => $reviewer_type,
                //     'project_document_id' => $reviewer_type === 'document' ? $project_document_id : null,
                //     'order' => $newOrder,
                //     'status' => $reviewer['status'] ?? 1, // Default to 1 if not set,
                //     'created_by' => Auth::user()->id,
                //     'updated_by' => Auth::user()->id,
                // ]);

                // // Insert the new reviewer at the last order + 1
                ProjectReviewer::create([
                    'order' => $newOrder,
                    'status' => $reviewer['status'] == true ? true : false, /// true or false, tells if the reviewer is the active reviewer or not
                    'project_id' => $this->project->id,
                    'project_document_id' => $reviewer['project_document_id']  ,
                    'reviewer_type' =>  $reviewer['reviewer_type']  ,
                    'user_id' => $reviewer['user_id'],
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'review_status' => $reviewer['review_status'],
                ]);

            }
        }


        $this->dispatch('formSaved');

        
        $project = Project::where('id',$this->project->id)->first();

        // reset all reviewers
        $project->resetCurrentProjectDocumentReviewers();


        // check if all the project reviewers on the project is approved, and if not, mark the project as in_review
        if ($project) {
            // Check if ALL reviewers are approved
            $allApproved = $project->project_reviewers->every(function ($reviewer) {
                return $reviewer->review_status === 'approved';
            });

            if (!$allApproved) {
                // If at least one is not approved, mark as in_review
                $project->status = 'in_review';
                $project->save();
            }
        }





        // notify creator, project reviewers and project subscribers 
        ProjectHelper::notifyReviewersAndSubscribersOnProjectReviewerUpdate($project, $project_document_id, $reviewer_type);

        // send a project review request to the current reviewer
        $reviewer = $project->getCurrentReviewer();
        $reviewer_user = User::find($reviewer->user_id);
        ProjectHelper::sendForReviewersProjectReviewNotification($reviewer_user,$project, $reviewer);




        Alert::success('Success', 'Project reviewer list saved successfully');
        return redirect()->route('project.reviewer.index', [
            'project' => $this->project->id,
            'project_document_id' => $project_document_id,
            'reviewer_type' => $reviewer_type,
        ]);
    }


    protected function enforceSingleActiveReviewer()
    {
        // If any reviewer has status = true, ensure all others are false
        $hasTrue = false;

        $this->reviewers = collect($this->reviewers)->map(function ($reviewer) use (&$hasTrue) {
            if ($reviewer['status'] && !$hasTrue) {
                $hasTrue = true;
                $reviewer['status'] = true;
            } else {
                $reviewer['status'] = false;
            }
            return $reviewer;
        })->toArray();

        // If none were true, make the first reviewer active
        if (!$hasTrue && count($this->reviewers) > 0) {
            $this->reviewers[0]['status'] = true;
        }
    }



    public function getReviewersProperty(){

        $query = ProjectReviewer::select('project_reviewers.*')
            ->where('project_id',$this->project->id)
            ;

        // Filter by reviewer type
        if (!empty($this->reviewer_type)) {

            // dd($this->reviewer_type);
            $query = $query->where(function ($q) {
                $q->where('reviewer_type', $this->reviewer_type);
            });
        }


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


        

        // dd($this->reviewer_type);

        // Filter by document_type_id or show default reviewers (where null)
        if (!empty($this->project_document_id)) {
            $query = $query->where(function ($q) {
                $q->where('project_document_id', $this->project_document_id);
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


            // if(!Auth::user()->can('system access global admin')){
            //     $query =  $query->where('reviewers.created_by','=',Auth::user()->id);
            // }

            // Adjust the query
            if (!Auth::user()->can('system access global admin') && !Auth::user()->can('system access admin')) {
                $query = $query->where('reviewers.created_by', '=', Auth::user()->id);
            }elseif(Auth::user()->can('system access admin')){
                $query = $query->whereNotIn('reviewers.created_by', $dsiGodAdminUserIds);
            } else {

            }
        */


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Name A - Z":
                    $query = ProjectReviewer::with('user')
                        ->whereHas('user') // Ensures the reviewer has a related user
                        ->orderBy(User::select('name')->whereColumn('users.id', 'reviewers.user_id'), 'ASC');
                    break;
            
                case "Name Z - A":
                    $query = ProjectReviewer::with('user')
                        ->whereHas('user') 
                        ->orderBy(User::select('name')->whereColumn('users.id', 'reviewers.user_id'), 'DESC');
                    break;

                case "Order Ascending":
                    $query =  $query->orderBy('project_reviewers.order','ASC');
                    break;

                case "Order Descending":
                    $query =  $query->orderBy('project_reviewers.order','DESC');
                    break;


                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $query =  $query->orderBy('project_reviewers.created_at','DESC');
                    break;

                case "Oldest Added":
                    $query =  $query->orderBy('project_reviewers.created_at','ASC');
                    break;

                case "Latest Updated":
                    $query =  $query->orderBy('project_reviewers.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $query =  $query->orderBy('project_reviewers.updated_at','ASC');
                    break;
                default:
                    $query =  $query->orderBy('project_reviewers.updated_at','DESC');
                    break;

            }


        }else{
            $query =  $query->orderBy('project_reviewers.order','ASC');

        }





        // return $query->paginate($this->record_count);

         return $query->get()->toArray();



    }


    public function render()
    {
        /*
        $reviewers = ProjectReviewer::select('project_reviewers.*')
            ;

        if (!empty($this->search)) {
            $search = $this->search;
        
            $reviewers = $reviewers->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%');
            });
        }
        

        
            // Find the role
            $role = Role::where('name', 'DSI God Admin')->first();

            if ($role) {
                // Get user IDs only if role exists
                $dsiGodAdminUserIds = $role->reviewers()->pluck('id');
            } else {
                // Set empty array if role doesn't exist
                $dsiGodAdminUserIds = [];
            }


            // if(!Auth::user()->can('system access global admin')){
            //     $reviewers =  $reviewers->where('project_reviewers.created_by','=',Auth::user()->id);
            // }

            // Adjust the query
            if (!Auth::user()->can('system access global admin') && !Auth::user()->can('system access admin')) {
                $reviewers = $reviewers->where('project_reviewers.created_by', '=', Auth::user()->id);
            }elseif(Auth::user()->can('system access admin')){
                $reviewers = $reviewers->whereNotIn('project_reviewers.created_by', $dsiGodAdminUserIds);
            } else {

            }
        */

        /*
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


                 
                 // * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 // * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                  

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
        */
                    
        return view('livewire.admin.project-reviewer.project-reviewer-list',[
            'reviewers' => $this->reviewers,
            'lastOrder' => $this->lastOrder,
        ]);
    }
}
