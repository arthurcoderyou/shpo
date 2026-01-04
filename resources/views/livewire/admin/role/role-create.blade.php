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
                    Create New Role
                    </h2>
                </div>
                <!-- End Col -->

                <!-- Grid -->
                <div class="grid grid-cols-12 gap-2 mt-2 ">

                    <div class="space-y-2 col-span-12   ">
                        
                        <x-ui.input 
                            id="name"
                            name="name"
                            type="text"
                            wire:model.live="name"   
                            label="Name"

                            required="true"
                              
                            placeholder="Enter role" 
                            :error="$errors->first('name')"
 
                            displayTooltip
                            position="top"
                            tooltipText="Please enter the name" 
                        />

                    </div>

                    <div class="space-y-2 col-span-12 ">
                         

                        <x-ui.textarea 
                            id="description"
                            name="description" 
                            wire:model.live="description"   
                            label="Description" 
                            placeholder="Enter description" 
                            :error="$errors->first('description')"
 
                            displayTooltip
                            position="top"
                            tooltipText="Please enter the description" 
                        />
                    </div>



                </div>
                <!-- End Grid -->

                <div class="mt-5 flex justify-center gap-x-2">
                    

                     <x-ui.button 
                        id="cancel" 
                        label="Cancel"
                        sr="Cancel" 
                        :linkHref="route('role.index')" {{-- to make it as a link --}}

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


</div>
<!-- End Card Section -->
