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
use App\Helpers\ProjectHelper;
use App\Models\ProjectReviewer;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Events\ReviewerListUpdated;
use Illuminate\Support\Facades\Log;
use App\Models\DocumentTypeReviewer;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectOpenReviewNotification;
use App\Notifications\ProjectOpenReviewNotificationDB;
use App\Notifications\ProjectReviewerUpdatedNotification;
use App\Notifications\ProjectReviewerUpdatedNotificationDB;

class ReviewerList extends Component
{

    use WithFileUploads;
    use WithPagination;

    public $search = '';
    public $sort_by = '';
    public $record_count = 100;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;

    public $file;

    // public $lastOrder;
    public $document_types;

    public $document_type_id;

    public $reviewer_type = "";

    public $documentTypesWithoutReviewers;
    public $allDocumentTypesHaveReviewers;
    public $hasInitialReviewers;
    public $hasFinalReviewers;




    public $user_id;
    public $order;
    public $status; 
    public $users;
  
    protected $listeners = [
        'reviewerCreated' => '$refresh', 
        'reviewerUpdated' => '$refresh',
        'reviewerDeleted' => '$refresh',
        
    ];


    public $reviewers = [];
     


    public function mount(){

        // $this->users = User::permission('system access reviewer')
        //     ->pluck('id', 'name')
        //     ->toArray();
 

        // DocumentTypes that don't have any reviewers
        $this->documentTypesWithoutReviewers = DocumentType::whereDoesntHave('reviewers')->pluck('name')->toArray();

        // Check if all document types have at least one reviewer
        $this->allDocumentTypesHaveReviewers = empty($this->documentTypesWithoutReviewers);

        // Check if there are reviewers by type
        $this->hasInitialReviewers = Reviewer::where('reviewer_type', 'initial')->exists();
        $this->hasFinalReviewers = Reviewer::where('reviewer_type', 'final')->exists();


        

        // Get the last order number
        // $this->lastOrder = Reviewer::max('order') ?? 0;
        
        // document type adjustment
            $this->document_types = DocumentType::orderBy('order')->get();
            

            // check the first document type
            $this->document_type_id = DocumentType::first()->id ?? null;
            
            // check the get request if it has one 
            $this->document_type_id = request('document_type_id') ?? $this->document_type_id;
        // ./  document type adjustment
         
        // reviewer_type adjustment
            $this->reviewer_type = "initial";
            
            // check the get request if it has reviewer_type 
            $this->reviewer_type = request('reviewer_type') ?? $this->reviewer_type;


            if($this->reviewer_type != "document"){
                $this->document_type_id = null;
            } 
            
        // ./ reviewer_type adjustment


        // insert the reviewers into an array
            $this->reviewers = $this->getReviewersProperty();


             
        // dd($this->lastOrder);


        // Refresh users list based on the same rules
        $this->users = $this->eligibleUsersQuery() 
            ->orderBy('name', 'asc')
            ->pluck('id', 'name')->toArray();


    }

    public function updatedReviewerType(){
        
        if ($this->reviewer_type !== 'document') {
            $this->document_type_id = null;
        }

        if ($this->reviewer_type == 'document') { 
            $this->document_type_id = DocumentType::first()->id ?? null;
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



    public function updatedDocumentTypeId(){
       $this->reviewers = $this->getReviewersProperty();
    }


     



    /**
     * Computed (live) property for last order
     */
    public function getLastOrderProperty()
    {
        // return Reviewer::where('document_type_id', $this->document_type_id)->count();
        return collect($this->reviewers)
            ->where('reviewer_type', $this->reviewer_type)
            ->when($this->reviewer_type === 'document', function ($collection) {
                return $collection->where('document_type_id', $this->document_type_id);
            }, function ($collection) {
                return $collection->whereNull('document_type_id');
            })
            ->max('order');
    }

    /*
    public function updateOrder($reviewer_id, $order, $direction, $document_type_id, $reviewer_type)
    {
        if ($direction == "move_up") {
            $prev_reviewer = Reviewer::where('reviewer_type', $reviewer_type)
                ->when($reviewer_type === 'document', function ($query) use ($document_type_id) {
                    return $query->where('document_type_id', $document_type_id);
                }, function ($query) {
                    return $query->whereNull('document_type_id');
                })
                ->where('order', '<', $order)
                ->orderBy('order', 'DESC')
                ->first();

            if ($prev_reviewer) {
                // Swap the orders
                $current_reviewer = Reviewer::find($reviewer_id);
                $tempOrder = $current_reviewer->order;

                $current_reviewer->order = $prev_reviewer->order;
                $prev_reviewer->order = $tempOrder;

                $current_reviewer->save();
                $prev_reviewer->save();
            }

        } elseif ($direction == "move_down") {
            $next_reviewer = Reviewer::where('reviewer_type', $reviewer_type)
                ->when($reviewer_type === 'document', function ($query) use ($document_type_id) {
                    return $query->where('document_type_id', $document_type_id);
                }, function ($query) {
                    return $query->whereNull('document_type_id');
                })
                ->where('order', '>', $order)
                ->orderBy('order', 'ASC')
                ->first();

            if ($next_reviewer) {
                $current_reviewer = Reviewer::find($reviewer_id);
                $tempOrder = $current_reviewer->order;

                $current_reviewer->order = $next_reviewer->order;
                $next_reviewer->order = $tempOrder;

                $current_reviewer->save();
                $next_reviewer->save();
            }
        }

        $this->resetOrder($document_type_id, $reviewer_type);
    }



    public function resetOrder($document_type_id, $reviewer_type)
    {
        $reviewers = Reviewer::where('reviewer_type', $reviewer_type)
            ->when($reviewer_type === 'document', function ($query) use ($document_type_id) {
                return $query->where('document_type_id', $document_type_id);
            }, function ($query) {
                return $query->whereNull('document_type_id');
            })
            ->orderBy('order', 'ASC')
            ->get();
    
        foreach ($reviewers as $index => $reviewer) {
            $reviewer->order = $index + 1;
            $reviewer->save();
        }
    }
    */

    public function updateOrder($index, $order, $direction, $document_type_id, $reviewer_type)
    {
        if (!isset($this->reviewers[$index])) {
            return;
        }

        // Filter reviewers by type and (optional) document_type_id
        $filtered = collect($this->reviewers)
            ->where('reviewer_type', $reviewer_type)
            ->when($reviewer_type === 'document', function ($collection) use ($document_type_id) {
                return $collection->where('document_type_id', $document_type_id);
            }, function ($collection) {
                return $collection->whereNull('document_type_id');
            })
            ->sortBy('order')
            ->values(); // Re-index the array

        // Find the position of the current reviewer in the filtered list
        $currentReviewer = $this->reviewers[$index];
        $position = $filtered->search(function ($item) use ($currentReviewer) {
            return $item['user_id'] == $currentReviewer['user_id']
                && $item['reviewer_type'] == $currentReviewer['reviewer_type']
                && ($item['document_type_id'] ?? null) == ($currentReviewer['document_type_id'] ?? null);
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
                    ($mainReviewer['document_type_id'] ?? null) == ($reviewer['document_type_id'] ?? null)
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
        $document_type_id = $this->document_type_id;
        $reviewer_type = $this->reviewer_type;

        $filtered = collect($this->reviewers)
            ->where('reviewer_type', $reviewer_type)
            ->when($reviewer_type === 'document', function ($collection) use ($document_type_id) {
                return $collection->where('document_type_id', $document_type_id);
            }, function ($collection) {
                return $collection->whereNull('document_type_id');
            })
            ->sortBy('order')
            ->values(); // Reset index

        foreach ($filtered as $i => $reviewer) {
            $reviewer['order'] = $i + 1;

            foreach ($this->reviewers as &$mainReviewer) {
                if (
                    $mainReviewer['user_id'] == $reviewer['user_id'] &&
                    $mainReviewer['reviewer_type'] == $reviewer['reviewer_type'] &&
                    ($mainReviewer['document_type_id'] ?? null) == ($reviewer['document_type_id'] ?? null)
                ) {
                    $mainReviewer['order'] = $reviewer['order'];
                    break;
                }
            }
        }
    }




    
    /**
     * Summary of updateDocumentTypeReviewerOrder
     * @param mixed $reviewer_id
     * @param mixed $direction
     * @return void
     
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
    */
 
   

    public function apply_to_all()
    {
        $reviewer_types = ['initial', 'document', 'final'];

        // dd($reviewer_types);


        // Get all projects where status is not 'approved', 'rejected', or 'draft'
        $projects = Project::whereNotIn('status', ['approved', 'rejected', 'draft','on_que'])->get();

        // dd($projects);

        // fetch all projects 
        foreach ($projects as $project) {
            // dd($project);


            // all new reviewers
            $allNewReviewerIds = [];


            // all reviewers to remove
            $allToRemoveIds = [];


            foreach ($reviewer_types as $type) {
                // Get all reviewers of the current type
                $reviewers = Reviewer::where('reviewer_type', $type)->orderBy('order')->get();

                // dd($reviewers);



                // Get existing reviewers for this project and type
                $existingReviewers = ProjectReviewer::where('project_id', $project->id)
                    ->where('reviewer_type', $type)
                    ->pluck('user_id', 'id')
                    ->toArray();

                // dd($existingReviewers);


                $newReviewerIds = $reviewers->pluck('user_id')->toArray();

                // 1. Remove project reviewers not in the master reviewer list
                $toRemove = array_diff($existingReviewers, $newReviewerIds);
                if (!empty($toRemove)) {

                    // Track unique "to remove" user_ids
                    foreach ($toRemove as $userId) {
                        if (!in_array($userId, $allToRemoveIds, true)) {
                            $allToRemoveIds[] = $userId;
                        }
                    }


                    ProjectReviewer::where('project_id', $project->id)
                        ->where('reviewer_type', $type)
                        ->whereIn('user_id', $toRemove)
                        ->delete();
                }

                // 2. Update order for existing reviewers
                foreach ($reviewers as $reviewer) {
                    ProjectReviewer::where('project_id', $project->id)
                        ->where('reviewer_type', $type)
                        ->where('user_id', $reviewer->user_id)
                        ->update(['order' => $reviewer->order]);
                }

                // 3. Add new reviewers not already in project reviewers
                foreach ($reviewers as $reviewer) {
                    if (!in_array($reviewer->user_id, $existingReviewers)) {
                        ProjectReviewer::create([
                            'project_id' => $project->id,
                            'user_id' => $reviewer->user_id,
                            'order' => $reviewer->order,
                            'reviewer_type' => $type,
                            'status' => false,
                            'review_status' => 'pending',
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                        ]);
                    }

                    // Track unique "new" user_ids
                    if (!in_array($reviewer->user_id, $allNewReviewerIds, true)) {
                        $allNewReviewerIds[] = $reviewer->user_id;
                    }

                }

                

                 
            }


  
            // 4. Reset all statuses for this project reviewers
            ProjectReviewer::where('project_id', $project->id)
                // ->where('reviewer_type', $type)
                ->update(['status' => false]);

            // 5. Activate the first reviewer (not approved)
            $project->resetCurrentProjectDocumentReviewers();



            // Uses in_array(..., true) for strict checks; finishes with array_unique as a final safety net.
            $allNewReviewerIds = array_values(array_unique($allNewReviewerIds));
            $allToRemoveIds    = array_values(array_unique($allToRemoveIds));


            // 6. Notify removed reviewers
            foreach ($allToRemoveIds as $user_id) {
                $user = User::find($user_id);
                if ($user && $project->status !== "draft") {
                    // Notification::send($user, new ProjectReviewerUpdatedNotification($project, $user));
                    // Notification::send($user, new ProjectReviewerUpdatedNotificationDB($project, $user));

                    // $current_reviewer = $project->getCurrentReviewer();
                    // if ($current_reviewer && $user->id === $current_reviewer->user->id) {
                    //     Notification::send($user, new ProjectReviewNotification($project, $current_reviewer));
                    //     Notification::send($user, new ProjectReviewNotificationDB($project, $current_reviewer));
                    // }

                    ProjectHelper::notifyReviewersAndSubscribersOnAdminReviewerUpdate($project, $user);


                }
            }

            // 7. Notify added reviewers
            foreach ($allNewReviewerIds as $user_id) {
                $user = User::find($user_id);
                if ($user && $project->status !== "draft") {
                    // Notification::send($user, new ProjectReviewerUpdatedNotification($project, $user));
                    // Notification::send($user, new ProjectReviewerUpdatedNotificationDB($project, $user));

                    // $current_reviewer = $project->getCurrentReviewer();
                    // if ($current_reviewer && $user->id === $current_reviewer->user->id) {
                    //     Notification::send($user, new ProjectReviewNotification($project, $current_reviewer));
                    //     Notification::send($user, new ProjectReviewNotificationDB($project, $current_reviewer));
                    // }


                    ProjectHelper::notifyReviewersAndSubscribersOnAdminReviewerUpdate($project, $user);

 
                }
            }

            // 8. Notify project creator
            $creator = User::find($project->created_by);
            if ($creator) {
                // Notification::send($creator, new ProjectReviewerUpdatedNotification($project, $creator));
                // Notification::send($creator, new ProjectReviewerUpdatedNotificationDB($project, $creator));
                ProjectHelper::notifyReviewersAndSubscribersOnAdminReviewerUpdate($project, $creator);
            }

            // 9. Notify admin
            $admin = Auth::user();
            // Notification::send($admin, new ProjectReviewerUpdatedNotification($project, $admin));
            // Notification::send($admin, new ProjectReviewerUpdatedNotificationDB($project, $admin));

            ProjectHelper::notifyReviewersAndSubscribersOnAdminReviewerUpdate($project, $admin);


            $current_reviewer = $project->getCurrentReviewer();

            // check project for open review
                // if there is a reviewer record but hte reviewer user is not added, it means it is open review
                // if the current reviewer is an open review, update all administrators that are there is an open review 
                if(!empty($current_reviewer) && empty($current_reviewer->user_id)){ 
                // if the current reviewer does not have a user assigned to it , meaning this is an open review 
                    // notify admininstrators about the open review



                    // Determine users based on reviewer type
                    $reviewerType = $current_reviewer->reviewer_type; // assuming $reviewer is available

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
                            Notification::send($user, new ProjectOpenReviewNotification($project,$current_reviewer));
                        } catch (\Throwable $e) {
                            Log::error('Failed to send ProjectOpenReviewNotification notification: ' . $e->getMessage(), [
                                'project_id' => $project->id,
                                'user_id' => $user->id,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }

                        try {
                            Notification::send($user, new ProjectOpenReviewNotificationDB($project,$current_reviewer));
                        } catch (\Throwable $e) {
                            Log::error('Failed to send ProjectOpenReviewNotificationDB notification: ' . $e->getMessage(), [
                                'project_id' => $project->id,
                                'user_id' => $user->id,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }
                    }



                }

            // ./ check project for open review

            

            // 10. Add review record
            Review::create([
                'project_review' => "The $type project reviewers list has been updated",
                'admin_review' => true,
                'project_id' => $project->id,
                'reviewer_id' => $admin->id,
                'review_status' => 'Approved',
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

 

            // 11. Log activity
            ActivityLog::create([
                'log_action' => "Project \"{$project->name}\" $type reviewer list updated",
                'log_username' => $admin->name,
                'created_by' => $admin->id,
            ]);


            
        }

        

        Alert::success('Success', 'Reviewers applied to all projects successfully');
        return redirect()->route('reviewer.index');
    }



    /*
    public function delete($id){
        $reviewer = Reviewer::find($id);

        $document_type_id = $reviewer->document_type_id;
 
        $reviewer->delete();

        // $order_start_number = $reviewer->order;

        $this->resetOrder($document_type_id, $reviewer->reviewer_type);
 
       
        // Alert::success('Success','Reviewer deleted successfully');
        // return redirect()->route('reviewer.index');

    }*/

    public function delete($index)
    {
        // Remove the reviewer from the array using the index
        unset($this->reviewers[$index]);

        // Reindex the array to avoid gaps in keys
        $this->reviewers = array_values($this->reviewers);

        // // Optional: sort the reviewers array by `order` again
        // $this->reviewers = collect($this->reviewers)->sortBy('order')->values()->toArray();

        $this->resetOrder();

    }





    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'user_id' => [
                // 'required',
                'nullable',
                Rule::unique('reviewers', 'user_id')
                    ->where(function ($query) {
                    // Reviewer type is initial or final
                    if (in_array($this->reviewer_type, ['initial', 'final'])) {
                        return $query->where('reviewer_type', $this->reviewer_type);
                    }

                    // Reviewer type is document — must match both type and document_type_id
                    if ($this->reviewer_type === 'document') {
                        return $query
                            ->where('reviewer_type', 'document')
                            ->where('document_type_id', $this->document_type_id);
                    }

                    // fallback to avoid error
                    return $query->whereNull('id'); // guarantees no match
                }),

            ],
             
            'reviewer_type' => [
                'required',
            ],

            'document_type_id' => [
                function ($attribute, $value, $fail) {
                    if (!empty($this->reviewer_type) &&  $this->reviewer_type == "document") {
                        if (empty($value)) {
                            $fail('The document type field is required');
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
            'document_type_id.required' => 'Document type is required',
        ]);



        // Custom validation for null user_id scenario
        if (is_null($this->user_id)) {
            $existingReviewer = Reviewer::whereNull('user_id')
                ->where('reviewer_type', $this->reviewer_type)
                ->where('document_type_id', $this->document_type_id)
                ->first();

            if ($existingReviewer) {
                $this->addError('user_id', 'A record without a user ID already exists for this reviewer type.');
            }
        }




    }

 

    public function validateReviewer()
    {
        $this->validate([
            'user_id' => [
                // 'required'
                 'nullable',
            ],
            'reviewer_type' => ['required'],
            'document_type_id' => [
                function ($attribute, $value, $fail) {
                    if (!empty($this->reviewer_type) &&  $this->reviewer_type === "document") {
                        if (empty($value)) {
                            $fail('The document type field is required.');
                        }
                    }
                },
            ],
            'order' => ['required'],
        ], [
            'user_id.required' => 'User is required',
            'document_type_id.required' => 'Document type is required',
        ]);

        // Manual uniqueness check within the array
        foreach ($this->reviewers as $reviewer) {

            



            if (
                $reviewer['user_id'] == $this->user_id &&
                $reviewer['reviewer_type'] == $this->reviewer_type &&
                (
                    $this->reviewer_type !== 'document' ||
                    ($reviewer['document_type_id'] ?? null) == $this->document_type_id
                )
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'user_id' => 'User is already added for this reviewer type' .
                                ($this->reviewer_type === 'document' ? ' and document type.' : '.'),
                ]);
            }


            // // Check if there is already a record with a null user_id for the same reviewer type and document type
            // if (is_null($reviewer['user_id']) && $reviewer['reviewer_type'] == $this->reviewer_type && ($this->reviewer_type !== 'document' || $reviewer['document_type_id'] == $this->document_type_id)) {
            //     throw \Illuminate\Validation\ValidationException::withMessages([
            //         'user_id' => 'A record without a user ID already exists for this reviewer type' .
            //                     ($this->reviewer_type === 'document' ? ' and document type.' : '.'),
            //     ]);
            // }

            

        }
    }



    /**
     * Handle save
     */
    // public function save()
    public function add()
    {
        
 



        $this->validateReviewer();



        $newReviewer = [
            'order' => 1, // Temporary default
            'status' => $this->status,
            'user_id' => $this->user_id,
            'reviewer_type' => $this->reviewer_type,
            'document_type_id' => $this->reviewer_type === 'document' ? $this->document_type_id : null,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'updated_at' => now(),
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
                    ? $reviewer['document_type_id'] == $this->document_type_id
                    : is_null($reviewer['document_type_id']));
        });

        if ($this->order === 'top') {
            // Increment order for matching reviewers
            $this->reviewers = collect($this->reviewers)->map(function ($rev) {
                if (
                    $rev['reviewer_type'] === $this->reviewer_type &&
                    ($this->reviewer_type === 'document'
                        ? $rev['document_type_id'] == $this->document_type_id
                        : is_null($rev['document_type_id']))
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


        
       

 
     
        // Alert::success('Success','Reviewer added successfully');
        // return redirect()->route('reviewer.index',[
        //     'document_type_id' => $this->document_type_id,
        //     'reviewer_type' => $this->reviewer_type
        // ]);
    }







    public function save()
    {
        // dd("here");
        $document_type_id = $this->document_type_id;
        $reviewer_type = $this->reviewer_type;


        //delete all existing reviewers 
        Reviewer::where('document_type_id', $document_type_id)
            ->where('reviewer_type', $reviewer_type)->delete();


        // Filter and sort the reviewers array
        $filtered = collect($this->reviewers)
            ->where('reviewer_type', $reviewer_type)
            ->when($reviewer_type === 'document', function ($collection) use ($document_type_id) {
                return $collection->where('document_type_id', $document_type_id);
            }, function ($collection) {
                return $collection->filter(function ($item) {
                    return empty($item['document_type_id']);
                });
            })
            ->sortBy('order')
            ->values(); // Reset index

        foreach ($filtered as $i => $reviewer) {
            $newOrder = $i + 1;

            // Try to find an existing reviewer
            $query = Reviewer::where('user_id', $reviewer['user_id'])
                ->where('reviewer_type', $reviewer_type);

            if ($reviewer_type === 'document') {
                $query->where('document_type_id', $document_type_id);
            } else {
                $query->whereNull('document_type_id');
            }

            $existingReviewer = $query->first();

            if ($existingReviewer) {
                // Update the order
                $existingReviewer->order = $newOrder;
                $existingReviewer->save();
            } else {
                // Create a new record
                Reviewer::create([
                    'user_id' => $reviewer['user_id'],
                    'reviewer_type' => $reviewer_type,
                    'document_type_id' => $reviewer_type === 'document' ? $document_type_id : null,
                    'order' => $newOrder,
                    'status' => $reviewer['status'] ?? 1, // Default to 1 if not set,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
            }
        }


        $this->dispatch('formSaved');


        $user = User::find(auth()->user()->id);

        //send an update on the notifications 
        try {

            event(new ReviewerListUpdated(auth()->user()->id));
        } catch (\Throwable $e) {
            Log::error('Failed to send ReviewerListUpdated event: ' . $e->getMessage(), [ 
                'user_id' => auth()->user()->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }



        Alert::success('Success', 'Reviewer list saved successfully');
        return redirect()->route('reviewer.index', [
            'document_type_id' => $document_type_id,
            'reviewer_type' => $reviewer_type,
        ]);
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
    //     if (!Auth::user()->can('system access global admin') && !Auth::user()->can('system access admin')) {
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

        // Filter by reviewer type
        if (!empty($this->reviewer_type)) {
            $query = $query->where(function ($q) {
                $q->where('reviewer_type', $this->reviewer_type);
            });
        }

        // dd($this->reviewer_type);

        // Filter by document_type_id or show default reviewers (where null)
        if (!empty($this->document_type_id) && ($this->reviewer_type == "document") ) {
            $query = $query->where(function ($q) {
                $q->where('document_type_id', $this->document_type_id);
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





        // return $query->paginate($this->record_count);
        // return $query->paginate($this->record_count)->items(); // Returns only the array of data

        return $query->get()->toArray();


        


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


        //     // if(!Auth::user()->can('system access global admin')){
        //     //     $reviewers =  $reviewers->where('reviewers.created_by','=',Auth::user()->id);
        //     // }

        //     // Adjust the query
        //     if (!Auth::user()->can('system access global admin') && !Auth::user()->can('system access admin')) {
        //         $reviewers = $reviewers->where('reviewers.created_by', '=', Auth::user()->id);
        //     }elseif(Auth::user()->can('system access admin')){
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
            // dd($this->reviewers);    

        $issues = [
            'no_reviewers' => Reviewer::count() === 0,
            'document_types_missing_reviewers' => !$this->allDocumentTypesHaveReviewers,
            'no_initial_reviewers' => !$this->hasInitialReviewers,
            'no_final_reviewers' => !$this->hasFinalReviewers,
        ];

        return view('livewire.admin.reviewer.reviewer-list',[
            'reviewers' => $this->reviewers ,
            'lastOrder' => $this->lastOrder,
            'issues' => $issues
        ]);
    }


}
