<div>
    <button
        type="button"
        @click="openReferences = true"
        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700 transition"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
            fill="none" stroke="white" class="w-5 h-5 text-emarald-50">
            <!-- Document -->
            <rect x="4" y="3" width="12" height="18" rx="2" stroke-width="1.8"/>
            <path d="M8 8h4M8 12h4" stroke-width="1.8" stroke-linecap="round"/>

            <!-- Nodes -->
            <circle cx="18" cy="8" r="2" stroke-width="1.6"/>
            <circle cx="18" cy="16" r="2" stroke-width="1.6"/>

            <!-- Connection -->
            <path d="M18 10v4" stroke-width="1.6" stroke-linecap="round"/>
        </svg>





        {{-- References --}}
    </button>


    <!-- ======================== -->
    <!-- Referencess -->
    <!-- ======================== -->
    <div
        x-show="openReferences"
        x-cloak
        x-transition.opacity
        @keydown.escape.window="openReferences = false" 
        @click.self="openReferences = false"
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
            <div class="flex items-center justify-between border-b bg-emerald-50 px-5 py-3">
                <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                 
                <svg class="w-5 h-5  text-slate-900  " fill="none" viewBox="0 0 24 24" stroke="black" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21.75 7.5l-9.75 6L2.25 7.5m19.5 0v9a2.25 2.25 0 01-2.25 2.25H4.5A2.25 2.25 0 012.25 16.5v-9m19.5 0L12 3 2.25 7.5"/>
                </svg>
                    References
                </h3>
                <button
                    @click="openReferences = false"
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




                <div  id="references" class="  px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
                        

                    {{-- @if($project->canPostInReferences()) --}}
                        {{-- <livewire:admin.project-References.project-References-create :project="$project" /> --}}
                    {{-- @endif --}}

                    <livewire:component.project.project-references-modal :project="$project" /> 


                </div>

                {{-- <livewire:admin.project-References.project-References-list :project="$project" /> --}}





            </div>

            <!-- Footer -->
            <div class="border-t px-5 py-3 flex justify-end gap-2 bg-slate-50">
                <button
                    type="button"
                    @click="openReferences = false"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Close
                </button>

                    
            </div>
        </div>
    </div> 

</div>