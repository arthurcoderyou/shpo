<?php

namespace App\Livewire\Components\HelperWidget\Project;

use Livewire\Component;
use App\Helpers\ProjectHelper;
use App\Models\HelpWdigetSettings;
use Illuminate\Support\Facades\Auth;

class ProjectRCNumberEvalHelpWdiget extends Component
{


    public bool $showGuide = false;

    public bool $dsa_status = false; // dont show again

    public $project_id;

    public $user_id;

    public function mount(int $project_id){

        $this->project_id = $project_id;

        $user = Auth::user();
        $this->user_id = $user->id;

        if ($user) {
            // $hasRcOrSubmitted = $user->projects()
            //     ->where(function ($q) {
            //         $q->where(function ($q2) {
            //             $q2->whereNotNull('rc_number')
            //                ->where('rc_number', '!=', '');
            //         })
            //         ->orWhere('status', 'submitted');
            //     })
            //     ->exists();

            // // "New" user if they have NO rc_number project AND NO submitted project
            // if (! $hasRcOrSubmitted) {
            //     $this->showGuide = true;
            // }


            $user_help_widget = HelpWdigetSettings::where('user_id', $user->id)
                ->where('widget','livewire.components.helper-widget.project.project-r-c-number-eval-help-wdiget')
                ->first();
                   

            if(!empty( $user_help_widget) && $user_help_widget->status == true){ // if the status is true, it means DO NOT SHOW again is marked as yes by the user so the guide must be hidden

                $this->showGuide = false;

                $this->dsa_status = $user_help_widget->status;
            }else{

                $this->showGuide = true; 
            } 



            if($user->can('system access admin') || $user->can('system access global admin') || $user->can('system access reviewer')){
                 $this->showGuide = false;
            }


        }
    }

     
    public function closeGuide()
    {   
        $this->updateDsaStatus(); // update status

        $this->showGuide = false;
    }

    public function submit_project($project_id){
        $this->updateDsaStatus(); // update status
        
        // dd($project_id);
        ProjectHelper::submit_project_for_rc_evaluation($project_id);

    }

    public function updateDsaStatus(){
        // dont show again 
         

        // dd($this->dsa_status);

        
        // update or create the settings
       HelpWdigetSettings::updateOrCreate([
            'user_id' => $this->user_id,
            'widget' => 'livewire.components.helper-widget.project.project-r-c-number-eval-help-wdiget'
       ],   
       
       [    
            'user_id' => $this->user_id,
            'widget' => 'livewire.components.helper-widget.project.project-r-c-number-eval-help-wdiget',
            'status' => $this->dsa_status,  // if checked, dsa_status is true so we have to revert it back
       ]);



    }



    public function render()
    {
        return view('livewire.components.helper-widget.project.project-r-c-number-eval-help-wdiget');
    }
}
