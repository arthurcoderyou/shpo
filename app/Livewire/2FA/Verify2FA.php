<?php

namespace App\LivewireFA;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Verify2FA extends Component
{

    

    public function render()
    {
        return view('livewire.2-f-a.verify2-f-a');
    }
}
