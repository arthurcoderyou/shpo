<?php

namespace App\Livewire\Setting;

use App\Models\Setting;
use Livewire\Component;
use App\Models\ActivityLog; 
use App\Events\PermissionCreated;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SettingEdit extends Component
{

    public $setting_id;
    public string $name;
    public string $key;
    public string $description;
 
    public function mount($id){
        $setting = Setting::findOrFail($id);
        $this->name = $setting->name;
        $this->key = $setting->key;
        $this->description = $setting->description;

        $this->setting_id = $setting->id;
    }


    public function updated($fields){
        $this->validateOnly($fields,[
            'key' => [
                'required',
                'string', 
                'unique:settings,name,'.$this->setting_id,
            ],
            'name' => [
                'required'
            ], 
            'description' => [
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
            'key' => [
                'required',
                'string', 
                'unique:settings,name,'.$this->setting_id,
            ],
            'name' => [
                'required'
            ], 
            'description' => [
                'required'
            ]

        ]);

        //save
        $setting = Setting::find($this->setting_id);
        $setting->name = $this->name;
        $setting->key = $this->key;
        $setting->description = $this->description;
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
        return redirect()->route('setting.index');
    }


    public function render()
    {
        return view('livewire.setting.setting-edit');
    }
}
