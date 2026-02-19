<div>
    <button
        type="button"
        @click="openDiscussion = true"
        class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-sky-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-700 transition"
    >
        <!-- User-add icon -->
        <svg class="w-5 h-5 text-sky-600" fill="none" viewBox="0 0 24 24"
            stroke="white" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M7 8h10M7 12h6m-7 8l-4 1 1-4a9 9 0 1116 3H7z" />
        </svg>
        {{-- Discussions --}}
    </button>


    <!-- ======================== -->
    <!-- Discussions -->
    <!-- ======================== -->
    <div
        x-show="openDiscussion"
        x-cloak
        x-transition.opacity
        @keydown.escape.window="openDiscussion = false" 
        @click.self="openDiscussion = false"
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
                <svg class="w-5 h-5 text-slate-900  " fill="none" stroke="black"
                    viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7 8h10M7 12h6m-7 8l-4 1 1-4a9 9 0 1116 3H7z" />
                </svg>  
                    Discussions
                </h3>
                <button
                    @click="openDiscussion = false"
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
                        

                    @if($project->canPostInDiscussion())
                        <livewire:admin.project-discussion.project-discussion-create :project="$project" />
                    @endif
                </div>

                

                @if(!empty($project_document))
                    <livewire:admin.project-discussion.project-discussion-list :project="$project" :project_document="$project_document" />
                    

                @else
                   <livewire:admin.project-discussion.project-discussion-list :project="$project"   />
                @endif

                





            </div>

            <!-- Footer -->
            <div class="border-t px-5 py-3 flex justify-end gap-2 bg-slate-50">
                <button
                    type="button"
                    @click="openDiscussion = false"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Close
                </button>

                    
            </div>
        </div>
    </div> 

</div>