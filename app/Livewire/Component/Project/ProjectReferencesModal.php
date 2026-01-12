<?php

namespace App\Livewire\Component\Project;

use App\Models\Project;
use Livewire\Component;
use App\Models\ProjectTimer;
use App\Models\ProjectReferences;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Events\ProjectReferences\ProjectReferencesLogEvent;
use App\Helpers\ActivityLogHelpers\ProjectReferenceLogHelper;
use App\Helpers\SystemNotificationHelpers\ProjectReferenceNotificationHelper;

class ProjectReferencesModal extends Component
{
 

    // dynamic listener 
        protected $listeners = [
            // add custom listeners as well
            // 'systemUpdate'       => 'handleSystemUpdate',
            // 'SystemNotification' => 'handleSystemNotification',
        ];

        protected function getListeners(): array
        {

            $listeners = [
                "projectReferenceEvent.{$this->project_id}" => 'loadProjectReferences',
            ];

            // if(!empty($this->project_document_id)){
            //     $listeners = array_merge($listeners, [
            //         "projectDocumentSubscriberEvent.{$this->project_document_id}" => '$refresh',
            //     ]);

            // }


            return array_merge($this->listeners, $listeners);
        }
    // ./ dynamic listener

    public Project $project;
    public int $project_id;
    public $rc_number;


    public $query = ''; // Search input
    public $projects = []; // Search results 

    public $selectedProjects = []; // Selected project references

 


    public function mount(Project $project){
 

        $this->project = $project; 
        $this->project_id = $project->id;
        $this->rc_number = $project->rc_number;

        
        // $this->loadSelectedUsers();
        $this->loadProjectReferencesBasedOnLotNumber();
    }

    public function loadProjectReferencesBasedOnLotNumber(){
        // check for project documents that has connections to project based on lot number 

        $current_reviewer = $this->project->getCurrentReviewer();
        $project = Project::find($this->project->id); 

        $project = $this->project;

        // Grab existing values safely
        $lotNumber = $project?->lot_number;
        $projectName = $project?->name;
        $rcNumber = $this->rc_number;

        // If nothing to search with, return empty or skip
        if (empty($lotNumber) && empty($projectName) && empty($rcNumber)) {
            $this->projects = [];
            return;
        }

        $projects = Project::query()
            // Optional: exclude the current project itself
            ->when($project?->id, fn ($q) => $q->where('id', '!=', $project->id))

            ->where(function ($q) use ($lotNumber, $projectName, $rcNumber) {

                // LOT NUMBER (exact OR contains)
                if (!empty($lotNumber)) {
                    $q->orWhere(function ($sub) use ($lotNumber) {
                        $sub->where('lot_number', $lotNumber)
                            ->orWhere('lot_number', 'LIKE', "%{$lotNumber}%");
                    });
                }

                // RC NUMBER (exact OR contains)
                if (!empty($rcNumber)) {
                    $q->orWhere(function ($sub) use ($rcNumber) {
                        $sub->where('rc_number', $rcNumber)
                            ->orWhere('rc_number', 'LIKE', "%{$rcNumber}%");
                    });
                }

                // PROJECT NAME (exact OR contains)
                if (!empty($projectName)) {
                    $q->orWhere(function ($sub) use ($projectName) {
                        $sub->where('name', $projectName)
                            ->orWhere('name', 'LIKE', "%{$projectName}%");
                    });
                }
            })
            ->limit(20)
            ->get()
            ->toArray();

        $this->projects = $projects;

        // dd($current_reviewer);


        // dd($project->references);

        // // check if it is the first in order 
        if(
            // !empty($current_reviewer ) && $current_reviewer->order == 1 && 
            !empty($project->references ) && count($project->references ) > 0 ) 
            {
                //  dd($this->selectedProjects);
                foreach($project->references as $proj){
                    
                    // $proj = Project::find($reference->referenced_project_id);
                    if(!empty($proj['rc_number']) && $proj['status'] !== "draft"){

                        // dd($proj['name']);

                        $this->selectedProjects[] = [
                            'id' => $proj['id'],
                            'name' => $proj['name'],
                            'lot_number' => $proj['lot_number'], 
                            'rc_number' => $proj['rc_number'], 
                            'location' => $proj['location'],
                            'type' => $proj['type'],
                            'agency' => $proj['agency'],
                        ];
                    } 
                }

            




        }



       

    }

    public function loadProjectReferences(){
        $this->selectedProjects = [];
        $project = $this->project;

        // // check if it is the first in order 
        if(
            // !empty($current_reviewer ) && $current_reviewer->order == 1 && 
            !empty($project->references ) && count($project->references ) > 0 ) 
            {
                //  dd($this->selectedProjects);
                foreach($project->references as $proj){
                    
                    // $proj = Project::find($reference->referenced_project_id);
                    if(!empty($proj['rc_number']) && $proj['status'] !== "draft"){

                        // dd($proj['name']);

                        $this->selectedProjects[] = [
                            'id' => $proj['id'],
                            'name' => $proj['name'],
                            'lot_number' => $proj['lot_number'], 
                            'rc_number' => $proj['rc_number'], 
                            'location' => $proj['location'],
                            'type' => $proj['type'],
                            'agency' => $proj['agency'],
                        ];
                    } 
                }
 


        }
    }

      public function updatedRcNumber($value)
    {

        // $this->validate([
        //     'rc_number' => [
        //         'required',
        //         'string',
        //         // Rule::unique('project_documents', 'rc_number'), // Ensure rc_number is unique
        //          Rule::unique('projects')
        //             ->where(fn ($query) => $query
        //                 ->where('name', $this->project->name)
        //                 ->where('lot_number', $this->project->lot_number)
        //                 ->where('rc_number', $this->rc_number)
        //             ), 

        //     ]
        // ],[
        //     'The rc number has already been taken. Please enter other combinations of rc number '
        // ]);

        $search = trim($value);
        $limit  = 20;

        $query = Project::query()
            ->whereNotNull('rc_number')
            ->select('id', 'name', 'location', 'lot_number', 'rc_number');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
                ->orWhere('lot_number', 'like', "%{$search}%")
                ->orWhere('rc_number', 'like', "%{$search}%");
            });
        }

        $projects = $query
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();

        // Store full Eloquent collection (if you still need it elsewhere)
        $this->projects = $projects;
 

        

    }





    // For the Search Project Functionality
        

        
        public function updatedQuery()
        {
            if (empty($this->query)) {
                $this->projects = null;
                return;
            }

            $user = Auth::user();

            // If somehow no authenticated user, return empty results safely
            if (!$user) {
                $this->projects = [];
                return;
            }

            // Extract selected project IDs to exclude from results
            $excludedIds = array_column($this->selectedProjects, 'id');

            $search = $this->query;

            $query = Project::query()
                ->select('id', 'name', 'lot_number',  'location', 'rc_number')
                ->whereNotNull('rc_number')
                // ->whereNotNull('project_number')
                ->when(!empty($excludedIds), function ($q) use ($excludedIds) {
                    $q->whereNotIn('id', $excludedIds);
                });

            // Optional: access control
            if (
                !$user->can('system access global admin')
                && !$user->can('system access admin')
            ) {
                $query->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                    // Add more rules if needed, e.g. shared projects, assignments, etc.
                });
            }

            // Search conditions
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('agency', 'like', "%{$search}%")
                    ->orWhere('lot_number', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('rc_number', 'like', "%{$search}%");
            });

            $this->projects = $query
                ->limit(10)
                ->get()
                ->toArray();
        }


 
        public function addProjectReference($projectId)
        {
            if (!in_array($projectId, array_column($this->selectedProjects, 'id'))) { 
                $project = Project::find($projectId);
                $this->selectedProjects[] = [


                    // 'id' => $project->id,
                    // 'project_name' => $project->name, 
                    // 'rc_number' => $project->rc_number, 
                    // 'location' => $project->location,
                    // 'type' => $project->type,
                    // 'agency' => $project->agency,



                    'id' => $project->id,
                    'name' => $project->name,
                    'rc_number' => $project->rc_number,
                    'project_number' => $project->project_number,
                    'location' => $project->location,
                    'type' => $project->type,
                    'agency' => $project->agency,
                ];
            }

            $this->query = '';
            $this->project_documents = [];
        }

        
        public function removeProjectReference($index)
        {
            unset($this->selectedProjects[$index]);
            $this->selectedProjects = array_values($this->selectedProjects); // Re-index array
        }

    // ./// For the Search Project Functionality






    public function update_project(){
        // dd("Here");

        

        $project = Project::find($this->project->id);  // get the current project document 
        $project_reviewer = $project->getCurrentReviewer(); // get the current reviewer

        // dd($project);

                    // $project_references = ProjectReferences::select('id','project_id')->where('project_id',$project->id)->get()->toArray();    
                    // dd($project_references);


        // dd($project_reviewer);
 

        $this->validate([
            'rc_number' => [
                'required',
                'string',
                // Rule::unique('project_documents', 'rc_number'), // Ensure rc_number is unique
                //  Rule::unique('projects')
                //     ->where(fn ($query) => $query
                //         ->where('name', $this->project->name)
                //         ->where('lot_number', $this->project->lot_number)
                //         ->where('rc_number', $this->rc_number)
                //     ), 

            ]
        ],[
            'The rc number has already been taken. Please enter other combinations of rc number '
        ]);


        // dd("All goods");
        if(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id && $project_reviewer->order == 1){
            // the review is automatically approved if the first reviewer had save and confirmed that the project is approved in the initial review
            $this->review_status = "reviewed";

        }

        // dd(empty($project_reviewer) ||  (!empty($project_reviewer) && $project_reviewer->user_id !== Auth::user()->id) ); 
        // dd(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id && $project_reviewer->order == 1);
        // dd($this->all());
         

        $project->rc_number = $this->rc_number;
        $project->updated_at = now();
        $project->updated_by = Auth::user()->id; 
        $project->status = "reviewed";
        // $project->allotted_review_time_hours = $this->allotted_review_time_hours;
        


        $project_timer = ProjectTimer::first();

        if(!empty($project_timer)){
            $this->reviewer_response_duration = $this->reviewer_response_duration ?? $project_timer->reviewer_response_duration ?? null;
            $this->reviewer_response_duration_type = $this->reviewer_response_duration_type ?? $project_timer->reviewer_response_duration_type ?? null;
            $this->submitter_response_duration = $this->submitter_response_duration ?? $project_timer->submitter_response_duration ?? null;
            $this->submitter_response_duration_type = $this->submitter_response_duration_type ?? $project_timer->submitter_response_duration_type ?? null;


        }

        $project->reviewer_response_duration = $this->reviewer_response_duration;
        $project->reviewer_response_duration_type = $this->reviewer_response_duration_type;
        // after updating the project, update the due date timers
        $project->reviewer_due_date = Project::calculateDueDate(now(),$this->reviewer_response_duration_type, $this->reviewer_response_duration );


        $project->submitter_response_duration = $this->submitter_response_duration;
        $project->submitter_response_duration_type = $this->submitter_response_duration_type;  
        // $project->submitter_due_date = Project::calculateDueDate(now(),$project->submitter_response_duration_type, $project->submitter_response_duration );
        $project->submitter_due_date = Project::calculateDueDate(now(),$this->submitter_response_duration_type, $this->submitter_response_duration );
 
        $project->save();



        // // delete existing project document references 
        // if(!empty($project->references)){
             
        //     // dd($project->references);

        //     // delete project_references
        //     if(!empty($project->references)){
        //         foreach($project->references as $reference){
        //             $reference->delete();
        //         } 
        //     }
        // }

        // dd($this->selectedProjects);

        $authId = Auth::id() ?? null;

        $updated_selected_project_references = [];

        // Save Project References (if any)
        if (!empty($this->selectedProjects)) {
            foreach ($this->selectedProjects as $selectedProject) {
                // \App\Models\ProjectReferences::create([
                //     'project_id' => $project->id,
                //     'referenced_project_id' => $selectedProject['id'],
                //     'created_by' => Auth::id(),
                //     'updated_by' => Auth::id(),
                // ]);

                // dd($this->selectedProjects);

                // dd($selectedProject['id']);

                $projectId = $project->id;
                $referenceId = $selectedProject['id'];


                $exists = \App\Models\ProjectReferences::where('project_id', $projectId)
                    ->where('referenced_project_id', $referenceId)
                    ->exists();

                if ($exists) {
                    
                    \App\Models\ProjectReferences::where('project_id', $projectId)
                    ->where('referenced_project_id', $referenceId)
                    ->delete();
                }

                // dd("Here");
                    // Optional: return message to the user
                    // return back()->with('alert.error', 'This reference already exists.');
                   
                    $project_reference =  \App\Models\ProjectReferences::create([
                        'project_id' => $projectId,
                        'referenced_project_id' => $referenceId,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);


                    $updated_selected_project_references[] =  $project_reference->id;


                    $message =  ProjectReferenceLogHelper::getActivityMessage('added',$project_reference->id,$authId);

                    // // log the event 
                    event(new ProjectReferencesLogEvent(
                        $message ,
                        $authId, 
                        $projectId, 

                    ));





            }


            // delete project references not included
            \App\Models\ProjectReferences::where('project_id', $projectId)
                ->whereNotIn('id', $updated_selected_project_references)
                ->delete();

        }


        $message = "";
 
        


        // send email notification to user 
            $authId = Auth::id() ?? null;

            // // Success message from the activity log project helper 
            // $message =  ProjectLogHelper::getProjectActivityMessage('ref-updated',$project->id,$authId);
    
            // // get the route 
            // $route = ProjectLogHelper::getRoute('ref-updated', $project->id);
             

             // Success message from the activity log project helper 
                $message =  ProjectReferenceLogHelper::getActivityMessage('updated',null,$authId,$project->id);

                // get route
                $route =  ProjectReferenceLogHelper::getRoute('updated', $project->id);


               
                // send notification
                    ProjectReferenceNotificationHelper::sendSystemNotification(
                        $message,
                        $route,  
                    );

                    // log event
                     // // log the event 
                    event(new ProjectReferencesLogEvent(
                        $message ,
                        $authId,  
                        $project->id


                    ));

                    // notify the project creator 

                        // check first it the authenticated user is the same as the project creator 
                        if($project->created_by == $authId){
                            //get creator id
                            $creatorId = $project->created_by;

                                // dd("false");
                                ProjectReferenceNotificationHelper::sendSystemNotificationForConnectedProjectCreator(
                                    $message,
                                    $route, 
                                    $project->id,
                                    $authId, 
                                    
                                ); 
                        }

                        
                    // ./ notify the project creator 

                // ./ send notification



 
        // // Alert::success('Success', $message);
        // return redirect()->route('project.show',[
        //     'project' => $this->project->id, 
        
        // ])
        // ->with('alert.success',$message)
        // ;


        // Fire Livewire browser event for Notyf
        $this->dispatch('notify', type: 'success', message: $message);

    }

    
    public function render()
    {
        return view('livewire.component.project.project-references-modal');
    }
}
