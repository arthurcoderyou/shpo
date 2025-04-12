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
                    Add Reviewer for  "{{ $project->name }}"
                    </h2>
                </div>
                <!-- End Col -->

                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2  ">

                    <div class="space-y-2 col-span-12 sm:col-span-6 ">
                        <label for="user_id" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            User
                        </label>

                        <select autofocus autocomplete="user_id"
                        wire:model="user_id" 
                        id="user_id"
                        name="user_id"
                        class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none ">

                            @if(!empty($users) && count($users) > 0) 
                                <option value="">Select User</option>
                                @foreach ($users as $user_name => $user_id)
                                    <option value="{{ $user_id }}">{{ $user_name }}</option>
                                @endforeach
                                 
                            @else  
                                <option disabled >No Users found</option>
                            @endif
                        </select>
                        @error('user_id')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-6 ">
                        <label for="order" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Order
                        </label>

                        <select autofocus autocomplete="order"
                        wire:model="order" 
                        id="order"
                        name="order"
                         class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none ">
                            <option value="">Select Order</option>
                            <option value="top">Add at the beginning of the order</option>
                            <option value="end">Add at the end of the order</option> 
                        </select>

                        @error('order')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    {{-- <div class="space-y-2 col-span-12 sm:col-span-4 ">
                        <label for="status" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Status
                        </label>

                        <select autofocus autocomplete="status"
                        wire:model="status" 
                        id="status"
                        name="status"
                         class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none ">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option> 
                        </select>

                        @error('status')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div> --}}



                </div>
                <!-- End Grid -->

                <div class="mt-5 flex justify-center gap-x-2">
                    <a href="{{ route('project.index') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
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
</div>
<!-- End Card Section -->
