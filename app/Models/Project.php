<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    use SoftDeletes;

 

    protected $table = "projects";
    protected $fillable = [
        'name',
        'description',
        'federal_agency', // this is shown as company
        'type', 
            // - federal project 
            // - company

        'status',
        # 'draft','submitted','in_review','approved','rejected','completed','cancelled',
        # on_que // status that the project is submitted but it is on que due to the submission restrictions    
        'allow_project_submission', // defines if the project can be submitted again
        # true to allow
        # false to not allow
        # a project can only be submitted again if the review is done
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',

        'project_number',
        'shpo_number',

        
        'submitter_response_duration_type',
        'submitter_response_duration',
        'submitter_due_date',
        'reviewer_response_duration',
        'reviewer_response_duration_type',
        'reviewer_due_date', 


        'latitude', 'longitude', 'location'
    ];

    // Automatically generate the project number
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($project) {
            $project->project_number = self::generateProjectNumber();
        });


        static::created(function ($project) {
            // event(new  \App\Events\ProjectCreated($project));

            try {
                event(new \App\Events\ProjectCreated($project , auth()->user()->id ));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectCreated event: ' . $e->getMessage(), [
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


        });

        static::updated(function ($project) {
            // event(new  \App\Events\ProjectUpdated($project));

            try {
                event(new \App\Events\ProjectUpdated($project,auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectUpdated event: ' . $e->getMessage(), [
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        });

        static::deleted(function ($project) {
            // event(new  \App\Events\ProjectDeleted(project: $project));


             try {
                event(new \App\Events\ProjectDeleted($project->id, auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectDeleted event: ' . $e->getMessage(), [
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


        });

       


    }



    /**
     * Get all of the project subscribers for the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_subscribers()
    {
        return $this->hasMany(ProjectSubscriber::class, 'project_id', 'id');
    }


    /**
     * Get all of the ProjectDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_documents()
    {
        return $this->hasMany(ProjectDocument::class, 'project_id', 'id');
    }



    /**
     * Get the user that owns the Reviewer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user that owns the Reviewer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updator() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }


    /**
     * Get all of the comments for the ProjectAttachments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments() # : HasMany
    {
        return $this->hasMany(ProjectAttachments::class, 'project_id', 'id');
    }

    /**
     * Get all of the comments for the ProjectAttachments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_reviewers() # : HasMany
    {
        return $this->hasMany(ProjectReviewer::class, 'project_id', 'id');
    }



    public function getProjectReviewersSortByOrder()
    {
        return $this->project_reviewers()
            ->orderBy('order', 'ASC')
            ->with('user') // Eager loading
            ->get();
    }


    /**
     * Get all of the comments for the Review
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_reviews() # : HasMany
    {
        return $this->hasMany(Review::class, 'project_id', 'id');
    }


  
    public function getStatus() #: string
    {
        switch ($this->status) {
            case 'submitted': 
                return "<span class='font-bold text-blue-500'>Submitted</span>";
            case 'in_review': 
                return "<span class='font-bold text-sky-500'>In Review</span>";
            case 'on_que': 
                return "<span class='font-bold text-pink-500'>On Que</span>";
            case 'approved': 
                return "<span class='font-bold text-lime-500'>Approved</span>";
            case 'rejected': 
                return "<span class='font-bold text-red-500'>Rejected</span>";
            case 'completed': 
                return "Completed";
            case 'cancelled': 
                return "Cancelled";
            default: 
                return "Draft";
        }
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'submitted'  => "Submitted",
            'in_review'  => "In Review",
            'approved'   => "Approved",
            'rejected'   => "Rejected",
            'completed'  => "Completed",
            'cancelled'  => "Cancelled",
            'on_que'    =>  "On Que",
            default      => "Draft",
        };
    }



    // public function checkSubmitStatus(){

    //     //check if the user had submitted it, 
    //     // return true to allow submission of project
    //     // retunr false to not allow submission of project 
    //     if($this->status == "submitted"){
    //         return true;
    //     }elseif($this->status == "in_review"){

    //         // check if the current user has evaluated 


    //         if(!empty($this->project_reviewers)){


    //         }else{
    //             // if there are no project reviewer yet, make sub
    //             return true;
    //         }





    //     }




    // }







    // public function getReview($reviewer_id = null){

    //     if(!empty($reviewer_id)){
    //         return $this->project_reviewers->firstWhere('user_id', $reviewer_id);

    //     }

    //     return $this->project_reviewers->firstWhere('user_id', Auth::id())->where('status',true);

    // }

    public function getReview($reviewer_id = null)
    {

        // dd($this->project_documents()->orderBy('id')->get());

        $userId = $reviewer_id ?? auth()->id();

        foreach ($this->project_documents()->orderBy('id')->get() as $document) {
                // dd($document->project_reviewers );

           $review = $document->project_reviewers()
                ->where('user_id', $userId)
                ->where('status', true)
                ->first();

            if ($review) {
                return $review;
            }
        }

        return null;
    }



    public function isNextReviewer(): bool
    {
        foreach ($this->project_documents()->orderBy('id')->get() as $document) {
            $reviewers = $document->project_reviewers()->orderBy('order')->get();

            foreach ($reviewers as $reviewer) {
                if ($reviewer->review_status !== 'approved' && $reviewer->status === false) {
                    return $reviewer->user_id === auth()->id();
                }
            }
        }

        return false;
    }


    public function isCurrentReviewer(): bool
    {
        foreach ($this->project_documents()->orderBy('id')->get() as $document) {
            $reviewers = $document->project_reviewers()->orderBy('order')->get();

            foreach ($reviewers as $reviewer) {
                if ($reviewer->review_status !== 'approved' && $reviewer->status === true) {
                    return $reviewer->user_id === auth()->id();
                }
            }
        }

        return false;
    }








    // public function isNextReviewer(): bool
    // {
    //     $reviewers = $this->project_reviewers()->orderBy('order')->get(); // Get all reviewers sorted by order

    //     foreach ($reviewers as $reviewer) {
    //         if ($reviewer->review_status !== 'approved') { // Find the first "pending" reviewer
    //             return $reviewer->user_id === auth()->id(); // Return true if it's the logged-in user
    //         }
    //     }

    //     return false; // No pending reviewers or the user is not next in line
    // }







    public function getCurrentReviewerUser()
    {
        return $this->project_reviewers()
        ->where('status', true)  
            // ->where('review_status', 'pending') // Find the first "pending" reviewer
            // ->orWhere('review_status', 'rejected') // Find the first "pending" reviewer
            
            ->orderBy('order')
            ->first()?->user; // Return the User model of the reviewer
    }

    public function getCurrentReviewer()
    {
        // return $this->project_reviewers() 
        //     ->where('status', true)  // find the first active reviewer
        //     ->orderBy('order')
        //     ->first(); // Return the User model of the reviewer

        // Iterate over all project documents ordered by id (i.e. oldest first)
        foreach ($this->project_documents()->orderBy('id')->get() as $document) {
            $reviewer = $document->project_reviewers()
                ->where('status', true)
                ->where('review_status', '!=', 'approved')
                ->orderBy('order')
                ->first();

            if ($reviewer) {
                return $reviewer;
            }
        }


    }


    public function getCurrentProjectDocument()
    {
        foreach ($this->project_documents()->orderBy('id')->get() as $document) {
            $hasPendingReview = $document->project_reviewers() 
                ->where('review_status', '!=', 'approved')
                ->exists();

            if ($hasPendingReview) {
                return $document;
            }
        }

        return null; // No matching document found
    }



    public function resetCurrentProjectDocumentReviewers()
    {
        foreach ($this->project_documents()->orderBy('id')->get() as $document) {
            $hasPendingReview = $document->project_reviewers()
                ->where('review_status', '!=', 'approved')
                ->exists();

            if ($hasPendingReview) {
                // Deactivate all reviewers
                $document->project_reviewers()->update(['status' => false]);

                // Activate the first reviewer by order
                $firstReviewer = $document->project_reviewers()
                    ->where('review_status', '!=', 'approved')
                    ->orderBy('order')
                    ->first();

                if ($firstReviewer) {
                    $firstReviewer->status = true;
                    $firstReviewer->save();
                }

                break; // Stop after the first matching document
            }
        }
    }






    public function getNextReviewer()
    {
        // Iterate over all project documents ordered by id (i.e. oldest first)
        foreach ($this->project_documents()->orderBy('id')->get() as $document) {
            $reviewer = $document->project_reviewers()
                ->where('status', false)
                ->where('review_status', '!=', 'approved')
                ->orderBy('order')
                ->first();

            if ($reviewer) {
                return $reviewer;
            }
        }

        // If no eligible reviewer is found
        return null;
    }


    // getProjectReviewerByUser original name
    public function checkIfUserIsProjectReviewer($user_id){
        return $this->project_reviewers()    
            ->where('user_id', $user_id)  // find the first active reviewer
            ->orderBy('order')
            ->first(); // Return the User model of the reviewer


    }

    /**
     * Count the number of projects that needs to be reviewed by reviewer
     * @return int
     */
    static public function countProjectsForReview($status = null){
        $projects = Project::select('projects.*');
 
        if(Auth::user()->hasRole('Reviewer')){
            $projects = $projects->whereNot('status','approved')
                ->whereHas('project_reviewers', function ($query) use ($status) {
                    $query->where('user_id', Auth::id())
                        ->where('status', true);

                        if(!empty($status)){
                            $query = $query->where('review_status',$status);
                        }
 
                });

            $projects = $projects->where('allow_project_submission',false);

        }

        

        if(Auth::user()->hasRole('Admin')){
            $projects = $projects->whereNot('status','approved')
                ->whereHas('project_reviewers', function ($query) {
                    $query->where('status', true)
                        ; // Filter by the logged-in user's ID
                })
                ->where('allow_project_submission',false);
        }       
    
        // do not show drafts to reviewers
        $projects = $projects->whereNot('status','draft');


        return $projects->count();
        
    }


    
     



    /**
     * Count the number of projects based on needing for update
     * @return int
     */
    static public function countProjectsForUpdate($owned = "no"){
        $projects = Project::select('projects.*');

        if(Auth::user()->hasRole('User')){
            $projects = $projects->whereNot('status','approved') // show projects that are pending update because the project needs to be updated after being reviewed
                ->whereNot('status','draft')
                ->where('allow_project_submission',true)
                
                ->where('created_by',Auth::user()->id);
        }elseif(Auth::user()->hasRole('Reviewer')){

            $projects = $projects->whereNot('status','approved') // show projects that are pending update because the project needs to be updated after being reviewed
                ->whereNot('status','draft')
                ->where('allow_project_submission',true);

            


        }

        // if(Auth::user()->hasRole('Admin')){
        //     $projects = $projects->whereNot('status','approved') // show projects that are pending update because the project needs to be updated after being reviewed
        //         ->whereNot('status','draft')
        //         ->where('allow_project_submission',true);
                
        //         // ->where('created_by',Auth::user()->id);
        // }

        if($owned == "yes"){

            $projects = $projects->where('created_by',Auth::user()->id);
        }


        return $projects->count();
        
    }





    static public function countProjects($status = null, $owned = "no"){

        $projects = Project::select('projects.*');

        if($owned == "no"){
          
            if(Auth::user()->hasRole('User')){
                $projects =  $projects->where('created_by',Auth::user()->id);


                if(!empty($status)){

                    $projects =  $projects->where('status',$status);


                }


            }


            if(Auth::user()->hasRole('Reviewer')){
                 

                //add status
                if(!empty($status)){

                    $projects =  $projects->where('status',$status);


                }
                

                // if route if my projects

                // do not  count drafts to reviewers
                $projects = $projects->whereNot('status','draft');


            }


            if(Auth::user()->hasRole('Admin')){
                 

                //add status
                if(!empty($status)){

                    $projects =  $projects->where('status',$status);


                }

                 


            }

          
        }else{


            if(!empty($status)){

                if($status == "in_review"){
                    $projects = $projects->where(function ($query) {
                        $query->where('status', 'in_review')
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('status', '!=', 'approved')
                                        ->where('status', '!=', 'draft')
                                        ->where('allow_project_submission', false);
                            });
                    });
                }else{
                    $projects =  $projects->where('status',$status);
                }

                


            }

            $projects =  $projects->where('created_by',Auth::user()->id);
        }


        return $projects->count();
    }



     static public function countMyProjects($status = null){

        $projects = Project::select('projects.*');
 
            $projects =  $projects->where('created_by',Auth::user()->id);


            if(!empty($status)){

                $projects =  $projects->where('status',$status);


            }
 


             


        return $projects->count();
    }



    // Prevent modification of project name (unless admin)
    // public function setNameAttribute($value)
    // {
    //     if (!auth()->user()->hasRole('Admin')) {
    //         throw new \Exception("You are not allowed to modify the project name.");
    //     }
    //     $this->attributes['name'] = $value;
    // }


    /**
     * Global Timer (Customize Project Due Date)
     * Set a default due date (e.g., 21 days after submission):
     * @return void
     */
    // public function setDueDate()
    // {
    //     if ($this->is_flagged_due) {
    //         $this->due_date = now()->addDays($this->global_timer_days);
    //         $this->save();
    //     }
    // }


    /**
     * Review Response Rate Timer
     * If all reviewers reviewed, reset the review timer:
     */

    // public function resetReviewTimer()
    // {
    //     if ($this->allReviewersCompleted()) {
    //         $this->due_date = now()->addDays($this->review_response_timer_days);
    //         $this->save();
    //     }
    // }

    // /**
    //  * Check Reviewers' Completion
    //  */
    // public function allReviewersCompleted()
    // {
    //     return $this->reviews()->whereNull('completed_at')->count() == 0;
    // }

    /**
     * Generate a Unique Project Number
     * You mentioned that the project number can be based on SHPO tracking or a custom system number.
     * Add this method to Projects.php:
     */
     
    public static function generateProjectNumber($shpo_tracking = null)
    {
        if ($shpo_tracking) {
            return 'SHPO-' . $shpo_tracking;
        }
    
        $lastProject = self::latest()->first();
        $nextNumber = $lastProject ? $lastProject->id + 2 : 1;
        
        return 'PRJ-' . str_pad($nextNumber, 9, '0', STR_PAD_LEFT); // PRJ-000001
    }


    static public function calculateDueDate($start_date, $duration_type, $duration)
    {
        if (!$start_date || !$duration || !$duration_type) {
            return null; // Ensure all required values exist
        }

        try {
            // Convert $start_date to a Carbon instance safely
            $start = Carbon::parse($start_date);

            // Ensure $duration is an integer
            $duration = (int) $duration;

            switch (strtolower($duration_type)) {
                case 'day':
                    $end_date = $start->addDays($duration);

                    if ($duration == 1) {
                        $end_date = Carbon::createFromFormat('Y-m-d', $start->toDateString());
                    }
                    break;
                case 'week':
                    $end_date = $start->addWeeks($duration);
                    break;
                case 'month':
                    $end_date = $start->addMonths($duration);
                    break;
                default:
                    return null; // Handle invalid duration_type
            }

            return $end_date; // Return Carbon instance
        } catch (\Exception $e) {
            return null; // Handle any unexpected errors
        }
    }


    

    public static function getAverageApprovalTime($timeRange = 'all')
    {
        $query = self::whereIn('status', ['approved']);

        // Apply time filters based on range
        switch ($timeRange) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'this_year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'all':
            default:
                // No additional filters, include all records
                break;
        }

        $projects = $query->orderBy('created_at', 'asc')->get();
        $project_approval_times = [];

        foreach ($projects as $project) {
             

            if ($project->status == "approved") {
                // Calculate time difference in hours 

                // Ensure updated_at is after created_at
                if ($project->updated_at && $project->updated_at->greaterThan($project->created_at)) {
                    // Calculate time difference in hours
                    $project_approval_time = $project->created_at->diffInHours($project->updated_at);
                    $project_approval_times[] = $project_approval_time;
                }
            }
        }

        return count($project_approval_times) > 0 ? array_sum($project_approval_times) / count($project_approval_times) : 0;
    }


    public function canPostInDiscussion($user = null)
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return false;
        }

        // 1. Is Creator?
        if ($this->created_by == $user->id) {
            return true;
        }

        // 2. Is assigned as a reviewer to this project?
        $isReviewer = $this->project_reviewers()->where('user_id', $user->id)->exists();
        if ($isReviewer) {
            return true;
        }

        // 3. Has role 'DSI God Admin' or 'Admin'?
        if ($user->hasRole('DSI God Admin') || $user->hasRole('Admin')) {
            return true;
        }

        return false;
    }





    // Scope based queries for the project list 
    // when using this, remove the word scope

    public function scopeOwnedBy(Builder $query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeNotDraft(Builder $query)
    {
        return $query->where('status', '!=', 'draft');
    }


    public function scopePendingUpdate(Builder $query)
    {
        return $query->where('status', '!=', 'approved')
                    ->where('status', '!=', 'draft')
                    ->where('allow_project_submission', true);
    }

    public function scopeInReview(Builder $query)
    {
        return $query->where(function ($q) {
            $q
                ->where('status', 'in_review')
                ->orWhere(function ($subQuery) {
                    $subQuery->where('status', '!=', 'approved')
                            ->where('status', '!=', 'draft')
                            ->where('allow_project_submission', false);
            });
        });
    }

// $projects = $projects->whereNot('status','approved')
//                 ->whereHas('project_reviewers', function ($query) use ($status) {
//                     $query->where('user_id', Auth::id())
//                         ->where('status', true);

//                         if(!empty($status)){
//                             $query = $query->where('review_status',$status);
//                         }
 
//                 });

    public function scopeAssignedToReviewer(Builder $query, $userId)
    {
        return $query->whereHas('project_reviewers', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('status', true);
        });
    }

    public function scopeWithSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%$search%")
            ->orWhere('federal_agency', 'LIKE', "%$search%")
            ->orWhere('type', 'LIKE', "%$search%")
            ->orWhere('description', 'LIKE', "%$search%")
            ->orWhere('location', 'LIKE', "%$search%")
            ->orWhere('latitude', 'LIKE', "%$search%")
            ->orWhere('longitude', 'LIKE', "%$search%")
            ->orWhereHas('project_reviewers.user', function ($q) use ($search) {
                $q->where('users.name', 'LIKE', "%$search%")
                    ->orWhere('users.email', 'LIKE', "%$search%") ;
            });
        });
    }

    public function scopeWithLocationFilter(Builder $query, array $locations)
    {
        return $query->where(function($q) use ($locations) {
            foreach ($locations as $location) {
                $q->where('location', 'LIKE', "%$location%") ;
            }
        });
    }


    public function scopeWithReviewStatus(Builder $query, $status)
    {
        if ($status == "approved") {
            return $query->where('status', 'approved');
        }
        return $query->whereHas('project_reviewers', function ($q) use ($status) {
            $q->where('status', true)->where('review_status', $status);
        });
    }




}
