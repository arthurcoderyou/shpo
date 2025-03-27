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
use Livewire\WithFileUploads;
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

    // public $attachments = []; // Initialize with one phone field
    // public $uploadedFiles = []; // Store file names

    public $existingFiles = [];


    public $project_number;
    public $shpo_number;
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

 
    public function mount($id){

        $project = Project::find($id);
        $this->project = $project;

        $this->project_id = $project->id;

        $this->name = $project->name;
        $this->type = $project->type;
        $this->description = $project->description;
        $this->federal_agency = $project->federal_agency;

        $this->project_number = $project->project_number ? $project->project_number: Project::generateProjectNumber();
        $this->shpo_number = $project->shpo_number ? $project->shpo_number : Project::generateProjectNumber(rand(10, 99)) ;
        $this->submitter_due_date = $project->submitter_due_date;
        $this->reviewer_due_date = $project->reviewer_due_date;
        $this->submitter_response_duration_type = $project->submitter_response_duration_type;
        $this->submitter_response_duration = $project->submitter_response_duration;
        $this->reviewer_response_duration = $project->reviewer_response_duration;
        $this->reviewer_response_duration_type = $project->reviewer_response_duration_type; 

        /**default is Guam coordinates */
        $this->latitude = $project->latitude ?? 13.4443; 
        $this->longitude = $project->longitude ?? 144.7937;
        $this->location = $project->location ?? "Guam";
        

        $this->location_directions[] =   Project::select(
                    'latitude', 'longitude'
                )
                ->where('id', $id) 
                ->get()
                ->toArray();

        // Load existing attachments

        // if(!empty($project->attachments)){
        //     $this->existingFiles = $project->attachments->map(function ($attachment) {
        //         return [
        //             'id' => $attachment->id,
        //             'name' => basename($attachment->attachment), // File name
        //             'path' => asset('storage/uploads/project_attachments/' . $attachment->attachment), // Public URL
        //         ];
        //     })->toArray();


        //     // dd($this->existingFiles);
        // }


        if (!empty($project->project_documents)) {
            $this->existingFiles = $project->project_documents
                ->sortByDesc('created_at') // Ensure newest files appear first
                ->groupBy(function ($document) {
                    return $document->created_at->format('M d, Y h:i A'); // Group by date
                })
                // ->map(function ($attachments) {
                //     return $attachments->map(function ($attachment) {
                //         return [
                //             'id' => $attachment->id,
                //             'name' => basename($attachment->attachment), // File name
                //             'path' => asset('storage/uploads/project_attachments/' . $attachment->attachment), // Public URL
                //         ];
                //     })->toArray();
                // })
                
                ->toArray();
        }
        // dd($project->project_documents);

        // if (!empty($project->project_documents)) {
        //     $this->existingFiles = ProjectDocument::with('project_attachments')
        //         ->where('project_id', $project->id)
        //         ->orderBy('created_at', 'desc')
        //         ->get()
        //         ->groupBy(function ($document) {
        //             return Carbon::parse($document->created_at)->format('Y-m-d'); // Groups by date (YYYY-MM-DD)
        //         });
        // }




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


        $this->documentTypes = DocumentType::all();
        $this->addProjectDocument();
        

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
        // // dd($key);
        // if (str_contains($key, 'document_type_id')) {

           


        //     $index = explode('.', $key)[1]; 
        //     $selectedType = $this->projectDocuments[$index]['document_type_id'] ?? null;
    
        //     if (!$selectedType) {
        //         $this->addError("projectDocuments.{$index}.document_type_id", "Please select a document type.");
        //         // dd("Please select a document type.");
        //         return;
        //     } elseif ($this->isDuplicateSelection($selectedType, $index)) {
        //         $this->addError("projectDocuments.{$index}.document_type_id", "This document type has already been selected.");
        //         $this->projectDocuments[$index]['document_type_id'] = null; // Reset selection
        //         return;
        //     } else {
        //         $this->resetErrorBag("projectDocuments.{$index}.document_type_id"); // Clear error
        //     }
        // }



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


    private function isDuplicateSelection($selectedType, $currentIndex)
    {
        foreach ($this->projectDocuments as $index => $document) {
            if ($index != $currentIndex && ($document['document_type_id'] ?? null) == $selectedType) {
                return true;
            }
        }
        return false;
    }


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


            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'required|string',


            // 'project_number' => [
            //     'required',
            //     'string',
            //     Rule::unique('projects', 'project_number')
            //         ->ignore($this->project_id), // Ensure project_number is unique
            // ],
            // 'shpo_number' => [
            //     'required',
            //     'string',
            //     Rule::unique('projects', 'shpo_number')
            //     ->ignore($this->project_id), // Ensure shpo_number is unique
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

    
    public function removeUploadedAttachment(int $id){

        // dd($id, gettype($id)); // Check the actual value and type
        // dd($id);
        // Find the attachment record
        $attachment = ProjectAttachments::find($id);

        if (!$attachment) {
            session()->flash('error', 'Attachment not found.');
            return;
        }

        // Construct the full file path
        $filePath = "public/uploads/project_attachments/{$attachment->attachment}";

        // Check if the file exists in storage and delete it
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // Delete the record from the database
        $attachment->delete();


        Alert::success('Success','Project attachment deleted successfully');
        return redirect()->route('project.edit',['project' => $attachment->project_id]);


    }


    public function submit_project($project_id){

        $this->save();


        
        $project = Project::find($project_id);
        $response_time_hours = 0;
        
        /** Update the response time */

            // Ensure updated_at is after created_at
            if ($this->project->updated_at && now()->greaterThan($project->updated_at)) {
                // Calculate time difference in hours
                // $response_time_hours = $this->project->updated_at->diffInHours(now()); 
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

            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'required|string',

            // 'project_number' => [
            //     'required',
            //     'string',
            //     Rule::unique('projects', 'project_number')
            //         ->ignore($this->project_id), // Ensure project_number is unique
            // ],
            // 'shpo_number' => [
            //     'required',
            //     'string',
            //     Rule::unique('projects', 'shpo_number')
            //     ->ignore($this->project_id), // Ensure shpo_number is unique
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

        ],[
            'latitude.required' => 'Location is required.',
            'longitude.required' => 'Location is required.',
            'location.required' => 'Location name must be searched and is required.',
        ]);


        // if (!empty($this->projectDocuments)) {
        //     $this->validate([
        //         'projectDocuments.*.document_type_id' => [
        //             'required', 
        //             'exists:document_types,id', 
        //             function ($attribute, $value, $fail) {
        //                 $selectedTypes = array_column($this->projectDocuments, 'document_type_id');
        //                 if (count(array_filter($selectedTypes)) !== count(array_unique(array_filter($selectedTypes)))) {
        //                     $fail('Each document type must be unique.');
        //                 }
        //             },
        //         ],
        //         'projectDocuments.*.attachments.*' => 'file|mimes:png,jpeg,jpg,pdf,docx,xlsx,csv,txt,zip|max:20480',
        //     ], [
        //         'projectDocuments.*.document_type_id.required' => 'Please select a document type.',
        //         'projectDocuments.*.document_type_id.exists' => 'The selected document type is invalid.',
                
        //         'projectDocuments.*.attachments.*.file' => 'Each attachment must be a valid file.',
        //         'projectDocuments.*.attachments.*.mimes' => 'Only PNG, JPEG, JPG, PDF, DOCX, XLSX, CSV, TXT, and ZIP files are allowed.',
        //         'projectDocuments.*.attachments.*.max' => 'Each file must not exceed 20MB.',
        //     ]);
        // }
        

        

        //save
        $project = Project::find( $this->project_id);


        $project->name = $this->name;
        
        
        $project->federal_agency = $this->federal_agency;
        $project->type = $this->type;
        $project->description = $this->description; 

        $project->project_number = $this->project_number;
        $project->shpo_number = $this->shpo_number;
        $project->submitter_response_duration_type = $this->submitter_response_duration_type;
        $project->submitter_response_duration = $this->submitter_response_duration;
        $project->submitter_due_date = $this->submitter_due_date;
        $project->reviewer_response_duration = $this->reviewer_response_duration;
        $project->reviewer_response_duration_type = $this->reviewer_response_duration_type;
        $project->reviewer_due_date = $this->reviewer_due_date;


        $project->latitude = $this->latitude;
        $project->longitude = $this->longitude  ;
        $project->location = $this->location ;

        $project->updated_by = Auth::user()->id;
        $project->updated_at = now();
        $project->save();

        

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
        return redirect()->route('project.index');
    }




    
    public function render()
    {
        return view('livewire.admin.project.project-edit');
    }
}
