<?php

namespace App\Livewire\Admin\Project;

 
use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use App\Models\ProjectTimer;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectCompany;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Validation\Rule;
use App\Models\ProjectSubscriber;
use App\Models\ProjectAttachments;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectFederalAgencies;
use App\Events\Project\ProjectLogEvent;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;
use App\Helpers\SystemNotificationHelpers\ProjectNotificationHelper;

class ProjectEdit extends Component
{

    // dynamic listener 
        protected $listeners = [
            // add custom listeners as well
            // 'systemUpdate'       => 'handleSystemUpdate',
            // 'SystemNotification' => 'handleSystemNotification',
        ];

        protected function getListeners(): array
        {
            return array_merge($this->listeners, [
                "projectEvent.{$this->project_id}" => 'loadData',
            ]);
        }
    // ./ dynamic listener 

    use WithFileUploads;
    public string $name = '';
    public string $description = '';
    public string $agency = ''; 

    public $type;

    public $attachments = []; // Attachments 
    public $document_type_id;

    public $existingFiles = [];


    public $project_number;
    public $rc_number;
    public $submitter_due_date;
    public $reviewer_due_date;

    public $submitter_response_duration_type;
    public $submitter_response_duration; 
    public $reviewer_response_duration;
    public $reviewer_response_duration_type;  
 

    public $name_override = false;

    public $project_id;

    public $project;

    public $latitude;
    public $longitude;
    public $location; 

    public $street;
    public $area;
    public $lot_number;


    public $location_directions = [];



    public $staff_engineering_data;
    public $staff_initials;
    public $lot_size;
    public $unit_of_size;
    public $site_area_inspection;
    public $burials_discovered_onsite;
    public $certificate_of_approval;
    public $notice_of_violation;



    public $project_types = [
        'Local Government' => 'Local Government',
        'Federal Government' => 'Federal Government', 
        'Private' => 'Private', 
    ];

    public $lot_size_unit_options = [
        "Square meters (sqm)" => "Square meters (sqm)",
        "Square feet (sqft)" => "Square feet (sqft)",
        "Hectares (ha)" => "Hectares (ha)",
        "Acres" => "Acres",
        "Square kilometers (sqkm)" => "Square kilometers (sqkm)",
        "Square miles" => "Square miles",
    ];


    /**
     * Each row: ['name' => string]
     * @var array<int, array{name:string}>
     */
    public array $companies = [
        ['name' => '']
    ];


    /**
     * Each row: ['name' => string]
     * @var array<int, array{name:string}>
     */
    public array $federal_agencies = [
        ['name' => '']
    ];

    

    public $selected_document_type_id;
    public $projectDocuments = []; // Array of project documents
    public $documentTypes = [];

 

    public $home_route;


    /**
     * Warnings
     */

    public array $warnings = [];   // 👈 plain array

    // Optional: helper methods
    protected function addWarning(string $field, string $message): void
    {
        $this->warnings[$field][] = $message;
    }

    protected function clearWarning(string $field): void
    {
        unset($this->warnings[$field]);
    }


    public function mount($id){
 

        $project = Project::find($id);
        $this->project = $project;

        if($project->created_by == Auth::id()){
            $this->home_route = route('project.index');
        }else{
            $this->home_route = route('project.index');
        }


        $this->project_id = $project->id;

        $this->loadData();

    }

    public function loadData(){

        $project = Project::find($this->project_id);


        $this->name = $project->name;
        $this->type = $project->type;
        $this->description = $project->description;
        $this->agency = $project->agency;

        $this->project_number = $project->project_number ? $project->project_number: Project::generateProjectNumber();
        // $this->rc_number = $project->rc_number ? $project->rc_number : Project::generateProjectNumber(rand(10, 99)) ;
        $this->rc_number = $project->rc_number ? $project->rc_number : null;
        $this->submitter_due_date = $project->submitter_due_date;
        $this->reviewer_due_date = $project->reviewer_due_date;
        $this->submitter_response_duration_type = $project->submitter_response_duration_type;
        $this->submitter_response_duration = $project->submitter_response_duration;
        $this->reviewer_response_duration = $project->reviewer_response_duration;
        $this->reviewer_response_duration_type = $project->reviewer_response_duration_type; 

        /**default is Guam coordinates */
        $this->latitude = $project->latitude ?? 13.4443; 
        $this->longitude = $project->longitude ?? 144.7937;
        $this->location = $project->location ;
        $this->street = $project->street ;
        $this->area = $project->area ;
        $this->lot_number = $project->lot_number ;


        $this->staff_engineering_data = $project->staff_engineering_data ?? User::generateInitials(Auth::user()->name);
        $this->staff_initials = $project->staff_initials ?? User::generateInitials(Auth::user()->name) ;
        $this->lot_size = $project->lot_size ;
        $this->unit_of_size = $project->unit_of_size ;
        $this->site_area_inspection = $project->site_area_inspection ;
        $this->burials_discovered_onsite = $project->burials_discovered_onsite ;
        $this->certificate_of_approval = $project->certificate_of_approval ;
        $this->notice_of_violation = $project->notice_of_violation ;

        

        $this->location_directions[] =   Project::select(
                    'latitude', 'longitude'
                )
                ->where('id', $project->id) 
                ->get()
                ->toArray();
 

       
        


        // check if user is admin to know if project name can be overriden without being a draft.
        if(Auth::user()->hasRole('Admin')){
            $this->name_override = true;

        }


        // dd($project->project_subscribers);

        if(!empty($project->project_subscribers)){
            foreach($project->project_subscribers as $subscriber){
                $this->selectedUsers[] = ['id' => $subscriber->user->id, 'name' => $subscriber->user->name];
            }
            
        }

        // Get used document_type_ids from the project's documents
        $usedDocumentTypeIds = $project->project_documents->pluck('document_type_id')->toArray();

        // Get only document types that are NOT used yet
        $this->documentTypes = DocumentType::whereNotIn('id', $usedDocumentTypeIds)->orderBy('order','ASC')->get();
  
        
        // dd($this->project->project_documents);

        $this->loadCompanies();
        $this->loadFederalAgencies(); 

        $this->loadProjects();

    }
















    // Project name relative data 

        public $projects = [];

        public function updatedName(){
            // dd($this->name);
            

             

            // Extract selected project IDs to exclude from results
            // $excludedIds = array_column($this->selectedProjects, 'id');

            
            $this->loadProjects();
            

        }

        // load projects
        public function loadProjects(){

            $search = $this->name;

            if (empty($search)) {
                $this->projects = null;
                return;
            }

            $user = Auth::user();


            $user = Auth::user();

            // If somehow no authenticated user, return empty results safely
            if (!$user) {
                $this->projects = [];
                return;
            }

            $query = Project::query()
                ->select('id', 'name', 'lot_number', 'location', 'rc_number')
                ->whereNotNull('rc_number');
                // ->whereNotNull('project_number')
                // ->when(!empty($excludedIds), function ($q) use ($excludedIds) {
                //     $q->whereNotIn('id', $excludedIds);
                // });

            // Optional: access control
            if (
                !$user->can('system access global admin')
                && !$user->can('system access admin')
            ) {
                $query->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                    // Add more rules if needed, e.g. shared projects, assignments, etc.
                });
            }

            // Search conditions
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('agency', 'like', "%{$search}%")
                    ->orWhere('lot_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('rc_number', 'like', "%{$search}%");
            });

            $this->projects = $query
                ->limit(10)
                ->get()
                ->toArray(); 
        }



    // ./ Project name relative data 

    
    // load project companies 
    public function loadCompanies(){
        $this->companies = [];
        $this->companies = $this->project->project_companies
        ->map(fn($company) => [
            'name' => $company->name
        ])
        ->toArray();


        if(empty($this->companies) || count($this->companies) == 0){
            $this->companies = [
                ['name' => '']
            ];
            
        }

    }




    // load project federal agencies  
    public function loadFederalAgencies(){
        $this->federal_agencies = [];
        $this->federal_agencies = $this->project->project_federal_agencies
        ->map(fn($agencies) => [
            'name' => $agencies->name
        ])
        ->toArray();

        if(empty($this->federal_agencies) || count($this->federal_agencies) == 0){
            $this->federal_agencies = [
                ['name' => '']
            ];
            
        }


    }


    // For the Search Subscriber Functionality
        

        public $query = ''; // Search input
        public $users = []; // Search results
        public $selectedUsers = []; // Selected subscribers

        public function updatedQuery()
        {
            if (!empty($this->query)) {
            $user      = Auth::user();
            $term      = '%' . $this->query . '%';

            $creator_user_id = $this->project->created_by; // get the creator of the project

            $this->users = User::query()
                // 🔍 Group all search columns together
                ->where(function ($search) use ($term) {
                    $search->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhere('company', 'like', $term)
                        ->orWhere('phone_number', 'like', $term);
                })

                // 🔐 Apply access restrictions only if NOT global admin or admin
                ->when(
                    ! $user->can('system access global admin') && ! $user->can('system access admin'),
                    function ($query) use ($user) {
                        $query->where(function ($q) use ($user) {

                            if ($user->can('system access reviewer')) {
                                // Reviewers can see users with 'system access user' or 'system access reviewer'
                                $q->where(function ($inner) {
                                    $inner->whereHas('permissions', function ($permQuery) {
                                        $permQuery->whereIn('name', [
                                            'system access user',
                                            'system access reviewer',
                                        ]);
                                    })->orWhereHas('roles.permissions', function ($permQuery) {
                                        $permQuery->whereIn('name', [
                                            'system access user',
                                            'system access reviewer',
                                        ]);
                                    });
                                });

                            } elseif ($user->can('system access user')) {
                                // Normal users can only see users with 'system access user'
                                $q->where(function ($inner) {
                                    $inner->whereHas('permissions', function ($permQuery) {
                                        $permQuery->where('name', 'system access user');
                                    })->orWhereHas('roles.permissions', function ($permQuery) {
                                        $permQuery->where('name', 'system access user');
                                    });
                                });
                            }

                        });
                    }
                )
                  ->where('id',"!=", $creator_user_id) // do not include the current creator of the project
                ->limit(10)
                ->get();


            } else {
                $this->users = [];
            }

            // dd($this->users);
        }


        public function addSubscriber($userId)
        {
            // Prevent duplicate selection
            if (!in_array($userId, array_column($this->selectedUsers, 'id'))) {
                $user = User::find($userId);
                $this->selectedUsers[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'company' => $user->company
                ];
            }

            // Clear search results
            $this->query = '';
            $this->users = [];
        }

        public function removeSubscriber($index)
        {
            unset($this->selectedUsers[$index]);
            $this->selectedUsers = array_values($this->selectedUsers); // Re-index array
        }
    // ./// For the Search Subscriber Functionality



 
    /**
     * Function to check duplicate name (and lot number if filled)
     * @return void
     */
    protected function checkDuplicateProjectWarning()
    {
        // Clear previous warnings for this field
        $this->clearWarning('name');

        // If both are empty, nothing to check
        if (! $this->name && ! $this->lot_number) {
            return;
        }

        $query = Project::query();

        // If user is a submitter, only check their own projects
        if (Auth::user()->can('system access user')) {
            $query->where('created_by', Auth::id());
        }

        $message = null;

        if ($this->name && $this->lot_number) {
            // Check by BOTH name and lot_number
            $query->where('name', $this->name)
                ->where('lot_number', $this->lot_number);

            $message = 'Warning: A project with the same title and lot number already exists. Please review before saving to avoid duplicate records.';
        } elseif ($this->name) {
            // Check by NAME only
            $query->where('name', $this->name);

            $message = 'Warning: One or more projects with the same title already exist. Please verify that this is not a duplicate project.';
        } else {
            // Optional: if you also want pure lot-only checks, you can enable this
            // $query->where('lot_number', $this->lot_number);
            // $message = 'Warning: A project with the same lot number already exists. Please verify before saving.';
            return; // for now, do nothing if only lot_number is present
        }

        // Ignore the current project when editing
        if ($this->project_id) {
            $query->where('id', '!=', $this->project_id);
        }

        if ($query->exists() && $message) {
            $this->addWarning('name', $message);
        }
    }
    

    public function updated($fields){
        $this->validateOnly($fields,[
            'name' => [
                'required',
                // Rule::unique('projects', 'name')
                //     ->where(fn ($query) => $query->where('lot_number', $this->lot_number))
                //     ->ignore($this->project_id),
                'string', 
                function ($attribute, $value, $fail) {
                    // Check if name is being changed
                    if ($this->project_id) {
                        $existingProject = Project::find($this->project_id);
                        if ($existingProject && $existingProject->name !== $value) {
                            // Only Admin can change the name
                            if ($this->name_override == false && $existingProject->status != "draft"){
                                $fail('Only an admin can modify the project name.');
                            }
                        }
                    }
                },
            ],

             
            'lot_number' => [
                // 'required',
                'string',
            ],
 

            // 'latitude' => 'required|numeric',
            // 'longitude' => 'required|numeric',
            'location' => 'required|string',


            // 'project_number' => [
            //     'required',
            //     'string',
            //     Rule::unique('projects', 'project_number')
            //         ->ignore($this->project_id), // Ensure project_number is unique
            // ],
            // 'rc_number' => [
            //     'required',
            //     'string',
            //     Rule::unique('projects', 'rc_number')
            //     ->ignore($this->project_id), // Ensure rc_number is unique
            // ],

            'agency' => [
                'nullable', 'string', 'required_if:type,Local Government,Federal Government',
            ],

            'type' => [
                'required'
            ],


            'submitter_response_duration' => [
                'required',
                'integer', 
            ],
            'submitter_response_duration_type' => [
                'required',
                'in:day,week,month'
            ],
            'reviewer_response_duration' => [
                'required',
                'integer', 
            ],
            'reviewer_response_duration_type' => [
                'required',
                'in:day,week,month'
            ],
            'submitter_due_date' => [
                'required',
                'date', 
            ],
            'reviewer_due_date' => [
                'required',
                'date', 
            ],

        //     'document_type_id' => [
        //         'nullable',
        //     ],
        //    'attachments' => [
        //         function ($attribute, $value, $fail) {
        //             if (!empty($this->document_type_id)) {
        //                 if (empty($value) || !is_array($value) || count($value) < 1) {
        //                     $fail('The attachments field is required and must contain at least one item when a document type is selected.');
        //                 }
        //             }
        //         },
        //     ],

        ],[
            'latitude.required' => 'Location is required.',
            'longitude.required' => 'Location is required.',
            'location.required' => 'Location name must be searched and is required.',
            'name.unique' => 'The project title is already registered to that lot number.',
        ]);


        if (in_array($fields, ['name', 'lot_number'])) {
            $this->checkDuplicateProjectWarning();
        }

        $this->updateDueDate();
    }

    public function updateDueDate(){
         
             
        $this->reviewer_due_date = Project::calculateDueDate($this->project->updated_at,$this->reviewer_response_duration_type, $this->reviewer_response_duration );
        $this->submitter_due_date = Project::calculateDueDate($this->project->updated_at,$this->submitter_response_duration_type, $this->submitter_response_duration );
        
 

    }

    
     

    /**Check if project is within open and close hours */
    private function isProjectSubmissionAllowed()
    {
        $projectTimer = ProjectTimer::first();

        if ($projectTimer->project_submission_restrict_by_time) {
            $currentTime = now();
            $openTime = $projectTimer->project_submission_open_time;
            $closeTime = $projectTimer->project_submission_close_time;

            if ($currentTime < $openTime || $currentTime > $closeTime) {
                return false;
            }
        }

        return true;
    }




    public function submit_project($project_id){

        ProjectHelper::submit_project($project_id);

    }



 

    /** Project Submission restriction  */
    private function checkProjectRequirements()
    {
        $projectTimer = ProjectTimer::first();

        // DocumentTypes that don't have any reviewers
        $documentTypesWithoutReviewers = DocumentType::whereDoesntHave('reviewers')->pluck('name')->toArray();

        // Check if all document types have at least one reviewer
        $allDocumentTypesHaveReviewers = empty($documentTypesWithoutReviewers);

    
        return [
            'response_duration' => !$projectTimer || (
                !$projectTimer->submitter_response_duration_type ||
                !$projectTimer->submitter_response_duration ||
                !$projectTimer->reviewer_response_duration ||
                !$projectTimer->reviewer_response_duration_type
            ),
            'project_submission_times' => !$projectTimer || (
                !$projectTimer->project_submission_open_time ||
                !$projectTimer->project_submission_close_time ||
                !$projectTimer->message_on_open_close_time
            ),
            'no_reviewers' => Reviewer::count() === 0,
            'no_document_types' => DocumentType::count() === 0,
            'document_types_missing_reviewers' => !$allDocumentTypesHaveReviewers,
        ];
    }



    /**
     * Handle project update.
     */
    public function save()
    {
         

        $this->validate([
            'name' => [
                'required',
                // Rule::unique('projects', 'name')
                //     ->where(fn ($query) => $query->where('lot_number', $this->lot_number))
                //     ->ignore($this->project_id),
                'string', 
                function ($attribute, $value, $fail) {
                    // Check if name is being changed
                    if ($this->project_id) {
                        $existingProject = Project::find($this->project_id);
                        if ($existingProject && $existingProject->name !== $value) {
                            // Only Admin can change the name
                            if ($this->name_override == false && $existingProject->status != "draft"){
                                $fail('Only an admin can modify the project name.');
                            }
                        }
                    }
                },
            ],

             
            'lot_number' => [
                // 'required',
                'string',
            ],


            // 'latitude' => 'required|numeric',
            // 'longitude' => 'required|numeric',
            // 'location' => 'required|string',

            // 'project_number' => [
            //     'required',
            //     'string',
            //     Rule::unique('projects', 'project_number')
            //         ->ignore($this->project_id), // Ensure project_number is unique
            // ],
            // 'rc_number' => [
            //     'required',
            //     'string',
            //     Rule::unique('projects', 'rc_number')
            //     ->ignore($this->project_id), // Ensure rc_number is unique
            // ],


            'agency' => [
                'nullable', 'string', 'required_if:type,Local Government,Federal Government',
            ],                                                                                                                         

            'type' => [
                'required'
            ],

            'submitter_response_duration' => [
                'required',
                'integer', 
            ],
            'submitter_response_duration_type' => [
                'required',
                'in:day,week,month'
            ],
            'reviewer_response_duration' => [
                'required',                                      
                'integer',         
            ],
            'reviewer_response_duration_type' => [
                'required',                                                                                            
                'in:day,week,month'
            ],
            'submitter_due_date' => [
                'required',
                'date', 
            ],
            'reviewer_due_date' => [
                'required',
                'date', 
            ],

        //     'document_type_id' => [
        //         'nullable',
        //     ],
        //    'attachments' => [
        //         function ($attribute, $value, $fail) {
        //             if (!empty($this->document_type_id)) {
        //                 if (empty($value) || !is_array($value) || count($value) < 1) {
        //                     $fail('The attachments field is required and must contain at least one item when a document type is selected.');
        //                 }
        //             }
        //         },
        //     ],

        ],[
            'latitude.required' => 'Location is required.',
            'longitude.required' => 'Location is required.',
            'location.required' => 'Location name must be searched and is required.',
        ]);
 

        //save
        $project = Project::find( $this->project_id);


        $project->name = $this->name;
        
        
        $project->agency = $this->agency;
        $project->type = $this->type;
        $project->description = $this->description; 

        $project->project_number = $this->project_number;
        $project->rc_number = $this->rc_number;
        $project->submitter_response_duration_type = $this->submitter_response_duration_type;
        $project->submitter_response_duration = $this->submitter_response_duration;
        $project->submitter_due_date = $this->submitter_due_date;
        $project->reviewer_response_duration = $this->reviewer_response_duration;
        $project->reviewer_response_duration_type = $this->reviewer_response_duration_type;
        $project->reviewer_due_date = $this->reviewer_due_date;


        $project->latitude = $this->latitude;
        $project->longitude = $this->longitude  ;
        $project->location = $this->location ;
        $project->area = $this->area ;
        $project->street = $this->street ;
        $project->lot_number = $this->lot_number ;

        $project->staff_engineering_data = $this->staff_engineering_data ;
        $project->staff_initials = $this->staff_initials ;
        $project->lot_size = $this->lot_size ;
        $project->unit_of_size = $this->unit_of_size ;
        $project->site_area_inspection = $this->site_area_inspection ;
        $project->burials_discovered_onsite = $this->burials_discovered_onsite ;
        $project->certificate_of_approval = $this->certificate_of_approval ;
        $project->notice_of_violation = $this->notice_of_violation ;

        $project->updated_by = Auth::user()->id;
        $project->updated_at = now();
        $project->save();

        
 


        // delete existing subscribers 
        if(!empty($project->project_subscribers)){
            // delete project subscribers
            if(!empty($project->project_subscribers)){
                foreach($project->project_subscribers as $subcriber){
                    $subcriber->delete();
                } 
            }
        }



        // Save Project Subscribers (if any)
        if (!empty($this->selectedUsers)) {
            foreach ($this->selectedUsers as $user) {
                ProjectSubscriber::create([
                    'project_id' => $project->id,
                    'user_id' => $user['id'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }







        // delete existing project companies 
        if(!empty($project->project_companies)){
            // delete project subscribers
            if(!empty($project->project_companies)){
                foreach($project->project_companies as $company){
                    $company->delete();
                } 
            }
        }



        // Save Project Companies 
         if (!empty($this->companies)) {
            foreach ($this->companies as $index => $company ) {
                if(!empty($company['name'])){
                    ProjectCompany::create([
                        'project_id' => $project->id,
                        'name' => $company['name'],
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);

                }

                
            }
        }



        // delete existing project federal agencies 
        if(!empty($project->project_federal_agencies)){
            // delete project subscribers
            if(!empty($project->project_federal_agencies)){
                foreach($project->project_federal_agencies as $agency){
                    $agency->delete();
                } 
            }
        }

        // Save Project Federal Agencies 
         if (!empty($this->federal_agencies)) {
            foreach ($this->federal_agencies as $index => $agency ) {
                if(!empty($company['name'])){
                    ProjectFederalAgencies::create([
                        'project_id' => $project->id,
                        'name' => $agency['name'],
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
 
            }
        }



 

        // ActivityLog::create([
        //     'log_action' => "Project \"".$this->name."\" updated ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        // Alert::success('Success','Project updated successfully');





        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = ProjectLogHelper::getActivityMessage('updated', $project->id, $authId);

            // get the route
            $route = ProjectLogHelper::getRoute('updated', $project->id);

            // log the event 
            event(new ProjectLogEvent(
                $message ,
                $authId, 
                $project->id,

            ));
    
            /** send system notifications to users */
                
                ProjectNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications
 

 


        return
            // redirect()->route('project.edit',['project' => $this->project->id])
            redirect($route)
            ->with('alert.success',$message)
        ;
    }


    public function delete($project_document_id){
        $project_document = ProjectDocument::findOrFail($project_document_id);
         // delete existing attachments 
         if(!empty($project_document->project_attachments)){
            // delete project attachments
            if(!empty($project_document->project_attachments)){
                foreach($project_document->project_attachments as $attachment){
                    $attachment->delete();
                } 
            }
        }

 
        $project_document->delete();

        ActivityLog::create([
            'log_action' => "Project Document \"".$project_document->document_type->name."\" deleted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);


        //update the project
        $project = Project::find($project_document->project_id);
        $project->updated_at = now();
        $project->updated_by = Auth::user()->id;
        $project->save();

        Alert::success('Success',"Project Document \"".$project_document->document_type->name."\" deleted ");
        return redirect()->route('project.edit',['project' => $this->project->id]);
    }

    
    public function render()
    {
        return view('livewire.admin.project.project-edit');
    }
}
