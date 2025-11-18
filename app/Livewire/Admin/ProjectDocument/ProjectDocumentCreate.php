<?php

namespace App\Livewire\Admin\ProjectDocument;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Project;
use App\Models\Setting;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Models\Attachments;
use App\Models\DocumentType;
use App\Models\ProjectTimer;
use Illuminate\Support\Arr; 
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Helpers\AttachmentHelper;
use App\Events\Attachment\Created;
use App\Events\Attachment\Deleted;
use App\Models\ProjectAttachments;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;
use App\Helpers\ProjectDocumentHelpers;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;


class ProjectDocumentCreate extends Component
{

    use WithFileUploads;
    public string $name = '';
    public string $description = '';
    public string $federal_agency = ''; 

    public $applicant;
    public $document_from;
    public $company;



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

    public $files = [];
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

     
 
 
 

    // public function updated($fields){
    //     $this->validateOnly($fields,[
             
    //        'attachments' => [
    //             function ($attribute, $value, $fail) {
    //                 if (!empty($this->document_type_id)) {
    //                     if (empty($value) || !is_array($value) || count($value) < 1) {
    //                         $fail('The attachments field is required and must contain at least one item when a document type is selected.');
    //                     }
    //                 }
    //             },
    //         ],

    //     ],[
             
    //     ]);

    // }
 
    
     

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



    
    public function save(){

        // $this->validate([
        //     'attachments.*' => File::types([
        //             'jpg','jpeg','png',
        //             'pdf', 'xlsx',
        //             'mp3', 'wav',
        //             'mp4'
        //             ])
        //             ->min('1kb')
        //             ->max('10mb'),
        // ]);


        $this->validate([
            'document_type_id' => 'required',
            'applicant' => ['string','required'],
            'document_from' => ['string','required'],
            'company' => ['string','required'],


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
            'document_type_id.required' => 'Please select a document',
        ]);


         

        //save
        $project = Project::find( $this->project_id);

        
        $project->updated_by = Auth::user()->id;
        $project->updated_at = now();
        $project->save(); 

        //create the project document 
        $project_document = new ProjectDocument();
        $project_document->project_id = $project->id;
        $project_document->document_type_id = $this->document_type_id;
        $project_document->created_by = Auth::user()->id;
        $project_document->updated_by = Auth::user()->id;
        $project_document->save();




        if (!empty($this->attachments)) {
            $now = Carbon::now();
            $dir = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}/".$now->format('Ymd_His');
            $disk = 'public';

            // Flatten in case you have nested arrays from the UI
            foreach ($this->attachments as $file) {

                // Case 1: Livewire/HTTP-uploaded file objects
                if ($file instanceof TemporaryUploadedFile || $file instanceof \Illuminate\Http\UploadedFile) {
                    $originalName = $file->getClientOriginalName();
                    $ext   = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
                    $mime  = $file->getMimeType();
                    $size  = $file->getSize() ?? 0;
                    $tmpPath = $file->getRealPath();

                    $storedName = $now->format('Ymd_His').'-'.$originalName;
                    $file->storeAs($dir, $storedName, $disk);

                // Case 2: Your code supplied an array (e.g., from a custom uploader)
                } elseif (is_array($file)) {
                    // Try to read conventional keys; adjust as needed
                    $originalName = $file['name'] ?? 'attachment';
                    $tmpPath      = $file['tmp_path'] ?? $file['path'] ?? null;

                    if (!$tmpPath || !is_readable($tmpPath)) {
                        Log::warning('Skipping attachment with missing/unreadable tmp path', ['file' => $file]);
                        continue;
                    }

                    $ext  = strtolower($file['extension'] ?? pathinfo($originalName, PATHINFO_EXTENSION));
                    $mime = $file['mime'] ?? @mime_content_type($tmpPath) ?: null;
                    $size = $file['size'] ?? @filesize($tmpPath) ?: 0;

                    $storedName = $now->format('Ymd_His').'-'.$originalName;
                    // Copy the file into your target disk/folder
                    Storage::disk($disk)->putFileAs($dir, new \Illuminate\Http\File($tmpPath), $storedName);

                } else {
                    // Unknown type; skip
                    Log::warning('Skipping unsupported attachment type', ['type' => gettype($file)]);
                    continue;
                }

                // Optional integrity/dimensions
                $sha256 = (isset($tmpPath) && is_readable($tmpPath)) ? @hash_file('sha256', $tmpPath) : null;
                $width = $height = null;
                if ($mime && str_starts_with(strtolower($mime), 'image/') && isset($tmpPath) && is_readable($tmpPath)) {
                    try { [$width, $height] = getimagesize($tmpPath) ?: [null, null]; } catch (\Throwable $e) {}
                }

                // Save DB row (NOTE: use your actual column names; if it's snake_case, change 'storedName' -> 'stored_name')
                ProjectAttachments::create([
                    'attachment'           => $originalName,
                    'project_id'           => $project->id,
                    'project_document_id'  => $project_document->id,
                    'filesystem'           => $disk,
                    'original_name'        => $originalName,
                    'stored_name'           => $storedName,      // or 'stored_name' if that’s your column
                    'disk'                 => $disk,
                    'path'                 => $dir,
                    'mime_type'            => $mime,
                    'extension'            => $ext,
                    'width'                => $width,
                    'height'               => $height,
                    'duration_seconds'     => null,
                    'sha256'               => $sha256,
                    'created_by'           => Auth::id(),
                    'updated_by'           => Auth::id(),
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ]);
            }
        }



        // return true;


        // 'project.project_document.edit_attachments
        ActivityLog::create([
            'log_action' => "Project \"".$this->name."\" updated ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project document created successfully');
        // return redirect()->route('project.project_document',['project' => $this->project->id,'project_document' => $project_document->id]);

        return redirect()->route('project.project-document.show',[
            'project' => $this->project->id,
            'project_document' => $project_document->id
        ]);


    }




     

 


    /**
     * Handle project update.
         public function save()
    {
         
        // set_time_limit(0); // temporary; see “Queue it” below for the real fix


        $document_upload_location = Setting::getOrCreateWithDefaults('document_upload_location');

        if(empty($document_upload_location) && empty($document_upload_location->value) ){

            Alert::error('Error','The Document Upload location had not been set by the administrator');
            return redirect()->route('project.project-document.index',['project' => $this->project->id] );

        } 


        $disk = $document_upload_location->value;

        if($disk == "ftp"){

  
            // 3) Fast preflight check (no Flysystem call yet — avoids long hangs)
            $diskCfg = config("filesystems.disks.$disk", []);
            $driver  = Arr::get($diskCfg, 'driver');


            // Default ports
            $host = Arr::get($diskCfg, 'host');
            $port = (int) Arr::get($diskCfg, 'port', $driver === 'sftp' ? 22 : 21);

            // Only ping when we actually have a remote disk config
            if (in_array($driver, ['ftp', 'sftp'], true) && $host) {
                $errno = 0; $errstr = '';
                // 3-second connect timeout — fail fast
                $conn = @fsockopen($host, $port, $errno, $errstr, 3);
                if (!$conn) {
                    Alert::error('Error', "Connection can’t be established with the {$disk} server ($host:$port).");
                    return redirect()->route('project.project-document.index', ['project' => $this->project->id]);
                }
                fclose($conn);
            }
            
        }elseif($disk == "public"){
            dd($disk);


        }

 

        $this->validate([
            'document_type_id' => 'required',
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
            'document_type_id.required' => 'Please select a document type',
        ]);

        
        

        if (!empty($this->attachments)) {

            // check connection if it is on ftp 
            if($disk == "ftp"){
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
            }
    

            //save
            $project = Project::find( $this->project_id);
    
            
            $project->updated_by = Auth::user()->id;
            $project->updated_at = now();
            $project->save(); 

            //create the project document 
            $project_document = new ProjectDocument();
            $project_document->project_id = $project->id;
            $project_document->document_type_id = $this->document_type_id;
            $project_document->created_by = Auth::user()->id;
            $project_document->updated_by = Auth::user()->id;
            $project_document->save();

            $now = Carbon::now(); // to ensure that only one date time will be used 

 
            foreach ($this->attachments as $file) {
         
                 
                $disk = $document_upload_location->value;
                // $originalFileName = $file['name'] ?? 'attachment';
                // $extension = $file['extension'] ?? pathinfo($originalFileName, PATHINFO_EXTENSION);
                // $baseName = pathinfo($originalFileName, PATHINFO_FILENAME);


                $filename = $file->getClientOriginalName(); 

                // Core facts
                $originalName = $file->getClientOriginalName();
                $ext          = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
                $mime         = $file->getMimeType();        // server-inferred, more trustworthy than ext
                $size         = $file->getSize() ?? 0;       // bytes
                $tmpPath      = $file->getRealPath();        // temp file for hashing/dimensions
 
                // $fileName = Carbon::now()->timestamp . '-p_' . $project->id . '-pd_' . $project_document->id . '-pdt' . $project_document->document_type->name . '-' . $baseName . '.' . $extension;
                $fileName = $now->timestamp.'-'.$originalName . '.' . $ext;

                $sourcePath = $file['path'];

                if (!file_exists($sourcePath)) {
                    logger()->warning("Source file does not exist: $sourcePath");
                    return;
                }


                
                // Category
                // $category     = FileCategoryHelper::categorize($mime, $ext);

                 // Unique stored name (timestamp + uuid + ext)
                // $storedName   = now()->format('Ymd_His') . '-' . Str::uuid() . ($ext ? ".{$ext}" : '');
                
                 
                // Read the file content once
                $fileContents = file_get_contents($sourcePath);

                // Common path used for all disks (relative)
                // dir
                $dir = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}/{$now}/{$fileName}";

                // FTP Upload
                if ($disk === 'ftp') {
                    $uploadSuccess = Storage::disk('ftp')->put($dir, $fileContents);

                    if (!$uploadSuccess) {
                        logger()->error("FTP upload failed: $dir");
                    } else {
                        @unlink($sourcePath);
                    }

                    
                }elseif($disk === 'local'){



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



                }elseif($disk == "public"){

 
                    // Store the file in the "photos" directory of the default filesystem disk
                    $file->storeAs(path: $dir,name: $fileName,options: $disk); 

                }

                // $attachment->original_name = $originalName;
                //     // $attachment->stored_name   = $storedName;
                //     $attachment->disk          = $disk;
                //     $attachment->path          = $dir; 

                //     // $attachment->category      = $category;
                //     $attachment->mime_type     = $mime;
                //     $attachment->extension     = $ext;
                //     $attachment->size_bytes    = $size;



                //     // Optional: SHA-256 for dedupe/integrity   
                //     $sha256 = null;
                //     if (is_readable($tmpPath)) {
                //         $sha256 = hash_file('sha256', $tmpPath);
                //     }

                //     // Optional: image dimensions
                //     $width = $height = null;
                //     if (str_starts_with(strtolower($mime ?? ''), 'image/')) {
                //         try {
                //             [$width, $height] = getimagesize($tmpPath) ?: [null, null];
                //         } catch (\Throwable $e) {
                //             Log::warning("Could not read image dimensions: {$originalName}", ['e' => $e->getMessage()]);
                //         }
                //     }


                //     $attachment->width         = $width;
                //     $attachment->height        = $height;
                //     $attachment->duration_seconds = null; // fill later if you add FFmpeg/getID3

                //     $attachment->sha256        = $sha256;

        
                // Save to the database
                ProjectAttachments::create([
                    'attachment' => $fileName,
                    'project_id' => $project->id,
                    'project_document_id' => $project_document->id,
                    'filesystem' => $disk,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            
            ActivityLog::create([
                'log_action' => "Project \"".$this->name."\" updated ",
                'log_username' => Auth::user()->name,
                'created_by' => Auth::user()->id,
            ]);

            Alert::success('Success','Project document created successfully');
            // return redirect()->route('project.project_document',['project' => $this->project->id,'project_document' => $project_document->id]);

            return redirect()->route('project.project-document.show',[
                'project' => $this->project->id,
                'project_document' => $project_document->id
            ]);

        }else{

            Alert::error('Error','The uploaded attachments for the document is missing');
            return redirect()->route('project.project-document.index',['project' => $this->project->id] );


        }

  

    }

    */


    
    public function render()
    {
        return view('livewire.admin.project-document.project-document-create',[
            
        ]);
    }
}
