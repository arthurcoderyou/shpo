<?php

namespace App\Livewire\Admin\Project;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use App\Models\Setting;
use Livewire\Component;
// use App\Models\Forum;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectFederalAgencies;
use Illuminate\Support\Facades\Storage;
use App\Events\Project\ProjectLogEvent; 
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

class ProjectCreate extends Component
{

    use WithFileUploads;
    public string $name = '';
    public string $description = '';
    public string $agency = ''; 
    public $type;

    public $document_type_id;
    public $attachments = []; // Attachments 


    public $project_number;
    public $rc_number;
    public $submitter_due_date;
    public $reviewer_due_date;

    public $submitter_response_duration_type = "day";
    public $submitter_response_duration = 1; 
    public $reviewer_response_duration = 1;
    public $reviewer_response_duration_type = "day";  
 


    public $latitude;
    public $longitude;
    public $location;

    public $street;
    public $area;
    public $lot_number;

    public $project;

    public $staff_engineering_data;
    public $staff_initials;
    public $lot_size;
    public $unit_of_size;
    public $site_area_inspection = false;
    public $burials_discovered_onsite = false;
    public $certificate_of_approval = false;
    public $notice_of_violation = false;

    public $company;

    public $installation;
    public $sub_area;
    public $project_size;


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

    // public function saveLocation()
    // {
    //     $this->validate([
    //         'latitude' => 'required|numeric',
    //         'longitude' => 'required|numeric',
    //         'location' => 'required|string',
    //     ]);

    //     Project::create([
    //         'latitude' => $this->latitude,
    //         'longitude' => $this->longitude,
    //         'location' => $this->location,
    //     ]);

    //     session()->flash('message', 'Location saved successfully!');
    // }

    // public $selected_document_type_id;
    // public $projectDocuments = []; // Array of project documents
    public $documentTypes = [];


    //settings that checks if the project location is required or not 
    public $project_location_bypass;


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



    public function mount(){

        $this->project_location_bypass = Setting::getOrCreateWithDefaults('project_location_bypass');

        // dd($this->project_location_bypass);

        $this->latitude = 13.4443;
        $this->longitude = 144.7937;



        if(!empty($this->project_location_bypass) && $this->project_location_bypass->value == "ACTIVE"){
            $project_default_latitude = Setting::getOrCreateWithDefaults('project_default_latitude'); 

            if(!empty($project_default_latitude)){

                $this->latitude = $project_default_latitude->value ;

            }

            $project_default_longitude = Setting::getOrCreateWithDefaults('project_default_longitude'); 

            if(!empty($project_default_longitude)){

                $this->longitude = $project_default_longitude->value ;

            }

            $project_default_location = Setting::getOrCreateWithDefaults('project_default_location') ; 

            if(!empty($project_default_location)){

                $this->location = $project_default_location->value ;

            }
             


        }

        // dd($this->location);

        // project timer 
        // set the reviewer response rate
        $project_timer = ProjectTimer::first();

        //setup the data for the reviewer to send a reviewer response
        if(!empty($project_timer)){ 
            
            $this->submitter_response_duration_type = $project_timer->submitter_response_duration_type ;
            $this->submitter_response_duration = $project_timer->submitter_response_duration ;
            $this->reviewer_response_duration = $project_timer->reviewer_response_duration ;
            $this->reviewer_response_duration_type = $project_timer->reviewer_response_duration_type ;  

            $this->reviewer_due_date = Project::calculateDueDate(now(),$project_timer->reviewer_response_duration_type, $project_timer->reviewer_response_duration );
            $this->submitter_due_date = Project::calculateDueDate(now(),$project_timer->submitter_response_duration_type, $project_timer->submitter_response_duration );
 
            
        } else{

            $this->reviewer_due_date = Project::calculateDueDate(now(),$this->reviewer_response_duration_type, $this->reviewer_response_duration );
            $this->submitter_due_date = Project::calculateDueDate(now(),$this->submitter_response_duration_type, $this->submitter_response_duration );
            
        }
  
        // dd($this->reviewer_due_date);

        // generate the project number 
        $this->project_number = Project::generateProjectNumber();

        // Generate the project number
        // $this->rc_number = Project::generateProjectNumber(rand(10, 99));


        $this->documentTypes = DocumentType::orderBy('order','ASC')->get(); 

        $this->company = Auth::user()->company ?? '';

    }

 


    // Project name relative data 

        public $projects = [];

        public function updatedName(){
            // dd($this->name);
            $search = $this->name;

            if (empty($search)) {
                $this->projects = null;
                return;
            }

            $user = Auth::user();

            // If somehow no authenticated user, return empty results safely
            if (!$user) {
                $this->projects = [];
                return;
            }

            // Extract selected project IDs to exclude from results
            // $excludedIds = array_column($this->selectedProjects, 'id');

            

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







    // For the Search Subscriber Functionality
        public $query = ''; // Search input
        public $users = []; // Search results
        public $selectedUsers = []; // Selected subscribers

        public function updatedQuery()
        {
            if (!empty($this->query)) {
                $user  = Auth::user();
                $term  = '%' . $this->query . '%';

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
                ->where('id',"!=",$user->id) // do not include the current creator of the project 
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
        // if ($this->project_id) {
        //     $query->where('id', '!=', $this->project_id);
        // }

        if ($query->exists() && $message) {
            $this->addWarning('name', $message);
        }
    }

    public function updated($fields){
        $this->validateOnly($fields,[
            // 'name' => [
            //     'required',
            //     'string',
            //     // Rule::unique('projects', 'name')
            //     //         ->where(fn ($query) => $query->where('lot_number', $this->lot_number)),
            //     // Rule::unique('projects')
            //     //     ->where(fn ($query) => $query
            //     //         ->where('name', $this->name)
            //     //         ->where('lot_number', $this->lot_number)
            //     //         ->where('rc_number', $this->rc_number)
            //     //     ),
            // ],
             'lot_number' => [
                // 'required',
                'string',
            ],

            // 'latitude' => 'required|numeric',
            // 'longitude' => 'required|numeric',
            'location' => 'required|string',

            // 'project_number' => [
            //     // 'required',
            //     'string',
            //     Rule::unique('projects', 'project_number'), // Ensure project_number is unique
            // ],
            // 'rc_number' => [
            //     // 'required',
            //     'string',
            //     Rule::unique('projects', 'rc_number'), // Ensure rc_number is unique
            // ],

            'agency' => [
                'nullable', 'string', 'required_if:type,Local Government,Federal Government',
            ],

            'company' => [
                'nullable', 'string', 'required_if:type,Private',
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
            
            // 'document_type_id' => [
            //     'required',
            // ],
            // 'attachments' => [
            //     'required',
            //     'array',
            //     'min:1', // Ensure at least one attachment
            // ],

        ],[
            'latitude.required' => 'Location is required.',
            'longitude.required' => 'Location is required.',
            'location.required' => 'Location name must be searched and is required.',
            'agency.required' => 'Company is required',
            'name.unique' => 'The project title is already registered to that lot number.',
        ]);


        if (in_array($fields, ['name', 'lot_number'])) {
            $this->checkDuplicateProjectWarning();
        }


        $this->updateDueDate();

 
    }


    

    public function updateDueDate(){
        $project_timer = ProjectTimer::first();
         

        //setup the data for the reviewer to send a reviewer response
        if(!empty($project_timer)){ 
            
             
            $this->reviewer_due_date = Project::calculateDueDate(now(),$project_timer->reviewer_response_duration_type, $project_timer->reviewer_response_duration );
            $this->submitter_due_date = Project::calculateDueDate(now(),$project_timer->submitter_response_duration_type, $project_timer->submitter_response_duration );
            
        } 
        else{
            $this->reviewer_due_date = Project::calculateDueDate(now(),$this->reviewer_response_duration_type, $this->reviewer_response_duration );
            $this->submitter_due_date = Project::calculateDueDate(now(),$this->submitter_response_duration_type, $this->submitter_response_duration );
 

        }
 

    }

   

    /**
     * Project save.
     */
    public function save()
    {

         
        $this->validate([
            'name' => [
                'required',
                'string',
                // Rule::unique('projects', 'name')
                //         ->where(fn ($query) => $query->where('lot_number', $this->lot_number)),
                // Rule::unique('projects')
                //     ->where(fn ($query) => $query
                //         ->where('name', $this->name)
                //         ->where('lot_number', $this->lot_number)
                //         ->where('rc_number', $this->rc_number)
                //     ), 

            ],
            'lot_number' => [
                // 'required',
                'string',
            ],

            // 'latitude' => 'required|numeric',
            // 'longitude' => 'required|numeric',
            // 'location' => 'required|string',

            // 'project_number' => [
            //     // 'required',
            //     'string',
            //     Rule::unique('projects', 'project_number'), // Ensure project_number is unique
            // ],
            // 'rc_number' => [
            //     // 'required',
            //     'string',
            //     Rule::unique('projects', 'rc_number'), // Ensure rc_number is unique
            // ],
            // 'description' => [
            //     'required'
            // ],
            'agency' => [
                'nullable', 'string', 'required_if:type,Local Government,Federal Government',
            ],

            'company' => [
                'nullable', 'string', 'required_if:type,Private',
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

            // 'document_type_id' => [
            //     'required',
            // ],

            // 'attachments' => [
            //     'required',
            //     'array',
            //     'min:1', // Ensure at least one attachment
            // ],


        ],[
            'latitude.required' => 'Location is required.',
            'longitude.required' => 'Location is required.',
            'location.required' => 'Location name must be searched and is required.', 
            'agency.required' => 'Company is required' ,
            'name.unique' => 'The project title is already registered to that lot number.',
        ]);

 
        // dd($this->all());
        //save
        $project = Project::create([
            'name' => $this->name,
            'agency' => $this->type !== "Private" ? $this->agency : '',
            'type' => $this->type,
            'allow_project_submission' => true,
            'description' => $this->description,

            'project_number' => $this->project_number,
            'rc_number' => $this->rc_number,
            'submitter_response_duration_type' => $this->submitter_response_duration_type,
            'submitter_response_duration' => $this->submitter_response_duration,
            'submitter_due_date' => $this->submitter_due_date,
            'reviewer_response_duration' => $this->reviewer_response_duration,
            'reviewer_response_duration_type' => $this->reviewer_response_duration_type,
            'reviewer_due_date' => $this->reviewer_due_date,

            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location' => $this->location,

            'street' => $this->street, 
            'area' => $this->area, 
            'lot_number' => $this->lot_number,


            'staff_engineering_data' => $this->staff_engineering_data ?? User::generateInitials(Auth::user()->name), 
            'staff_initials' => $this->staff_initials ?? User::generateInitials(Auth::user()->name), 
            'lot_size' => $this->lot_size,
            'unit_of_size' => $this->unit_of_size,
            'site_area_inspection' => $this->site_area_inspection,
            'burials_discovered_onsite' => $this->burials_discovered_onsite,
            'certificate_of_approval' => $this->certificate_of_approval,
            'notice_of_violation' => $this->notice_of_violation,


            'installation'  => $this->installation,
            'sub_area'  => $this->sub_area,
            'project_size'  => $this->project_size,
 

            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]); 

   
        $user = User::find(Auth::user()->id);
        $user->company = $this->company;
        $user->save();
           

        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper || message for all
            $message = ProjectLogHelper::getActivityMessage('created', $project->id, $authId,'all');

            // get the route
            $route = ProjectLogHelper::getRoute('created', $project->id);

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
 

         // Save Project Subscribers (if any)
        if (!empty($this->selectedUsers)) {
            foreach ($this->selectedUsers as $index => $user ) {
                ProjectSubscriber::create([
                    'project_id' => $project->id,
                    'user_id' => $user['id'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
 

            }
        }





        // get the message from the helper || message for submitter
        $message = ProjectLogHelper::getActivityMessage('created', $project->id, $authId,'submitter');
        // Alert::success('Success','Project created successfully');
        return 
        // redirect()->route('project.show',['project'=> $project->id])
            redirect($route )
            ->with('alert.success',$message)
            ;
    }

  

    public function render()
    {
        return view('livewire.admin.project.project-create');
    }
}
