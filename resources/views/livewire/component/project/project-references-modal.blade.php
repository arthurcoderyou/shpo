 
<div>
    
    <div class="col-span-12" 
        >  
        <div class="rounded-2xl border border-slate-200 "
            
        >


            <div class="  p-4">

            
                <h2 class="text-lg font-semibold text-gray-800 ">

                
                        Update Project References 
                </h2>


                @if(Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('system access admin')    )
                    {{-- <div class="space-y-2 col-span-12   ">
                        <label for="allotted_review_time_hours" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Project Allotted Review hours
                        </label>

                        <input
                        type="number"
                        autofocus autocomplete="allotted_review_time_hours"
                        wire:model="allotted_review_time_hours"
                        id="allotted_review_time_hours"  class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder=""> 

                        @error('allotted_review_time_hours')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>
                    --}}


                    <div class="space-y-2 col-span-12 mb-2">
                        <label for="rc_number" class="inline-block text-sm font-medium text-gray-800 mt-2.5">
                            Project RC number
                        </label>

                        <div class="relative" 
                        >
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm font-medium">
                                RC#:
                            </span>

                            {{-- <input
                                
                                autocomplete="rc_number"
                                wire:model="rc_number"
                                id="rc_number"
                                class="py-2 px-3 pl-14 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                placeholder=""> --}}
                            
                            <div class="py-2 px-3 pl-14 pe-11 block w-full   shadow-sm rounded-lg text-sm   disabled:opacity-50 disabled:pointer-events-none"  >
                                <x-project.text-dropdown-search
                                    name="rc_number"
                                    label=""
                                    :value="$rc_number"
                                    placeholder="Search or select project..."
                                    :options="$projects"    
                                    wire:model.live="rc_number"   
                                    
                                />
                            </div>




                        </div>

                        @error('rc_number')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                        <!-- Project references section --> 
                        <x-ui.project.project-reference-section
                            :projects="$projects"
                            :selectedProjects="$selectedProjects"
                            query="query"
                            removeAction="removeProjectReference"
                        />
                    <!-- ./ Project references section -->
                    
                    




                @endif 


                <div class="space-y-2 col-span-12   ">
                    <div class="mt-5 flex justify-center gap-x-2">

                    


                        {{-- <a href="{{ route('project-document.index') }}" 
                        wire:navigate
                        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                            Cancel
                        </a>
                         --}}

                        @if(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id)
                            <button type="button"
                            onclick="confirm('Are you sure, you want to save this project information details? This will also approve the review of the project') || event.stopImmediatePropagation()"
                            wire:click.prevent="update_project"
                            class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-lime-600 text-white hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none">
                                Save
                            </button>
                        @else
                            <button type="button"
                            onclick="confirm('Are you sure, you want to save this project information details?') || event.stopImmediatePropagation()"
                            wire:click.prevent="update_project"
                            class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-lime-600 text-white hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none">
                                Save
                            </button>
                        @endif


                        

                    </div>
                </div>

            </div>
        </div>
    </div>
    



      {{-- Do not remove --}}
    {{-- 
        Essential for getting the model id from the browser bar 
        This is to get model id for : 
        1. Full page load (hard refresh, direct URL, normal navigation)
        2. Livewire SPA navigation (wire:navigate)
    --}}
    @push('scripts')
        <script>

            (function () {

                function getData(){
                    window.pageProjectId = @json(optional(request()->route('project'))->id ?? request()->route('project') ?? null);
                    console.log(window.pageProjectId);

                    window.pageProjectDocumentId = @json(optional(request()->route('project_document'))->id ?? request()->route('project_document') ?? null);
                    console.log(window.pageProjectDocumentId);
                    

                    const pageProjectId = window.pageProjectId; // can be null
                    const pageProjectDocumentId = window.pageProjectDocumentId; // can be null

                    //  Listener for the project discussion events
                    if (pageProjectId) {
                        console.log(`listening to : ${pageProjectId}`);
                        window.Echo.private(`project.project_reference.${pageProjectId}`)
                            .listen('.event', (e) => {
                                console.log('[project model-scoped]');

                                let dispatchEvent = `projectReferenceEvent.${pageProjectId}`;
                                Livewire.dispatch(dispatchEvent); 

                                console.log(dispatchEvent); 

                            });
                    }

                    // Listener for the project document discussion events
                    if (pageProjectDocumentId) {
                        console.log(`listening to : ${pageProjectDocumentId}`);
                        window.Echo.private(`project.project_document.project_reference.${pageProjectDocumentId}`)
                            .listen('.event', (e) => {
                                console.log('[project model-scoped]');

                                let dispatchEvent = `projectDocumentReferenceEvent.${pageProjectDocumentId}`;
                                Livewire.dispatch(dispatchEvent); 

                                console.log(dispatchEvent); 

                            });
                    }






                }

                /**
                 * 1. Full page load (hard refresh, direct URL, normal navigation)
                 */
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        getData();
                    });
                } else {
                    // DOM already loaded
                    getData();
                }

                /**
                 * 2. Livewire SPA navigation (wire:navigate)
                 */
                document.addEventListener('livewire:navigated', () => {
                    getData();
                });







            })();
 


        </script>
    @endpush


</div>