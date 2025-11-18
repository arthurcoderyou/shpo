<?php

namespace App\Livewire\Admin\ProjectDocumentReviewer;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Enums\PeriodUnit;
use Illuminate\Support\Str;
use App\Models\DocumentType;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ProjectDocumentHelpers;
use App\Helpers\ProjectReviewerHelpers;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectDocumentReviewerList extends Component
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

    /** ProjectDocument */
    /** @var \Illuminate\Database\Eloquent\ProjectDocument */
    public ProjectDocument $project_document;


    /** Project */
    /** @var \Illuminate\Database\Eloquent\Project */
    public Project $project;

 
    
    /** Project id */
    /** @var int */
    public int $project_id;


    /** Project document id */
    /** @var int */
    public int $project_document_id;


    /** Array of period unit options */
    /** @var array<int,int> */
    public array $period_unit_options = [];         // period_unit_options [ options for the period ]

    
    public $period_unit = "day";
    public $period_value = 0;



    public function mount( $project_document_id)
    {

        $this->project_document = ProjectDocument::findOrFail($project_document_id);
        $this->project_document_id = $this->project_document->id;
        $this->project = Project::findOrFail($this->project_document->project_id);
        $this->project_id = $this->project->id;

        // dd($project);
        // Load document types
        // $this->documentTypes = DocumentType::orderBy('name')->get(['id','name'])->toArray();
        // $this->documentTypes = [
        //     ['id' => 1, 'name' => 'Archaeological Survey'],
        //     ['id' => 2, 'name' => 'Architectural Plans'],
        //     ['id' => 3, 'name' => 'Photos'],
        // ];

        $this->period_unit_options = collect(PeriodUnit::cases())
                ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                ->toArray();

         


        // Set default current type
        $this->currentTypeId = $this->project_document->document_type_id ?? null;       // the first document on the array will be the default current document 

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
        
         
        if (!empty($this->project_document->document_type_id)) {


            $typeId = (int) $this->project_document->document_type_id; 

           

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

            if ($documentType && $project_reviewers) {
                foreach ($project_reviewers as $reviewer) {

                    if (!empty($reviewer->user_id) && $reviewer->user) {
                        // PERSON (claimed / specific user)
                        $user = $reviewer->user;

                        $assigned[] = [ 
                            'row_uid'   => (string) Str::uuid(),
                            'slot_type' => 'person',                         // explicit person row
                            'user_id'   => (int) $user->id,
                            'project_reviewer_id' => $reviewer->id, // notes the project reviewer id 
                            'name'      => $user->name ?: null,
                            // ✅ Spatie role names (array of strings)
                            'roles'     => $user->getRoleNames()->values()->all(),
                            'order'     => ++$order,

                            'status'     => $reviewer->status,
                            'review_status'     => $reviewer->review_status,

                            'period_value' => $reviewer->period_value,
                            'period_unit' => $reviewer->period_unit,

                        ];
                    } else {
                        // OPEN SLOT (no user yet)
                        $assigned[] = [
                            'row_uid'   => (string) Str::uuid(),
                            'slot_type' => 'open',                           // explicit open slot
                            'slot_role'      => $reviewer->slot_role ?? 'reviewer',    // optional: intended role for slot
                            'user_id'   => null,
                            'project_reviewer_id' => $reviewer->id, // notes the project reviewer id 
                            'name'      => null,
                            // Open slot has no roles until claimed
                            'roles'     => [],
                            'order'     => ++$order,

                            'status'     => $reviewer->status,
                            'review_status'     => $reviewer->review_status,

                            'period_value' => $reviewer->period_value,
                            'period_unit' => $reviewer->period_unit,

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

        $this->validate([
            'selectedByType' => 'required|array|min:1',
            'period_unit' => 'required',
            'period_value' => 'required'
        ],[
            
            'period_unit.required' => 'Please select a unit',
            'period_value.required' => 'Please enter a period'
        ]);

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

                    'period_value' => $this->period_value,
                    'period_unit' => $this->period_unit,

                ];
            }
        }

        $this->assignedByType[$typeId] = $assigned;     // add to the master list of assigned options [users] per type 
        $this->selectedByType[$typeId] = [];       // clear chips
        $this->repeatByType[$typeId]   = 1;        // reset to 1 (optional)

        $this->period_unit = null;
        $this->period_value = 0;
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


        $this->validate([ 
            'period_unit' => 'required',
            'period_value' => 'required'
        ],[
            
            'period_unit.required' => 'Please select a unit',
            'period_value.required' => 'Please enter a period'
        ]);


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

                'period_unit' => $this->period_unit,
                'period_value' => $this->period_value,
            ];
        }





        $this->assignedByType[$typeId] = $assigned;
        // optional reset
        $this->openRepeatByType[$typeId] = 1;

        $this->period_unit = null;
        $this->period_value = 0;


    }

    // @this.update({{ $typeId }}, targetRowId,period_value,period_unit);
    public function update_row(int $typeId, string $rowUid, int $period_value, string $period_unit){

        $typeId = (int) $typeId; // make sure typeId is an integer
    
         
        $typeId = (int) $typeId; 

        $map = collect($this->assignedByType[$typeId] ?? [])->keyBy('row_uid');
        $updated = [];
        $i = 1;
        
        // dd("typeId: ".$typeId." || rowUid: ".$rowUid. " || period_value: ". $period_value. " || period_unit: ". $period_unit);
        // dd($map);

        // keep any rows not present in $rowUids at the end (safety)
        foreach ($map as $uid => $row) {

            if ($map->has($uid) && $row['row_uid'] == $rowUid ) {
                $row = $map[$rowUid];
                $row['period_value'] = $period_value;
                $row['period_unit'] = $period_unit;
                $updated[] = $row;
            }else{

                $updated[] = $row;
            }
 
        }

         
        $this->assignedByType[$typeId] = $updated;

    }
    
    
    public function save()
    {

        // dd($this->assignedByType);
        $project = Project::findOrFail($this->project_id);

        // 1. Check for existing project reviewers 
        // get project reviwers that are not approved   
        // $project_reviewers = $project_document->project_reviewers()
        //     ->where('review_status','!=','approved')
        //     ->get('id')
        //     ->;
        $project_document = ProjectDocument::findOrFail($this->project_document_id);



        if(!empty($this->assignedByType)){


            $selected_project_reviewer_ids = ProjectReviewerHelpers::extractProjectReviewerIds($this->assignedByType);
            /**
             * 
             * #items: array:7 [▼
                    0 => 914
                    1 => 960
                    2 => 961
                    3 => 962
                    4 => 963
                    5 => 964
                    6 => 965
                ]
             * 
             */


            $existing_project_reviewer_ids = $existingIds = $project_document
                ->project_reviewers()      // relation to ProjectReviewer
                ->pluck('id'); 
                
            /**
                 * #items: array:12 [▼
                    0 => 914
                    1 => 955 to delete
                    2 => 956 to delete
                    3 => 957 to delete
                    4 => 958 to delete
                    5 => 959 to delete
                    6 => 960
                    7 => 961
                    8 => 962
                    9 => 963
                    10 => 964
                    11 => 965
                ]
             */

            // $deleted_existing_project_reviewer_ids

            // to check project reviewers ids for deletion, check for the difference on what is not included on the selected 
            $to_delete = $existing_project_reviewer_ids->diff($selected_project_reviewer_ids); // in DB but not in new array 
            /**
             *  The table shows that our logic is correct
             * #items: array:5 [▼
                    1 => 955
                    2 => 956
                    3 => 957
                    4 => 958
                    5 => 959
                ]
             * 
             */

            // dd($to_delete);

            // now, check if there are existing reviewers ids to be deleted that has reviews. Because project reviewer with reviews cannot be deleted 
            if(!empty($to_delete) && count($to_delete) > 0 ){
                foreach($to_delete as $key => $project_reviewer_id){

                    $project_reviewer = ProjectReviewer::find($project_reviewer_id);
                    
                    $review_count = Review::returnReviewCount($project_reviewer->id, $project_document->id );
                    if($review_count > 0)
                    {
                        Alert::error('Error', 'Project reviewer list cannot be saved because you cannot delete project reviewers that had already made reviews to the document');
                        return redirect()->route('project.document.reviewer.index', [
                            'project' => $this->project->id,  
                            'project_document' => $project_document->id,
                        ]);
                    }
                }


                // if there are no, delete those project reviewers 
                foreach($to_delete as $key => $project_reviewer_id){

                    $project_reviewer = ProjectReviewer::find($project_reviewer_id);
                    
                    $review_count = Review::returnReviewCount($project_reviewer->id, $project_document->id );
                    if($review_count == 0 || $review_count == null)
                    {
                        $project_reviewer->delete();
                    }
                }






            }

            





            foreach($this->assignedByType as $document_type_id => $assigned){
                 
 
                
                // dd($project_document);

                

                // insert the new assigned reviewers
                if(!empty($assigned) && empty($row['project_reviewer_id'])){
                    foreach($assigned as $row){ 


                        // dd($row);

                        // if it is an exisiting project reviewer record, find it and update its details 
                        if(!empty($row['project_reviewer_id'])){
                            $project_reviewer = ProjectReviewer::find($row['project_reviewer_id']);

                            $project_reviewer->order = $row['order']; 
                            $project_reviewer->user_id =  $row['user_id'] ?? null;  
                            $project_reviewer->updated_by = Auth::user()->id;
                            $project_reviewer->updated_at = now();
                            $project_reviewer->period_unit = $row['period_unit']->value;
                            $project_reviewer->period_value = $row['period_value'];  
                            $project_reviewer->save();

                        }else{
                            // if it is not existing, create it 

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

                                'period_unit' => $row['period_unit']->value,
                                'period_value' => $row['period_value'],

            
                            ]); 

                        }
                        


                        
                    } 
                    

                    // After seeding/ensuring rows, align the "current" reviewer pointers.
                    // This keeps your original behavior.
                    ProjectDocument::resetCurrentProjectDocumentReviewersByDocument($project_document->id);


                    // set the project document reviewers
                    ProjectReviewerHelpers::sendNotificationOnReviewerListUpdate($project_document);


                    $submission_type = "initial_submission";
                    // send notification to the current reviewer
                    ProjectReviewerHelpers::sendReviewNotificationToReviewer($project_document, $submission_type);

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
        return view('livewire.admin.project-document-reviewer.project-document-reviewer-list');
    }
}
