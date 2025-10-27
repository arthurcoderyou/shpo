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
use Illuminate\Support\Str;

class ReviewerList extends Component
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


    public function mount()
    {
        // Load document types
        // $this->documentTypes = DocumentType::orderBy('name')->get(['id','name'])->toArray();
        // $this->documentTypes = [
        //     ['id' => 1, 'name' => 'Archaeological Survey'],
        //     ['id' => 2, 'name' => 'Architectural Plans'],
        //     ['id' => 3, 'name' => 'Photos'],
        // ];

        $this->documentTypes = DocumentType::orderBy('order','asc') // document types 
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

            $documentType = DocumentType::with(['reviewers.user.roles'])->find($typeId);

            if ($documentType && $documentType->reviewers) {
                foreach ($documentType->reviewers as $reviewer) {

                    if (!empty($reviewer->user_id) && $reviewer->user) {
                        // PERSON (claimed / specific user)
                        $user = $reviewer->user;

                        $assigned[] = [
                            'row_uid'   => (string) Str::uuid(),
                            'slot_type' => 'person',                         // explicit person row
                            'user_id'   => (int) $user->id,
                            'name'      => $user->name ?: null,
                            // ✅ Spatie role names (array of strings)
                            'roles'     => $user->getRoleNames()->values()->all(),
                            'order'     => ++$order,
                        ];
                    } else {
                        // OPEN SLOT (no user yet)
                        $assigned[] = [
                            'row_uid'   => (string) Str::uuid(),
                            'slot_type' => 'open',                           // explicit open slot
                            'slot_role'      => $reviewer->slot_role ?? 'reviewer',    // optional: intended role for slot
                            'user_id'   => null,
                            'name'      => null,
                            // Open slot has no roles until claimed
                            'roles'     => [],
                            'order'     => ++$order,
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
                    'name'      => $user['name'],
                    // optional convenience copy (ok to remove and rely on rolesFor(user_id))
                    'roles'     => $user['roles'] ?? [],
                    'order'     => ++$order,

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
                // 'claimed_at' => null,
                'order'      => ++$order,



            ];
        }





        $this->assignedByType[$typeId] = $assigned;
        // optional reset
        $this->openRepeatByType[$typeId] = 1;
    }


    
 
    public function save()
    {

        // dd($this->assignedByType);

        if(!empty($this->assignedByType)){
            foreach($this->assignedByType as $document_type_id => $assigned){
                // dd($assigned['order']);
                // dd($document_type_id);
                $document_type = DocumentType::find($document_type_id);

                // get reviewers that are on that document type 
                // dd($document_type->reviewers);
                // delete all reviewers previuosly added
                if(!empty($document_type->reviewers)){
                    foreach($document_type->reviewers as $reviewer){
                        $reviewer->delete();
                    } 

                }

                // insert the new assigned reviewers
                if(!empty($assigned)){
                    foreach($assigned as $row){ 

                        // dd( $row['slot_role']);
                        Reviewer::create([
                            
                            'order' => $row['order'],
                            'status' => false,
                            'user_id' => $row['user_id'] ?? null,

                            'slot_type' => $row['slot_type'] ?? 'person' ,              // explicit: this row is a person (not open slot)
                            'slot_role'      => $row['slot_role'] ?? null,    // optional: intended role for slot

                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                            'document_type_id' => $document_type_id,
                            'reviewer_type' => 'document', // initial, document, final
                        ]); 
                    } 
                }

            }

 
        }

        // find the document type



        /**
            array:3 [▼ // app\Livewire\Admin\Reviewer\ReviewerList.php:330
                66 => array:2 [▼
                    0 => array:7 [▼
                    "row_uid" => "6549eb03-1dc3-461b-8c03-81adb5d9055c"
                    "id" => 71
                    "slot_type" => "person"
                    "user_id" => 71
                    "name" => "Milane"
                    "roles" => array:1 [▼
                        0 => "Reviewer"
                    ]
                    "order" => 1
                    ]
                    1 => array:7 [▼
                    "row_uid" => "55d1ba9c-aa0b-49ee-86c7-deacae5c6ba4"
                    "id" => 10
                    "slot_type" => "person"
                    "user_id" => 10
                    "name" => "Vehi"
                    "roles" => array:1 [▼
                        0 => "Reviewer"
                    ]
                    "order" => 2
                    ]
                ]
                57 => array:3 [▼
                    0 => array:7 [▼
                    "row_uid" => "3d73b51d-5c33-4710-a96f-f74207a2f10b"
                    "id" => 71
                    "slot_type" => "person"
                    "user_id" => 71
                    "name" => "Milane"
                    "roles" => array:1 [▼
                        0 => "Reviewer"
                    ]
                    "order" => 1
                    ]
                    1 => array:7 [▼
                    "row_uid" => "17ecafbc-69f9-4637-8152-3137fd66e442"
                    "id" => 10
                    "slot_type" => "person"
                    "user_id" => 10
                    "name" => "Vehi"
                    "roles" => array:1 [▼
                        0 => "Reviewer"
                    ]
                    "order" => 2
                    ]
                    2 => array:7 [▼
                    "row_uid" => "bd541ca9-b604-4d58-9b32-cdfe0e93d72a"
                    "id" => 32
                    "slot_type" => "person"
                    "user_id" => 32
                    "name" => "Quirino Cervania"
                    "roles" => array:2 [▼
                        0 => "Reviewer"
                        1 => "Admin"
                    ]
                    "order" => 3
                    ]
                ]
                67 => array:1 [▼
                    0 => array:7 [▼
                    "row_uid" => "fc1629be-57e0-4bc3-bffc-800878900c15"
                    "id" => 10
                    "slot_type" => "person"
                    "user_id" => 10
                    "name" => "Vehi"
                    "roles" => array:1 [▼
                        0 => "Reviewer"
                    ]
                    "order" => 1
                    ]
                ]
            ]
         * 
         */


        // Persist all $assignedByType to DB (per document type)
        // Example table: project_document_type_reviewers (project_id, document_type_id, user_id, order)
        // Wrap in transaction; upsert rows & delete removed ones.
        // $this->dispatchBrowserEvent('notify', ['message' => 'Reviewers per document type saved.']);

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
            // 'reviewer_type' => $reviewer_type,
        ]);


    }




 
    public function render()
    {
        
        
        // dd($this->users);
        // dd($this->options);
        return view('livewire.admin.reviewer.reviewer-list',[
             
            
        ]);
    }


}
