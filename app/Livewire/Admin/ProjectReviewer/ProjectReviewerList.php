<?php

namespace App\Livewire\Admin\ProjectReviewer;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use App\Models\DocumentType;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Validation\Rule;
use App\Events\ReviewerListUpdated;
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

    protected $listeners = [
        'reviewerCreated' => '$refresh', 
        'reviewerUpdated' => '$refresh',
        'reviewerDeleted' => '$refresh',
        
    ];

    

    /** @var array<int,array{id:int,name:string}> 
     * The document types saved on the system
     * */ 
    public array $documentTypes = [];          // [{id, name}, ...]          
    public ?int $currentTypeId = null;         // active doc type tab

    /** @var array<int,array{id:int,name:string}> */
    public array $optionsByType = [];          // [docTypeId => [{id,name}, ...]]       // options by type 

    /** @var array<int,array<int>> */
    public array $selectedByType = [];         // [docTypeId => [userId, ...]]      // selected by type 

    /** @var array<int,array<int,array{id:int,name:string,order:int}>> */
    public array $assignedByType = [];         // [docTypeId => [[id,name,order], ...]]     // assigned by type 

    /** “Times to add” per type */
    /** @var array<int,int> */
    public array $repeatByType = [];        // repeat by type 

    /** Array of options”  */
    /** @var array<int,int> */
    public array $options = [];         // options [ actually this is the array that stores the user records that are options ]


    /** Project */
    /** @var \Illuminate\Database\Eloquent\Project */
    public Project $project;


    
    /** Project id */
    /** @var int */
    public int $project_id;

    public function mount( $id)
    {


        $this->project = Project::findOrFail($id);
        $this->project_id = $this->project->id;
        // dd($project);
        // Load document types
        // $this->documentTypes = DocumentType::orderBy('name')->get(['id','name'])->toArray();
        // $this->documentTypes = [
        //     ['id' => 1, 'name' => 'Archaeological Survey'],
        //     ['id' => 2, 'name' => 'Architectural Plans'],
        //     ['id' => 3, 'name' => 'Photos'],
        // ];


        // get only the document_type_id values from the relation
        $documentTypeIds = $this->project->project_documents
            ->pluck('document_type_id') 
            ->toArray();


        $this->documentTypes = DocumentType::whereIn('id', $documentTypeIds)
            ->orderBy('order','asc') // document types 
            // ->orderBy('order','asc')
            ->get(['id', 'name'])
            ->toArray();

        // dd($this->documentTypes);
        /**
         * 
            array:3 [▼ // app\Livewire\Admin\Reviewer\ReviewerList.php:72
                0 => array:2 [▼
                    "id" => 57
                    "name" => "Final Report"
                ]
                1 => array:2 [▼
                    "id" => 66
                    "name" => "HAAB/HAER"
                ]
                2 => array:2 [▼
                    "id" => 67
                    "name" => "Inadvertant"
                ]
            ]
         */


        // Set default current type
        $this->currentTypeId = $this->documentTypes[0]['id'] ?? null;       // the first document on the array will be the default current document 

        // Pull users + their roles in one go (no N+1)
        $users = User::select('id','name')
            ->with('roles:id,name') // eager load roles
            ->orderBy('name')
            ->get('id');

        // dd($users);

        // dd($users);

        $perms = [
            // 'system access admin', 
            'system access reviewer'
        ]; // set the permissions that the user must have atleast one of it 

        $this->options = User::select('id','name')
            ->with('roles:id,name')
            ->permission($perms) // <- Spatie scope: matches direct perms OR via roles
            ->orderBy('name')
            ->get()
            ->map(fn($u) => [
                'id'    => (int) $u->id,
                'name'  => $u->name,
                'roles' => $u->getRoleNames()->toArray(), // ['admin','reviewer'] or []
            ])
            ->toArray();

        // dd($this->options);


        // Load available reviewer options per doc type (customize per your rules)
        // Example uses the same pool for all, but you can vary per type.
        // Example options (replace with DB)
        
         
        foreach ($this->documentTypes as $t) {
            $typeId = (int) $t['id']; 

            // enable this if you want to change the options [users] and make it different in every document type 
            // Map to the structure Alpine expects
            /** 
            $this->optionsByType[$typeId]  = $users->map(function ($u) {

                // dd($u->getRoleNames()->toArray());

                return [
                    'id'    => $u->id,
                    'name'  => $u->name,
                    'roles' => $u->getRoleNames()->toArray(), // ['admin','reviewer'] or []
                ];
            })->toArray();
            */
  

            $this->selectedByType[$typeId] = [];
            $this->assignedByType[$typeId] = [];
            $this->repeatByType[$typeId] = 1; // default

            // Load previously added reviewers
            $assigned = $this->assignedByType[$typeId] ?? [];
            $order    = empty($assigned) ? 0 : max(array_column($assigned, 'order'));



            $project_document = ProjectDocument::where('project_id',$this->project->id)
                ->where('document_type_id',$typeId )
                ->first();

            $project_reviewers = ProjectReviewer::where('project_id',$this->project->id)
                ->where('project_document_id', $project_document->id)
                ->get();

            $documentType = DocumentType::with(['reviewers.user.roles'])->find($typeId);

            if ($documentType && $project_reviewers) {
                foreach ($project_reviewers as $reviewer) {

                    if (!empty($reviewer->user_id) && $reviewer->user) {
                        // PERSON (claimed / specific user)
                        $user = $reviewer->user;

                        $assigned[] = [
                            'row_uid'   => (string) Str::uuid(),
                            'slot_type' => 'person',                         // explicit person row
                            'user_id'   => (int) $user->id,
                            'project_reviewer_id' => $reviewer->id,
                            'name'      => $user->name ?: null,
                            // ✅ Spatie role names (array of strings)
                            'roles'     => $user->getRoleNames()->values()->all(),
                            'order'     => ++$order,

                            'status'     => $reviewer->status,
                            'review_status'     => $reviewer->review_status,

                        ];
                    } else {
                        // OPEN SLOT (no user yet)
                        $assigned[] = [
                            'row_uid'   => (string) Str::uuid(),
                            'slot_type' => 'open',                           // explicit open slot
                            'slot_role'      => $reviewer->slot_role ?? 'reviewer',    // optional: intended role for slot
                            'user_id'   => null,
                            'project_reviewer_id' => $reviewer->id,
                            'name'      => null,
                            // Open slot has no roles until claimed
                            'roles'     => [],
                            'order'     => ++$order,

                            'status'     => $reviewer->status,
                            'review_status'     => $reviewer->review_status,

                        ];
                    }
                }
            }

            $this->assignedByType[$typeId] = $assigned;




        }
         

        // If editing existing data, prefill $this->assignedByType[...] with rows including row_uid + order.
    }

    /** Add selected users N times (N = repeatByType[currentType]) */
    public function addSelected(): void
    {
        $typeId = (int) ($this->currentTypeId ?? 0);        // get the current typeId
        if (!$typeId) return;       // if it is not set, return nothing

        $selected = collect($this->selectedByType[$typeId] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->values();

        // dd($selected);
        /**
            Illuminate\Support\Collection {#1806 ▼ // app\Livewire\Admin\Reviewer\ReviewerList.php:164
                #items: array:2 [▼
                    0 => 71
                    1 => 32
                ]
                #escapeWhenCastingToString: false
            }
         */


        if ($selected->isEmpty()) return;       // check if there is nothing selected, to stop the function and return nothing if empty

        $repeat = max(1, (int) ($this->repeatByType[$typeId] ?? 1));        // sets how many times it will be repeatedly added to the assigned list 
        $assigned = $this->assignedByType[$typeId] ?? [];       // list of assigned based on the selected current typeID
        $order = empty($assigned) ? 0 : max(array_column($assigned, 'order'));      // order in which the option [user] selected will be added to the assigned array 

        // $options = collect($this->optionsByType[$typeId] ?? []);
        $options = collect($this->options ?? []);
        // dd($this->options);

        // dd($selected);
        /**
            Illuminate\Support\Collection {#1806 ▼ // app\Livewire\Admin\Reviewer\ReviewerList.php:185
                #items: array:2 [▼
                    0 => 71
                    1 => 10
                ]
                #escapeWhenCastingToString: false
            } 
         */

        foreach ($selected as $userId) {
            $user = $options->firstWhere('id', $userId);
            if (!$user) continue;

            for ($i = 0; $i < $repeat; $i++) {
                $assigned[] = [
                    'row_uid' => (string) Str::uuid(),   // unique slot id 

                    'slot_type' => 'person',              // explicit: this row is a person (not open slot)
                    'slot_role'      => null,    // optional: intended role for slot_type = open
                    'user_id'   => (int) $user['id'],     // ✅ use user_id to match the table’s Alpine lookup
                    'project_reviewer_id' => null,
                    'name'      => $user['name'],
                    // optional convenience copy (ok to remove and rely on rolesFor(user_id))
                    'roles'     => $user['roles'] ?? [],
                    'order'     => ++$order,

                    'status'     => false,
                    'review_status'     => 'pending',

                ];
            }
        }

        $this->assignedByType[$typeId] = $assigned;     // add to the master list of assigned options [users] per type 
        $this->selectedByType[$typeId] = [];       // clear chips
        $this->repeatByType[$typeId]   = 1;        // reset to 1 (optional)


        // dd($assigned);
    }

    /** Remove a single occurrence by row UID */
    public function remove(string $rowUid, int $typeId): void       // gets the rowUid and the type 
    {
        $typeId = (int) $typeId; // make sure typeId is an integer


        // Find the row in memory for this type
        $row = collect($this->assignedByType[$typeId] ?? [])
            ->firstWhere('row_uid', $rowUid);

        // If not found, just bail (or notify)
        if (!$row) {
            $this->dispatch('toast', type: 'error', message: 'Row not found.');
            return;
        }

        $projectReviewerId = data_get($row, 'project_reviewer_id'); // might be null for brand-new rows

        if ($projectReviewerId) {
            // 1) Make sure it exists in DB
            $project_reviewer = ProjectReviewer::find($projectReviewerId);

            if ($project_reviewer ) {
                // 2) Block removal if there are dependent reviews/links
                if (!empty($project_reviewer) && count($project_reviewer->reviews) > 0 ) {
                    $this->dispatch('toast', type: 'error', message: 'This reviewer cannot be removed because there are existing reviews linked to it.');
                    return;
                }

                // 3) Safe to delete persisted row (optional — uncomment if you want to delete DB row)
                // ProjectReviewer::whereKey($projectReviewerId)->delete();
            }
        }
    
        // 1. Look at the assigned list for this typeId
        // 2. Keep only the rows where row_uid is NOT equal to the one we want to remove
        // 3. Reindex the array so keys are 0,1,2... again
        $this->assignedByType[$typeId] = array_values(array_filter(
            $this->assignedByType[$typeId] ?? [],
            fn ($r) => $r['row_uid'] !== $rowUid
        ));

        // Recalculate or normalize the "order" field after removal
        $this->reindexOrder($typeId);
    }

    /** Reorder rows for a given type by array of row_uids */
    public function reorder(int $typeId, array $rowUids): void
    {
        $typeId = (int) $typeId;
        $rowUids = array_values(array_unique(array_map('strval', $rowUids)));

        $map = collect($this->assignedByType[$typeId] ?? [])->keyBy('row_uid');
        $ordered = [];
        $i = 1;

        foreach ($rowUids as $uid) {
            if ($map->has($uid)) {
                $row = $map[$uid];
                $row['order'] = $i++;
                $ordered[] = $row;
            }
        }

        // keep any rows not present in $rowUids at the end (safety)
        foreach ($map as $uid => $row) {
            if (!in_array($uid, $rowUids, true)) {
                $row['order'] = $i++;
                $ordered[] = $row;
            }
        }

        $this->assignedByType[$typeId] = $ordered;
    }
 

    protected function reindexOrder(int $typeId): void
    {
        $order = 1;
        foreach ($this->assignedByType[$typeId] as &$row) {
            $row['order'] = $order++;
        }
        unset($row);
    }




    public array $openRoleByType = [];    // [$typeId => 'admin'|'reviewer']
    public array $openRepeatByType = [];  // [$typeId => 1..20]

    public function addOpenSlots($custom_role = "reviewer"): void
    {
        $typeId = (int) ($this->currentTypeId ?? 0);
        if (!$typeId) return;

        if(!empty($custom_role)){
            $role   = $custom_role;
        }else{
            $role   = $this->openRoleByType[$typeId]  ?? 'reviewer'; 
        } 

        $repeat = max(1, (int) ($this->openRepeatByType[$typeId] ?? 1));

        $assigned = $this->assignedByType[$typeId] ?? [];
        $order = empty($assigned) ? 0 : max(array_column($assigned, 'order'));

        for ($i = 0; $i < $repeat; $i++) {
            $assigned[] = [
                'row_uid'    => (string) Str::uuid(),
                'slot_type'  => 'open',
                'slot_role'       => $role,
                'user_id'    => null,
                'project_reviewer_id' => null,
                // 'claimed_at' => null,
                'order'      => ++$order,

                'status'     => false,
                'review_status'     => 'pending',


            ];
        }





        $this->assignedByType[$typeId] = $assigned;
        // optional reset
        $this->openRepeatByType[$typeId] = 1;
    }


    
 
    public function save()
    {

        dd($this->assignedByType);
        $project = Project::where('id',$this->project_id)->first();

        if(!empty($this->assignedByType)){
            foreach($this->assignedByType as $document_type_id => $assigned){
                 
 
                $project_document = ProjectDocument::where('project_id',$this->project_id )
                    ->where('document_type_id',$document_type_id )
                    ->first();

                $project_reviewers = $project_document->project_reviewers()
                        ->where('review_status','!=','approved')
                        ->get();
                    foreach($project_reviewers as $reviewer){
                        $review_count = Review::returnReviewCount($reviewer->id);
                        if($review_count > 0)
                        {
                            continue;
                        }

                        $reviewer->delete();
                    }


                // insert the new assigned reviewers
                if(!empty($assigned) && empty($row['project_reviewer_id'])){
                    foreach($assigned as $row){ 

 

                        // dd( $row['slot_role']);
                        ProjectReviewer::create([
                            
                            'order' => $row['order'],
                            'status' => false,
                            'project_id' => $this->project_id ,

                            'user_id' => $row['user_id'] ?? null,

                            'slot_type' => $row['slot_type'] ?? 'person' ,              // explicit: this row is a person (not open slot)
                            'slot_role'      => $row['slot_role'] ?? null,    // optional: intended role for slot

                            'project_document_id' => $project_document->id,

                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                            'document_type_id' => $document_type_id,
                            'reviewer_type' => 'document', // initial, document, final
                            'review_status' => 'pending',
                             
           
                        ]); 
                    } 


                    // notify creator, project reviewers and project subscribers 
                    ProjectHelper::notifyRevs_Subs_on_RevUpd($project, $project_document->id, 'document');

                    
                    // reset all reviewers on the project document type 
                    Project::resetCurrentProjectDocumentReviewersByDocument($document_type_id,$project->id);

                }


                // send a project review request to the current reviewer
                $reviewer = Project::getCurrentReviewerByProjectDocument($project_document->id);
                    
                if($reviewer->slot_type == "person"){// if the current reviewer is a person
                    $reviewer_user = User::find($reviewer->user_id); 
                    ProjectHelper::sendForReviewersProjectReviewNotification($reviewer_user,$project, $reviewer);

                }else if($reviewer->slot_type == "open"){
 
                    ProjectHelper::sendForReviewersOpenProjectReviewNotification($project, $reviewer);
                    

                }
                
                



            }

 
        }

       
        

        


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

 



        Alert::success('Success', 'Project reviewer list saved successfully');
        return redirect()->route('project.document.reviewer.index', [
            'project' => $this->project->id, 
            'project_document' => $project_document->id,
        ]);


    }


    public function render()
    {
        
       
        return view('livewire.admin.project-reviewer.project-reviewer-list',[
            
        ]);
    }
}
