<!-- Card Section -->
<div class="max-w-full px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    {{-- <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div> --}}

    <form wire:submit="save">
        <!-- Card -->
        <div class="bg-white rounded-xl shadow ">


            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 ">
                    Edit Permission
                    </h2>
                </div>
                <!-- End Col -->

                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2 mt-2 ">

                    <div class="space-y-2 col-span-12 sm:col-span-6  ">
                        <x-ui.input 
                            id="name"
                            name="name"
                            type="text"
                            wire:model.live="name"   
                            label="Permission"

                            required="true"
                              
                            placeholder="Enter Permission" 
                            :error="$errors->first('name')"
 
                            displayTooltip
                            position="top"
                            tooltipText="Please enter the name" 
                        />


                    </div>



                    <div class="space-y-2 col-span-12 sm:col-span-6  ">
                        
                        <x-ui.select 
                            id="module"
                            name="module" 
                            wire:model.live="module"   
                            label="Module"

                            required="true"
                            :options="$modules"
                              
                            placeholder="Enter Module" 
                            :error="$errors->first('module')"
 
                            displayTooltip
                            position="top"
                            tooltipText="Please select the module" 
                        />


                    </div>




                </div>
                <!-- End Grid -->
 

                <div class="mt-5 flex justify-center gap-x-2">
                    
                    <x-ui.button 
                        id="cancel" 
                        label="Cancel"
                        sr="Cancel" 
                        :linkHref="route('permission.index')" {{-- to make it as a link --}}

                        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none"
                    />
  
                    <x-ui.button 
                        id="save" 
                        type="button"
                        label="Save"
                        sr="Save"
 
                        onclick="confirm('Are you sure, you want to save this record?') || event.stopImmediatePropagation()" 
                        wire:click.prevent="save" 


                        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                    />
                </div>


            </div>
        </div>
        <!-- End Card -->
    </form>

    <!--  Loaders --> 
        <!-- Floating Loading Notification -->
        <div 
            wire:loading 
        class="fixed top-4 right-4 z-50 w-[22rem] max-w-[calc(100vw-2rem)]
                rounded-2xl border border-slate-200 bg-white shadow-lg"
        role="status"
        aria-live="polite"
        >
            <div class="flex items-start gap-3 p-4">
                <!-- Spinner -->
                <svg class="h-5 w-5 mt-0.5 animate-spin text-slate-600 shrink-0"
                    viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z" />
                </svg>

                <!-- Text + Progress -->
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-slate-900">
                        Loading data…
                    </div>
                    <div class="mt-0.5 text-xs text-slate-600">
                        Fetching the latest records. Please wait.
                    </div>

                    <!-- Indeterminate Progress Bar -->
                    <div class="relative mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div
                        class="absolute inset-y-0 left-0 w-1/3 rounded-full bg-slate-400"
                        style="animation: indeterminate-bar 1.2s ease-in-out infinite;"
                        ></div> 

                    </div>
                </div>
            </div>
        </div>


        {{-- wire:target="save"   --}}
        <div wire:loading  wire:target="save"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Saving record...
                    </div>
                </div>
            </div>

            
        </div>
    <!--  ./ Loaders -->




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
                    window.pagePermissionId = @json(optional(request()->route('permission'))->id ?? request()->route('permission') ?? null);
                    console.log(window.pagePermissionId);

                    const pagePermissionId = window.pagePermissionId; // can be null
                    // 2) Conditionally listen to the model-scoped user channel
                    if (pagePermissionId) {
                        console.log(`listening to : ${pagePermissionId}`);
                        window.Echo.private(`permission.${pagePermissionId}`)
                            .listen('.event', (e) => {
                                console.log('[permission model-scoped]');

                                let dispatchEvent = `permissionEvent.${pagePermissionId}`;
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
<!-- End Card Section -->
