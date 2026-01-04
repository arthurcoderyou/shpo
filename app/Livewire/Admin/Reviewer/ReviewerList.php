<?php

namespace App\Livewire\Admin\Reviewer;

use App\Helpers\SystemNotificationHelpers\ReviewerNotificationHelper;
use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\Reviewer;
use App\Enums\PeriodUnit;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
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
use App\Events\Reviewer\ReviewerLogEvent;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectOpenReviewNotification;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\ActivityLogHelpers\ReviewerLogHelper;
use App\Notifications\ProjectOpenReviewNotificationDB;
use App\Notifications\ProjectReviewerUpdatedNotification;
use App\Notifications\ProjectReviewerUpdatedNotificationDB;

class ReviewerList extends Component
{

    use WithFileUploads;
    use WithPagination;

    protected $listeners = [
        'reviewerEvent' => 'loadData', 
        // 'reviewerUpdated' => '$refresh',
        // 'reviewerDeleted' => '$refresh',
        
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


    /** Array of period unit options */
    /** @var array<int,int> */
    public array $period_unit_options = [];         // period_unit_options [ options for the period ]


    

    
    public $period_unit = "day";
    public $period_value = 0;

    public $selected_user_id = null;

    public array $user_options = [];
    public array $user_admin_options = [];


    public function mount()
    {
        

       


        // Load document types
        // $this->documentTypes = DocumentType::orderBy('name')->get(['id','name'])->toArray();
        // $this->documentTypes = [
        //     ['id' => 1, 'name' => 'Archaeological Survey'],
        //     ['id' => 2, 'name' => 'Architectural Plans'],
        //     ['id' => 3, 'name' => 'Photos'],
        // ];


        $this->documentTypes = collect(DocumentType::orderBy('order','asc')->get())
                ->mapWithKeys(fn ($doc) => [$doc->id => $doc->name])
                ->toArray();

        $this->period_unit_options = collect(PeriodUnit::cases())
                ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                ->toArray();

        

        

        // dd($this->period_unit_options);

        // dd($this->documentTypes);
         


        // Set default current type
        $this->currentTypeId = DocumentType::orderBy('order','asc')->first()->id ?? null;       // the first document on the array will be the default current document 

        if(request()->has('document_type_id')){
            $this->currentTypeId = request()->get('document_type_id');
            
        }





        $this->loadData();
        
    }
 

    public function loadData(){

        


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


        // dd($this->options);

 
 

        /** Updated  */

        $defaultAdmin = User::permission('system access admin')
            ->latest('id')   // same as orderBy('id','desc')
            ->first();



        foreach ($this->documentTypes as $key => $label) {
            $typeId = (int) $key; 

            $this->selectedByType[$typeId] = [];
            $this->assignedByType[$typeId] = [];
            $this->repeatByType[$typeId]   = 1; // default

            // Start with current assigned array
            $assigned = $this->assignedByType[$typeId] ?? [];
            $order    = empty($assigned) ? 0 : max(array_column($assigned, 'order'));

            $documentType = DocumentType::with(['reviewers.user.roles'])->find($typeId);

            if ($documentType && $documentType->reviewers) {
                foreach ($documentType->reviewers as $reviewer) {

                    if (!empty($reviewer->user_id) && $reviewer->user) {
                        // PERSON (claimed / specific user)
                        $user = $reviewer->user;

                        $assigned[] = [
                            'row_uid'      => (string) Str::uuid(),
                            'slot_type'    => 'person',
                            'user_id'      => (int) $user->id,
                            'name'         => $user->name ?: null,
                            'roles'        => $user->getRoleNames()->values()->all(),
                            'order'        => ++$order,
                            'period_value' => $reviewer->period_value,
                            'period_unit'  => $reviewer->period_unit,
                        ];
                    } else {
                        // OPEN SLOT (no user yet)
                        $assigned[] = [
                            'row_uid'      => (string) Str::uuid(),
                            'slot_type'    => 'open',
                            'slot_role'    => $reviewer->slot_role ?? 'reviewer',
                            'user_id'      => null,
                            'name'         => null,
                            'roles'        => [],
                            'order'        => ++$order,
                            'period_value' => $reviewer->period_value,
                            'period_unit'  => $reviewer->period_unit,
                        ];
                    }
                }
            }

            // 🔥 If nothing was loaded, create defaults:
            if (empty($assigned)) {
                $order = 0;

                 // 1) OPEN SLOT
                $assigned[] = [
                    'row_uid'      => (string) Str::uuid(),
                    'slot_type'    => 'open',
                    'slot_role'    => 'reviewer',     // or something from config
                    'user_id'      => null,
                    'name'         => null,
                    'roles'        => [],
                    'order'        => ++$order,
                    'period_value' => 1,
                    'period_unit'  => "day",
                ];

                // 2) PERSON SLOT → first user with "system access admin" permission
                if (!empty($defaultAdmin)) {
                    $assigned[] = [
                        'row_uid'      => (string) Str::uuid(),
                        'slot_type'    => 'person',
                        'user_id'      => (int) $defaultAdmin->id,
                        'name'         => $defaultAdmin->name ?: null,
                        'roles'        => $defaultAdmin->getRoleNames()->values()->all(),
                        'order'        => ++$order,
                        'period_value' => 1,   // set defaults as you need
                        'period_unit'  => "day",
                    ];
                }

               
            }






            /*
            |--------------------------------------------------------------------------
            | ✅ Enforce FIRST and LAST slot rules
            |--------------------------------------------------------------------------
            | - First slot must be `slot_type = 'open'`
            | - Last slot must be `slot_type = 'person'` AND user has `system access admin`
            */

            if (!empty($assigned)) {
                // --- FIRST SLOT RULE ---
                $firstIndex = 0;
                $first      = $assigned[$firstIndex];

                if (($first['slot_type'] ?? null) !== 'open') {
                    // Override first slot as OPEN
                    $assigned[$firstIndex] = [
                        'row_uid'      => (string) Str::uuid(),
                        'slot_type'    => 'open',
                        'slot_role'    => $first['slot_role'] ?? 'admin',
                        'user_id'      => null,
                        'name'         => null,
                        'roles'        => [],
                        'order'        => $first['order'] ?? 1,
                        'period_value' => $first['period_value'] ?? 1,
                        'period_unit'  => $first['period_unit'] ?? 'day',
                    ];
                }

                // --- LAST SLOT RULE ---
                $lastIndex = count($assigned) - 1;
                $last      = $assigned[$lastIndex];

                $needsOverride = false;

                if (($last['slot_type'] ?? null) !== 'person') {
                    $needsOverride = true;
                } else {
                    $userId = $last['user_id'] ?? null;

                    if (empty($userId)) {
                        $needsOverride = true;
                    } else {
                        $user = User::find($userId);

                        // `hasPermissionTo` includes permissions via roles (Spatie)
                        if (
                            ! $user ||
                            ! $user->hasPermissionTo('system access admin')
                        ) {
                            $needsOverride = true;
                        }
                    }
                }

                if ($needsOverride && !empty($defaultAdmin)) {
                    // Override LAST slot with last admin user
                    $assigned[$lastIndex] = [
                        'row_uid'      => (string) Str::uuid(),
                        'slot_type'    => 'person',
                        'user_id'      => (int) $defaultAdmin->id,
                        'name'         => $defaultAdmin->name ?: null,
                        'roles'        => $defaultAdmin->getRoleNames()->values()->all(),
                        'order'        => $assigned[$lastIndex]['order'] ?? ($lastIndex + 1),
                        'period_value' => $assigned[$lastIndex]['period_value'] ?? 1,
                        'period_unit'  => $assigned[$lastIndex]['period_unit'] ?? 'day',
                    ];
                }
            }
















            $this->assignedByType[$typeId] = $assigned;
        }

 


        if(count($this->assignedByType[$this->currentTypeId]) == 1){
            $perms = [
                'system access admin', 
                 
            ]; // set the permissions that the user must have atleast one of it 
         
        }

             

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


         

        // If editing existing data, prefill $this->assignedByType[...] with rows including row_uid + order.

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

        $typeId = (int) ($this->currentTypeId ?? 0);
        if (!$typeId) return;

        // Selected user IDs for this type
        $selected = collect($this->selectedByType[$typeId] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->values();

        if ($selected->isEmpty()) return;

        $repeat   = max(1, (int) ($this->repeatByType[$typeId] ?? 1));
        $assigned = $this->assignedByType[$typeId] ?? [];

        // Current max order
        $order = empty($assigned) ? 0 : max(array_column($assigned, 'order'));

        // Options (user list)
        $options = collect($this->options ?? []);

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
                    'name'         => $user['name'],
                    'roles'        => $user['roles'] ?? [],
                    'order'        => ++$order,
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

        // Save back
        $this->assignedByType[$typeId] = $assigned;

        // Reset helpers
        $this->selectedByType[$typeId] = [];
        $this->repeatByType[$typeId]   = 1;
        $this->period_unit             = null;
        $this->period_value            = 0;


        // Recalculate or normalize the "order" field after removal
        $this->reindexOrder($typeId);


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

    // public function addOpenSlots($custom_role = "reviewer"): void
    // {

    //     $this->validate([ 
    //         'period_unit' => 'required',
    //         'period_value' => 'required'
    //     ],[
            
    //         'period_unit.required' => 'Please select a unit',
    //         'period_value.required' => 'Please enter a period'
    //     ]);


    //     $typeId = (int) ($this->currentTypeId ?? 0);
    //     if (!$typeId) return;

    //     if(!empty($custom_role)){
    //         $role   = $custom_role;
    //     }else{
    //         $role   = $this->openRoleByType[$typeId]  ?? 'reviewer'; 
    //     } 

    //     $repeat = max(1, (int) ($this->openRepeatByType[$typeId] ?? 1));

    //     $assigned = $this->assignedByType[$typeId] ?? [];
    //     $order = empty($assigned) ? 0 : max(array_column($assigned, 'order'));

    //     for ($i = 0; $i < $repeat; $i++) {
    //         $assigned[] = [
    //             'row_uid'    => (string) Str::uuid(),
    //             'slot_type'  => 'open',
    //             'slot_role'       => $role,
    //             'user_id'    => null,
    //             // 'claimed_at' => null,
    //             'order'      => ++$order,
    //             'period_unit' => $this->period_unit,
    //             'period_value' => $this->period_value,
                
    //         ];
    //     }


 
    //     $this->assignedByType[$typeId] = $assigned;
    //     // optional reset
    //     $this->openRepeatByType[$typeId] = 1;

    //     $this->period_unit = null;
    //     $this->period_value = 0;

    // }






    public function addOpenSlots($custom_role = "reviewer"): void
    {
        $this->period_unit = "day";
        $this->validate([ 
            'period_unit' => 'required',
            'period_value' => 'required|integer|min:1',
        ],[
            'period_unit.required' => 'Please select a unit',
            'period_value.required' => 'Please enter a period'
        ]);

        $typeId = (int) ($this->currentTypeId ?? 0);
        if (!$typeId) return;

        $role = !empty($custom_role)
            ? $custom_role
            : ($this->openRoleByType[$typeId] ?? 'reviewer');

        // GET CURRENT ASSIGNED SLOTS
        $assigned = $this->assignedByType[$typeId] ?? [];

        // -----------------------------------------
        // 🔍 CHECK THE SECOND-TO-THE-LAST ITEM
        // -----------------------------------------
        if (count($assigned) >= 2) {

            // index of second-to-last item
            $secondToLastIndex = count($assigned) - 2;
            $secondToLastItem  = $assigned[$secondToLastIndex];

            // 👉 INSERT YOUR CUSTOM CONDITION HERE
            // Example:
            if ($secondToLastItem['slot_type'] === 'open') {
                // do something...
                // e.g., return; or modify values
            }
        }
        // -----------------------------------------

        $repeat = max(1, (int) ($this->openRepeatByType[$typeId] ?? 1));
        $order = empty($assigned) ? 0 : max(array_column($assigned, 'order'));

        // ADD NEW SLOTS
        for ($i = 0; $i < $repeat; $i++) {
            $assigned[] = [
                'row_uid'       => (string) Str::uuid(),
                'slot_type'     => 'open',
                'slot_role'     => $role,
                'user_id'       => null,
                'order'         => ++$order,
                'period_unit'   => $this->period_unit,
                'period_value'  => $this->period_value,
            ];
        }

        $this->assignedByType[$typeId] = $assigned;

        // reset fields
        $this->openRepeatByType[$typeId] = 1;
        $this->period_unit   = null;
        $this->period_value  = 0;
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

                            'period_value'      => $row['period_value'] ?? null,    
                            'period_unit'      => $row['period_unit'] ?? null,    


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

        // //send an update on the notifications 
        // try {

        //     event(new ReviewerListUpdated(auth()->user()->id));
        // } catch (\Throwable $e) {
        //     Log::error('Failed to send ReviewerListUpdated event: ' . $e->getMessage(), [ 
        //         'user_id' => auth()->user()->id,
        //         'trace' => $e->getTraceAsString(),
        //     ]);
        // }




        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = ReviewerLogHelper::getActivityMessage('updated', $authId);

            // get the route
            $route = ReviewerLogHelper::getRoute('updated');

            // log the event 
            event(new ReviewerLogEvent(
                $message ,
                $authId, 

            ));
    
            /** send system notifications to users */
                
                ReviewerNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications
 







        // Alert::success('Success', 'Reviewer list saved successfully');
        return redirect()->route('reviewer.index', [
            'document_type_id' => $this->currentTypeId,
            // 'reviewer_type' => $reviewer_type,
        ])
        ->with('alert.success',$message)
        ;


    }




 
    public function render()
    {
        
        
        // dd($this->users);
        // dd($this->options);
        return view('livewire.admin.reviewer.reviewer-list',[
             
            
        ]);
    }


}
