<?php

namespace App\Livewire\Admin\Project;

use Carbon\Carbon;
use App\Models\User;
// use App\Models\Forum;
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
    public $shpo_number;
    public $submitter_due_date;
    public $reviewer_due_date;

    public $submitter_response_duration_type = "day";
    public $submitter_response_duration = 1; 
    public $reviewer_response_duration = 1;
    public $reviewer_response_duration_type = "day";  
 

    public $latitude;
    public $longitude;
    public $location;

    public $project;

    public $project_types = [
        'Local',
        'Federal',
        'Private'
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


    public function mount(){
        $this->latitude = 13.4443;
        $this->longitude = 144.7937;

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
        // $this->shpo_number = Project::generateProjectNumber(rand(10, 99));


        $this->documentTypes = DocumentType::orderBy('order','ASC')->get(); 


    }


    // For the Search Subscriber Functionality
        public $query = ''; // Search input
        public $users = []; // Search results
        public $selectedUsers = []; // Selected subscribers

        public function updatedQuery()
        {
            if (!empty($this->query)) {
                $this->users = User::where('name', 'like', '%' . $this->query . '%')->limit(10)->get();
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
            ],

            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'required|string',

            // 'project_number' => [
            //     // 'required',
            //     'string',
            //     Rule::unique('projects', 'project_number'), // Ensure project_number is unique
            // ],
            // 'shpo_number' => [
            //     // 'required',
            //     'string',
            //     Rule::unique('projects', 'shpo_number'), // Ensure shpo_number is unique
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
            
            'document_type_id' => [
                'required',
            ],
            'attachments' => [
                'required',
                'array',
                'min:1', // Ensure at least one attachment
            ],

        ],[
            'latitude.required' => 'Location is required.',
            'longitude.required' => 'Location is required.',
            'location.required' => 'Location name must be searched and is required.',
            'federal_agency.required' => 'Company is required'
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


        // Check FTP connection before processing
        try {
            Storage::disk('ftp')->exists('/'); // Basic check
            // dd("ftp works");

        } catch (\Exception $e) {
            // Handle failed connection
            // logger()->error("FTP connection failed: " . $e->getMessage());
            // return; // Exit or show error as needed

            Alert::error('Error','Connection cannot be stablished with the FTP server');
            return redirect()->route('project.create' );

        }
        // dd("ftp does not work");

        // $errors = $this->checkProjectRequirements(); // only required on project submission
         


        // dd($this->all());

        $this->validate([
            'name' => [
                'required',
                'string', 
            ],

            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'required|string',

            // 'project_number' => [
            //     // 'required',
            //     'string',
            //     Rule::unique('projects', 'project_number'), // Ensure project_number is unique
            // ],
            // 'shpo_number' => [
            //     // 'required',
            //     'string',
            //     Rule::unique('projects', 'shpo_number'), // Ensure shpo_number is unique
            // ],
            'description' => [
                'required'
            ],
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

            'document_type_id' => [
                'required',
            ],
            'attachments' => [
                'required',
                'array',
                'min:1', // Ensure at least one attachment
            ],


        ],[
            'latitude.required' => 'Location is required.',
            'longitude.required' => 'Location is required.',
            'location.required' => 'Location name must be searched and is required.', 
            'federal_agency.required' => 'Company is required' 
        ]);


        
        

        

        //save
        $project = Project::create([
            'name' => $this->name,
            'federal_agency' => $this->federal_agency,
            'type' => $this->type,
            'allow_project_submission' => true,
            'description' => $this->description,

            'project_number' => $this->project_number,
            'shpo_number' => $this->shpo_number,
            'submitter_response_duration_type' => $this->submitter_response_duration_type,
            'submitter_response_duration' => $this->submitter_response_duration,
            'submitter_due_date' => $this->submitter_due_date,
            'reviewer_response_duration' => $this->reviewer_response_duration,
            'reviewer_response_duration_type' => $this->reviewer_response_duration_type,
            'reviewer_due_date' => $this->reviewer_due_date,

            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location' => $this->location,

            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        

        if (!empty($this->attachments)) {

            try {
                Storage::disk('ftp')->exists('/'); // Basic check
                // dd("ftp works");

            } catch (\Exception $e) {
                // Handle failed connection
                logger()->error("FTP connection failed: " . $e->getMessage());
                // return; // Exit or show error as needed

                Alert::error('Error','Connection cannot be stablished with the FTP server');
                return redirect()->route('project.create' );

            }


            //create the project document 
            $project_document = new ProjectDocument();
            $project_document->project_id = $project->id;
            $project_document->document_type_id = $this->document_type_id;
            $project_document->created_by = Auth::user()->id;
            $project_document->updated_by = Auth::user()->id;
            $project_document->save();



           
            $date = now(); // to ensure that only one date time will be used 

            // try {
            //     event(new \App\Events\ProjectCreated($project));
            // } catch (\Throwable $e) {
            //     // Log the error without interrupting the flow
            //     Log::error('Failed to dispatch ProjectCreated event: ' . $e->getMessage(), [
            //         'project_id' => $project->id,
            //         'trace' => $e->getTraceAsString(),
            //     ]);
            // }

            // event(new  \App\Events\ProjectDocumentCreated($project_document));


            foreach ($this->attachments as $file) {
        
                // // Store the original file name
                // $originalFileName = $file->getClientOriginalName(); 

                // // Generate a unique file name
                // $fileName = Carbon::now()->timestamp . '-' . $project->id . '-' . $originalFileName . '.' . $file->getClientOriginalExtension();

                // // Generate a unique file name
                // $fileName = Carbon::now()->timestamp . '-' . $review->id . '-' . uniqid() . '.' . $file['extension'];


                /*
                    $originalFileName = $file['name'] ?? 'attachment';
                    $extension = $file['extension'] ?? pathinfo($originalFileName, PATHINFO_EXTENSION);

                    $fileName = Carbon::now()->timestamp . '-' . $project->id . '-' . pathinfo($originalFileName, PATHINFO_FILENAME) . '.' . $extension;


            
                    // Move the file manually from temporary storage
                    $sourcePath = $file['path'];
                    $destinationPath = storage_path("app/public/uploads/project_attachments/{$fileName}");
            
                    // Ensure the directory exists
                    if (!file_exists(dirname($destinationPath))) {
                        mkdir(dirname($destinationPath), 0777, true);
                    }
            
                    // Move the file to the destination
                    if (file_exists($sourcePath)) {
                        rename($sourcePath, $destinationPath);
                    } else {
                        // Log or handle the error (file might not exist at the temporary path)
                        continue;
                    }
                */

                $originalFileName = $file['name'] ?? 'attachment';
                $extension = $file['extension'] ?? pathinfo($originalFileName, PATHINFO_EXTENSION);
                $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);

                $fileName = Carbon::now()->timestamp . '-p_' . $project->id . '-pd_' . $project_document->id. '-pdt' .$project_document->document_type->name. '-' . $baseName . '.' . $extension;

                $sourcePath = $file['path'];

                if (!file_exists($sourcePath)) {
                    logger()->warning("Source file does not exist: $sourcePath");
                    continue;
                }

                // Read the file content
                $fileContents = file_get_contents($sourcePath);

                // Destination path on FTP
                $ftpPath = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}_{$project_document->document_type->name}/{$date}/{$fileName}";

                // Create directory if not exists (Flysystem handles this automatically when uploading a file)
                $uploadSuccess = Storage::disk('ftp')->put($ftpPath, $fileContents);

                if (!$uploadSuccess) {
                    logger()->error("Failed to upload file to FTP: $ftpPath");
                    continue;
                }

                // Delete local temp file
                unlink($sourcePath);

 
        
                // Save to the database
                // ProjectAttachments::create([
                //     'attachment' => $fileName,
                //     'project_id' => $project->id,
                //     'project_document_id' => $project_document->id,
                //     'created_by' => Auth::user()->id,
                //     'updated_by' => Auth::user()->id,
                //     'created_at' => $date ,
                //     'updated_at' => $date ,
                // ]);

                $attachment = new ProjectAttachments([
                    'attachment' => $fileName,
                    'project_id' => $project->id,
                    'project_document_id' => $project_document->id,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                $attachment->timestamps = false;
                $attachment->created_at = $date;
                $attachment->updated_at = $date;
                $attachment->save();




            }
        }


       

        // project timer 
        
         

        // if (!empty($this->attachments)) {
        //     foreach ($this->attachments as $file) {

        //         dd($this->attachments);

        //         // if ($file instanceof \Illuminate\Http\UploadedFile) { // Ensure it's a file

        //             // dd($this->attachments);

        //             // Generate a unique file name
        //             $fileName = Carbon::now()->timestamp . '-'.$project->id.'-' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        //             // Store the file in 'uploads/project_attachments' (inside the 'public' disk)
        //             $file->storeAs('uploads/project_attachments', $fileName, 'public');
        
        //             // Optionally save the filename to the database
        //             // $project->attachments()->create([
        //             //     'file_name' => $fileName,
        //             // ]);

        //             ProjectAttachments::create([
        //                 'attachment' => $fileName, 
        //                 'project_id' => $project->id,
        //                 'created_by' => Auth::user()->id,
        //                 'updated_by' => Auth::user()->id,
        //             ]);
            



        //         // }
        //     }
        // }

        // if (!empty($this->attachments)) {
        //     foreach ($this->attachments as $file) {
        
        //         // Generate a unique file name
        //         $fileName = Carbon::now()->timestamp . '-' . $project->id . '-' . uniqid() . '.' . $file['extension'];
        
        //         // Move the file manually from temporary storage
        //         $sourcePath = $file['path'];
        //         $destinationPath = storage_path("app/public/uploads/project_attachments/{$fileName}");
        
        //         // Ensure the directory exists
        //         if (!file_exists(dirname($destinationPath))) {
        //             mkdir(dirname($destinationPath), 0777, true);
        //         }
        
        //         // Move the file to the destination
        //         if (file_exists($sourcePath)) {
        //             rename($sourcePath, $destinationPath);
        //         } else {
        //             // Log or handle the error (file might not exist at the temporary path)
        //             continue;
        //         }
        
        //         // Save to the database
        //         ProjectAttachments::create([
        //             'attachment' => $fileName,
        //             'project_id' => $project->id,
        //             'created_by' => Auth::user()->id,
        //             'updated_by' => Auth::user()->id,
        //         ]);
        //     }
        // }

        // if (!empty($this->attachments)) {
        //     foreach ($this->attachments as $file) {
                
        //         // Store the original file name
        //         $originalFileName = $file['name']; // Assuming 'name' contains the original file name
        
        //         // Generate a unique file name
        //         $fileName = Carbon::now()->timestamp . '-' . $project->id . '-' . uniqid() . '.' . $file['extension'];
        
        //         // Move the file manually from temporary storage
        //         $sourcePath = $file['path'];
        //         $destinationPath = storage_path("app/public/uploads/project_attachments/{$fileName}");
        
        //         // Ensure the directory exists
        //         if (!file_exists(dirname($destinationPath))) {
        //             mkdir(dirname($destinationPath), 0777, true);
        //         }
        
        //         // Move the file to the destination
        //         if (file_exists($sourcePath)) {
        //             rename($sourcePath, $destinationPath);
        //         } else {
        //             // Log or handle the error (file might not exist at the temporary path)
        //             continue;
        //         }
        
        //         // Save to the database
        //         ProjectAttachments::create([
        //             'attachment' => $fileName,  // Stored file name 
        //             'project_id' => $project->id,
        //             'created_by' => Auth::user()->id,
        //             'updated_by' => Auth::user()->id,
        //         ]);
        //     }
        // }


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




        // //create the project forum for this project 
        // Forum::create([
        //     'project_id' => $project->id,   
        //     'description' => "This forum serves as a central hub for both project \"".$this->name."\" collaborators and users. Team members can coordinate and share updates, while users can ask questions, provide feedback, and engage in discussions related to the project. It’s a space built for communication, clarity, and community around your project. ",
        //     'title' => "Community Forum for Project Collaboration\"".$this->name."\"",
        //     'created_by' => Auth::user()->id,
        //     'updated_by' => Auth::user()->id,
        // ]);


 


        // ActivityLog::create([
        //     'log_action' => "Project \"".$this->name."\" created ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        
        // try {
        //     event(new \App\Events\ProjectCreated($project));
        // } catch (\Throwable $e) {
        //     // Log the error without interrupting the flow
        //     Log::error('Failed to dispatch ProjectCreated event: ' . $e->getMessage(), [
        //         'project_id' => $project->id,
        //         'trace' => $e->getTraceAsString(),
        //     ]);
        // }


        Alert::success('Success','Project created successfully');
        return redirect()->route('project.show',['project'=> $project->id]);
    }



    /** Project Submission restriction  */
    private function checkProjectRequirements()
    {
        $projectTimer = ProjectTimer::first();

        // DocumentTypes that don't have any reviewers
        $documentTypesWithoutReviewers = DocumentType::whereDoesntHave('reviewers')->pluck('name')->toArray();

        // Check if all document types have at least one reviewer
        $allDocumentTypesHaveReviewers = empty($documentTypesWithoutReviewers);

        // Check if there are reviewers by type
        $hasInitialReviewers = Reviewer::where('reviewer_type', 'initial')->exists();
        $hasFinalReviewers = Reviewer::where('reviewer_type', 'final')->exists();

    
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
            'no_document_types' => DocumentType::count() === 0, // Add a new error condition
            'document_types_missing_reviewers' => !$allDocumentTypesHaveReviewers,
            'no_initial_reviewers' => !$hasInitialReviewers,
            'no_final_reviewers' => !$hasFinalReviewers,
        ];
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

    



    public function submit_project(){


        $errors = $this->checkProjectRequirements();
        $errorMessages = [];

        foreach ($errors as $key => $error) {
            if ($error) {
                switch ($key) {
                    case 'response_duration':
                        $errorMessages[] = 'Response duration settings are not yet configured. Please wait for the admin to set it up.';
                        break;
                    case 'project_submission_times':
                        $errorMessages[] = 'Project submission times are not set. Please wait for the admin to configure them.';
                        break;
                    case 'no_reviewers':
                        $errorMessages[] = 'No reviewers have been set. Please wait for the admin to assign them.';
                        break;
                    case 'no_document_types':
                        $errorMessages[] = 'Document types have not been added. Please wait for the admin to set them up.';
                        break;
                }
            }
        }


        if (!$this->isProjectSubmissionAllowed()) {
            $openTime = ProjectTimer::first()->project_submission_open_time;
            $closeTime = ProjectTimer::first()->project_submission_close_time;

            $errorMessages[] = 'Project submission is currently restricted. Please try again between ' . $openTime->format('h:i A') . ' and ' . $closeTime->format('h:i A');
        }



        if (!empty($errorMessages)) {
            session()->flash('error', implode(' ', $errorMessages));
            return;
        }

 

        $this->validate([
            'name' => [
                'required',
                'string', 
            ],

            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'required|string',

            // 'project_number' => [
            //     // 'required',
            //     'string',
            //     Rule::unique('projects', 'project_number'), // Ensure project_number is unique
            // ],
            // 'shpo_number' => [
            //     // 'required',
            //     'string',
            //     Rule::unique('projects', 'shpo_number'), // Ensure shpo_number is unique
            // ],
            'description' => [
                'required'
            ],
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

        ],[
            'latitude.required' => 'Location is required.',
            'longitude.required' => 'Location is required.',
            'location.required' => 'Location name must be searched and is required.', 
            'federal_agency.required' => 'Company is required' 
        ]);


        if (!empty($this->projectDocuments)) {
            $this->validate([
                'projectDocuments.*.document_type_id' => [
                    'required', 
                    'exists:document_types,id', 
                    function ($attribute, $value, $fail) {
                        $selectedTypes = array_column($this->projectDocuments, 'document_type_id');
                        if (count(array_filter($selectedTypes)) !== count(array_unique(array_filter($selectedTypes)))) {
                            $fail('Each document type must be unique.');
                        }
                    },
                ],
                'projectDocuments.*.attachments.*' => 'file|mimes:png,jpeg,jpg,pdf,docx,xlsx,csv,txt,zip|max:20480',
            ], [
                'projectDocuments.*.document_type_id.required' => 'Please select a document type.',
                'projectDocuments.*.document_type_id.exists' => 'The selected document type is invalid.',
                
                'projectDocuments.*.attachments.*.file' => 'Each attachment must be a valid file.',
                'projectDocuments.*.attachments.*.mimes' => 'Only PNG, JPEG, JPG, PDF, DOCX, XLSX, CSV, TXT, and ZIP files are allowed.',
                'projectDocuments.*.attachments.*.max' => 'Each file must not exceed 20MB.',
            ]);
        }
        

        

        //save
        $project = Project::create([
            'name' => $this->name,
            'federal_agency' => $this->federal_agency,
            'type' => $this->type,
            'allow_project_submission' => true,
            'description' => $this->description,

            'project_number' => $this->project_number,
            'shpo_number' => $this->shpo_number,
            'submitter_response_duration_type' => $this->submitter_response_duration_type,
            'submitter_response_duration' => $this->submitter_response_duration,
            'submitter_due_date' => $this->submitter_due_date,
            'reviewer_response_duration' => $this->reviewer_response_duration,
            'reviewer_response_duration_type' => $this->reviewer_response_duration_type,
            'reviewer_due_date' => $this->reviewer_due_date,

            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location' => $this->location,

            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        
 


        if (!empty($this->projectDocuments) && count($this->projectDocuments) > 0) {


            // Save Project Documents
            foreach ($this->projectDocuments as $doc) {

                if(!empty($doc['document_type_id'])){

                    $projectDocument = ProjectDocument::create([
                        'project_id' => $project->id,
                        'document_type_id' => $doc['document_type_id'],
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);

                    // Handle Attachments (if any)
                    if (!empty($doc['attachments'])) {
                        foreach ($doc['attachments'] as $file) {
                            // Store the original file name
                            $originalFileName = $file->getClientOriginalName(); 

                            // Generate a unique file name
                            $fileName = Carbon::now()->timestamp . '-' . $project->id . '-' . $originalFileName . '.' . $file->getClientOriginalExtension();

                            // Move file to storage/app/public/uploads/project_attachments
                            $filePath = $file->storeAs('uploads/project_attachments', $fileName, 'public');



                            // Save to ProjectAttachments table
                            ProjectAttachments::create([
                                'attachment' => $fileName,  // Stored file name 
                                'project_id' => $project->id,
                                'project_document_id' => $projectDocument->id, // Link to Project Document
                                'created_by' => Auth::id(),
                                'updated_by' => Auth::id(),
                            ]);
                        }
                    }
                
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


        
        $project = Project::find($project->id);
         
        ProjectHelper::submit_project($project);



    }




    public function render()
    {
        return view('livewire.admin.project.project-create');
    }
}
