<?php

namespace App\Livewire\Admin\Project;

use App\Models\ProjectCompany;
use App\Models\ProjectFederalAgencies;
use Carbon\Carbon;
use App\Models\User;
// use App\Models\Forum;
use App\Models\Review;
use App\Models\Project;
use App\Models\Setting;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use App\Models\ProjectTimer;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Validation\Rule;
use App\Models\ProjectSubscriber;
use App\Models\ProjectAttachments;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;

class ProjectCreate extends Component
{

    use WithFileUploads;
    public string $name = '';
    public string $description = '';
    public string $federal_agency = ''; 
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


    public $project_types = [
        'Local' => 'Local',
        'Federal' => 'Federal', 
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

  

    }

 


    // For the Search Subscriber Functionality
        public $query = ''; // Search input
        public $users = []; // Search results
        public $selectedUsers = []; // Selected subscribers

        public function updatedQuery()
        {
            if (!empty($this->query)) {
                $user = Auth::user();

                $this->users = User::where(function ($mainQuery) use ($user) {
                    $mainQuery->where('name', 'like', '%' . $this->query . '%');

                    // Only apply filters if NOT global admin or admin
                    if (!$user->can('system access global admin') && !$user->can('system access admin')) {

                        $mainQuery->where(function ($q) use ($user) {

                            if ($user->can('system access reviewer')) {
                                // Reviewers can see users with 'system access user' or 'system access reviewer'
                                $q->where(function ($inner) {
                                    $inner->whereHas('permissions', function ($permQuery) {
                                        $permQuery->whereIn('name', ['system access user', 'system access reviewer']);
                                    })->orWhereHas('roles.permissions', function ($permQuery) {
                                        $permQuery->whereIn('name', ['system access user', 'system access reviewer']);
                                    });
                                });
                            } elseif ($user->can('system access user')) {
                                // Users can only see users with 'system access user'
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
                })
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
                $this->selectedUsers[] = ['id' => $user->id, 'name' => $user->name];
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


     
 


    public function updated($fields){
        $this->validateOnly($fields,[
            'name' => [
                'required',
                'string',
                Rule::unique('projects', 'name')
                        ->where(fn ($query) => $query->where('lot_number', $this->lot_number)),
            ],
             'lot_number' => [
                'required',
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

            'federal_agency' => [
                'required'
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
            'federal_agency.required' => 'Company is required',
            'name.unique' => 'The project title is already registered to that lot number.',
        ]);

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
                Rule::unique('projects', 'name')
                        ->where(fn ($query) => $query->where('lot_number', $this->lot_number)),
            ],
             'lot_number' => [
                'required',
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
            // 'federal_agency' => [
            //     'required'
            // ],

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
            'federal_agency.required' => 'Company is required' ,
            'name.unique' => 'The project title is already registered to that lot number.',
        ]);

 
        // dd($this->all());
        //save
        $project = Project::create([
            'name' => $this->name,
            'federal_agency' => $this->federal_agency,
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
 

            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]); 




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
 

 

        Alert::success('Success','Project created successfully');
        return redirect()->route('project.show',['project'=> $project->id]);
    }

  

    public function render()
    {
        return view('livewire.admin.project.project-create');
    }
}
