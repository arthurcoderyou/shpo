<!-- Table Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div>

    <!-- Card -->
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden ">
            <!-- Header -->
            <div class="px-3 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 ">
                <div>
                <h2 class="text-xl font-semibold text-gray-800 ">
                    Document Types
                </h2>
                <p class="text-sm text-gray-600 ">
                    Listing of document types 
                </p>
                </div>

                <div>
                <div class="inline-flex gap-x-2">


                    <input type="text" wire:model.live="search"
                        class="py-2 px-3 inline-flex items-center gap-x-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                        placeholder="Search">


                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="sort_by" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500   ">
                            <option value="">Sort By</option>
                            <option>Order 1 - 100</option>
                            <option>Order 100 - 1</option> 
                            <option>Name A - Z</option>
                            <option>Name Z - A</option> 
                            <option>Latest Added</option>
                            <option>Oldest Added</option>
                            <option>Latest Updated</option>
                            <option>Oldest Updated</option>
                        </select>
                    </div>

                     
                     
                </div>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <table class="min-w-full divide-y divide-gray-200 ">
                <thead class="bg-gray-50 ">
                <tr>
                    {{-- <th scope="col" class="px-2 py-3 text-start">
                        <label for="hs-at-with-checkboxes-main" class="flex">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                wire:click="toggleSelectAll"
                                wire:change="updateSelectedCount"
                                class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                                id="hs-at-with-checkboxes-main">
                            <span class="sr-only">Checkbox</span>
                        </label>
 
                    </th> --}}

                    @if(Auth::user()->hasRole('Admin') || Auth::user()->can('project reviewer edit') || Auth::user()->hasRole('DSI God Admin'))
                    <th scope="col" class="px-2 py-3 text-start">
                        
                    </th>
                    @endif


                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Name 
                            </span>
                        </div>
                    </th>


                    <th scope="col" class="px-2 py-3 ">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Display Order
                            </span>
                        </div>
                    </th>
                    



                    <th scope="col" class="px-2 py-3 ">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Modified
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-end"> 
                    </th>
 
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 ">

                    @if(!empty($document_types) && count($document_types) > 0)
                        @foreach ($document_types as $document_type)
                            <tr>
                              

                                @if(Auth::user()->hasRole('Admin') || Auth::user()->can('project reviewer edit') || Auth::user()->hasRole('DSI God Admin'))
                                    <td class="w-10 whitespace-nowrap flex flex-row items-center">
                                        

                                        <div class="flex flex-col">
                                            <button {{ $document_type->order  == 1 ? 'disabled' : '' }} type="button" wire:click="updateOrder({{ $document_type->id }}, {{ $document_type->order }},'move_up')" class="p-1 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " >
                
                                                <div class="hs-tooltip flex">
                
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 11.25-3-3m0 0-3 3m3-3v7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                    <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded shadow-sm " role="tooltip">
                                                        Move order up
                                                    </span>
                                                </div>
                
                
                                            </button>
                
                
                                            
                                            <button {{ $document_type->order == $lastOrder ? 'disabled' : '' }} type="button" wire:click="updateOrder({{ $document_type->id }}, {{ $document_type->order }},'move_down')" class="p-1 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " >
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
                                @endif


                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                            <div class="grow">
                                                <span class="block text-sm text-gray-500 ">{{ $document_type->name ? $document_type->name : '' }}</span> 
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                            <div class="grow">
                                                <span class="block text-sm text-gray-500 ">{{ $document_type->order  }}</span> 
                                            </div>
                                        </div>
                                    </div>
                                </td>
 


                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500 ">
                                                {{ \Carbon\Carbon::parse($document_type->updated_at)->format('d M, H:i') }}
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </td>



                                <td class="w-4 whitespace-nowrap text-end">
                                    <div class="px-2 py-2">
                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('document type edit') )
                                        <!-- edit -->
                                        <a href="{{ route('document_type.edit',['document_type' => $document_type->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"/></svg>
                                        </a>
                                        @endif
                                         

                                        {{-- @if( Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasPermissionTo('reviewer delete') ) --}}
                                        <!-- delete -->
                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('document type delete') )
                                        <button
                                        onclick="confirm('Are you sure, you want to delete this record?') || event.stopImmediatePropagation()"
                                        wire:click.prevent="delete({{ $document_type->id }})"
                                        type="button" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.7 23.7 0 0 0 -21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0 -16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/></svg>
                                        </button>
                                        {{-- @endif --}}
                                        @endif



                                    </div>
                                </td>



                            </tr>
                        @endforeach
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
                {{ $document_types->links() }}

                <div class="inline-flex items-center gap-x-2">
                    <p class="text-sm text-gray-600 ">
                    Showing:
                    </p>
                    <div class="max-w-sm space-y-3">
                    <select wire:model.live="record_count" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500   ">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                        <option>200</option>
                    </select>
                    </div>
                    <p class="text-sm text-gray-600 ">
                        {{ count($document_types) > 0 ? 'of '.$document_types->total()  : '' }}
                    </p>
                </div>


            </div>
            <!-- End Footer -->


            </div>
        </div>
        </div>
    </div>
    <!-- End Card -->
</div>
<!-- End Table Section -->
