<?php

namespace App\Livewire\Admin\ProjectReviewer;

use App\Events\ProjectReviewer\ProjectReviewerLogEvent;
use App\Events\ReviewerListUpdated;
use App\Helpers\ActivityLogHelpers\ProjectDocumentLogHelper;
use App\Helpers\ProjectDocumentHelpers;
use App\Helpers\ProjectHelper;
use App\Helpers\ProjectReviewerHelpers;
use App\Helpers\SystemNotificationHelpers\ProjectDocumentNotificationHelper;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use App\Models\Review;
use App\Models\Reviewer;
use App\Models\User;
use App\Notifications\ProjectReviewerUpdatedNotification;
use App\Notifications\ProjectReviewerUpdatedNotificationDB;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectReviewerList extends Component
{


    use WithFileUploads;
    use WithPagination;

    // protected $listeners = [
    //     'reviewerCreated' => '$refresh', 
    //     'reviewerUpdated' => '$refresh',
    //     'reviewerDeleted' => '$refresh',
        
    // ];

    protected function getListeners(): array
    {
        $listeners = $this->listeners;

        if (!empty($this->project_id)) {
            $listeners["projectReviewerEvent.{$this->project_id}"] = 'loadData';
        }

        if (!empty($this->project_document_id)) {
            $listeners["projectDocumentReviewerEvent.{$this->project_document_id}"] = 'loadData';
        }

        return $listeners;
    }


    public $attachments = []; // Initialize with one phone field 
    public string $page_mode = "project"; // can be project or document 
    // project means only project reviewer
    // document means project document reviewers 
    

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
    public int $project_document_id;

    public $period_unit = "day";
    public $period_value = 0;

    public array $user_options = [];
    public array $user_admin_options = [];


    public function mount( $project_id, $project_document_id = 0)
    {

       

        $this->project_id = $project_id;
        $this->project_document_id = $project_document_id;

       

        



        $this->loadData();

 
        // dd($this->assignedByType);



    }


    public function loadData(){


        $this->project = Project::findOrFail($this->project_id);
        $this->project_id = $this->project->id;

        if(!empty( $this->project_document_id)){
            $this->project_document = ProjectDocument::findOrFail($this->project_document_id);
            $this->project_document_id = $this->project_document->id;
        }   
        



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
        $this->currentTypeId = $this->documentTypes[0]['id'] ?? 0;       // the first document on the array will be the default current document 

        if(!empty(  $this->project_document_id)){
            // Set default current type
            $this->currentTypeId = $this->project_document->document_type_id ?? 0;       // the first document on the array will be the default current document 
        }

        if($this->currentTypeId !== 0){
            $this->page_mode = "document";
        }


        $perms = [
            'system access admin', 
            'system access reviewer'
        ]; // set the permissions that the user must have atleast one of it 
        
 
        // get user options for the select 
        $this->user_options = User::with('roles:id,name')
            ->permission($perms) // <- Spatie scope: matches direct perms OR via roles
            ->get()
            ->mapWithKeys(function ($user) {

                // Get roles as string, default to "No role"
                $roles = $user->roles->pluck('name')->join(', ');
                $roles = $roles ?: 'No role';

                // Build label
                $label = "{$user->name} ({$roles})";

                return [$user->id => $label];
            })
            ->toArray();


        // get user (only admin) options for the select 
        $this->user_admin_options = User::with('roles:id,name')
            ->permission(['system access admin']) // <- Spatie scope: matches direct perms OR via roles
            ->get()
            ->mapWithKeys(function ($user) {

                // Get roles as string, default to "No role"
                $roles = $user->roles->pluck('name')->join(', ');
                $roles = $roles ?: 'No role';

                // Build label
                $label = "{$user->name} ({$roles})";

                return [$user->id => $label];
            })
            ->toArray();




        // Pull users + their roles in one go (no N+1)
        $users = User::select('id','name')
            ->with('roles:id,name') // eager load roles
            ->orderBy('name')
            ->get('id');

        // dd($users);

        // dd($users);

        $perms = [
            'system access admin', 
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
                ->orderBy('order','ASC')
                ->get();

            $documentType = DocumentType::with(['reviewers.user.roles'])->find($typeId);

            if ( $project_document && $documentType && $project_reviewers) {
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

                            'period_value' => $reviewer->period_value ?? 1,
                            'period_unit'  => $reviewer->period_unit ?? 'day',

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

                            'period_value' => $reviewer->period_value ?? 1,
                            'period_unit'  => $reviewer->period_unit ?? 'day',

                        ];
                    }
                }
            }

            $this->assignedByType[$typeId] = $assigned;




        }
         

        // If editing existing data, prefill $this->assignedByType[...] with rows including row_uid + order.







        // this is for typeId 0 || for project reviewers without connected project document 

        $typeId = 0;

        $this->selectedByType[$typeId] = [];
        $this->assignedByType[$typeId] = [];
        $this->repeatByType[$typeId] = 1; // default

        // Load previously added reviewers
        $assigned = $this->assignedByType[$typeId] ?? [];
        $order    = empty($assigned) ? 0 : max(array_column($assigned, 'order'));



        // $project_document = ProjectDocument::where('project_id',$this->project->id)
        //     ->where('document_type_id',$typeId )
        //     ->first();

        $project_reviewers = ProjectReviewer::where('project_id',$this->project->id)
            // ->where('project_document_id', $project_document->id)
            ->whereNull('project_document_id')
            ->get();

        // dd($project_reviewers);

        // $documentType = DocumentType::with(['reviewers.user.roles'])->find($typeId);

        if (
            // $documentType && 
        
        $project_reviewers) {
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

                        'period_value' => $reviewer->period_value ?? 1,
                        'period_unit'  => $reviewer->period_unit ?? 'day',

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

                        'period_value' => $reviewer->period_value ?? 1,
                        'period_unit'  => $reviewer->period_unit ?? 'day',

                    ];
                }
            }
        }

        $this->assignedByType[$typeId] = $assigned;

            
        // ./ this is for typeId 0 || for project reviewers without connected project document 


    }


    // public function updatedCurrentTypeId(){
    //     dd($this->currentTypeId);

    // }

    public function testLoadData(){

        $this->project = Project::findOrFail($this->project_id);
        $this->project_id = $this->project->id;

        if(!empty( $this->project_document_id)){
            $this->project_document = ProjectDocument::findOrFail($this->project_document_id);
            $this->project_document_id = $this->project_document->id;
        }   


        $this->assignedByType = [];
        // dd($this->)
        dd("Here");
        

    }



    /** Add selected users N times (N = repeatByType[currentType]) */
    public function addSelected(): void
    {
        $this->period_unit = "day";
        $this->validate([
            'selectedByType' => 'required|array|min:1',
            'period_unit'    => 'required',
            'period_value' => 'required|integer|min:1',
        ],[
            'period_unit.required'  => 'Please select a unit',
            'period_value.required' => 'Please enter a period',
        ]);

        if($this->page_mode == "document"){
            $typeId = (int) ($this->currentTypeId ?? 0);        // get the current typeId
            if (!$typeId) return;       // if it is not set, return nothing     
        }else{

            $typeId = 0; // 0 is dedicated for the project RC reviewers 
        }
       

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

        // foreach ($selected as $userId) {
        //     $user = $options->firstWhere('id', $userId);
        //     if (!$user) continue;

        //     for ($i = 0; $i < $repeat; $i++) {
        //         $assigned[] = [
        //             'row_uid' => (string) Str::uuid(),   // unique slot id 

        //             'slot_type' => 'person',              // explicit: this row is a person (not open slot)
        //             'slot_role'      => null,    // optional: intended role for slot_type = open
        //             'user_id'   => (int) $user['id'],     // ✅ use user_id to match the table’s Alpine lookup
        //             'project_reviewer_id' => null,
        //             'name'      => $user['name'],
        //             // optional convenience copy (ok to remove and rely on rolesFor(user_id))
        //             'roles'     => $user['roles'] ?? [],
        //             'order'     => ++$order,

        //             'status'     => false,
        //             'review_status'     => 'pending',

        //             'period_value' => $this->period_value,
        //             'period_unit'  => $this->period_unit,

        //         ];
        //     }
        // }



        // Collect all new rows here first
        $newRows = [];

        foreach ($selected as $userId) {
            $user = $options->firstWhere('id', $userId);
            if (!$user) continue;

            for ($i = 0; $i < $repeat; $i++) {
                $newRows[] = [
                    'row_uid'      => (string) Str::uuid(),   // unique slot id

                    'slot_type'    => 'person',              // explicit: this row is a person (not open slot)
                    'slot_role'    => null,
                    'user_id'      => (int) $user['id'],
                    'project_reviewer_id' => null,
                    'name'         => $user['name'],
                    'roles'        => $user['roles'] ?? [],
                    'order'        => ++$order,

                    'status'     => false,
                    'review_status'     => 'pending',
                    
                    'period_value' => $this->period_value,
                    'period_unit'  => $this->period_unit,
                ];
            }
        }

        // If nothing valid was built, stop
        if (empty($newRows)) {
            return;
        }

        /**
         * Insert as SECOND TO LAST if there are already 2+ items
         * - If 0 or 1 item: just append
         * - If 2+ items: insert before last
         */
        if (count($assigned) <= 1) {
            // Nothing to protect at the end → normal append
            $assigned = array_merge($assigned, $newRows);
        } else {
            // Remove the last row temporarily
            $lastRow  = array_pop($assigned);

            // Append new rows
            $assigned = array_merge($assigned, $newRows);

            // Put last row back
            $assigned[] = $lastRow;
        }




        $this->assignedByType[$typeId] = $assigned;     // add to the master list of assigned options [users] per type 
        $this->selectedByType[$typeId] = [];       // clear chips
        $this->repeatByType[$typeId]   = 1;        // reset to 1 (optional)

        $this->period_unit             = null;
        $this->period_value            = 0;

        // Recalculate or normalize the "order" field after removal
        $this->reindexOrder($typeId);



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


    // updating reviewer record
     
    // @this.update({{ $typeId }}, targetRowId,period_value,period_unit);
    public function update_row(int $typeId, string $rowUid, int $period_value, string $period_unit, $user_id = null)
    {
        $typeId = (int) $typeId;

        // dd($typeId);

        // Get original rows for this type
        $rows = $this->assignedByType[$typeId] ?? [];

        // Find the index of the row we are updating
        $rowIndex = collect($rows)->search(function ($row) use ($rowUid) {
            return isset($row['row_uid']) && $row['row_uid'] == $rowUid;
        });

        // Determine if this is the "first slot" and is open
        $isFirstSlotAndOpen = false;
        if ($rowIndex !== false && $rowIndex === 0 && isset($rows[0]['slot_type']) && $rows[0]['slot_type'] === 'open') {
            $isFirstSlotAndOpen = true;
        }

        // Rebuild rows for this type (your original logic)
        $map     = collect($rows)->keyBy('row_uid');
        $updated = [];

        foreach ($map as $uid => $row) {
            if ($map->has($uid) && $row['row_uid'] == $rowUid) {
                $row = $map[$rowUid];
                $row['period_value'] = $period_value;
                $row['period_unit']  = $period_unit;

                if (!empty($user_id)) {
                    $user           = User::find($user_id);
                    if ($user) {
                        $row['name']   = $user->name;
                        $row['roles']  = $user->getRoleNames()->values()->all();
                        $row['user_id'] = $user_id;
                    }
                }

                $updated[] = $row;
            } else {
                $updated[] = $row;
            }
        }

        // Save back for this type
        $this->assignedByType[$typeId] = $updated;

        /**
         * 🔁 Extra logic:
         * If the updated row is the FIRST slot and slot_type = "open",
         * update the FIRST open admin slot in EACH type
         * to use the same period_unit (and optionally period_value).
         */
        if ($isFirstSlotAndOpen) {

            // dd("Here");

            foreach ($this->assignedByType as $tId => $typeRows) {
                if (empty($typeRows)) {
                    continue;
                }

                // Find FIRST open admin slot in this type
                foreach ($typeRows as $idx => $slot) {
                    $slotType = $slot['slot_type'] ?? null;
                    $slotRole = $slot['slot_role'] ?? null;

                    // adjust 'admin' if your admin slot_role uses a different label
                    if ($slotType === 'open' && $slotRole === 'admin') {
                        $this->assignedByType[$tId][$idx]['period_value']  = $period_value;
                        // If you also want to sync value, uncomment:
                        // $this->assignedByType[$tId][$idx]['period_value'] = $period_value;
                        break; // only first open admin per type
                    }
                }
            }
        }


    }
 
    public function save()
    {   
 
        $this->saveReviewerList();

 
        // $firstProjectDocument = $project->project_documents->first();

        // $project_document_id = $firstProjectDocument->id ?? null;


        // Alert::success('Success', 'Project reviewer list saved successfully');
        // return redirect()->route('project.document.reviewer.index', [
        //     'project' => $this->project->id, 
        //     'project_document' => $project_document_id,
        // ]);

        $message = 'Project reviewer list saved successfully';
        if(!empty($this->project_document_id)){

            // Alert::success('Success','Project review submitted successfully');
            return redirect()->route('project.document.reviewer.index',[
                // 'project' => $this->project->id,
                'project_document' => $this->project_document_id,
            
            ])
            ->with('alert.success',$message)
            ;

        }

        // Alert::success('Success', 'Project reviewer list saved successfully');
        return redirect()->route('project.reviewer.index', [
            'project' => $this->project->id, 
            // 'project_document' => $project_document_id,
        ]) 
        ->with('alert.success',$message);


    }



    public function saveReviewerList(){
        // dd($this->assignedByType);
        $project = Project::where('id',$this->project_id)->first();

        // dd($this->project_document_id);

        $detectChanges = [];


        if(!empty($this->assignedByType)  ){
            foreach($this->assignedByType as $document_type_id => $assigned){

                // dd($assigned);

                 
                if( $document_type_id !== 0 ){
                    
                    $project_document = ProjectDocument::where('project_id',$this->project_id )
                        ->where('document_type_id',$document_type_id )
                        ->first();

                    // dd($project_document->document_type->name);

                    // $project_reviewers = $project_document->project_reviewers()
                    //         ->where('review_status','!=','approved')
                    //         ->where('review_status','!=','reviewed')
                    //         ->get();



                        // foreach($project_reviewers as $reviewer){
                        //     $review_count = Review::returnReviewCount($reviewer->id);
                        //     if($review_count > 0)
                        //     {
                        //         continue;
                        //     }

                        //     $reviewer->delete();
                        // }

                    // get the reviewer ids that are saved
                    $savedProjectReviewerIds = [];

                    $message = "Project reviewer updated";

                    // insert the new assigned reviewers
                    if(!empty($assigned)  ){

                        $changed = false;

                        foreach($assigned as $row){ 
                            // dd($row['project_reviewer_id']);

                            // dd($row['order']);

                            if(empty($row['project_reviewer_id'])){ 

                                // dd( $row['slot_role']);
                                $project_reviewer = ProjectReviewer::create([
                                    
                                    'order' => $row['order'],
                                    'status' => false,
                                    'project_id' => $this->project_id ,

                                    'user_id' => $row['user_id'] ?? null,

                                    'slot_type' => $row['slot_type'] ?? 'person' ,              // explicit: this row is a person (not open slot)
                                    'slot_role'      => $row['slot_role'] ?? null,    // optional: intended role for slot

                                    'period_value' => $row['period_value']   ?? 1,
                                    'period_unit'  => $row['period_unit'] ?? 'day',


                                    'project_document_id' => $project_document->id,

                                    'created_by' => Auth::user()->id,
                                    'updated_by' => Auth::user()->id,
                                    'document_type_id' => $document_type_id,
                                    'reviewer_type' => 'document', // initial, document, final
                                    'review_status' => 'pending',
                                    
                
                                ]); 


                                $savedProjectReviewerIds[] = $project_reviewer->id;

                                $changed = true;
 
 
                            }else{


                                $project_reviewer = ProjectReviewer::find($row['project_reviewer_id']);

                                $original = [
                                    'order'        => $project_reviewer->order,
                                    'user_id'      => $project_reviewer->user_id,
                                    'status'       => $project_reviewer->status,
                                    'period_value' => $project_reviewer->period_value,
                                    'period_unit'  => $project_reviewer->period_unit,
                                ];


                                // ProjectReviewer::where('id', $row['project_reviewer_id'])
                                // ->update([
                                    
                                //     'order'               => $row['order'],
                                //     'user_id'               => $row['user_id'],
                                //     'status'              => false,
                                //     'project_id'          => $this->project_id,
                                //     'project_document_id' => $project_document->id,
                                //     'period_value'        => $row['period_value'] ?? 1,
                                //     'period_unit'         => $row['period_unit'] ?? 'day',
                                //     'updated_by'          =>  Auth::user()->id,
                                // ]);


                                $project_reviewer->order = $row['order'];
                                $project_reviewer->user_id = $row['user_id'];
                                $project_reviewer->status = false;
                                $project_reviewer->project_id = $this->project_id;
                                $project_reviewer->project_document_id = $project_document->id;
                                $project_reviewer->period_value = $row['period_value'];
                                $project_reviewer->period_unit = $row['period_unit'];
                                $project_reviewer->updated_by = Auth::user()->id;
                                $project_reviewer->save();

 

                                $fieldsToCheck = [
                                    'order',
                                    'user_id',
                                    // 'status',
                                    'period_value',
                                    'period_unit',
                                ];

                                foreach ($fieldsToCheck as $field) {
                                    if ($original[$field] != $row[$field]) {
                                        $changed = true;

                                        // dd("ORIG: ".$original[$field]." UPDATED:".$project_reviewer->{$field}." EQUALS:".$changed);
                                    }
                                }


                                
                                $savedProjectReviewerIds[] = $row['project_reviewer_id'];

                                 
                            }

                            
                        } 


                        // save the changes
                        if($changed){
                             $detectChanges[$project_document->id] = true;

                        }else{
                            $detectChanges[$project_document->id] = false;

                        }

 
                        // notify creator, project reviewers and project subscribers 
                        // ProjectHelper::notifyRevs_Subs_on_RevUpd($project, $project_document->id, 'document');

                        
                    

                    }


                    // if(!empty($savedProjectReviewerIds)){
                        ProjectReviewer::where('project_document_id', $project_document->id)
                            ->whereNotIn('id', $savedProjectReviewerIds)
                            ->delete();
                    // }


    

                    // send a project review request to the current reviewer
                    // $reviewer = Project::getCurrentReviewerByProjectDocument($project_document->id);
                        
                    // if($reviewer->slot_type == "person"){// if the current reviewer is a person
                    //     $reviewer_user = User::find($reviewer->user_id); 
                    //     ProjectHelper::sendForReviewersProjectReviewNotification($reviewer_user,$project, $reviewer);

                    // }else if($reviewer->slot_type == "open"){
    
                    //     ProjectHelper::sendForReviewersOpenProjectReviewNotification($project, $reviewer);
                        

                    // }

                    // reset all reviewers on the project document type 
                    // Project::resetCurrentProjectDocumentReviewersByDocument($document_type_id,$project->id);
                    ProjectDocument::resetCurrentProjectDocumentReviewersByDocument($project_document->id);

                    // send notification to the current reviewer
                    ProjectReviewerHelpers::sendReviewNotificationToReviewer($project_document, "initial_submission");
                    
                    






                
                }


            }

 
        }

        // dd($detectChanges);

       
        
            
        if(!empty($detectChanges)){
            foreach($detectChanges as $project_document_id => $change_status){

                if($change_status == true){

                    // log the event and trigger real time events

                    // logging and system notifications
                        $authId = Auth::id() ?? null;
                        $projectId =  $project->id; 
                        $projectDocumentId =   $project_document_id;

                        // logging for the project document 
                            // Success message from the activity log project helper
                            // $message =  ProjectDocumentLogHelper::getActivityMessage('updated',$project_document->id,$authId);
                            $message = "Reviewers on Document '{$project_document->document_type->name}' on project '{$project->name}' updated";
                    
                            // get the route 
                            // $route = ProjectDocumentLogHelper::getRoute('updated', $project_document->id);
                            $route = route('project.reviewer.index',['project' => $project->id]);
                            

                            // // log the event 
                            event(new ProjectReviewerLogEvent(
                                $message ,
                                $authId, 
                                $projectId,
                                $projectDocumentId,

                            ));
                        // ./ logging for the project document  
                        

                        /** send system notifications to users */
                            /** send system notifications to users */
                                
                                ProjectDocumentNotificationHelper::sendSystemNotification(
                                    message: $message,
                                    route: $route 
                                );

                            /** ./ send system notifications to users */
                        /** ./ send system notifications to users */
                    // ./ logging and system notifications

                    // dd($projectId);
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


    }





    public function returnStatusConfig($status){
        return ProjectDocumentHelpers::returnStatusConfig($status);
    }


    public function set_as_current($project_reviewer_id){

        $project_reviewer = ProjectReviewer::find($project_reviewer_id);
        $reviewer_name = $project_reviewer->user->name ?? "Unnamed reviewer";
        $project_document = ProjectDocument::find($project_reviewer->project_document_id);
        $project = Project::find($project_reviewer->project_id);
        $submission_type = "suplemental_submission";

        


        // update the project reviewer
        DB::transaction(function () use ($project_reviewer_id) {

            $projectReviewer = ProjectReviewer::query()->findOrFail($project_reviewer_id);

            $projectDocumentId = $projectReviewer->project_document_id;
            $currentOrder      = (int) $projectReviewer->order;

            // 1) Set THIS reviewer to pending
            $projectReviewer->update([
                'review_status' => 'pending',
            ]);

            // 2) Set ALL reviewers ABOVE this order (order > current) to pending
            ProjectReviewer::query()
                ->where('project_document_id', $projectDocumentId)
                ->where('order', '>', $currentOrder)
                ->update([
                    'review_status' => 'pending',
                ]);

        });

        // update skipped project reviewers
        $this->updateSkippedProjectDocumentReviewers($project_reviewer_id);
        


        

        // After seeding/ensuring rows, align the "current" reviewer pointers.
        // This keeps your original behavior.
        ProjectDocument::resetCurrentProjectDocumentReviewersByDocument($project_document->id);


        // send notification to the current reviewer
        ProjectReviewerHelpers::sendReviewNotificationToReviewer($project_document,$submission_type);


        // log the event and trigger real time events

        // logging and system notifications
            $authId = Auth::id() ?? null;
            $projectId =  $project->id; 
            $projectDocumentId =   $project_document->id;

            // logging for the project document 
                // Success message from the activity log project helper
                // $message =  ProjectDocumentLogHelper::getActivityMessage('updated',$project_document->id,$authId);
                $message = "Reviewer '{$reviewer_name}' on Document '{$project_document->document_type->name}' on project '{$project->name}' had been set as the current reviewer";
        
                // get the route 
                // $route = ProjectDocumentLogHelper::getRoute('updated', $project_document->id);
                $route = route('project.reviewer.index',['project' => $project->id]);
                

                // // log the event 
                event(new ProjectReviewerLogEvent(
                    $message ,
                    $authId, 
                    $projectId,
                    $projectDocumentId,

                ));
            // ./ logging for the project document  
            

            /** send system notifications to users */
                /** send system notifications to users */
                    
                    ProjectDocumentNotificationHelper::sendSystemNotification(
                        message: $message,
                        route: $route 
                    );

                /** ./ send system notifications to users */
            /** ./ send system notifications to users */
        // ./ logging and system notifications





        $message = "Project reviewer '{$reviewer_name}' updated as the current reviewer";
        // Alert::success('Success','Project review submitted successfully');
        return redirect()->route('project.document.reviewer.index',[
            // 'project' => $this->project->id,
            'project_document' => $project_document->id,
        
        ])
        ->with('alert.success',$message)
        ;


    }







    public function updateSkippedProjectDocumentReviewers($project_reviewer_id){
        $project_reviewer = ProjectReviewer::find($project_reviewer_id);
        $reviewer_name = $project_reviewer->user->name ?? "Unnamed reviewer";
        $project_document = ProjectDocument::find($project_reviewer->project_document_id);
        $project = Project::find($project_reviewer->project_id); 

        // set all previuos reviewers to be reviewed 
        $review_status = "reviewed";

        $project_review = "Project review had been approved";

        $order = $project_reviewer->order;


        $project_reviewers = ProjectReviewer::where('order','<',$order)
            ->where('project_id',$project->id)
            ->where('project_document_id',$project_document->id)
            ->where('review_status','pending')
            ->get();

        // dd($project_reviewers);


        foreach($project_reviewers as $reviewer){

                // update current reviewer
                // dd($project_reviewer);

                // update review status 
                $reviewer->review_status = $review_status; 
                $reviewer->status = false;  
    
                
                // add update date time and updater 
                $reviewer->updated_at = now();
                $reviewer->updated_by = Auth::user()->id;
                $reviewer->save();
            // ./ update project reviewer
    

    
            // Add review

                //add review
                $review = new Review(); 
                $review->project_review = $project_review;
                $review->project_id = $project_document->project_id;
                $review->project_document_id = $project_document->id;
                $review->project_document_status = $project_document->status; 
                $review->reviewer_id = Auth::user()->id; // this is considered the user 
    


                $review->project_reviewer_id = $reviewer->id;

                /** Update the review time */
                    /**
                     * Review Time is now based on last_submitted_at of the project
                     * 
                     * last_reviewed_at
                     * 
                     */

                    // Ensure updated_at is after created_at
                    if ($project_document->updated_at && now()->greaterThan($project_document->updated_at)) {
                        // Calculate time difference in hours
                        // $review->review_time_hours = $project_document->updated_at->diffInHours(now()); 
                        $review->review_time_hours = $project_document->updated_at->diffInSeconds(now()) / 3600; // shows hours in decimal
                    }
        
                /** ./ Update the review time */
        
                // update review status
                $review->review_status = $review_status;   
    
                // add create & update datetime and the current user  
                $review->created_by = Auth::user()->id;
                $review->updated_by = Auth::user()->id;
                $review->created_at = now();
                $review->updated_at = now();
                $review->save();
 


            // ./ Add review

        }


 
        


         
 
  


    }


















    public function updatedPageMode(){
        if($this->page_mode == "project"){

            // / Set default current type
            $this->currentTypeId = 0;       // the first document on the array will be the default current document 
        }elseif($this->page_mode == "document"){
            // / Set default current type
            $this->currentTypeId = $this->documentTypes[0]['id'] ?? 0;       // the first document on the array will be the default current document 
        }

    }   


    public function render()
    {
        
       
        return view('livewire.admin.project-reviewer.project-reviewer-list',[
            
        ]);
    }
}
