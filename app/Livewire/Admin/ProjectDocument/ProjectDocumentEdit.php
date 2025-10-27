<?php

namespace App\Livewire\Admin\ProjectDocument;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Setting;
use Livewire\Component;
use App\Models\ActivityLog;
use Illuminate\Support\Arr; 
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Models\ProjectAttachments;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ProjectDocumentHelpers;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\LivewireFilepond\WithFilePond;
use Illuminate\Validation\Rules\File;

class ProjectDocumentEdit extends Component
{

    use WithFilePond;
    use WithFileUploads;
    public $project_id;
    public $project_document_id;

    public $attachments = []; // Attachments 

    public $project;
    public $project_document;

    public $files = [];

    public function mount($project_id, $project_document_id){
        $this->project_id = $project_id;
        $this->project_document_id = $project_document_id;
        $this->project = Project::findOrFail($this->project_id);
        $this->project_document = ProjectDocument::findOrFail($this->project_document_id);
    }


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


        $project_document = ProjectDocument::findOrFail($this->project_document_id);
        $project_document->updated_by = Auth::user()->id;
        $project_document->updated_at = now();
        $project_document->save();


        $project = Project::findOrFail($this->project_id);
        $project->updated_by = Auth::user()->id;
        $project->updated_at = now();
        $project->save();


        if (!empty($this->files)) {
  
            // //create the project document 
            // $project_document = new ProjectDocument();
            // $project_document->project_id = $project->id;
            // $project_document->document_type_id = $this->document_type_id;
            // $project_document->created_by = Auth::user()->id;
            // $project_document->updated_by = Auth::user()->id;
            // $project_document->save();



           
            $now = Carbon::now(); // to ensure that only one date time will be used 

            // dd($this->attachments);
            

            foreach ($this->files as $file) {
        
                

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
                $storedName   = $now->format('Ymd_His') . '-' . $originalName ;
                // dd($storedName);

                $dir          = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}/{$now}";
                $disk         = 'ftp';

 
                // dd($file);
                // Store the file in the "photos" directory of the default filesystem disk
                $file->storeAs(path: $dir,name: $storedName,options: 'ftp'); 





                // $sourcePath = $file['path'];

                // if (!file_exists($sourcePath)) {
                //     logger()->warning("Source file does not exist: $sourcePath");
                //     return;
                // }

                // Read the file content once
                // $fileContents = file_get_contents($sourcePath);

                // // Common path used for all disks (relative)
                // // $relativePath = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}_{$project_document->document_type->name}/{$date}/{$fileName}";

                // $relativePath = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}/{$now}/{$fileName}";
                // // ==========================
                // // 1. FTP Upload
                // // ==========================
                // if ($disk === 'ftp') {
                //     $uploadSuccess = Storage::disk('ftp')->put($relativePath, $fileContents);

                //     if (!$uploadSuccess) {
                //         logger()->error("FTP upload failed: $relativePath");
                //     } else {
                //         @unlink($sourcePath);
                //     }

                    
                // }else{



                //     // ==========================
                //     // 2. Local Disk and Public Upload
                //     // ==========================
                   
                //     $destinationPath = storage_path("app/public/uploads/project_attachments/{$fileName}");
            
                //     // Ensure the directory exists
                //     if (!file_exists(dirname($destinationPath))) {
                //         mkdir(dirname($destinationPath), 0777, true);
                //     }
            
                //     // Move the file to the destination
                //     if (file_exists($sourcePath)) {
                //         rename($sourcePath, $destinationPath);
                //     } else {
                //         // Log or handle the error (file might not exist at the temporary path)
                //         continue;
                //     }



                // }






 
         

                // $attachment = new ProjectAttachments([
                //     'attachment' => $fileName,
                //     'project_id' => $project->id,
                //     'project_document_id' => $project_document->id,
                //     'created_by' => Auth::user()->id,
                //     'updated_by' => Auth::user()->id,
                // ]);

                // $attachment->timestamps = false;
                // $attachment->created_at = $date;
                // $attachment->updated_at = $date;
                // $attachment->save();

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


                // Save to the database
                ProjectAttachments::create([
                    'attachment' => $originalName,
                    'project_id' => $project->id,
                    'project_document_id' => $project_document->id,
                    'filesystem' => $disk,

                    'original_name' => $originalName,
                    'storedName' => $storedName,

                    'disk' => $disk,
                    'path' => $dir,
                    // 'category',
                    'mime_type' => $mime,
                    'extension' => $ext,

                    'width' => $width ,
                    'height' => $height,

                    'duration_seconds' => null,

                    'sha256' => $sha256, 
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                


            }
        }



        return true;

    }


    public function submit_project($project_id){

        ProjectHelper::submit_project($project_id);

    }

    public function submit_project_document($project_document_id){

        ProjectDocumentHelpers::submit_project_document($project_document_id);

    }
    

    public function getExistingFilesProperty()
    {
        if (empty($this->project_document) || $this->project_document->project_attachments->isEmpty()) {
            return [];
        }

       return $this->project_document->project_attachments
            ->sortByDesc('created_at')
            ->groupBy(function ($document) {
                return optional($document->last_submitted_at)->format('M d, Y h:i A') ?? 'Unsubmitted';
            })
            ->toArray();

    }



    public function save(){
        // dd("Here");

        $document_upload_location = Setting::getOrCreateWithDefaults('document_upload_location');

        if(empty($document_upload_location) && empty($document_upload_location->value) ){

            Alert::error('Error','The Document Upload location had not been set by the administrator');
            return redirect()->route('project.project_document.edit_attachments',[
                'project' => $this->project->id,
                'project_document' => $this->project_document->id
            ] );

        } 


        

        // // Check FTP connection before processing
        // try {
        //     Storage::disk($document_upload_location->value)->exists('/'); // Basic check
        //     // dd($document_upload_location->value." works");

        // } catch (\Exception $e) {
        //     // Handle failed connection
        //     // logger()->error("FTP connection failed: " . $e->getMessage());
        //     // return; // Exit or show error as needed

        //     Alert::error('Error','Connection cannot be stablished with the '.$document_upload_location->value.' server');
        //     return redirect()->route('project.project_document.edit_attachments',[
        //         'project' => $this->project->id,
        //         'project_document' => $this->project_document->id
        //     ] );

        // }



        // document_type_id

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


        ]);


 
        $project_document = ProjectDocument::findOrFail($this->project_document_id);
        $project_document->updated_by = Auth::user()->id;
        $project_document->updated_at = now();
        $project_document->save();


        $project = Project::findOrFail($this->project_id);
        $project->updated_by = Auth::user()->id;
        $project->updated_at = now();
        $project->save();

        // if (!empty($this->attachments)) {
  
        //     // //create the project document 
        //     // $project_document = new ProjectDocument();
        //     // $project_document->project_id = $project->id;
        //     // $project_document->document_type_id = $this->document_type_id;
        //     // $project_document->created_by = Auth::user()->id;
        //     // $project_document->updated_by = Auth::user()->id;
        //     // $project_document->save();



           
        //     $now = Carbon::now(); // to ensure that only one date time will be used 

        //     // dd($this->attachments);
            

        //     foreach ($this->attachments as $file) {
        
                

        //        $filename = $file->getClientOriginalName(); 

        //         // Core facts
        //         $originalName = $file->getClientOriginalName();
        //         $ext          = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
        //         $mime         = $file->getMimeType();        // server-inferred, more trustworthy than ext
        //         $size         = $file->getSize() ?? 0;       // bytes
        //         $tmpPath      = $file->getRealPath();        // temp file for hashing/dimensions

        //         // Category
        //         // $category     = FileCategoryHelper::categorize($mime, $ext);

        //          // Unique stored name (timestamp + uuid + ext)
        //         // $storedName   = now()->format('Ymd_His') . '-' . Str::uuid() . ($ext ? ".{$ext}" : '');
        //         $dir          = "uploads/project_attachments/project_{$this->project_id}/project_document_{$this->project_document_id}/{$now}/.$originalName ";
        //         $disk         = 'ftp';


        //         $path = $now = now();
        //         $to   = "uploads/project_attachments/project_240/project_document_238/{$now}/.$originalName ";

        //         // Store the file in the "photos" directory of the default filesystem disk
        //         $file->storeAs(path: $dir,name: $filename,options: 'ftp'); 





        //         // $sourcePath = $file['path'];

        //         // if (!file_exists($sourcePath)) {
        //         //     logger()->warning("Source file does not exist: $sourcePath");
        //         //     return;
        //         // }

        //         // Read the file content once
        //         // $fileContents = file_get_contents($sourcePath);

        //         // // Common path used for all disks (relative)
        //         // // $relativePath = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}_{$project_document->document_type->name}/{$date}/{$fileName}";

        //         // $relativePath = "uploads/project_attachments/project_{$project->id}/project_document_{$project_document->id}/{$now}/{$fileName}";
        //         // // ==========================
        //         // // 1. FTP Upload
        //         // // ==========================
        //         // if ($disk === 'ftp') {
        //         //     $uploadSuccess = Storage::disk('ftp')->put($relativePath, $fileContents);

        //         //     if (!$uploadSuccess) {
        //         //         logger()->error("FTP upload failed: $relativePath");
        //         //     } else {
        //         //         @unlink($sourcePath);
        //         //     }

                    
        //         // }else{



        //         //     // ==========================
        //         //     // 2. Local Disk and Public Upload
        //         //     // ==========================
                   
        //         //     $destinationPath = storage_path("app/public/uploads/project_attachments/{$fileName}");
            
        //         //     // Ensure the directory exists
        //         //     if (!file_exists(dirname($destinationPath))) {
        //         //         mkdir(dirname($destinationPath), 0777, true);
        //         //     }
            
        //         //     // Move the file to the destination
        //         //     if (file_exists($sourcePath)) {
        //         //         rename($sourcePath, $destinationPath);
        //         //     } else {
        //         //         // Log or handle the error (file might not exist at the temporary path)
        //         //         continue;
        //         //     }



        //         // }






 
         

        //         // $attachment = new ProjectAttachments([
        //         //     'attachment' => $fileName,
        //         //     'project_id' => $project->id,
        //         //     'project_document_id' => $project_document->id,
        //         //     'created_by' => Auth::user()->id,
        //         //     'updated_by' => Auth::user()->id,
        //         // ]);

        //         // $attachment->timestamps = false;
        //         // $attachment->created_at = $date;
        //         // $attachment->updated_at = $date;
        //         // $attachment->save();

        //          // Optional: SHA-256 for dedupe/integrity   
        //         $sha256 = null;
        //         if (is_readable($tmpPath)) {
        //             $sha256 = hash_file('sha256', $tmpPath);
        //         }

        //         // Optional: image dimensions
        //         $width = $height = null;
        //         if (str_starts_with(strtolower($mime ?? ''), 'image/')) {
        //             try {
        //                 [$width, $height] = getimagesize($tmpPath) ?: [null, null];
        //             } catch (\Throwable $e) {
        //                 Log::warning("Could not read image dimensions: {$originalName}", ['e' => $e->getMessage()]);
        //             }
        //         }


        //         // Save to the database
        //         ProjectAttachments::create([
        //             'attachment' => $originalName,
        //             'project_id' => $project->id,
        //             'project_document_id' => $project_document->id,
        //             'filesystem' => $disk,

        //             'original_name' => $originalName,

        //             'disk' => $disk,
        //             'path' => $dir,
        //             // 'category',
        //             'mime_type' => $mime,
        //             'extension' => $ext,

        //             'width' => $width ,
        //             'height' => $height,

        //             'duration_seconds' => null,

        //             'sha256' => $sha256, 
        //             'created_by' => Auth::user()->id,
        //             'updated_by' => Auth::user()->id,
        //             'created_at' => $now,
        //             'updated_at' => $now,
        //         ]);
                


        //     }
        // }




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
                    'storedName'           => $storedName,      // or 'stored_name' if that’s your column
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


        // ActivityLog::create([
        //     'log_action' => "Project attachments on \"".$project_document->document_type->name."\" updated ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
         // ]);

        Alert::success('Success',"Project attachments on \"".$project_document->document_type->name."\" updated ");
        return redirect()->route('project.project-document.show',[
            'project' => $this->project->id,
            'project_document' => $this->project_document->id,
        ]);



    }



    
    public function render()
    {
        return view('livewire.admin.project-document.project-document-edit',[
            'existingFiles' => $this->existingFiles,
        ]);
    }
}
