<?php

namespace App\Livewire\Setting;

use App\Models\Setting;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Events\PermissionCreated;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SettingManagerEdit extends Component
{

    public $setting_id;
    public string $name;
    public string $key;
    public string $description;

    public $value; 
    public $value_type;

    public $options;
 
    public function mount($id){
        $setting = Setting::findOrFail($id);
        $this->name = $setting->name;
        $this->key = $setting->key;
        $this->description = $setting->description;

        $this->value = $setting->value;
        $this->value_type = $setting->value_type;

        $this->setting_id = $setting->id;

        // set the selection 
        $this->setOptionsBasedOnKey();

    }



    public function setOptionsBasedOnKey(){

        $default_options = [
            'Yes',
            'No'
        ];

        $project_location_bypass_options = [
            'ACTIVE',
            'INACTIVE'
        ];

        $document_upload_location_options = [
            "ftp",
            "local",
            "public",
            
        ];


        switch($this->key){
            case "document_upload_location":
                $this->options = $document_upload_location_options;
                break;
            case "project_location_bypass":
                $this->options = $project_location_bypass_options;
                break;
            default:
                $this->options = $default_options;
        }



    }

    public function updated($fields){
        $this->validateOnly($fields,[
             
            'value' => [
                'required'
            ], 
            'value_type' => [
                'required'
            ]

        ]);
    }


    /**
     * Handle an incoming save request.
     */
    public function save()
    {
        $this->validate([
            'value' => [
                'required'
            ], 
            'value_type' => [
                'required'
            ]


        ]);

        //save
        $setting = Setting::find($this->setting_id);
        $setting->value = $this->value;
        $setting->value_type = $this->value_type; 
        $setting->updated_by = Auth::user()->id;
        $setting->updated_at = now();
        $setting->save();
         

        // event(new PermissionCreated($permission,auth()->user()->id));

        ActivityLog::create([
            'log_action' => "Setting \"".$this->name."\" updated ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Setting updated successfully');
        return redirect()->route('setting.manager');
    }




    public function render()
    {
        return view('livewire.setting.setting-manager-edit');
    }
}
