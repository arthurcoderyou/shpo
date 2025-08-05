<?php

namespace App\Livewire\Setting;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Events\PermissionDeleted;
use RealRashid\SweetAlert\Facades\Alert;

class SettingManager extends Component
{


 protected $listeners = [ 
        'settingCreated' => '$refresh',
        'settingUpdated' => '$refresh',
        'settingDeleted' => '$refresh',
    ];

    use WithFileUploads;
    use WithPagination;

    public $search = '';
    public $sort_by = '';
    public $record_count = 10;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;

    public $file;

    public $module;


    // Method to delete selected records
    public function deleteSelected()
    {
        Setting::whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records

        // event(new PermissionDeleted(null,auth()->user()->id));

        Alert::success('Success','Selected settings deleted successfully');
        return redirect()->route('setting.index');
    }

    // This method is called automatically when selected_records is updated
    public function updateSelectedCount()
    {
        // Update the count when checkboxes are checked or unchecked
        $this->count = count($this->selected_records);
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selected_records =  Setting::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){
        $setting = Setting::find($id);


        $setting->delete();
        // event(new settingDeleted($setting,auth()->user()->id));

        Alert::success('Success','Setting deleted successfully');
        return redirect()->route('setting.index');

    }



    public function getSettingsProperty(){
         $settings = Setting::select('settings.*');


        if (!empty($this->search)) {
            $search = $this->search;


            $settings = $settings->where(function($query) use ($search){
                $query =  $query->where('settings.name','LIKE','%'.$search.'%')
                    ->orWhere('settings.key','LIKE','%'.$search.'%')
                    ->orWhere('settings.description','LIKE','%'.$search.'%');
            });


        }
 
 

        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){

                case "Name A - Z":
                    $settings =  $settings->orderBy('settings.name','ASC');
                    break;

                case "Name Z - A":
                    $settings =  $settings->orderBy('settings.name','DESC');
                    break;

                case "Key A - Z":
                    $settings =  $settings->orderBy('settings.key','ASC');
                    break;

                case "Key Z - A":
                    $settings =  $settings->orderBy('settings.key','DESC');
                    break;

                case "Description A - Z":
                    $settings =  $settings->orderBy('settings.description','ASC');
                    break;

                case "Description Z - A":
                    $settings =  $settings->orderBy('settings.description','DESC');
                    break;

               
                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $settings =  $settings->orderBy('settings.created_at','DESC');
                    break;

                case "Oldest Added":
                    $settings =  $settings->orderBy('settings.created_at','ASC');
                    break;

                case "Latest Updated":
                    $settings =  $settings->orderBy('settings.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $settings =  $settings->orderBy('settings.updated_at','ASC');
                    break;
                default:
                    $settings =  $settings->orderBy('settings.updated_at','DESC');
                    break;

            }


        }else{
            $settings =  $settings->orderBy('settings.updated_at','DESC');

        }





        $settings = $settings->paginate($this->record_count);


        return $settings;
    }




    public function render()
    {
        return view('livewire.setting.setting-manager',[
            'settings' => $this->settings
        ]);
    }
}
