<?php

use Livewire\Volt\Component;
use App\Helpers\ProjectHelper;

new class extends Component {
    public ?string $label = 'Cancel';
    public string $route = '#'; // default fallback

    public function mount()
    {
        // Safe to call helpers here
        $this->route = ProjectHelper::returnHomeProjectRoute();
    }
};
?>

<a href="{{ $route }}"
   wire:navigate
   class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
    {{ $label }}
</a>
