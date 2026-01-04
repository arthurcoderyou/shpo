<div 
    x-data="{ 
        helpOpen: false,
        
    }" 
    class="fixed bottom-6 right-6 z-40"
>

    {{-- Floating Help Button --}}
    <button
        type="button"
        @click="helpOpen = !helpOpen"
        class="flex items-center gap-2 rounded-full bg-blue-600 text-white shadow-lg px-4 py-2 text-sm font-medium hover:bg-blue-700 focus:outline-none"
    >
        <span class="flex h-7 w-7 items-center justify-center rounded-full bg-white/10 text-base">
            ?
        </span>
        <span class="hidden sm:inline">Help</span>
    </button>

    {{-- Help Panel --}}
    <div
        x-show="helpOpen"
        x-transition
        @click.away="helpOpen = false"
        class="mt-2 w-72 rounded-xl bg-white shadow-xl border border-slate-200 p-3 space-y-2"
    >
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
            Help & Tips
        </p>

        {{-- Link to open the main guide modal --}}
        <button
            type="button"
            class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-slate-50 flex gap-2"
            @click="
                helpOpen = false;
                $wire.openShowFirstProjectGuide(); 
            "
        >
            <span class="text-blue-500 text-lg">🧭</span>
            <div>
                <div class="font-medium text-slate-800">Getting Started</div>
                <div class="text-xs text-slate-500">How to create and submit your first project.</div>
            </div>
        </button>

        {{-- more items can be added here --}}

       
 



    </div>

</div>
