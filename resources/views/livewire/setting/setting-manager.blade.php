<!-- Table Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    {{-- <!-- Loading for list -->
    <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div> --}}

    <!-- Card -->
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden ">
            <!-- Header -->
            <div class=" px-3 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 text-nowrap">
                <div>
                <h2 class="text-xl font-semibold text-gray-800 ">
                    Setting Manager
                </h2>
                <p class="text-sm text-gray-600 ">
                    Listing of setting values 
                </p>
                </div>

                <div>
                <div class="inline-flex gap-x-2">


                    <input type="text" wire:model.live="search"
                        class="min-w-32 py-2 px-3 inline-flex items-center gap-x-2 border-gray-200  rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                        placeholder="Search">


                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="sort_by" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500   ">
                            <option value="">Sort By</option>
                            <option>Name A - Z</option>
                            <option>Name Z - A</option>
                            <option>Key A - Z</option>
                            <option>Key Z - A</option>
                            <option>Description A - Z</option>     
                            <option>Description Z - A</option>
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
            <table class="min-w-full divide-y divide-gray-200 0">
                <thead class="bg-gray-50 ">
                <tr>
                    

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Name
                            </span>

                            
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Value
                            </span>
                        </div>
                    </th>

                    <th>
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Value Type
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

                    @if(!empty($settings) && count($settings) > 0)
                        @foreach ($settings as $setting)
                            <tr>
                                 


                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500">{{ $setting->name }}</span>
                                        </div>

                                        <div class="grow mt-1" x-data="{ copied: false }">
                                            <div class="flex items-center gap-2">
                                                <span class="block text-sm font-mono text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                                    {{ $setting->key }}
                                                </span>

                                                <button
                                                    @click="navigator.clipboard.writeText('{{ $setting->key }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                    class="text-blue-600 hover:text-blue-800 transition"
                                                    title="Copy to clipboard"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M8 16h8M8 12h8m-8-4h8m2 12H6a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h7a2 2 0 012 2v10a2 2 0 01-2 2z" />
                                                    </svg>
                                                </button>

                                                <span x-show="copied" class="text-xs text-green-500">Copied!</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>


                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500 ">{{ $setting->value }}</span>
                                        </div>
                                        </div>
                                    </div>
                                </td>

                                 <td class="text-wrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500 uppercase ">{{ $setting->value_type }}</span>
                                        </div>
                                        </div>
                                    </div>
                                </td>



                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500 ">
                                                {{ \Carbon\Carbon::parse($setting->updated_at)->format('d M, H:i') }}
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </td>



                                <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <!-- edit -->
                                        <a href="{{ route('setting.manager.edit',['setting' => $setting->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"/></svg>
                                        </a>

                                        


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
                {{ $settings->links() }}

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
                        {{ count($settings) > 0 ? 'of '.$settings->total()  : '' }}
                    </p>
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

        {{-- wire:target="delete"   --}}
        <div wire:loading  wire:target="delete"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Deleting record...
                    </div>
                </div>
            </div>

            
        </div>


        

        {{-- wire:target="executeForceDelete"   --}}
        <div wire:loading  wire:target="executeForceDelete"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Deleting record...
                    </div>
                </div>
            </div>

            
        </div>


        
        {{-- wire:target="executeRecover"   --}}
        <div wire:loading  wire:target="executeRecover"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Recovering record...
                    </div>
                </div>
            </div>

            
        </div>



    <!-- ./  Loaders -->
    
</div>
<!-- End Table Section -->
