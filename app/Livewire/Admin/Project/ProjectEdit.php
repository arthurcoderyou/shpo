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
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Validation\Rule;
use App\Models\ProjectSubscriber;
use App\Models\ProjectAttachments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;

class ProjectEdit extends Component
{

    use WithFileUploads;
    public string $name = '';
    public string $description = '';
    public string $federal_agency = ''; 

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

    public $project_types = [
        'Local',
        'Federal',
        'Private'
    ];
    

    public $selected_document_type_id;
    public $projectDocuments = []; // Array of project documents
    public $documentTypes = [];


    public $query = ''; // Search input
    public $users = []; // Search results
    public $selectedUsers = []; // Selected subscribers

    public $home_route;
    public function mount($id){

        $project = Project::find($id);
        $this->project = $project;

        if($project->created_by == Auth::id()){
            $this->home_route = route('project.index');
        }else{
            $this->home_route = route('project.index');
        }


        $this->project_id = $project->id;

        $this->name = $project->name;
        $this->type = $project->type;
        $this->description = $project->description;
        $this->federal_agency = $project->federal_agency;

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
        

        $this->location_directions[] =   Project::select(
                    'latitude', 'longitude'
                )
                ->where('id', $id) 
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
    }

    



    // For the Search Subscriber Functionality
        

        public function updatedQuery()
        {
            if (!empty($this->query)) {
                $this->users = User::where('name', 'like', '%' . $this->query . '%')
                    ->whereNot('id','=',$this->project->created_by)
                    ->limit(10)->get();
            } else {
                $this->users = [];
            }
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
                function ($attribute, $value, $fail) {
                    // Check if name is being changed
                    if ($this->project_id) {
                        $existingProject = Project::find($this->project_id);
                        if ($existingProject && $existingProject->name !== $value) {
                            // Only Admin can change the name
                            if ($this->name_override == false && $existingProject->status != "draft") {
                                $fail('Only an admin can modify the project name.');
                            }
                        }
                    }
                },
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
        
        
        $project->federal_agency = $this->federal_agency;
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



        ActivityLog::create([
            'log_action' => "Project \"".$this->name."\" updated ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project updated successfully');
        return redirect()->route('project.edit',['project' => $this->project->id]);
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
