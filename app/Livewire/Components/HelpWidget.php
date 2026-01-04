<?php

namespace App\Livewire\Components;

use Livewire\Component;

class HelpWidget extends Component
{

    public bool $showFirstProjectGuide = false;

    public function openShowFirstProjectGuide(){

        $this->showFirstProjectGuide = true;
    }



    public function openGuide()
    {
        // Emit event to any page-level listener
        $this->dispatch('open-guide');
    }



    public function render()
    {
        return view('livewire.components.help-widget');
    }
}
