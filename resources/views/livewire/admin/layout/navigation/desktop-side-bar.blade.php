<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<aside id="desktop-sidebar" data-collapsed="true"  class="hidden lg:flex lg:flex-col data-[collapsed=true]:w-20 transition-[width] lg:w-72 lg:min-h-screen lg:sticky lg:top-0 lg:border-r bg-white">



    
    <!-- Brand -->
    {{-- Desktop sidebar brand --}}
    <livewire:admin.layout.navigation.brand
        variant="desktop"
        name="Your App"
        subtitle="Admin Portal"
        initial="A"               {{-- optional; auto-derived from name if omitted --}}
        :use-auth-brand="true"
        badgeClass="bg-indigo-600 text-white"
    />




    {{-- Desktop (collapsible sidebar via .group wrapper on parent) --}}
    <div class="group data-[collapsed=true]:w-16 w-full">
        <livewire:admin.layout.navigation.nav variant="desktop" />
    </div>



    {{-- <div class="px-4 py-4 border-t">
        <button class="w-full inline-flex items-center justify-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium hover:bg-gray-50">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
            <span class="label group-data-[collapsed=true]:hidden">Logout</span>
        </button>
    </div> --}}



</aside>
