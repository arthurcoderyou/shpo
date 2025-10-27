<!-- Table Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    {{-- <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div> --}}
 
    {{-- <form wire:submit="save"> --}}
    <div>
        <!-- Card -->
        <div class="bg-white rounded-xl shadow mb-2">


            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 ">
                    Add Reviewer
                    </h2>
                </div>
                <!-- End Col -->

                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2  ">

                    <!-- reviewer type -->
                    <x-reviewer-type-info :type="$reviewer_type" />
 

                    <div class="space-y-2 col-span-12 sm:col-span-4">
                        <label for="user_id" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            User
                        </label>

                        <select autofocus autocomplete="user_id"
                        wire:model="user_id" 
                        id="user_id"
                        name="user_id"
                        class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none ">

                            @if(!empty($users) && count($users) > 0) 
                                <option value="">Open Review</option>
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

                    <div class="space-y-2 col-span-12 sm:col-span-4">
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

                    <div class="space-y-2 col-span-12 sm:col-span-4">
                        <label for="reviewer_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Reviewer Type
                        </label>

                        <select autofocus autocomplete="reviewer_type"
                        wire:model.live="reviewer_type" 
                        id="reviewer_type"
                        name="reviewer_type"
                         class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none ">
                            <option value="initial">Initial Reviewers</option>
                            <option value="document">Document</option>
                            <option value="final">Final Reviewers</option>
                            
                        </select>

                        @error('reviewer_type')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>


                    @if($reviewer_type == "document")
                        <div class="space-y-2 col-span-12 ">
                        {{-- <div class="space-y-2 col-span-12 sm:col-span-4"> --}}
                            <label for="document_type_id" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                Document Type
                            </label>

                            <select autofocus autocomplete="document_type_id"
                            wire:model.live="document_type_id" 
                            id="document_type_id"
                            name="document_type_id"
                            class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "> 
                                @if(!empty($document_types))
                                    @foreach ($document_types as $document_type)
                                        <option value="{{ $document_type->id }}">{{ $document_type->name }}</option> 
                                    @endforeach
                                @endif
                                
                            </select>

                            @error('document_type_id')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror


                        </div>
                    @endif

                    




                </div>
                <!-- End Grid -->

                <div class="mt-5 flex justify-center gap-x-2">
                    {{-- <a href="{{ route('role.index') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                        Cancel
                    </a> --}}
                    <button type="button" wire:click="add" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-sky-600 text-white hover:bg-sky-700 focus:outline-none focus:bg-sky-700 disabled:opacity-50 disabled:pointer-events-none">
                        Add 
                    </button>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
    {{-- </form> --}}


    <!-- Card -->
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden  ">
            <!-- Header -->
            <div class="px-3 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 ">
                <div>
                <h2 class="text-xl font-semibold text-gray-800 ">
                    Reviewers
                </h2>
                <p class="text-sm text-blue-600 mt-1 max-w-4xl">
                    <strong>Note:</strong> Please make sure to <span class="font-semibold">save your changes</span> before selecting another document type or leaving the page. Unsaved changes will be lost, and updates will only be applied once saved. Saved reviewers will be visible to all users. 
                </p>
                <p class="text-sm text-yellow-600 mt-1 max-w-4xl">
                    <strong>OPEN REVIEW:</strong> An open review is a review type where all admins are notified and receive a review notification. The first admin to open the review will automatically become the assigned reviewer.  
                </p>

                </div>

                <div>
                <div class="inline-flex gap-x-2">

                    {{-- 
                    <input type="text" wire:model.live="search"
                        class="py-2 px-3 inline-flex items-center gap-x-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                        placeholder="Search">
                    --}}
                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="reviewer_type" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">  
                            <option value="initial">Initial Reviewers</option>
                            <option value="document">Document Reviewers</option>
                            <option value="final">Final Reviewers</option>
                            
                        </select>
                    </div>
                     

                    @if($reviewer_type == "document")
                        <div class="inline-flex items-center gap-x-2 text-nowrap">

                            <select wire:model.live="document_type_id" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                                <option value="">Document Type</option>
                                @if(!empty($document_types))
                                    @foreach ($document_types as $document_type)
                                        <option value="{{ $document_type->id }}">{{ $document_type->name }}</option> 
                                    @endforeach
                                @endif
                                
                            </select>
                        </div>
                    @endif
                    
                    {{--  

                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="sort_by" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                            <option value="">Sort By</option>
                            <option>Name A - Z</option>
                            <option>Name Z - A</option>
                            <option>Order Ascending</option>
                            <option>Order Descending</option>
                            <option>Latest Added</option>
                            <option>Oldest Added</option>
                            <option>Latest Updated</option>
                            <option>Oldest Updated</option>
                        </select>
                    </div>

                     --}}
                    @if( (!isset($issues['no_reviewers']) || $issues['no_reviewers'] == false)  
                    &&  (!isset($issues['document_types_missing_reviewers']) || $issues['document_types_missing_reviewers'] == false)  
                    &&  (!isset($issues['no_initial_reviewers']) || $issues['no_initial_reviewers'] == false)
                    &&  (!isset($issues['no_final_reviewers']) || $issues['no_final_reviewers'] == false)
                    )
                        @if( Auth::user()->can('system access global admin') || Auth::user()->hasPermissionTo('reviewer apply to all') )
                            <button title="This is to apply the reviewer list here for all NOT APPROVED projects"
                                onclick="confirm('Are you sure, you want to apply this to all records? If you do this, all not approved projects will apply this list and order of reviewers. They will be notified on the changes of reviewers list and order. Are you still sure to proceed? ') || event.stopImmediatePropagation()"
                                wire:click.prevent="apply_to_all" 
                                class="text-nowrap py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-sky-500 text-white shadow-sm hover:bg-sky-50 hover:text-sky-600 hover:border-sky-500 focus:outline-sky-500 focus:text-sky-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M352 256c0 22.2-1.2 43.6-3.3 64l-185.3 0c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64l185.3 0c2.2 20.4 3.3 41.8 3.3 64zm28.8-64l123.1 0c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64l-123.1 0c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32l-116.7 0c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0l-176.6 0c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0L18.6 160C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192l123.1 0c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64L8.1 320C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6l176.6 0c-6.1 36.4-15.5 68.6-27 94.6c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352l116.7 0zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6l116.7 0z"/></svg>
                                
                                APPLY TO ALL 
                            </button>
                        @endif
                    @endif

                    <a href="{{ route('reviewer.index') }}"
                        wire:navigate
                        class="hs-tooltip hs-tooltip-toggle py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-yellow-500 text-white shadow-sm hover:bg-yellow-50 hover:text-yellow-600   hover:border-yellow-500 focus:outline-yellow-500 focus:text-yellow-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                        <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M105.1 202.6c7.7-21.8 20.2-42.3 37.8-59.8c62.5-62.5 163.8-62.5 226.3 0L386.3 160 352 160c-17.7 0-32 14.3-32 32s14.3 32 32 32l111.5 0c0 0 0 0 0 0l.4 0c17.7 0 32-14.3 32-32l0-112c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 35.2L414.4 97.6c-87.5-87.5-229.3-87.5-316.8 0C73.2 122 55.6 150.7 44.8 181.4c-5.9 16.7 2.9 34.9 19.5 40.8s34.9-2.9 40.8-19.5zM39 289.3c-5 1.5-9.8 4.2-13.7 8.2c-4 4-6.7 8.8-8.1 14c-.3 1.2-.6 2.5-.8 3.8c-.3 1.7-.4 3.4-.4 5.1L16 432c0 17.7 14.3 32 32 32s32-14.3 32-32l0-35.1 17.6 17.5c0 0 0 0 0 0c87.5 87.4 229.3 87.4 316.7 0c24.4-24.4 42.1-53.1 52.9-83.8c5.9-16.7-2.9-34.9-19.5-40.8s-34.9 2.9-40.8 19.5c-7.7 21.8-20.2 42.3-37.8 59.8c-62.5 62.5-163.8 62.5-226.3 0l-.1-.1L125.6 352l34.4 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L48.4 288c-1.6 0-3.2 .1-4.8 .3s-3.1 .5-4.6 1z"/></svg>
                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded-md shadow-2xs dark:bg-neutral-700" role="tooltip">
                            Refresh the page
                        </span>
                    </a>
                </div>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <table class="min-w-full divide-y divide-gray-200 ">
                <thead class="bg-gray-50 ">
                <tr>
                    <th scope="col" class="px-2 py-3 text-start">
                        {{-- <label for="hs-at-with-checkboxes-main" class="flex">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                wire:click="toggleSelectAll"
                                wire:change="updateSelectedCount"
                                class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                                id="hs-at-with-checkboxes-main">
                            <span class="sr-only">Checkbox</span>
                        </label> --}}
 
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Reviewer
                            </span>
                        </div>
                    </th>



                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Order  
                            </span>
                        </div>
                    </th>



                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Modified
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-end"></th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 ">

                    @if(!empty($reviewers) && count($reviewers) > 0)
                        @foreach ($reviewers as $key => $reviewer)
                            <tr>
                                <td class="w-10 whitespace-nowrap flex flex-row items-center">
                                    {{-- <div class="px-2 py-2 align-self-center">
                                        <label for="reviewer_{{ $reviewer['id'] }}" class="flex">
                                            <input type="checkbox"
                                            wire:model="selected_records"
                                            wire:change="updateSelectedCount"
                                            class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                                            id="reviewer_{{ $reviewer['id'] }}"
                                            value="{{ $reviewer['id'] }}"
                                            >
                                            <span class="sr-only">Checkbox</span>
                                        </label>
                                    </div> --}}


                                    <div class="flex flex-col">
                                        <button {{ $reviewer['order']  == 1 ? 'disabled' : '' }} type="button" 
                                            {{-- wire:click="updateOrder( {{ $reviewer['id'] }},{{ $reviewer['order'] }},'move_up',{{ $reviewer['document_type_id'] ?? 0 }}, '{{ $reviewer['reviewer_type'] }}' )"  --}}
                                            wire:click="updateOrder( {{ $key }},{{ $reviewer['order'] }},'move_up',{{ $reviewer['document_type_id'] ?? 0 }}, '{{ $reviewer['reviewer_type'] }}' )"
                                            class="p-1 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " >
            
                                            <div class="hs-tooltip flex">
            
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 11.25-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                                <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm " role="tooltip">
                                                    Move order up
                                                </span>
                                            </div>
            
            
                                        </button>
            
            
                                        
                                        <button {{ $reviewer['order'] == $lastOrder ? 'disabled' : '' }} type="button" 
                                            {{-- wire:click="updateOrder( {{ $reviewer['id'] }},{{ $reviewer['order'] }},'move_down',{{ $reviewer['document_type_id'] ?? 0 }} , '{{ $reviewer['reviewer_type'] }}' )"  --}}
                                            wire:click="updateOrder( {{ $key }},{{ $reviewer['order'] }},'move_down',{{ $reviewer['document_type_id'] ?? 0 }} , '{{ $reviewer['reviewer_type'] }}' )" 
                                            class="p-1 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " >
                                            <div class="hs-tooltip flex">
            
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 12.75 3 3m0 0 3-3m-3 3v-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                                <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm " role="tooltip">
                                                    Move order down
                                                </span>
                                            </div>
            
            
                                        </button>
            
                                    </div>
            
            



                                </td>

                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                            <div class="grow">
                                                @php   
                                                    $user =  getUser($reviewer['user_id']);
                                                @endphp 
                                                @if($reviewer['user_id'] && !empty($user))
                                                    

                                                    <span class="block text-sm text-gray-500 ">{{ $user ? $user->name : '' }}</span>
                                                    <span class="block text-sm text-gray-500 ">{{ $user ? $user->email : '' }}</span>
                                                @else 
                                                    <span class="block text-sm text-gray-500 ">{{ __('Open Review') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                            <div class="grow">
                                                <span class="block text-sm text-gray-500 ">{{ $reviewer['order'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>



                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500 ">
                                                {{ $reviewer['updated_at'] ? \Carbon\Carbon::parse($reviewer['updated_at'])->format('d M, h:i A') : \Carbon\Carbon::now()->format('d M, h:i A') }}
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </td>



                                <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">

                                         

                                        @if( Auth::user()->can('system access global admin') || Auth::user()->hasPermissionTo('reviewer delete') )
                                        <!-- delete -->
                                         
                                        <button
                                        onclick="confirm('Are you sure, you want to delete this record?') || event.stopImmediatePropagation()"
                                        {{-- wire:click.prevent="delete({{ $reviewer['id'] }})" --}}
                                        wire:click.prevent="delete({{ $key }})" 
                                        type="button" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.7 23.7 0 0 0 -21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0 -16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/></svg>
                                        </button>
                                        @endif



                                    </div>
                                </td>



                            </tr>

                            
                        @endforeach

                        <tr>
                            <td colspan="5" class=" text-center px-2 py-2">
                                <button type="button" 
                                    onclick="confirm('Are you sure you want to save the reviewer order? This will be reflected system-wide.') || event.stopImmediatePropagation()" 
                                {{-- wire:click.prevent="delete({{ $reviewer['id'] }})" --}}
                                    wire:click.prevent="save" 
                                class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Save
                                </button>
                            </td>
                        </tr>


                    @else
                        <tr>
                            <th scope="col" class="px-6 py-3 text-start">
                                <div class="flex items-center gap-x-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                                    No records found
                                    </span>
                                </div>
                            </th>
                        </tr>
                    @endif
                </tbody>
            </table>
            <!-- End Table -->

            <!-- Footer -->
            <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 ">
                {{-- {{ $reviewers->links() }} --}}

                <div class="inline-flex items-center gap-x-2">
                    <p class="text-sm text-gray-600 ">
                    Showing:
                    </p>
                    <div class="max-w-sm space-y-3">
                    <select wire:model.live="record_count" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                        <option>200</option>
                    </select>
                    </div>
                    {{-- <p class="text-sm text-gray-600 ">
                        {{ count($reviewers) > 0 ? 'of '.$reviewers->total()  : '' }}
                    </p> --}}
                </div>


            </div>
            <!-- End Footer -->


            </div>
        </div>
        </div>
    </div>
    <!-- End Card -->


    <!--  Loaders -->
        {{-- wire:target="table"   --}}
        <div wire:loading 
            class="p-0 m-0"
            style="padding: 0; margin: 0;">
            <div class="absolute right-4 top-4 z-10 inline-flex items-center gap-2 px-4 py-3 rounded-md text-sm text-white bg-blue-600 border border-blue-700 shadow-md animate-pulse mb-4 mx-3">
                <div>   
                    <svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                </div>
                <div>
                    Loading lists, please wait...
                </div> 
            </div>
        </div>


        
        {{-- wire:target="apply_to_all"   --}}
        <div wire:loading  wire:target="apply_to_all"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Applying Reviewer List to project records...
                    </div>
                </div>
            </div>

            
        </div>


        
        

         {{-- wire:target="reviewer_type"   --}}
        <div wire:loading  wire:target="reviewer_type"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Updating reviewer list records...
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


</div>
<!-- End Table Section -->
@push('scripts')
{{-- <script>
    let formDirty = false;

    // Watch for changes in inputs to mark form as dirty
    document.addEventListener('input', () => {
        formDirty = true;
    });

    // Ask before leaving if form is dirty
    window.addEventListener('beforeunload', function (e) {
        if (formDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Optional: Reset dirty flag after save
    Livewire.on('formSaved', () => {
        formDirty = false;
    });
</script> --}}
@endpush