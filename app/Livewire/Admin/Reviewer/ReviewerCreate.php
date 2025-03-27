<?php

namespace App\Livewire\Admin\Reviewer;

use App\Models\User;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth; 
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
class ReviewerCreate extends Component
{

    public $user_id;
    public $order;
    public $status;


    public $users;


    public function mount(){
        // $this->users = User::get()->pluck('id','name')->toArray();

        $this->users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Reviewer');
        })->pluck('id', 'name')->toArray();

        $this->status = true;
    }

    public function updated($fields){
        $this->validateOnly($fields,[
            'user_id' => [
                
                'unique:reviewers,user_id',
                'required', 
            ],
            'order' => [
                'required',
            ],
            'status' => [
                'required',
            ],

        ],[
            'user_id.required' => 'User is required' ,
            'user_id.unique' => 'User is already added on the order' 
        ]);
    }


    /**
     * Handle an incoming registration request.
     */
    public function save()
    {
        $this->validate([
            'user_id' => [
               
                'unique:reviewers,user_id',
                'required', 
            ],
            'order' => [
                'required',
            ],
            'status' => [
                'required',
            ],

        ],[
            'user_id.required' => 'User is required' ,
            'user_id.unique' => 'User is already added on the order' 
        ]);


         
        if ($this->order === 'top') {
            // Move all existing reviewers up by 1
            Reviewer::query()->increment('order');

            // Insert the new reviewer at the top (order = 1)
            Reviewer::create([
                'order' => 1,
                'status' => $this->status,
                'user_id' => $this->user_id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        } elseif ($this->order === 'end') {
            // Get the last order number
            $lastOrder = Reviewer::max('order') ?? 0;

            // Insert the new reviewer at the last order + 1
            Reviewer::create([
                'order' => $lastOrder + 1,
                'status' => $this->status,
                'user_id' => $this->user_id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        } 

        // //save
        // Reviewer::create([
        //     'user_id' => $this->user_id,
        //     'order' => $this->order,
        //     'status' => $this->status,
        // ]);

        $user = User::find($this->user_id);

        ActivityLog::create([
            'log_action' => "Reviewer \"".$user->name."\" added ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Reviewer added successfully');
        return redirect()->route('reviewer.index');
    }


    public function render()
    {
        return view('livewire.admin.reviewer.reviewer-create');
    }
}
