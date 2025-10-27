<?php

namespace App\Livewire\Admin\ProjectDocument;

use App\Models\Project;
use Livewire\Component;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Models\ProjectAttachments;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ProjectDocumentHelpers;

class ProjectDocumentReview extends Component
{


    /** Actions with Password Confirmation panel */
        public $passwordConfirm = '';
        public $passwordError = null;


        /** Delete Confirmation  */
            public $confirmingDelete = false; // closes the confirmation delete panel
            
            public $recordId = null; 
            public function confirmDelete($recordId)
            {
                $this->confirmingDelete = true;
                $this->recordId = $recordId;
                $this->passwordConfirm = '';
                $this->passwordError = null;
            }

            public function executeDelete()
            {
                if (!Hash::check($this->passwordConfirm, auth()->user()->password)) {
                    $this->passwordError = 'Incorrect password.';
                    return;
                }

                $this->selected_record = $this->recordId;

                // delete the record
                $this->delete();

                $this->reset(['confirmingDelete', 'passwordConfirm', 'recordId', 'passwordError','selected_record']); 
            }

        /** ./ Delete Confirmation */

    /** ./ Actions with Password Confirmation panel */


    public $project_id;
    public $project_document_id;

    public $attachments = []; // Attachments 

    public $project;
    public $project_document;


    public $counts; 

    public function mount($project_id, $project_document_id){
        $this->project_id = $project_id;
        $this->project_document_id;
        $this->project = Project::findOrFail($this->project_id);
        $this->project_document = ProjectDocument::findOrFail($this->project_document_id);


        $this->counts = $this->project_document->attachmentTypeCounts();

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

        // document_type_id

        $this->validate([
            
            'attachments' => [
                'required',
                'array',
                'min:1', // Ensure at least one attachment
            ],


        ]);



        
 

        
        /*
        if (!empty($this->attachments)) {

            //update the project document 
            $project_document = ProjectDocument::findOrFail($this->project_document_id);
            $project_document->updated_by = Auth::user()->id;
            $project_document->updated_at = now();
            $project_document->save();


            $project = Project::findOrFail($this->project_id);
            $project->updated_by = Auth::user()->id;
            $project->updated_at = now();
            $project->save();

            // create the newly added attachments 
            foreach ($this->attachments as $file) {
        
                // // Store the original file name
                // $originalFileName = $file->getClientOriginalName(); 

                // // Generate a unique file name
                // $fileName = Carbon::now()->timestamp . '-' . $project->id . '-' . $originalFileName . '.' . $file->getClientOriginalExtension();

                // // Generate a unique file name
                // $fileName = Carbon::now()->timestamp . '-' . $review->id . '-' . uniqid() . '.' . $file['extension'];



                $originalFileName = $file['name'] ?? 'attachment';
                $extension = $file['extension'] ?? pathinfo($originalFileName, PATHINFO_EXTENSION);

                $fileName = Carbon::now()->timestamp . '-' . $this->project->id . '-' . pathinfo($originalFileName, PATHINFO_FILENAME) . '.' . $extension;


        
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
        
                // Save to the database
                ProjectAttachments::create([
                    'attachment' => $fileName,
                    'project_id' => $this->project->id,
                    'project_document_id' => $project_document->id,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
            }
        }
        */
        
        
        $project_document = ProjectDocument::findOrFail($this->project_document_id);
        $project_document->updated_by = Auth::user()->id;
        $project_document->updated_at = now();
        $project_document->save();


        $project = Project::findOrFail($this->project_id);
        $project->updated_by = Auth::user()->id;
        $project->updated_at = now();
        $project->save();

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


            // //create the project document 
            // $project_document = new ProjectDocument();
            // $project_document->project_id = $project->id;
            // $project_document->document_type_id = $this->document_type_id;
            // $project_document->created_by = Auth::user()->id;
            // $project_document->updated_by = Auth::user()->id;
            // $project_document->save();



           
            $date = now(); // to ensure that only one date time will be used 

            

            foreach ($this->attachments as $file) {
        
               

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






        // ActivityLog::create([
        //     'log_action' => "Project attachments on \"".$project_document->document_type->name."\" updated ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
         // ]);

        Alert::success('Success',"Project attachments on \"".$project_document->document_type->name."\" updated ");
        return redirect()->route('project.project_document',[
            'project' => $this->project->id,
            'project_document' => $this->project_document->id,
        ]);



    }


    public function deleteAttachment(){



    }

    public function render()
    {
        return view('livewire.admin.project-document.project-document-review',[
            'existingFiles' => $this->existingFiles,
        ]);
    }
}
