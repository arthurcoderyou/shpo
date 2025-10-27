<?php

namespace App\Livewire\Admin\Project\ProjectDocument;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProjectDocumentList extends Component
{
    
    
    /** Actions with Password Confirmation panel */

        public $selected_record;
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



    use WithFileUploads;

    protected $listeners = [
        'projectCreated' => '$refresh',
        'projectUpdated' => '$refresh',
        'projectDeleted' => '$refresh',
        'projectSubmitted' => '$refresh',
        'projectQueued' => '$refresh',
        'projectDocumentCreated' => '$refresh',
        'projectDocumentUpdated' => '$refresh',
        'projectDocumentDeleted' => '$refresh',
        
    ];
    
    public string $name = '';
    public string $description = '';
    public string $federal_agency = ''; 
    public $type;
    public $shpo_number;
    public $project_number;

    public $attachments = []; // Initialize with one phone field
    public $uploadedFiles = []; // Store file names
 

    public $project_id;

    public $user;

    public $status;


    public $enable_submit = true;

    public $project_reviewer;

    public $project;

    public $latitude;
    public $longitude;
    public $location;

    public $location_directions = [];

 
    public $selectedUsers;

    public $home_route;
    public function mount($project_id){

        $project = Project::find($project_id);
        $this->project = $project;

        // return the route that is appropriate for the user        || NOT REDIRECT BUT ROUTE INSTANCE
        $this->home_route = ProjectHelper::returnHomeProjectRoute($project);
 
        $this->project_id = $project->id;

        $this->name = $project->name;
        $this->description = $project->description;
        $this->federal_agency = $project->federal_agency;
        $this->type = $project->type;

        $this->shpo_number = $project->shpo_number;

        $this->project_number = $project->project_number;

        $this->status = $project->getStatus();
        
        $this->user = $project->updator;
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
 


        if($project->status == "submitted"){
            $this->enable_submit = false;
        }


        if(!empty($project->getCurrentReviewer())){
            $this->project_reviewer = $project->getCurrentReviewer();

        }



        /**default is Guam coordinates */
        $this->latitude = $project->latitude ?? 13.4443; 
        $this->longitude = $project->longitude ?? 144.7937;
        $this->location = $project->location ?? "Guam";
        

        $this->location_directions[] =   Project::select(
                    'latitude', 'longitude'
                )
                ->where('id', $project_id) 
                ->get()
                ->toArray();

                
            
        if(!empty($project->project_subscribers)){
            foreach($project->project_subscribers as $subscriber){
                $this->selectedUsers[] = ['id' => $subscriber->user->id, 'name' => $subscriber->user->name];
            }
            
        }


    }


    


    public function delete(){

        dd($this->selected_record);

        $project_document = ProjectDocument::find($this->selected_record);

        if(empty($project_document)){
            abort(404, 'Project document not found');
        }


        // dd($project_document);
        Alert::success('Success',"Project attachments on \"".$project_document->document_type->name."\" updated ");
        return redirect()->route('project.project-document.index',[
            'project' => $project_document->project->id, 
        ]);



    }
 



    




    public function render()
    {
        return view('livewire.admin.project.project-document.project-document-list');
    }





}
