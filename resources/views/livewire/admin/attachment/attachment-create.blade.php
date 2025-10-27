<div class="  p-4 sm:px-6 lg:px-8  mx-auto grid grid-cols-1 sm:grid-cols-12 gap-3">
    {{-- <x-filepond::upload wire:model="file" multiple /> --}}



    <div class="col-span-12 sm:col-span-4">
        <div class="w-full">
            <x-filepond::upload wire:model="files" multiple /> 
        </div>
        

    </div>


    <div class="col-span-12 sm:col-span-8">
        


         <!-- Card -->
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 max-w-full w-full align-middle ">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm  overflow-x-auto ">
                    


                        <!-- Header -->
                        <div class="w-full   px-4 py-2 border-b border-gray-200 bg-white">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-2">
                                <!-- Title and Subtitle -->
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                        Uploads 
                                        <span class="inline-flex justify-center items-center w-6 h-6 text-xs font-medium bg-black text-white rounded-full">
                                            {{ 0 }}
                                        </span>
                                    </h2>
                                    <p class="text-sm text-gray-500">{{ 'Uploaded files' }}</p>

                                    @can('system access global admin') 
                                    <button 
                                        onclick="confirm('Are you sure you want to test notification?') || event.stopImmediatePropagation()" 
                                        wire:click="save"
                                        type="button"
                                        class="rounded-md bg-red-800 px-2.5 py-1 text-xs font-medium text-white">
                                        Open Notification
                                    </button> 

 
                                    @endcan


                                </div>

                                <!-- Action Bar -->
                                <div class="flex  flex-wrap items-center gap-2 justify-start sm:justify-end mt-2 sm:mt-0">

                                    <!-- Search -->
                                    <input type="text" wire:model.live="search"
                                        class="min-w-[160px] px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Search">

                                

                                    
                                    {{-- 
                                    <select wire:model.live="document_status" class="min-w-[140px] text-sm py-1.5 px-2 border rounded-md">
                                        <option value="">Document Status</option>
                                        @foreach ($document_status_options as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>

                                    <select wire:model.live="document_type_id" class="min-w-[140px] text-sm py-1.5 px-2 border rounded-md">
                                        <option value="">Document Type</option>
                                        @foreach ($document_type_options as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
    
                                    <select wire:model.live="sort_by" class="min-w-[160px] text-sm py-1.5 px-2 border rounded-md">
                                        <option value="">Sort By</option>
                                        
                                        <option>Document Name A - Z</option>
                                        <option>Document Name Z - A</option>
                                        <option>Project Name A - Z</option>
                                        <option>Project Name Z - A</option>
                                        <!-- Add rest of the sort options -->
                                    </select> --}}
    
    

                                </div>
                            </div>
                        </div>
                        <!-- End Header -->



            
                        <!-- Table -->
                        <table class="w-full divide-y divide-gray-200 overflow-x-auto">
                            <thead class="bg-gray-50 ">
                            <tr>
                                
                                <th class=" w-4    px-3 py-3  ">
                                    <div class="flex items-center gap-x-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                                        Actions
                                        </span>
                                    </div>
                                </th>
                                
                                <th scope="col" class="px-2 py-3 text-start">
                                    <div class="flex items-center gap-x-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                                        Document
                                        </span>
                                    </div>
                                </th>


                                <th scope="col" class="px-2 py-3 text-start">
                                    <div class="flex items-center gap-x-2">
                                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                                        Date Added
                                        </span>
                                    </div>
                                </th>
  

                                

                                
                            </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 ">

                                @if(!empty($attachments) && count($attachments) > 0)
                                    @foreach ($attachments as $attachment)
                                        <tr>
                                            {{-- Actions --}}
                                            <td class="w-fill text-nowrap align-top px-4 py-3   ">
                                                <div class="flex items-center justify-between space-x-2">
                                                    <div class="flex items-center gap-1">
    
                                                        <button 
                                                            onclick="confirm('Are you sure you want to delete this attachment?') || event.stopImmediatePropagation()" 
                                                            wire:click="delete({{ $attachment->id }})"
                                                            type="button"
                                                            class="rounded-md bg-red-800 px-2.5 py-1 text-xs font-medium text-white">
                                                            Delete
                                                        </button> 
                                                        @can('system access global admin')
                                                        <button 
                                                            onclick="confirm('Are you sure you want to copy this attachment?') || event.stopImmediatePropagation()" 
                                                            wire:click="copy({{ $attachment->id }})"
                                                            type="button"
                                                            class="rounded-md bg-sky-800 px-2.5 py-1 text-xs font-medium text-white">
                                                            Copy
                                                        </button> 
                                                        @endcan

                                                        {{-- <el-dropdown class="inline-block p-0">
                                                            <button class=" inline-flex rounded-md border border-slate-200 p-1 text-slate-600 hover:bg-slate-50">
                                                                
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                                    <circle cx="12" cy="5" r="2" />
                                                                    <circle cx="12" cy="12" r="2" />
                                                                    <circle cx="12" cy="19" r="2" />
                                                                </svg>

                                                            </button>

                                                            <el-menu anchor="bottom end" popover class="m-0 w-56 origin-top-right rounded-md bg-white p-0 shadow-lg outline outline-1 outline-black/5 transition [--anchor-gap:theme(spacing.2)] [transition-behavior:allow-discrete] data-[closed]:scale-95 data-[closed]:transform data-[closed]:opacity-0 data-[enter]:duration-100 data-[leave]:duration-75 data-[enter]:ease-out data-[leave]:ease-in">
                                                                <div class="py-1">
                                                                    
                                                                    <!-- Submit Project Document -->
                                                                    <button
                                                                        
                                                                        onclick="confirm('Are you sure you want to open this project for review? If you proceed, you will be assigned as the official reviewer.') || event.stopImmediatePropagation()" 
                                                                        type="button"
                                                                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none"
                                                                        
                                                                    >   
                                                                        <div class="flex justify-between items-center">
                                                                            <div>
                                                                                Review
                                                                            </div>

                                                                            <div>
                                                                                <x-svg.submit class="text-red-600 hover:text-red-700 size-4 shrink-0" title="Review Project" />
                                                                            </div>
                                                                        </div>

                                                                        
                                                                    </button> 
                                                                    



                                                                    <a
                                                                                    
                                                                        href="#"
                                                                        
                                                                        wire:navigate
                                                                        class="block w-full px-4 py-2 text-left text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none"
                                                                        
                                                                    >   
                                                                        <div class="flex justify-between items-center">
                                                                            <div>
                                                                                Review
                                                                            </div>

                                                                            <div>
                                                                                <x-svg.submit class="text-red-600 hover:text-red-700 size-4 shrink-0" title="Review Project" />
                                                                            </div>
                                                                        </div>

                                                                        
                                                                    </a>
    

                                                                </div>
                                                            </el-menu>
                                                        </el-dropdown> --}}
                        

                                                    </div>
                                                </div>
                                            </td>
                                            {{-- ./ Actions --}}

                                            <td class=" text-nowrap align-top whitespace-nowrap px-4 py-3 text-sm text-gray-700">

                                                
                                                <div class="flex items-center gap-2">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 ring-1 ring-slate-200">
                                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4 7a2 2 0 0 1 2-2h7l5 5v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M13 5v4h4"/></svg>
                                                    </div>
                                                    <div>
                                                        <span    
                                                            class="font-medium text-slate-900 hover:underline max-w-42 text-wrap">
                                                            {{ $attachment->attachment }}
                                                        </span>
                                                        <div class="text-xs text-slate-500 uppercase">
                                                             Size: {{ $this->convertBytes($attachment->size_bytes) }}
                                                        </div> 
                                                        <div class="text-xs text-slate-500 uppercase">
                                                             Type: {{ $attachment->mime_type }}
                                                        </div> 

                                                    </div>
        

                                                </div>
                                                    
                                            </td>
                                            
                                            <td class="px-4 py-2 align-top">
                                                  
                                                <div class="text-xs text-slate-500">
                                                    @php    
                                                        $formatted = $this->returnFormattedDatetime($attachment->created_at);
                                                        $userName = $this->returnFormattedUser($attachment->created_by);
                                                    @endphp

                                                    @if($formatted)
                                                        <div class="text-sm">Added at {{ $formatted }}</div>
                                                        <div class="text-xs text-slate-400 max-w-42 text-wrap">
                                                            Added by {{ $userName }}
                                                        </div>
                                                    @else
                                                        <div class="text-sm text-slate-400 italic">No data yet</div>
                                                    @endif
                                                </div>  
                                            </td>
                                            
    

                                                
    
 


                                            
        

                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <th scope="col" colspan="6" class="px-6 py-3 text-start">
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
                            {{ $attachments->links() }}

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
                                <p class="text-sm text-gray-600 ">
                                    {{ count($attachments) > 0 ? 'of '.$attachments->total()  : '' }}
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

     

    {{-- <form wire:submit="save">
        <div
            x-data="{ uploading: false, progress: 0 }"
            x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-cancel="uploading = false"
            x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress"
        >
            <!-- File Input -->
            <input type="file" wire:model="photo">

            <button type="submit">Save</button>
    
            <!-- Progress Bar -->
            <div x-show="uploading">
                <progress max="100" x-bind:value="progress"></progress>
            </div>
        </div>
    
        <!-- ... -->
    </form> --}}

 
    <div wire:loading  wire:target="copy"
    
    >
        <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
            <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                </svg>
                <div class="text-sm font-medium">
                    Copying record...
                </div>
            </div>
        </div>

        
    </div>

 

</div>
