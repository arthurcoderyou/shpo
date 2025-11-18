<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectDocument extends Model
{
    use SoftDeletes;
    protected $table = "project_documents";

    protected $fillable = [ 
        
        'project_id',
        'document_type_id',
        'created_by',
        'updated_by',
        'status', // 'draft','submitted','in_review','approved','rejected','completed','cancelled', 'changes_requested','reviewed'
        'last_submitted_at',
        'last_submitted_by',

        'allow_project_submission',

        'rc_number',

        'submitter_response_duration_type',
        'submitter_response_duration',
        'submitter_due_date',

        'reviewer_response_duration',
        'reviewer_response_duration_type',
        'reviewer_due_date',


        'type',


        'permit_number',
        'application_type', 


        'applicant',
        'document_from',
        'company',
        'comments',
        'findings',




    ];
     
    public static function boot()
    {
        parent::boot();
        

        static::created(function ($project_document) {
            // event(new  \App\Events\ProjectDocumentCreated($project_document));

             try {
                event(new \App\Events\ProjectDocumentCreated($project_document, auth()->user()->id ));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectDocumentCreated event: ' . $e->getMessage(), [
                    'project_document_id' => $project_document->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


        });

        static::updated(function ($project_document) {
            // event(new  \App\Events\ProjectDocumentUpdated($project_document));

            try {
                event(new \App\Events\ProjectDocumentUpdated($project_document, auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectDocumentUpdated event: ' . $e->getMessage(), [
                    'project_document_id' => $project_document->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        });

        static::deleted(function ($project_document) {
            // event(new  \App\Events\ProjectDocumentDeleted($project_document));

            try {
                event(new \App\Events\ProjectDocumentDeleted($project_document->id, auth()->user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectDocumentDeleted event: ' . $e->getMessage(), [
                    'project_document_id' => $project_document->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

        });


    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_submitted_at' => 'datetime', 
        ];
    }


    /**
     * Get all of the project attachments 
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_attachments()
    {
        return $this->hasMany(ProjectAttachments::class, 'project_document_id', 'id');
    }

    
    /**
     * Get all of the project discussions 
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_discussions()
    {
        return $this->hasMany(ProjectDiscussion::class, 'project_document_id', 'id');
    }


    /**
     * Get all of the project reviewers  
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_reviewers()
    {
        return $this->hasMany(ProjectReviewer::class, 'project_document_id', 'id');
    }

    /**
     * Get all of the reviews on the project document
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project_reviews() # : HasMany
    {
        return $this->hasMany(Review::class, 'project_document_id', 'id');
    }




    /**
     * Get the Document Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function document_type() # : BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id', 'id');
    }

    /**
     * Get the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project() # : BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
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
     * Get the user that submits the Project Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function submitter() # : BelongsTo
    {
        return $this->belongsTo(User::class, 'last_submitted_by', 'id');
    }

    
    /**
     * Get all of the project_document_references for the ProjectDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function document_references()
    {
        return $this->hasMany(ProjectDocumentReferences::class, 'project_document_id', 'id');
    }

    /**
     * Get all of the referenced documents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referenced_document()
    {
        return $this->hasMany(ProjectDocumentReferences::class, 'referenced_project_document_id', 'id');
    }


    /**
     * Get all of the referenced documents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function re_review_requests()
    {
        return $this->hasMany(ReReviewRequest::class, 'project_document_id', 'id');
    }


    // // get the current reviewer based on the project document 
    // public function getCurrentReviewer(): bool
    // { 
    //     $reviewers = $this->project_reviewers()->orderBy('order')->get();

    //     foreach ($reviewers as $reviewer) {
    //         if ($reviewer->review_status !== 'approved' && $reviewer->status === true) {
    //             return $reviewer;
    //         }
    //     } 

    //     return false;
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
        return $this->project_reviewers() 
            ->where('status', true)  // find the first active reviewer
            ->orderBy('order')
            ->first(); // Return the User model of the reviewer

        // Iterate over all project documents ordered by id (i.e. oldest first)
        // foreach ($this->project_documents()->orderBy('id')->get() as $document) {
        //     $reviewer = $document->project_reviewers()
        //         ->where('status', true)
        //         ->where('review_status', '!=', 'approved')
        //         ->orderBy('order')
        //         ->first();

        //     if ($reviewer) {
        //         return $reviewer;
        //     }
        // }


    }


    public function isLastRemainingReviewer($reviewerId)
    {
        // Get all reviewers ordered by their sequence or ID
        $reviewers = $this->project_reviewers()->orderBy('id')->get();

        // Find the last reviewer (highest order or last ID)
        $lastReviewer = $reviewers->last();

        if (! $lastReviewer) {
            return false; // No reviewers assigned
        }

        // Check if this reviewer is the last reviewer
        $isLast = $lastReviewer->user_id == $reviewerId;

        // Count reviewers who haven't reviewed or approved yet
        $remaining = $reviewers->filter(function ($r) {
            return !in_array($r->review_status, ['reviewed', 'approved']);
        });

        // If this reviewer is the last AND the only remaining unreviewed reviewer
        return $isLast && $remaining->count() === 1 && $remaining->first()->user_id == $reviewerId;
    }




    static public function resetCurrentProjectDocumentReviewersByDocument($project_document_id){


        $project_document = ProjectDocument::find($project_document_id);

        if(empty($project_document)){
            return;
        }

        $project_document->project_reviewers()->update(['status' => false]);

        // Activate the first eligible reviewer  
        $firstReviewer = ProjectReviewer::where('project_document_id',$project_document->id)
            // ->where('review_status', '!=', 'approved') 
            ->where(function ($q){
                $q->where('review_status','!=','approved')  
                    ->where('review_status','!=','reviewed');
            })

            ->orderBy('order')
            ->first();

        if ($firstReviewer) {
            $firstReviewer->status = true;
            $firstReviewer->save();
            
        } 


    }


    // Scope based queries for the project list 
    // when using this, remove the word scope

    public function scopeOwnedBy(Builder $query, $userId)
    {
        return $query->where('created_by', $userId);
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

    public function scopeNotDraft(Builder $query)
    {
        return $query->where('status', '!=', 'draft');
    }


    public function getCurrentReviewerByProjectDocument()
    {
        return $this->project_reviewers()->where('status', true)  // find the first active reviewer 
            ->orderBy('order')
            ->first(); // Return the User model of the reviewer 
    }


     public static function generateProjectNumber($shpo_tracking = null)
    {
        if ($shpo_tracking) {
            return 'RC#-' . $shpo_tracking;
        }
    
        $lastProject = self::latest()->first();
        $nextNumber = $lastProject ? $lastProject->id + 2 : 1;
        
        return 'PRJ-' . str_pad($nextNumber, 9, '0', STR_PAD_LEFT); // PRJ-000001
    }



    // app/Models/ProjectDocument.php

    public function scopeApplySortingUsingWhereHas(Builder $q, string $sortBy): Builder
    {
        switch ($sortBy) {
            case "Document Name A - Z":
                return $q->withAggregate('document_type as document_type_name', 'name')
                        ->orderBy('document_type_name', 'ASC');

            case "Document Name Z - A":
                return $q->withAggregate('document_type as document_type_name', 'name')
                        ->orderBy('document_type_name', 'DESC');

            case "Project Name A - Z":
                return $q->withAggregate('project as project_name', 'name')
                        ->orderBy('project_name', 'ASC');

            case "Project Name Z - A":
                return $q->withAggregate('project as project_name', 'name')
                        ->orderBy('project_name', 'DESC');

            case "Description A - Z":
                return $q->withAggregate('project as project_description', 'description')
                        ->orderBy('project_description', 'ASC');

            case "Description Z - A":
                return $q->withAggregate('project as project_description', 'description')
                        ->orderBy('project_description', 'DESC');

            case "Federal Agency A - Z":
                return $q->withAggregate('project as project_agency', 'federal_agency')
                        ->orderBy('project_agency', 'ASC');

            case "Federal Agency Z - A":
                return $q->withAggregate('project as project_agency', 'federal_agency')
                        ->orderBy('project_agency', 'DESC');

            case "Nearest Submission Due Date":
                return $q->withAggregate('project as project_submitter_due', 'submitter_due_date')
                        ->withCount([
                            'project_reviewers as pending_submission_count' => fn($r) => $r->where('status', true)->where('review_status', 'rejected'),
                            'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
                        ])
                        ->orderByDesc('pending_submission_count')
                        ->orderByDesc('not_fully_approved_count')
                        ->orderBy('project_submitter_due', 'ASC');

            case "Farthest Submission Due Date":
                return $q->withAggregate('project as project_submitter_due', 'submitter_due_date')
                        ->withCount([
                            'project_reviewers as pending_submission_count' => fn($r) => $r->where('status', true)->where('review_status', 'rejected'),
                            'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
                        ])
                        ->orderByDesc('pending_submission_count')
                        ->orderByDesc('not_fully_approved_count')
                        ->orderBy('project_submitter_due', 'DESC');

            case "Nearest Reviewer Due Date":
                return $q->withAggregate('project as project_reviewer_due', 'reviewer_due_date')
                        ->withCount([
                            'project_reviewers as pending_review_count' => fn($r) => $r->where('status', true)->where('review_status', 'pending'),
                            'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
                        ])
                        ->orderByDesc('pending_review_count')
                        ->orderByDesc('not_fully_approved_count')
                        ->orderBy('project_reviewer_due', 'ASC');

            case "Farthest Reviewer Due Date":
                return $q->withAggregate('project as project_reviewer_due', 'reviewer_due_date')
                        ->withCount([
                            'project_reviewers as pending_review_count' => fn($r) => $r->where('status', true)->where('review_status', 'pending'),
                            'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
                        ])
                        ->orderByDesc('pending_review_count')
                        ->orderByDesc('not_fully_approved_count')
                        ->orderBy('project_reviewer_due', 'DESC');

            // case "Latest Added":
            //     return $q->withAggregate('project as project_created_at', 'created_at')
            //             ->orderBy('project_created_at', 'DESC');

            // case "Oldest Added":
            //     return $q->withAggregate('project as project_created_at', 'created_at')
            //             ->orderBy('project_created_at', 'ASC');

            // case "Latest Updated":
            //     return $q->withAggregate('project as project_updated_at', 'updated_at')
            //             ->orderBy('project_updated_at', 'DESC');

            // case "Oldest Updated":
            //     return $q->withAggregate('project as project_updated_at', 'updated_at')
            //             ->orderBy('project_updated_at', 'ASC');

            default:
                if (request()->routeIs('project.pending_project_update')) {
                    return $q->withAggregate('project as project_submitter_due', 'submitter_due_date')
                            ->orderBy('project_submitter_due', 'ASC');
                } elseif (request()->routeIs('project.in_review')) {
                    return $q->withAggregate('project as project_reviewer_due', 'reviewer_due_date')
                            ->withCount([
                                'project_reviewers as pending_review_count' => fn($r) => $r->where('review_status', 'pending')
                            ])
                            ->orderByDesc('pending_review_count')
                            ->orderBy('project_reviewer_due', 'ASC');
                }
                return $q->withAggregate('project as project_updated_at', 'updated_at')
                        ->orderBy('project_updated_at', 'DESC');
        }
    }


    // app/Models/ProjectDocument.php

    public function scopeApplySearchUsingWhereHas(Builder $q, string $search, int $project_id = null, int $document_type_id = null): Builder
    {
        $q = $q->where(function ($q) use ($search) {
                $q->withAggregate('document_type as document_type_name', 'name')
                    ->where('document_type_name','LIKE','%'.$search.'%')
                    ->withAggregate('project as project_name', 'name')
                        ->orWhere('project_name','LIKE','%'.$search.'%'); 
                        ; 
            });
 
        if(!empty($project_id)){

            $q = $q->where('project_id',$project_id);

        }

        if(!empty($document_type_id)){

            $q = $q->where('document_type_id',$document_type_id);

        }



        return $q;
    }




    public function scopeApplySorting(Builder $q, string $sortBy): Builder
    {
        switch ($sortBy) {
             

            // case "Description A - Z":
            //     return $q->withAggregate('project as project_description', 'description')
            //             ->orderBy('project_description', 'ASC');

            // case "Description Z - A":
            //     return $q->withAggregate('project as project_description', 'description')
            //             ->orderBy('project_description', 'DESC');

            // case "Federal Agency A - Z":
            //     return $q->withAggregate('project as project_agency', 'federal_agency')
            //             ->orderBy('project_agency', 'ASC');

            // case "Federal Agency Z - A":
            //     return $q->withAggregate('project as project_agency', 'federal_agency')
            //             ->orderBy('project_agency', 'DESC');

            // case "Nearest Submission Due Date":
            //     return $q->withAggregate('project as project_submitter_due', 'submitter_due_date')
            //             ->withCount([
            //                 'project_reviewers as pending_submission_count' => fn($r) => $r->where('status', true)->where('review_status', 'rejected'),
            //                 'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
            //             ])
            //             ->orderByDesc('pending_submission_count')
            //             ->orderByDesc('not_fully_approved_count')
            //             ->orderBy('project_submitter_due', 'ASC');

            // case "Farthest Submission Due Date":
            //     return $q->withAggregate('project as project_submitter_due', 'submitter_due_date')
            //             ->withCount([
            //                 'project_reviewers as pending_submission_count' => fn($r) => $r->where('status', true)->where('review_status', 'rejected'),
            //                 'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
            //             ])
            //             ->orderByDesc('pending_submission_count')
            //             ->orderByDesc('not_fully_approved_count')
            //             ->orderBy('project_submitter_due', 'DESC');

            // case "Nearest Reviewer Due Date":
            //     return $q->withAggregate('project as project_reviewer_due', 'reviewer_due_date')
            //             ->withCount([
            //                 'project_reviewers as pending_review_count' => fn($r) => $r->where('status', true)->where('review_status', 'pending'),
            //                 'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
            //             ])
            //             ->orderByDesc('pending_review_count')
            //             ->orderByDesc('not_fully_approved_count')
            //             ->orderBy('project_reviewer_due', 'ASC');

            // case "Farthest Reviewer Due Date":
            //     return $q->withAggregate('project as project_reviewer_due', 'reviewer_due_date')
            //             ->withCount([
            //                 'project_reviewers as pending_review_count' => fn($r) => $r->where('status', true)->where('review_status', 'pending'),
            //                 'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
            //             ])
            //             ->orderByDesc('pending_review_count')
            //             ->orderByDesc('not_fully_approved_count')
            //             ->orderBy('project_reviewer_due', 'DESC');

            case "Latest Added":
                return $q ->orderBy('created_at', 'DESC');

            case "Oldest Added":
                return $q ->orderBy('created_at', 'ASC');

            case "Latest Updated":
                return $q ->orderBy('updated_at', 'DESC');

            case "Oldest Updated":
                return $q->orderBy('updated_at', 'ASC');

            default:
                if (request()->routeIs('project.pending_project_update')) {
                    return $q->withAggregate('project as project_submitter_due', 'submitter_due_date')
                            ->orderBy('project_submitter_due', 'ASC');
                } elseif (request()->routeIs('project.in_review')) {
                    return $q->withAggregate('project as project_reviewer_due', 'reviewer_due_date')
                            ->withCount([
                                'project_reviewers as pending_review_count' => fn($r) => $r->where('review_status', 'pending')
                            ])
                            ->orderByDesc('pending_review_count')
                            ->orderBy('project_reviewer_due', 'ASC');
                }
                // return $q->withAggregate('project as project_updated_at', 'updated_at')
                        // ->orderBy('project_updated_at', 'DESC');

                return $q->orderBy('created_at', 'DESC');

        }
    }
 
    // protected function applyRouteBasedFilters($query)
    public function scopeApplyRouteBasedFilters(Builder $q, string $route): Builder
    {

        // dd($this->route);

        $user = Auth::user();
        $userId = $user->id;
 
        switch ($route) {
            // case 'project.index':
            //     // Owned projects
            //     $query->ownedBy($userId);
            //     break;

            // case 'project.index.update-pending':
            //     // Owned, update pending, not draft
            //     $query->ownedBy($userId)
            //         ->pendingUpdate($query)
            //         ->notDraft($query);
            //     break;

            // case 'project.index.review-pending':
            //     // Owned, review pending, not draft
            //     $query->ownedBy($userId)
            //         ->inReview($query)
            //         ->notDraft($query);
            //     break;

            // case 'project.index.all':
            //     break;

            // case 'project.index.all.no-drafts':
            //     // All, no draft
            //     $query->notDraft($query);
            //     break;

            // case 'project.index.update-pending.all-linked':
            //     // User is reviewer; project is update pending and linked to user
            //     $query->pendingUpdate($query)
            //         ->notDraft($query)
            //         ->whereHas('project_reviewers', function ($q) use ($userId) {
            //             $q->where('user_id', $userId)->where('status', true);
            //         });
            //     break;

            // case 'project.index.review-pending.all-linked':
            //     // User is reviewer; project is review pending and linked to user
            //     $query->inReview($query)
            //         ->notDraft($query)
            //         ->whereHas('project_reviewers', function ($q) use ($userId) {
            //             $q->where('user_id', $userId)
            //                 ->where('status', true)
            //                 // ->where('review_status','pending')
            //                 ;
            //         });
            //     break;

            // case 'project.index.update-pending.all':
            //     // All update-pending projects
            //     $query->pendingUpdate($query)->notDraft($query);
            //     break;

            case 'project-document.index.review-pending':
                // All review-pending projects
 
                return $q
                    ->inReview($q)
                    ->notDraft($q)
                    ->whereHas('project_reviewers', function ($quer) use ($userId) {
                        $quer->where('user_id',$userId)
                            // ->where('status', true)
                            ->where('status', true)
                            ->where('review_status', 'pending')
                            // ->where('slot_type', 'person')
                             
                            ;
                    }); 
            case 'project-document.index.changes-requested':
                // All review-pending projects
 
                return $q
                    ->inReview($q)
                    ->notDraft($q)
                    ->whereHas('project_reviewers', function ($quer) use ($userId) {
                        $quer
                            // ->where('user_id',$userId)
                            // ->where('status', true)
                            // ->where('status', true)
                            ->where('review_status', 'changes_requested')
                            // ->where('slot_type', 'person')
                             
                            ;
                    }); 

            case 'project-document.index.open-review':
                // User is reviewer; project is review pending and linked to user
                // dd($q);
                return $q
                    ->inReview($q)
                    ->notDraft($q)
                    ->whereHas('project_reviewers', function ($quer) use ($userId) {
                        $quer->whereNull('user_id')
                            // ->where('status', true)
                            ->where('status', true)
                            ->where('review_status', 'pending')
                            ->where('slot_type', 'open') 
                            ;
                    }); 
            // case 'project.project_documents.open':
            //     // User is reviewer; project is review pending and linked to user
            //     $query->inReview($query)
            //         ->notDraft($query)
            //         ->whereHas('project_reviewers', function ($q) use ($userId) {
            //             $q->whereNull('user_id')
            //                 ->where('status', true)
            //                 ->where('review_status', 'pending')
            //                  ->where('slot_type', 'open')
            //                 // ->where('review_status','pending')
            //                 ;
            //         });
            //     break;


            default:
                // Default to owned
                return $q->ownedBy($userId); 
        } 
 
    }

    // function to filter the records based on the review_status
    public function scopeApplyReviewStatusBasedFilters(Builder $q, string $status){
        $user = Auth::user();
        $userId = $user->id;

        // dd($status);
 
        switch ($status) {
             

            case 'pending': 
                // pending 
                // submission is not allowed
                // review status is pending

                if($user->hasPermissionTo('system access user')){
                    return $q
                        ->where('allow_project_submission', false)
                        ->whereHas('project_reviewers', function ($query) use ($userId) {
                            $query->where('status', true)
                            ->where('review_status', 'pending');
                        });
                }elseif( $user->hasPermissionTo('system access admin') || $user->hasPermissionTo('system access reviewer') ){
                    return $q
                    ->where('allow_project_submission', false)
                    ->whereHas('project_reviewers', function ($query) use ($userId) {
                        $query->where('user_id',$userId) // if for reviewers and admin, ensure that they are the current reviewer
                        ->where('status', true)
                        ->where('review_status', 'pending');
                    });


                }
                
            case 'approved': 
                // approved
                // submission is not allowed
                // review status is rejected
                return $q
                    ->where('allow_project_submission', false)
                    ->whereHas('project_reviewers', function ($query) use ($userId) {
                        $query->where('status', true)
                        ->where('review_status', 'approved');
                    });

            case 'rejected':
                // rejected
                // submission is allowed
                // review status is rejected
                return $q
                    ->where('allow_project_submission', true)
                    ->whereHas('project_reviewers', function ($query) use ($userId) {
                        $query->where('status', true)
                        ->where('review_status', 'rejected');
                    });

            case 'open_review':
                // open_review
                // submisiosn is not allowed
                // review status is rejected

                // dd("Here");
                return $q
                    ->where('allow_project_submission', false)
                    ->whereHas('project_reviewers', function ($query) use ($userId) {
                        $query->where('status', true)
                        ->where('slot_type', 'open')
                        ->where('review_status', 'pending');
                    });

            case 'changes_requested':
                // changes_requested
                // submission is allowed
                // review status is changes requested 
                if($user->hasPermissionTo('system access user')){
                    return $q->where('created_by',$userId)
                        ->where('allow_project_submission', true)
                        ->whereHas('project_reviewers', function ($query) use ($userId) {
                            $query->where('status', true)
                            ->where('review_status', 'changes_requested');
                        }); 
                }elseif( $user->hasPermissionTo('system access admin') || $user->hasPermissionTo('system access reviewer') ){
                    
                    return $q 
                        ->where('allow_project_submission', true)
                        ->whereHas('project_reviewers', function ($query) use ($userId) {
                            $query->where('status', true)
                            ->where('review_status', 'changes_requested');
                        }); 


                }


                
           


            default:
                // Default to just pass it back

                if(Auth::user()->hasPermissionTo('system access user')){
                    return $q->ownedBy($userId);
                }else{
                    return $q;
                }

                 
        } 

    }

    public static function countBasedOnReviewStatus(string $review_status,$project_id = null){

        $query = ProjectDocument::query();  

        if(!empty($project_id)){
            // dd($project_id);
            $query = $query->where('project_id',$project_id);

        }

        $query = $query->applyReviewStatusBasedFilters($review_status);


        if(Auth::user()->hasPermissionTo('system access user')){
             $query = $query->where('created_by',Auth::user()->id);
            
        }

        return $query->count() ?? 0;

    }






    /**
     * Count attachments by file type group.
     * Usage: $projectDocument->attachmentTypeCounts()
     *
     * @return array
     */
    public function attachmentTypeCounts(): array
    {
        $attachments = $this->project_attachments; // assume already eager-loaded or will lazy-load

        $counts = [
            'all'    => 0,
            'pdf'    => 0,
            'images' => 0,
            'videos' => 0,
            'docs'   => 0,
            'other'  => 0,
        ];

        // Define extensions by type
        $images = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $videos = ['mp4', 'avi', 'mov', 'mkv', 'webm'];
        $docs   = ['doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'ppt', 'pptx'];

        foreach ($attachments as $file) {
            $counts['all']++;

            $ext = strtolower(pathinfo($file->attachment ?? '', PATHINFO_EXTENSION));

            if ($ext === 'pdf') {
                $counts['pdf']++;
            } elseif (in_array($ext, $images)) {
                $counts['images']++;
            } elseif (in_array($ext, $videos)) {
                $counts['videos']++;
            } elseif (in_array($ext, $docs)) {
                $counts['docs']++;
            } else {
                $counts['other']++;
            }
        }

        return $counts;
    }

    /**
     * Optional accessor for automatic $document->attachment_type_counts
     */
    public function getAttachmentTypeCountsAttribute(): array
    {
        return $this->attachmentTypeCounts();
    }



    /**
     * Check if this project document has any reviewers that are not approved yet.
     *
     * @return bool
     */
    public function hasUnapprovedReviewers(): bool
    {
        return $this->project_reviewers()
            ->where('review_status', '!=', 'approved')
            ->exists();
    } 


    /**
     * Are there reviewers after the current one that still aren't approved?
     */
    public function hasNextUnapprovedReviewerAfter(ProjectReviewer $current): bool
    {
        return $this->project_reviewers()
            ->where('order', '>', $current->order ?? 0)
            ->where('review_status', '!=', 'approved')
            ->where('review_status', '!=', 'reviewed')
            ->exists();
    }

    /**
     * Get the next reviewer in order that isn't approved yet.
     */
    public function nextUnapprovedReviewerAfter(?ProjectReviewer $current): ?ProjectReviewer
    {
        if (!$current) {
            return $this->project_reviewers()
                ->where('review_status', '!=', 'approved')
                ->where('review_status', '!=', 'reviewed')
                ->orderBy('order')
                ->first();
        }

        return $this->project_reviewers()
            ->where('order', '>', $current->order ?? 0)
            ->where('review_status', '!=', 'approved')
            ->where('review_status', '!=', 'reviewed')
            ->orderBy('order')
            ->first();
    }




    /**
     * Get the count of the project document based on the route
     */

     /**
     * Count projects based on route context
     *
     * @param string $route   Route name
     * @param string|null $status Optional project status filter
     * @return int
     */
    public static function countProjectDocuments($route = 'project.index', $status = null)
    {
        $userId = Auth::id();
        $query = ProjectDocument::query();  // for the project 

        switch ($route) {
 
            case 'project-document.index':
                // submitter
                // Owned project documents
                if(Auth::user()->hasPermissionTo('system access user')){
                    $query->where('created_by', $userId);

                    // dd("Here");
                }

                // dd( $query);
                $query->where('id','>',0);
                
                break;
 

            case 'project-document.index.changes-requested':
                // submitter
                // changes requested
                $query->where('created_by', $userId)
                    ->whereNotIn('status', ['approved', 'draft'])
                    ->where('allow_project_submission', true)
                    ->whereHas('project_reviewers', function ($q) use ($userId) {
                        $q->orWhere('review_status', 'rejected')
                        ->orWhere('review_status', 'changes_requested');
                    });
                break;

            case 'project.index.review-pending.all-linked':
                // Reviewer-linked projects with pending review
                $query->whereNotIn('status', ['approved', 'draft'])
                    // project submission is allowed to allow the submitter to resubmit the project document
                    ->where('allow_project_submission', true)
                    ->whereHas('project_reviewers', function ($q) use ($userId) {
                        $q->where('review_status', 'changes_requested') // changes is requested by reviewer
                        ->orWhere('review_status', 'rejected')   // changes is requested by reviewer
                        // ->where('review_status','pending')
                        ;
                    });
                break;

            case 'project.index.update-pending.all':
                // All projects pending update (not filtered by ownership or reviewers)
                $query->whereNotIn('status', ['approved', 'draft'])
                    ->where('allow_project_submission', true);
                break;

            case 'project.index.review-pending.all':
                // All projects pending review (not filtered by ownership or reviewers)
                $query->whereNotIn('status', ['approved', 'draft'])
                    ->where('allow_project_submission', false);
                break;
                
            
            case 'project.index.open-review': 
                // Projects with open review
                $query->whereNotIn('status', ['approved', 'draft','in_review'])
                    // ->where('allow_project_submission', false)
                    ->whereHas('project_reviewers', function ($q) use ($userId) {
                        $q->whereNull('user_id')
                        ->where('status', true)
                        // ->where('review_status','pending')
                        ;
                    }); 
                break; 


 


            default:
                // Fallback: owned projects
                $query->where('created_by', $userId);
                break;
        }

        // Optional status filter
        if (!empty($status)) {
            $query->where('status', $status);
        }
 
        return $query->count();
 

        
    }




    
    public function getLastReview()
    {
        return $this->project_reviews()
            ->whereIn('review_status', ['approved', 'reviewed']) // ✅ filter allowed statuses
            ->latest('created_at')                        // or 'reviewed_at' if preferred
            ->first();
    }





}
