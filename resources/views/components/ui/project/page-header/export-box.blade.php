@props([
    'count' => 0, 
])


<div>
    <button
        type="button"
        {{ $count == 0 ? 'disabled' : '' }}
        @click="openExport = true"
        class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-sky-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-700 transition disabled:opacity-50 disabled:pointer-events-none"
    >
        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
        Export ({{ $count }})
    </button>


    <!-- ======================== -->
    <!-- Project Export -->
    <!-- ======================== -->
    <div
        x-show="openExport"
        x-cloak
        x-transition.opacity
        @keydown.escape.window="openExport = false" 
        @click.self="openExport = false"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        aria-modal="true" role="dialog"
    >
        <!-- Modal Box -->
        <div
            x-transition
            @click.stop
            class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden"
        >
            <!-- Header -->
            <div class="flex items-center justify-between border-b bg-sky-50 px-5 py-3">
                <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                      
                    <svg class="w-5 h-5 text-slate-900" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    Project Export
                </h3>
                <button
                    @click="openExport = false"
                    class="text-slate-500 hover:text-slate-700 transition"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">




                <div  id="discussion" class="  px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
                    <!-- Export Button -->
                    {{-- @if(Auth::user()->can('system access global admin') || Auth::user()->can('system access user')) --}}
                        <button
                            onclick="confirm('Are you sure, you want to export this records?') || event.stopImmediatePropagation()"
                            wire:click.prevent="export"
                           
                            id="project_export"
                            type="button"
                            class="py-1.5 px-2  inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-excel-green-500 text-white shadow-sm hover:bg-excel-green-50 hover:text-excel-green-600 hover:border-excel-green-500
                            focus:outline-none focus:bg-gray-50 focus:text-excel-green-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg> 
                        </button> 
                    {{-- @endif --}}
 
                </div>
 





            </div>

            <!-- Footer -->
            <div class="border-t px-5 py-3 flex justify-end gap-2 bg-slate-50">
                <button
                    type="button"
                    @click="openExport = false"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Close
                </button>

                    
            </div>
        </div>
    </div> 

</div>