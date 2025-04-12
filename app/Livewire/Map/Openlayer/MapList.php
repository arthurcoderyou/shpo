<?php

namespace App\Livewire\Map\Openlayer;

use App\Models\ActivityLog;
use App\Models\DocumentType;
use App\Models\Project;
use App\Models\ProjectAttachments;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use App\Models\ProjectSubscriber;
use App\Models\ProjectTimer;
use App\Models\Review;
use App\Models\Reviewer;
use App\Models\User;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use RealRashid\SweetAlert\Facades\Alert;
use Livewire\Attributes\On;

class MapList extends Component
{
    
    use WithFileUploads;
    public string $name = '';
    public string $description = '';
    public string $federal_agency = ''; 
    public $type;

    // public $attachments = []; // Initialize with one phone field
    // public $uploadedFiles = []; // Store file names


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

    public $markers = [];
    public $marker_count = 0;


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

    public $selected_document_type_id;
    public $projectDocuments = []; // Array of project documents
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


        $this->documentTypes = DocumentType::all();
        $this->addProjectDocument();


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


    // protected $listeners = ['updateLocation'];

    // public function updateLocation($lat, $lng, $name)
    // {
    //     $this->latitude = $lat;
    //     $this->longitude = $lng;
    //     $this->location = $name;
    // }

    // Add a new Project Document with Dropzone Support
    public function addProjectDocument()
    {
        $this->projectDocuments[] = [
            'document_type_id' => null,
            'attachments' => [],
            'uploaded_files' => [] // To track selected files
        ];
    }

    public function removeFile($docIndex, $fileIndex)
    {
        unset($this->projectDocuments[$docIndex]['uploaded_files'][$fileIndex]);
        $this->projectDocuments[$docIndex]['uploaded_files'] = array_values($this->projectDocuments[$docIndex]['uploaded_files']);
    }

 

    // Remove a specific Project Document
    public function removeProjectDocument($index)
    {
        unset($this->projectDocuments[$index]);
        $this->projectDocuments = array_values($this->projectDocuments);
    }


    public function updatedProjectDocuments($value, $key)
    {
         


        // Extract index and field name (e.g., projectDocuments.0.attachments)
        $keys = explode('.', $key);
        if (count($keys) === 3 && $keys[2] === 'attachments') {
            $index = $keys[1];

            // Append new files
            if (is_array($this->projectDocuments[$index]['attachments'])) {
                foreach ($this->projectDocuments[$index]['attachments'] as $file) {
                    $this->projectDocuments[$index]['uploaded_files'][] = [
                        'name' => $file->getClientOriginalName(),
                        'temp_path' => $file->getRealPath()
                    ];
                }
            }

            // Clear attachments input after storing them in uploaded_files
            $this->projectDocuments[$index]['attachments'] = [];
        }
    }


    // // Add a new Marker 
    // #[On('addMarker')]
    // public function addMarker($id)
    // {
    //     $this->markers[] = [ 
    //         'title' => null,
    //         'description' => null,
    //         'fill' => null,
    //         'stroke' => null,
    //     ];


    //     // $this->dispatch('markerAdded', $id); // Notify the frontend
    // }

    #[On('addMarker')]
    public function addMarker()
    {
        $index = count($this->markers); // Get the next available index
        $this->markers[$index] = [ 
            'title' => null,
            'description' => null,
            'fill' => '#FF0000',
            'stroke' => '#000000',
        ];

        $this->dispatch('markerAdded', $index); // Notify the frontend

        // Emit event to JavaScript with the latest count
        // $this->dispatchBrowserEvent('markerCountUpdated', ['count' => count($this->markers)]);


        $this->marker_count = count($this->markers); // Update the marker count
    }


    // removing the marker
    public function removeMarker($index)
    {
        // Remove marker from Livewire array
        if (isset($this->markers[$index])) {
            unset($this->markers[$index]);
            $this->markers = array_values($this->markers); // Re-index array
        }

        // Dispatch event to frontend
        $this->dispatch('markerRemoved', $index);
    }

 
    // // updating the marker
    // public function updateMarker($index)
    // {
    //     // Emit an event to update the marker on the frontend
    //     $this->emit('markerUpdated', $index, $this->markers[$index]['title'], $this->markers[$index]['description']);
    // }




    private function isDuplicateSelection($selectedType, $currentIndex)
    {
        foreach ($this->projectDocuments as $index => $document) {
            if ($index != $currentIndex && ($document['document_type_id'] ?? null) == $selectedType) {
                return true;
            }
        }
        return false;
    }

    public function updatedMarkers($value, $key)
    {
        // Extract the first number from the key
        preg_match('/\d+/', $key, $matches);
        $numericKey = $matches[0] ?? $key; // Default to original key if no number is found

        // Dispatch event with formatted key
        // $this->dispatch('updateMarkerTitle&Description', key: "marker-$numericKey", title: $value);


        $this->dispatch('updateMarkerTitle&Description', key: "$numericKey", title: $value);
    }

    public function updated($field, $value)
{
        // Perform validation
        $this->validateOnly($field, [
            'name' => ['required', 'string'],
            'federal_agency' => ['required'],
            'type' => ['required'],
            'submitter_response_duration' => ['required', 'integer'],
            'submitter_response_duration_type' => ['required', 'in:day,week,month'],
            'reviewer_response_duration' => ['required', 'integer'],
            'reviewer_response_duration_type' => ['required', 'in:day,week,month'],
            'submitter_due_date' => ['required', 'date'],
            'reviewer_due_date' => ['required', 'date'],
        ], [
            'latitude.required' => 'Location is required.',
            'longitude.required' => 'Location is required.',
            'location.required' => 'Location name must be searched and is required.',
            'federal_agency.required' => 'Company is required',
        ]);

        if (str_starts_with($field, 'markers.')) {
            preg_match('/markers\.(\d+)\.title/', $field, $matches);
    
            if (!empty($matches[1])) {
                $index = (int) $matches[1];
                \Log::info("Updating marker at index: {$index}");
    
                // Dispatch event to JavaScript
                $this->dispatch('markerUpdated', $index, $this->markers[$index]);
            }
        }

        $this->updateDueDate();
    }

    public function updateMarkerTitle($index)
    {
        // Validate the title if needed
        $this->validate([
            "markers.{$index}.title" => 'required|string',
        ]);

        // Update the marker title
        $title = $this->markers[$index]['title'];

        // Log to check if it works
        \Log::info("Marker title updated for index {$index}: {$title}");


        // dd($index);

        // Send update back to JavaScript
        $this->dispatch('markerUpdated', $index, $title);
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

    // // Method to add a new attachment input
    // public function addAttachment()
    // {
    //     $this->attachments[] = ''; // Add a new empty attachment input
    // }

 
    // public function updatedAttachments($value, $key)
    // {

    //     // dd($value);

    //     // if ($value) {
    //     //     // Store the uploaded file
    //     //     $fileName = Carbon::now()->timestamp . '-' . uniqid() . '.' . $value->extension();
    //     //     $value->storeAs('uploads/product_reviewers', $fileName, 'public');

    //     //     // Store filename in uploadedFiles array
    //     //     $this->uploadedFiles[$key] = $fileName;
    //     // }
    // }

    /**
     * Handle an incoming registration request.
     */
    public function save()
    {

        dd($this->markers);

        // dd($this->all());

        $this->validate([
            'name' => [
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
            // 'latitude.required' => 'Location is required.',
            // 'longitude.required' => 'Location is required.',
            // 'location.required' => 'Location name must be searched and is required.', 
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

            // 'latitude' => $this->latitude,
            // 'longitude' => $this->longitude,
            // 'location' => $this->location,

            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        


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






        
        



        ActivityLog::create([
            'log_action' => "Project \"".$this->name."\" created ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project created successfully');
        return redirect()->route('project.index');
    }

    public function submit_project(){

        $this->validate([
            'name' => [
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
            // 'latitude.required' => 'Location is required.',
            // 'longitude.required' => 'Location is required.',
            // 'location.required' => 'Location name must be searched and is required.', 
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
        $response_time_hours = 0;
        
        /** Update the response time */

            // Ensure updated_at is after created_at
            if ($project->updated_at && now()->greaterThan($project->updated_at)) {
                // Calculate time difference in hours
                // $response_time_hours = $project->updated_at->diffInHours(now()); 
                $response_time_hours = $project->updated_at->diffInSeconds(now()) / 3600; // shows hours in decimal
            }
 
        /** ./ Update the response time */

        
        // if the project is a draft, create the default values
        if($project->status == "draft"){
            // Fetch all reviewers in order
            $reviewers = Reviewer::orderBy('order')->get();

            foreach ($reviewers as $reviewer) {
                ProjectReviewer::create([
                    'order' => $reviewer->order,
                    'review_status' => 'pending',
                    'project_id' => $project->id,
                    'user_id' => $reviewer->user_id,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                
            }
            
            // while status is true for project reviewer, this means that the project reviewer is the active/current reviewer o
            $reviewer = ProjectReviewer::where('project_id', $project->id)
                ->where('review_status', 'pending') 
                ->orderBy('order', 'asc')
                ->first();


            // update the first reviewer as the current reviewer
            $reviewer->status = true;
            $reviewer->save();


            // submitting a project creates a review that the user had submitted the project
            // the condition is that the project creator id must be hte same to the reviewer id
            Review::create([
                'viewed' => true,
                'project_review' => 'The project had been submitted', // message for draft projects
                'project_id' => $project->id,
                'reviewer_id' =>  $project->created_by,
                'review_status' => 'submitted',
                'created_by' => $project->created_by,
                'updated_by' => $project->created_by,
                'response_time_hours' => $response_time_hours,
                
            ]);


            // Send notification email to reviewer
            $user = User::find( $reviewer->user_id);
            if ($user) {
                Notification::send($user, new ProjectReviewNotification($project, $reviewer));

                //send notification to the database
                Notification::send($user, new ProjectReviewNotificationDB($project, $reviewer));
                

                 // update the subscribers 

                    //message for the subscribers 
                    $message = "The project '".$project->name."' had been submitted by '".Auth::user()->name."'";
            

                    if(!empty($project->project_subscribers)){

                        $sub_project = Project::where('id',$project->id)->first(); // get the project to be used for notification

                        foreach($project->project_subscribers as $subcriber){

                            // subscriber user 
                            $sub_user = User::where('id',$subcriber->user_id)->first();

                            if(!empty($sub_user)){
                                // notify the next reviewer
                                Notification::send($sub_user, new ProjectSubscribersNotification($sub_user, $sub_project,'project_submitted',$message ));
                                /**
                                 * Message type : 
                                 * @case('project_submitted')
                                        @php $message = "A new project, <strong>{$project->name}</strong>, has been submitted for review. Stay tuned for updates."; @endphp
                                        @break

                                    @case('project_reviewed')
                                        @php $message = "The project <strong>{$project->name}</strong> has been reviewed. Check out the latest status."; @endphp
                                        @break

                                    @case('project_resubmitted')
                                        @php $message = "The project <strong>{$project->name}</strong> has been updated and resubmitted for review."; @endphp
                                        @break

                                    @case('project_reviewers_updated')
                                        @php $message = "The list of reviewers for the project <strong>{$project->name}</strong> has been updated."; @endphp
                                        @break

                                    @default
                                        @php $message = "There is an important update regarding the project <strong>{$project->name}</strong>."; @endphp
                                */


                            }
                            


                        }
                    } 
                // ./ update the subscribers 


            }





        }else{ // if not, get the current reviewer

            $reviewer = $project->getCurrentReviewer();
            $reviewer->review_status = "pending";
            $reviewer->save();


            
            // submitting a project creates a review that the user had submitted the project
            // the condition is that the project creator id must be hte same to the reviewer id
            Review::create([
                'viewed' => true,
                'project_review' => 'The project had been re-submitted', // message for draft projects
                'project_id' => $project->id,
                'reviewer_id' =>  $project->created_by,
                'review_status' => 're_submitted',
                'created_by' => $project->created_by,
                'updated_by' => $project->created_by,
                'response_time_hours' => $response_time_hours,
                
            ]);


            // Send notification email to reviewer
            $user = User::find( $reviewer->user_id);
            if ($user) {
                Notification::send($user, new ProjectReviewFollowupNotification($project, $reviewer));

                //send notification to the database
                Notification::send($user, new ProjectReviewFollowupNotificationDB($project, $reviewer));


                // update the subscribers 

                    //message for the subscribers 
                    $message = "The project '".$project->name."' had been re-submitted by '".Auth::user()->name."'";
            

                    if(!empty($project->project_subscribers)){

                        $sub_project = Project::where('id',$project->id)->first(); // get the project to be used for notification

                        foreach($project->project_subscribers as $subcriber){

                            // subscriber user 
                            $sub_user = User::where('id',$subcriber->user_id)->first();

                            if(!empty($sub_user)){
                                // notify the next reviewer
                                Notification::send($sub_user, new ProjectSubscribersNotification($sub_user, $sub_project,'project_resubmitted',$message ));
                                /**
                                 * Message type : 
                                 * @case('project_submitted')
                                        @php $message = "A new project, <strong>{$project->name}</strong>, has been submitted for review. Stay tuned for updates."; @endphp
                                        @break

                                    @case('project_reviewed')
                                        @php $message = "The project <strong>{$project->name}</strong> has been reviewed. Check out the latest status."; @endphp
                                        @break

                                    @case('project_resubmitted')
                                        @php $message = "The project <strong>{$project->name}</strong> has been updated and resubmitted for review."; @endphp
                                        @break

                                    @case('project_reviewers_updated')
                                        @php $message = "The list of reviewers for the project <strong>{$project->name}</strong> has been updated."; @endphp
                                        @break

                                    @default
                                        @php $message = "There is an important update regarding the project <strong>{$project->name}</strong>."; @endphp
                                */


                            }
                            


                        }
                    } 
                // ./ update the subscribers


            }
        }



        $project->status = "submitted";
        $project->allow_project_submission = false; // do not allow double submission until it is reviewed
        $project->updated_at = now();
        $project->save();
        


        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" submitted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project submitted successfully');
        return redirect()->route('project.index');


    }




    public function render()
    {
        // return view('livewire.admin.project.project-create');

        return view('livewire.map.openlayer.map-list');
    }

    // public function render()
    // {
    //     return view('livewire.map.openlayer.map-list');
    // }
}
