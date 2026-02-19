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

    public $type;

    // public $attachments = []; // Attachments 
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



    

 



    public $files = [];
 
    public $record_count = 10;
    public $count = 0;

    public $selected_attachments = [];



    





    public function validateUploadedFile(){

        $this->validate([
            'files.*' => File::types([
                    'jpg','jpeg','png',
                    'pdf', 'xlsx',
                    'mp3', 'wav',
                    'mp4'
                    ])
                    ->min('1kb')
                    ->max('10mb'),


        ]);








        $user = Auth::user(); 

        // $this->dispatch("attachment-created"); 

        try {
            foreach($this->files as $file){
 
                $filename = $file->getClientOriginalName(); 

                // Core facts
                $originalName = $file->getClientOriginalName();
                $ext          = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
                $mime         = $file->getMimeType();        // server-inferred, more trustworthy than ext
                $size         = $file->getSize() ?? 0;       // bytes
                $tmpPath      = $file->getRealPath();        // temp file for hashing/dimensions

                // Category
                // $category     = FileCategoryHelper::categorize($mime, $ext);

                 // Unique stored name (timestamp + uuid + ext)
                // $storedName   = now()->format('Ymd_His') . '-' . Str::uuid() . ($ext ? ".{$ext}" : '');
                $dir          = 'files_'.$user->id;
                $disk         = 'ftp';



                // Store the file in the "photos" directory of the default filesystem disk
                $file->storeAs(path: 'files_'.$user->id,name: $filename,options: 'ftp'); 


                $from = 'files_'.$user->id."/".$filename;

                // check if the file is completely uploaded on the ftp server
                if (Storage::disk('ftp')->exists($from)) { 


                    $attachment = new Attachments();
                    $attachment->attachment = $filename;
    
                    $attachment->original_name = $originalName;
                    // $attachment->stored_name   = $storedName;
                    $attachment->disk          = $disk;
                    $attachment->path          = $dir; 

                    // $attachment->category      = $category;
                    $attachment->mime_type     = $mime;
                    $attachment->extension     = $ext;
                    $attachment->size_bytes    = $size;



                    // Optional: SHA-256 for dedupe/integrity   
                    $sha256 = null;
                    if (is_readable($tmpPath)) {
                        $sha256 = hash_file('sha256', $tmpPath);
                    }

                    // Optional: image dimensions
                    $width = $height = null;
                    if (str_starts_with(strtolower($mime ?? ''), 'image/')) {
                        try {
                            [$width, $height] = getimagesize($tmpPath) ?: [null, null];
                        } catch (\Throwable $e) {
                            Log::warning("Could not read image dimensions: {$originalName}", ['e' => $e->getMessage()]);
                        }
                    }


                    $attachment->width         = $width;
                    $attachment->height        = $height;
                    $attachment->duration_seconds = null; // fill later if you add FFmpeg/getID3

                    $attachment->sha256        = $sha256;




                    $attachment->created_by = Auth::user()->id;
                    $attachment->updated_by = Auth::user()->id;
                    $attachment->save();


                    $this->selected_records[] = $attachment->id;


                }

            }
    
            // Success message
            // $this->dispatch('post-created',type: 'success',message: "Files Uploaded!"); 

            
 


            // clear the files 
            $this->files = [];

             
           
        } catch (\Throwable $e) {
            // Log error for debugging
            Log::error('Photo upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Flash user-friendly error message
            // $this->dispatch('post-created',type: 'error',message: 'Error uploading photos: ' . $e->getMessage());  
            return true;
        }


        if(!empty($attachment)){

            
            /**Private Channel */
            try {


            event(new Created($attachment, Auth::user()->id));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to send Created event: ' . $e->getMessage(), [
                    'authId' => Auth::user()->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            
        }


        return true;

    }




    public function returnFormattedDatetime($datetime){
        return ProjectDocumentHelpers::returnFormattedDatetime($datetime);

    }

    public function returnFormattedUser($user_id){
        return ProjectDocumentHelpers::returnFormattedUser($user_id);
    }


    public function convertBytes($bytes){
        return AttachmentHelper::convertBytes($bytes,"MB");
    }



    public function getAttachmentsProperty()
    {

        $user = Auth::user();
        $userId = $user->id;

        // $projectIdsSub = $this->buildProjectBaseQuery();

            // dd($projectIdsSub );


        $attachments = Attachments::query();
            // Only attachments for the filtered projects
            // ->whereIn('project_id', $projectIdsSub)
            // Avoid N+1
            // ->with([
            //     // 'project:id,name,project_number,status,submitter_due_date,reviewer_due_date',
            //     // 'document_type:id,name' // adjust columns to your table
            // ]);

 
        // $attachments  = $this->applyRouteBasedFilters($attachments );
 
 
        $attachments = $attachments->ownedBy($userId);  
 




        // if (!empty($this->uploaded_from) && !empty($this->uploaded_to)) {
        //     $docs->whereBetween('created_at', [$this->uploaded_from, $this->uploaded_to]);
        // }

        // if(!empty($this->sort_by)){
        //     $docs = $docs->applySortingUsingWhereHas($this->sort_by)  // sorrting with aggregate to other model relationships
        //         ->applySorting($this->sort_by); // sorrting directly to the project document model
        // }else{

        //     $cods = $docs->applySorting($this->sort_by);
            
        // }
 
        // Paginate documents
        $paginated = $attachments->orderBy('created_at','DESC')
            ->paginate($this->record_count);
        $this->project_documents_count = $paginated->total();

        return $paginated;
    }



    public function delete($attachment_id){


        $user = Auth::user();

        $attachment = Attachments::find($attachment_id);


        // dd($attachment);
        if($attachment->attachment){

            $from = 'files_'.$user->id."/".$attachment->attachment;

            // dd(Storage::disk('ftp')->exists($from));

            // check if the file is completely uploaded on the ftp server
            if (Storage::disk('ftp')->exists($from)) { 

                // delete the file on the ftp server
                Storage::disk('ftp')->delete("files_".$user->id."/{$attachment->attachment}"); 


                // check if the file does not exist any more 
                // if (!Storage::disk('ftp')->exists($from)){
                    // delete the database record 
                    // $attachment->delete();
                    // $attachment->save();
                // }
                

            }

            
        }


        // delete the record on the database
        $attachment->delete(); 

 
            
        /**Private Channel */
        try {
 
            event(new Deleted( Auth::user()->id));
        } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to send Created event: ' . $e->getMessage(), [
                'authId' => Auth::user()->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
        

 




        

    }





    /**
     * Handle project update.
     */
    public function save()
    {
         


        $user = Auth::user();



        set_time_limit(0); // temporary; see “Queue it” below for the real fix


        $document_upload_location = Setting::getOrCreateWithDefaults('document_upload_location');

        if(empty($document_upload_location) && empty($document_upload_location->value) ){

            Alert::error('Error','The Document Upload location had not been set by the administrator');
            return redirect()->route('project.project-document.index',['project' => $this->project->id] );

        } 


        $disk = $document_upload_location->value;


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


 

        $this->validate([
            'document_type_id' => 'required',
            'selected_attachments' => [
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

        
        

        if (!empty($this->selected_attachments)) {



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


            foreach ($this->selected_attachments as $key => $attachment_id) {
        
                 
                $attachment = Attachments::find($attachment_id);



                // copy the attachment to the new project folder 

                 if($attachment->attachment){
                    $from = 'files_'.$user->id.'/'.$attachment->attachment;
                    $to   = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}/{$now}/.$attachment->attachment";
                    // $to = "uploads";


                    
                    try { 
                        // dd(Storage::disk('ftp')->exists($from));
                        if (Storage::disk('ftp')->exists($from)) {
                            Storage::disk('ftp')->copy($from, $to);
                            // dd("ok");
                        }

 


                    } catch (\Throwable $e) {
                        // Log the error without interrupting the flow
                        Log::error('Failed to send Copied event: ' . $e->getMessage(), [
                            'authId' => Auth::user()->id,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }





                    

                }

 
 
        
                // Save to the database
                ProjectAttachments::create([
                    'attachment' => $attachment->attachment,
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


    
    public function render()
    {
        return view('livewire.admin.project-document.project-document-create',[
            'attachments' => $this->attachments,
        ]);
    }
}
