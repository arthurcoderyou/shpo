<?php

namespace App\Livewire\Admin\Project;

use Carbon\Carbon;
use App\Models\Project;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use Livewire\WithFileUploads;
use App\Models\ProjectReviewer;
use App\Models\ProjectAttachments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use App\Notifications\ProjectReviewNotification;
use Illuminate\Support\Facades\Notification;



class ProjectReview extends Component
{

    use WithFileUploads;
    public string $name = '';
    public string $description = '';
    public string $federal_agency = ''; 
    public $type;
    public $shpo_number;
    public $project_number;



    public $attachments = []; // Initialize with one phone field
    public $uploadedFiles = []; // Store file names

    public $existingFiles = [];

    public $project_id;

    public $user;

    public $status;


    public $enable_submit_review = true;

    public $project;



    public $latitude;
    public $longitude;
    public $location;

    public $location_directions = [];

    public $selectedUsers;

 
    public function mount($id){

        $project = Project::find($id);
        $this->project = $project;

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

        // if (!empty($project->attachments)) {
        //     $this->existingFiles = $project->attachments
        //         ->sortByDesc('created_at') // Ensure newest files appear first
        //         ->groupBy(function ($attachment) {
        //             return $attachment->created_at->format('M d, Y h:i A'); // Group by date
        //         })
        //         ->map(function ($attachments) {
        //             return $attachments->map(function ($attachment) {
        //                 return [
        //                     'id' => $attachment->id,
        //                     'name' => basename($attachment->attachment), // File name
        //                     'path' => asset('storage/uploads/project_attachments/' . $attachment->attachment), // Public URL
        //                 ];
        //             })->toArray();
        //         })->toArray();
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
        

        // dd( $this->existingFiles);

        if($project->getReview()->review_status !== "pending"){
            $this->enable_submit = false;
        }




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

        if(!empty($project->project_subscribers)){
            foreach($project->project_subscribers as $subscriber){
                $this->selectedUsers[] = ['id' => $subscriber->user->id, 'name' => $subscriber->user->name];
            }
            
        }   

    }


    public function updated($fields){
        $this->validateOnly($fields,[
            'name' => [
                'required',
                'string', 
            ],
            'description' => [
                'required'
            ],
            'federal_agency' => [
                'required'
            ]

        ]);
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
        
        $project = Project::find($project_id);
        

        $project->status = "submitted";
        $project->updated_at = now();
        $project->save();


        // Fetch all reviewers in order
        $reviewers = Reviewer::orderBy('order')->get();

        foreach ($reviewers as $reviewer) {
            $projectReviewer = ProjectReviewer::create([
                'order' => $reviewer->order,
                // 'status' => 'pending',
                'project_id' => $project->id,
                'user_id' => $reviewer->user_id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            
        }
         
        // while status is true for project reviewer, this means that the project had been reviewed but not approved and it is still true for reviewing
        $firstReviewer = ProjectReviewer::where('project_id', $project->id)
            ->where('status', true) // Status true means not yet approved
            ->orderBy('order', 'asc')
            ->first();

        
        // Send notification email to reviewer
        $user = User::find( $firstReviewer->user_id);
        if ($user) {
            Notification::send($user, new ProjectReviewNotification($project, $projectReviewer));
        }


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
        return view('livewire.admin.project.project-review');
    }
}
