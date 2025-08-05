<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

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

                <div class="sm:col-span-12 space-y-1">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Update Setting
                    </h2>

                    <p class="text-sm text-gray-600">
                        <span class="font-semibold text-gray-700">Name:</span>
                        {{ $name ?? '—' }}
                    </p>

                    <p class="text-sm text-gray-600">
                        <span class="font-semibold text-gray-700">Key:</span>
                        <code class="bg-gray-100 text-gray-800 px-1 py-0.5 rounded font-mono text-xs">{{ $key ?? '—' }}</code>
                    </p>

                    @if (!empty($description))
                        <p class="text-sm text-gray-600">
                            <span class="font-semibold text-gray-700">Description:</span>
                            {{ $description }}
                        </p>
                    @endif
                </div>


                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2  ">

                   

                    <div class="space-y-2 col-span-12 ">
                        <label for="value_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Value Type
                        </label>   
                        <select id="value_type" name="value_type" autocomplete="value_type"
                        wire:model.live="value_type"
                        class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none ">
                            <option value="">Select a value type</option>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="longtext">Long Text</option>
                            <option value="selection">Selection</option>
                        </select>
                              
                        @error('value_type')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>


                    <div class="space-y-2 col-span-12">
                        <label for="value" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Value
                        </label>

                        @if ($value_type === 'text')
                            <input type="text" wire:model="value" id="value"
                                class="py-2 px-3 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Enter text value" />
                        @elseif ($value_type === 'number')
                            <input type="number" wire:model="value" id="value"
                                class="py-2 px-3 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Enter numeric value" />
                        @elseif ($value_type === 'longtext')
                            <textarea wire:model="value" id="value"
                                    class="py-2 px-3 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500"
                                    rows="4" placeholder="Enter long text value"></textarea>
                        @elseif ($value_type === 'selection' && !empty($options))
                            <select wire:model="value" id="value"
                                    class="py-2 px-3 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Select an option --</option>
                                @foreach ($options as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        @elseif ($value_type === 'selection')
                            <p class="text-sm text-gray-500 italic">No selection options available for this setting.</p>
                        @endif

                        @error('value')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>


 




                </div>
                <!-- End Grid -->
 

                <div class="mt-5 flex justify-center gap-x-2">
                    <a href="{{ route('setting.manager') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                        Cancel
                    </a>
                    <button type="submit" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                    Save
                    </button>
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


        {{-- wire:target="value_type"   --}}
        <div wire:loading  wire:target="value_type"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Loading...
                    </div>
                </div>
            </div>

            
        </div>
    <!--  ./ Loaders -->

    
</div>
<!-- End Card Section -->
