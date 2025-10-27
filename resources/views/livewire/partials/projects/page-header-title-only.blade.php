<?php

use Livewire\Volt\Component;

new class extends Component {
     
    // Optional: allow overriding chips/texts from parent if needed
    public ?string $fallbackDescription = 'This is a project';

    // Page title
    public string $pageTitle;

 
};
?>
<div   class="  px-4 sm:px-6 lg:px-8 py-2 mx-auto grid grid-cols-12 gap-x-2">
    <!-- Header Card -->
    <section class="col-span-12 rounded-2xl border border-slate-200 bg-white   px-4 py-6 ">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-semibold text-sky-900">
                        {{ $pageTitle }}
                    </h1>
                    
                </div>

                

                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                    
                </div>
            </div>

            
        </div>
    </section>
</div>
