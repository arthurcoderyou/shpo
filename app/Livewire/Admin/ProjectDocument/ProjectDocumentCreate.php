<?php

namespace App\Livewire\Admin\ProjectDocument;

use Carbon\Carbon;
use App\Models\User;
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
use App\Models\ProjectAttachments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectDocumentCreate extends Component
{

    use WithFileUploads;
    public string $name = '';
    public string $description = '';
    public string $federal_agency = ''; 

    public $type;

    public $attachments = []; // Attachments 
    public $document_type_id;

    public $existingFiles = [];

 
 

    public $name_override = false;

    public $project_id;

    public $project;
   
    

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
 
  

        // Get used document_type_ids from the project's documents
        $usedDocumentTypeIds = $project->project_documents->pluck('document_type_id')->toArray();

        // Get only document types that are NOT used yet
        $this->documentTypes = DocumentType::whereNotIn('id', $usedDocumentTypeIds)->orderBy('order','ASC')->get();
  
        
        // dd($this->project->project_documents);
    }

     
 
 
 

    public function updated($fields){
        $this->validateOnly($fields,[
             
           'attachments' => [
                function ($attribute, $value, $fail) {
                    if (!empty($this->document_type_id)) {
                        if (empty($value) || !is_array($value) || count($value) < 1) {
                            $fail('The attachments field is required and must contain at least one item when a document type is selected.');
                        }
                    }
                },
            ],

        ],[
             
        ]);

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
         
        $document_upload_location = Setting::getOrCreateWithDefaults('document_upload_location');

        if(empty($document_upload_location) && empty($document_upload_location->value) ){

            Alert::error('Error','The Document Upload location had not been set by the administrator');
            return redirect()->route('project.show',['project' => $this->project->id] );

        } 


        

        // Check FTP connection before processing
        try {
            Storage::disk($document_upload_location->value)->exists('/'); // Basic check
            // dd($document_upload_location->value." works");

        } catch (\Exception $e) {
            // Handle failed connection
            // logger()->error("FTP connection failed: " . $e->getMessage());
            // return; // Exit or show error as needed

            Alert::error('Error','Connection cannot be stablished with the '.$document_upload_location->value.' server');
            return redirect()->route('project.show',['project' => $this->project->id] );

        }



        $this->validate([
            'attachments' => [
                function ($attribute, $value, $fail) {
                    if (!empty($this->document_type_id)) {
                        if (empty($value) || !is_array($value) || count($value) < 1) {
                            $fail('The attachments field is required and must contain at least one item when a document type is selected.');
                        }
                    }
                },
            ],

        ],[
            
        ]);
 

        //save
        $project = Project::find( $this->project_id);
 
         
        $project->updated_by = Auth::user()->id;
        $project->updated_at = now();
        $project->save();

        

        if (!empty($this->attachments)) {

            //create the project document 
            $project_document = new ProjectDocument();
            $project_document->project_id = $project->id;
            $project_document->document_type_id = $this->document_type_id;
            $project_document->created_by = Auth::user()->id;
            $project_document->updated_by = Auth::user()->id;
            $project_document->save();

            $date = now(); // to ensure that only one date time will be used 


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


                $disk = $document_upload_location->value;
                $originalFileName = $file['name'] ?? 'attachment';
                $extension = $file['extension'] ?? pathinfo($originalFileName, PATHINFO_EXTENSION);
                $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);

                $fileName = Carbon::now()->timestamp . '-p_' . $project->id . '-pd_' . $project_document->id . '-pdt' . $project_document->document_type->name . '-' . $baseName . '.' . $extension;

                $sourcePath = $file['path'];

                if (!file_exists($sourcePath)) {
                    logger()->warning("Source file does not exist: $sourcePath");
                    return;
                }

                // Read the file content once
                $fileContents = file_get_contents($sourcePath);

                // Common path used for all disks (relative)
                $relativePath = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}_{$project_document->document_type->name}/{$date}/{$fileName}";

                // ==========================
                // 1. FTP Upload
                // ==========================
                if ($disk === 'ftp') {
                    $uploadSuccess = Storage::disk('ftp')->put($relativePath, $fileContents);

                    if (!$uploadSuccess) {
                        logger()->error("FTP upload failed: $relativePath");
                    } else {
                        @unlink($sourcePath);
                    }

                    
                }else{



                    // ==========================
                    // 2. Local Disk and Public Upload
                    // ==========================
                   
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



                }



        
                // Save to the database
                ProjectAttachments::create([
                    'attachment' => $fileName,
                    'project_id' => $project->id,
                    'project_document_id' => $project_document->id,
                    'filesystem' => $disk,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
            }

            

        }

 



        ActivityLog::create([
            'log_action' => "Project \"".$this->name."\" updated ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project updated successfully');
        return redirect()->route('project.project_document',['project' => $this->project->id,'project_document' => $project_document->id]);
    }

 

    
    public function render()
    {
        return view('livewire.admin.project-document.project-document-create');
    }
}
