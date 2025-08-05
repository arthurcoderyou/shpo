<?php

namespace App\Livewire\Setting;

use App\Models\Setting;
use Livewire\Component;
use App\Models\ActivityLog; 
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SettingCreate extends Component
{

    public string $name;
    public string $key;
    public string $description;
 
    public function mount(){
        
    }


    public function updated($fields){
        $this->validateOnly($fields,[
            'key' => [
                'required',
                'string',
                'unique:settings,key',
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
                'unique:settings,name',
            ],
            'name' => [
                'required'
            ], 
            'description' => [
                'required'
            ]

        ]);

        //save
        $setting = Setting::create([
            'name' => $this->name,
            'key' => $this->key,
            'description' => $this->description,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);

        // event(new PermissionCreated($permission,auth()->user()->id));

        ActivityLog::create([
            'log_action' => "Setting \"".$this->name."\" created ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Setting created successfully');
        return redirect()->route('setting.index');
    }



    public function render()
    {
        return view('livewire.setting.setting-create');
    }
}
