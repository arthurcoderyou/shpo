<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div>

    <form wire:submit="save">
        <!-- Card -->
        <div class="bg-white rounded-xl shadow ">


            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 ">
                    Add Document Type
                    </h2>
                </div>
                <!-- End Col -->

                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2  ">
  
                    <div class="space-y-2 col-span-12 ">
                        <label for="name" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Name
                        </label>

                        <input
                        autofocus autocomplete="name"
                        wire:model="name"
                        id="name" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        @error('name')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror

                    </div>
 
 


                </div>
                <!-- End Grid -->

                <div class="mt-5 flex justify-center gap-x-2">
                    {{-- <a href="{{ route('role.index') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                        Cancel
                    </a> --}}
                    <button type="submit" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                    Save
                    </button>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </form>
</div>
<!-- End Card Section -->
