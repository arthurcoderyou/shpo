<?php
    function isImageMimeForReview($filename) {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $imageExtensions);
    }
?>


<!-- Table Section -->
<div class=" px-4 py-6 sm:px-6 lg:px-8  mx-auto"

    x-data="{
        showModal: false,  
        showDocumentModal: false,
        handleKeydown(event) {
            if (event.keyCode == 191) {
                this.showModal = true; 
                this.showDocumentModal = true; 
            }
            if (event.keyCode == 27) { 
                this.showModal = false; 
                this.showDocumentModal = false; 
                $wire.search = '';
            }

        },
        search_project() {
            this.showModal = false;
            this.showDocumentModal = false; 
            {{-- $wire.search = '';  --}}
        }
    }"
>

    <script src="https://cdn.jsdelivr.net/npm/@preline/remove-element@2.6.0/index.min.js"></script>


    {{-- <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div> --}}

    <!-- Card -->
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden ">
            <!-- Header -->
            <div class="px-3 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 ">
                <div>
                <h2 class="text-xl font-semibold text-gray-800 ">
                    Review on Project {{ $project->name ?? "" }}
                </h2>
                <p class="text-sm text-gray-600  ">
                    NOTE: Select a project to enable export of reviews
                </p>
                </div>

                <div>
                <div class="inline-flex gap-x-2">


                    <input type="text" wire:model.live="search"
                        class="py-2 px-3 inline-flex max-w-32 items-center gap-x-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                        placeholder="Search">


                    @if( $routeIsIndex 
                       )
                        <button
                            @click="showModal = true" type="button"
                            @keydown.window="handleKeydown" 
                            class="py-2 px-3 inline-flex min-w-12 items-center gap-x-2 border-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50   "
                            {{-- href="{{ route('schedule.index') }}"> --}}
                            >
                            
                            {{ !empty($project->name) ? $project->name : "Search Project" }} 

                            <svg class="shrink-0 size-[.8em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                        </button>


                        @if(!empty($project))
                            <button
                                @click="showDocumentModal = true" type="button"
                                @keydown.window="handleKeydown" 
                                class="py-2 px-3 inline-flex min-w-12 items-center gap-x-2 border-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50   "
                                {{-- href="{{ route('schedule.index') }}"> --}}
                                >
                                
                                {{ !empty($project_document->document_type->name) ? $project_document->document_type->name : "Search Project Document" }} 

                                <svg class="shrink-0 size-[.8em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                            </button>
                        @endif





                    @endif


                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="view_status" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                            <option value="">View Status</option> 
                            <option value="viewed">Viewed</option>
                            <option value="not_view">Not Viewed</option> 
                        </select>
                    </div>


                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="review_status" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                            <option value="">Review Status</option> 
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option> 
                        </select>
                    </div>
                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="sort_by" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                            <option value="">Sort By</option> 
                            <option>Latest Added</option>
                            <option>Oldest Added</option>
                            <option>Latest Updated</option>
                            <option>Oldest Updated</option>
                        </select>
                    </div>

                    {{-- @if( Auth::user()->can('activity log delete') || Auth::user()->can('system access global admin')) 
                        <button
                            onclick="confirm('Are you sure, you want to delete this records?') || event.stopImmediatePropagation()"
                            wire:click.prevent="deleteSelected"
                            {{ $count == 0 ? 'disabled' : '' }}
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-red-500 text-white shadow-sm hover:bg-red-50 hover:text-red-600 hover:border-red-500 focus:outline-red-500 focus:text-red-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            Delete ({{ $count }})
                        </button>
                     @endif --}}
                       
                            
                    {{-- @if( !empty($project_id)  )  --}}
                        {{-- <button
                             
                            wire:click="generatePdf"
                            {{ count($selected_records) == 0 ? 'disabled' : '' }}
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-blue-500 text-white shadow-sm hover:bg-blue-50 hover:text-blue-600 hover:border-blue-500 focus:outline-blue-500 focus:text-blue-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                             
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M216 0h80c13.3 0 24 10.7 24 24v168h87.7c17.8 0 26.7 21.5 14.1 34.1L269.7 378.3c-7.5 7.5-19.8 7.5-27.3 0L90.1 226.1c-12.6-12.6-3.7-34.1 14.1-34.1H192V24c0-13.3 10.7-24 24-24zm296 376v112c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V376c0-13.3 10.7-24 24-24h146.7l49 49c20.1 20.1 52.5 20.1 72.6 0l49-49H488c13.3 0 24 10.7 24 24zm-124 88c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20zm64 0c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20z"/></svg>

                            PDF 
                        </button> --}}
                     {{-- @endif --}}

                    <x-ui.table.reset-button class="px-3 py-1.5" wireClick="resetFilters" />
                </div>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <table class="min-w-full divide-y divide-gray-200 ">
                <thead class="bg-gray-50 ">
                <tr>
                    <th scope="col" class="px-2 py-3 text-start max-w-32 text-wrap">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Actions
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Review
                            </span>
                        </div>
                    </th>

  
                    <th scope="col" class="px-6 py-3 text-end"></th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 ">

                     


                    @if(!empty($reviews) && count($reviews) > 0 && !empty($project))
                        @foreach ($reviews as $review)
                            <tr>

                                @php
                                                

                                    if(request()->routeIs("review.index")){

                                        if(!empty($review->project)){
                                            $next_reviewer = $review->project->getCurrentReviewer(); 
                                            $project = $review->project;
                                        }

                                        

                                    }

                                @endphp


                                {{-- <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <label for="project_{{ $review->id }}" class="flex">
                                            <input type="checkbox"
                                            wire:model="selected_records"
                                            wire:change="updateSelectedCount"
                                            class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                                            id="project_{{ $review->id }}"
                                            value="{{ $review->id }}"
                                            >
                                            <span class="sr-only">Checkbox</span>
                                        </label>
                                    </div>
                                </td> --}}

                                <!-- Actions -->
                                <td class="w-fill text-nowrap align-top px-4 py-3   ">
                                    <div class="flex items-center justify-between space-x-2">
                                        <div class="flex items-center gap-1">



                                            @if($review->project->creator->id == Auth::user()->id )
                                                @if($review->viewed == false)

                                                    <button 
                                                        onclick="confirm('Mark this as viewed?') || event.stopImmediatePropagation()" title="Mark as Viewed"
                                                        wire:click.prevent="mark_as_viewed({{ $review->id }})"
                                                        type="button"
                                                        class="rounded-md bg-lime-600 px-2.5 py-1 text-xs font-medium text-white">
                                                        Mark as Viewed
                                                    </button> 
                                                @else   
                                                    <span class="text-sm text-gray-600 ">
                                                        Viewed
                                                    </span>
                                                @endif

                                            @elseif(!Auth::user()->hasRole('User'))
                                                @if($review->viewed == false)
                                                    <span class="text-sm text-gray-600 max-w-32 text-wrap">
                                                        Not Viewed by {{ $review->project->creator->name }}
                                                    </span>
                                                @else   
                                                    <span class="text-sm text-gray-600 max-w-32 text-wrap ">
                                                        Viewed at 
                                                        <strong>{{ \Carbon\Carbon::parse($review->updated_at)->format('M d, Y h:i A') }}</strong> 
                                                        by 
                                                        <strong>{{ $review->project->creator->name }}</strong> 
                                                    </span>
                                                @endif
                                            @endif


 
                                            {{-- -
                                            <el-dropdown class="inline-block p-0">
                                                <button class=" inline-flex rounded-md border border-slate-200 p-1 text-slate-600 hover:bg-slate-50">
                                                    
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                                        <circle cx="12" cy="5" r="2" />
                                                        <circle cx="12" cy="12" r="2" />
                                                        <circle cx="12" cy="19" r="2" />
                                                    </svg>

                                                </button>

                                                <el-menu anchor="bottom end" popover class="m-0 w-56 origin-top-right rounded-md bg-white p-0 shadow-lg outline outline-1 outline-black/5 transition [--anchor-gap:theme(spacing.2)] [transition-behavior:allow-discrete] data-[closed]:scale-95 data-[closed]:transform data-[closed]:opacity-0 data-[enter]:duration-100 data-[leave]:duration-75 data-[enter]:ease-out data-[leave]:ease-in">
                                                    <div class="py-1">
                                                        

                                                          
                                                        
                                                        @if(Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('project submit'))
                                                            @if($project_document->allow_project_submission == true)
                                                            <!-- Submit Project Document -->
                                                            <button
                                                                {{ $project_document->allow_project_submission == true ? '' : 'disabled' }}
                                                                onclick="confirm('Are you sure, you want to submit this project?') || event.stopImmediatePropagation()"

                                                                wire:click.prevent="submit_project_document({{ $project_document->id }})"
                                                                type="button"
                                                                class="block w-full px-4 py-2 text-left text-sm  
                                                                
                                                                {{ $project_document->allow_project_submission == true ? 'text-gray-700' : 'text-gray-300' }}
                                                                
                                                                focus:bg-gray-100 focus:text-gray-900 focus:outline-none"
                                                            >   
                                                                <div class="flex justify-between items-center">
                                                                    <div>
                                                                        Submit
                                                                    </div>

                                                                    <div>
                                                                        <x-svg.submit class="text-red-600 hover:text-red-700 size-4 shrink-0" title="Submit" />
                                                                    </div>
                                                                </div>

                                                                
                                                            </button>
                                                            @endif
                                                        @endif
                                                        <!-- #endregion -->
                                                            

                                                       
 
                                                            
                                                        @if(Auth::user()->can('system access global admin') 
                                                        // || Auth::user()->can('project delete')
                                                        )
                                                            <!-- Force Delete-->
                                                            <button
                                                                onclick="confirm('Are you sure, you want to delete this record?') || event.stopImmediatePropagation()"
                                                                wire:click.prevent="delete({{ $project_document->id }})"
                                                                type="button"
                                                                class="block w-full px-4 py-2 text-left text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none"
                                                            >   
                                                                <div class="flex justify-between items-center">
                                                                    <div>
                                                                        Delete
                                                                    </div>

                                                                    <div>
                                                                        <x-svg.delete class="text-red-600 hover:text-red-700 size-4 shrink-0" title="Delete" />
                                                                    </div>
                                                                </div>

                                                                
                                                            </button>
                                                        @endif  
                                                         

                                                    </div>
                                                </el-menu>
                                            </el-dropdown>
                                             --}}

                                        </div>
                                    </div>
                                </td>
                                <!-- ./ Actions -->


                                @php    
                                    $config = $this->returnStatusConfig($review->review_status); 
                                    $document_config = $this->returnStatusConfig($review->project_document_status); 
                                    $reviewerName = $this->returnReviewerName($review);
                                    $slotType = $this->returnSlotData($review,'slot_type');
                                    $slotRole = $this->returnSlotData($review,'slot_role');
                                    // $reviewStatus = $this->returnReviewStatus($project_document);
                                    // $dueAtText = $this->returnDueDate($project_document, 'dueAtText');
                                    // $dueAtDiff = $this->returnDueDate($project_document, 'dueAtDiff');
                                    // $flags = $this->returnReviewFlagsStatus($project_document);
                                @endphp



                                <!-- Review -->
                                <td class="px-4 py-2 align-top"
                                
                                    x-data="{ 

                                        roleColors: {
                                            'global admin': 'bg-red-100 text-red-700 ring-red-200',
                                            'admin':        'bg-amber-100 text-amber-800 ring-amber-200',
                                            'reviewer':     'bg-sky-100 text-sky-700 ring-sky-200',
                                            'user':         'bg-slate-100 text-slate-700 ring-slate-200',
                                            '__none':       'bg-zinc-100 text-zinc-700 ring-zinc-200',
                                        },

                                        badgeCls(role){
                                            const key = (role || '').toLowerCase();
                                            const base = 'px-1.5 py-0.5 rounded-md text-xs ring-1';
                                            return `${base} ${(this.roleColors[key] ?? this.roleColors['__none'])}`;
                                        }, 
                                    }"  
                                    >

                                    <!-- Review status badge -->
                                    <div class="mb-1">
                                        <div class="flex items-center gap-1">
                                            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414L9 13.414l4.707-4.707Z"/>
                                            </svg>
                                            <span class="text-[11px] font-medium text-slate-700">Review status:</span> 

                                            <span class="inline-flex items-center gap-1 rounded-full {{ $config['bg'] }} px-2 py-0.5 text-[11px] font-semibold {{ $config['text'] }} ring-1 ring-inset {{ $config['ring'] }}">
                                                {{ $config['label'] }}  
                                            </span>

                                        </div> 
                                        
                                    </div>

                                    @if(!empty($review->project_document_id))
                                    <!-- Document status badge -->
                                    <div class="mb-1">
                                        <div class="flex items-center gap-1">
                                            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414L9 13.414l4.707-4.707Z"/>
                                            </svg>
                                            <span class="text-[11px] font-medium text-slate-700">Document status:</span> 

                                            <span class="inline-flex items-center gap-1 rounded-full {{ $document_config['bg'] }} px-2 py-0.5 text-[11px] font-semibold {{ $document_config['text'] }} ring-1 ring-inset {{ $document_config['ring'] }}">
                                                {{ $document_config['label'] }}  
                                            </span>

                                        </div> 
                                        
                                    </div>
                                    @endif

                                    @if(Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('system access reviewer') )
                                    <!-- Reviewer block -->
                                    <div class="space-y-1 text-[11px] leading-4 text-slate-600">

                                        <div class="flex items-center gap-1">
                                            <!-- Eye/user icon -->
                                            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            <span class="font-medium text-slate-700">Reviewer:</span>
                                            <span class="truncate">{{ $reviewerName }}</span>
 
                                            @if($slotType === 'person')

                                                @php 
                                                     
                                                    $roles = $this->getUserRoles($review->reviewer_id);

                                                    // dd($roles);
                                                @endphp

                                                {{-- <span class="ml-1 rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] text-slate-700">{{ $slotRole }}</span> --}}
                                                <!-- If this row has a userId (person or claimed open slot), show that user's roles -->
                                                @if(!empty($roles))
                                                    <div class="flex flex-wrap items-center gap-1.5">
                                                        @foreach ($roles as $role)
                                                            <span :class="badgeCls('{{ $role }}')"  >{{ $role }}</span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span :class="badgeCls('')">No role</span>
                                                @endif


                                            @elseif(($slotType === 'open'))

                                                @if($slotType === 'open' && empty($project_document->user_id)) 
                                                    <span class="ml-1 rounded border border-amber-200 bg-amber-50 px-1.5 py-0.5 text-[10px] text-amber-700">Open</span>
                                                    @php 
                                                         

                                                        $roles = $this->getUserRoles($review->reviewer_id);

                                                        // dd($roles);
                                                    @endphp
                                                    {{-- <span class="ml-1 rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] text-slate-700">{{ $slotRole }}</span> --}}
                                                    <!-- If this row has a userId (person or claimed open slot), show that user's roles -->
                                                    @if(!empty($roles))
                                                        <div class="flex flex-wrap items-center gap-1.5">
                                                            @foreach ($roles as $role)
                                                                <span :class="badgeCls('{{ $role }}')"  >{{ $role }}</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span :class="badgeCls('')">No role</span>
                                                    @endif

                                                    
                                                @elseif($slotType === 'open' && !empty($project_document->user_id))
                                                    <span class="ml-1 rounded border border-sky-200 bg-sky-50 px-1.5 py-0.5 text-[10px] text-sky-700">Claimed</span>
                                                @else
                                                    <span class="ml-1 rounded border border-amber-200 bg-amber-50 px-1.5 py-0.5 text-[10px] text-amber-700">Open</span>
                                                @endif
                                            @endif
                                                


                                        </div>

                                        {{-- @if(!empty($reviewStatus))
                                        <div class="flex items-center gap-1">
                                            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414L9 13.414l4.707-4.707Z"/>
                                            </svg>
                                            <span class="font-medium text-slate-700">Review status:</span>
                                            <span class="uppercase tracking-wide">{{ $reviewStatus }}</span>
                                        </div>
                                        @endif

                                        <div class="flex items-center gap-1">
                                            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3.5 9h17M5 20h14a2 2 0 0 0 2-2v-9H3v9a2 2 0 0 0 2 2Z"/>
                                            </svg>
                                            <span class="font-medium text-slate-700">Expected:</span>
                                            @if($dueAtText)
                                                <span>{{ $dueAtText }}</span>
                                                <span class="text-slate-400">({{ $dueAtDiff }})</span>
                                            @else
                                                <span class="italic text-slate-400">No due date</span>
                                            @endif
                                        </div>

                                        <!-- Flags -->
                                        <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                            @php
                                                $flagLabels = [
                                                    'requires_project_update' => 'Project update',
                                                    // 'requires_document_update' => 'Document update',
                                                    'requires_attachment_update' => 'Attachment update',
                                                ];
                                            @endphp

                                            @foreach($flagLabels as $key => $label)
                                                @if($flags[$key] ?? false)
                                                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-1.5 py-0.5 ring-1 ring-amber-200 text-[10px] font-medium text-amber-700">
                                                        <svg class="size-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a8 8 0 100 16 8 8 0 000-16Zm.75 4a.75.75 0 00-1.5 0v5.25c0 .414.336.75.75.75h3.5a.75.75 0 000-1.5h-2.75V6Z"/></svg>
                                                        {{ $label }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-50 px-1.5 py-0.5 ring-1 ring-slate-200 text-[10px] text-slate-500">
                                                        <svg class="size-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-1-5 5-5-1.414-1.414L9 10.172 7.414 8.586 6 10l3 3Z"/></svg>
                                                        {{ $label }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div> --}}
                                        @if(Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('system access reviewer') )
                                        <div>
                                            <ul class="  flex flex-col gap-y-3">
                                                <li class="flex items-center gap-x-2.5">
                                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                                    <a class="text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2  " href="mailto:{{ $review->reviewer->email }}">
                                                        {{ $review->reviewer->email }}
                                                    </a>
                                                </li>
                                            
                                            
                                            </ul>
                                        </div>
                                        @endif

                                            


                                    </div>
                                    @endif
                                                 
 
    

  

                                </td>
                                <!-- ./ Review -->


 
                                <!-- Reviewer Information -->
                                <td class="size-auto text-wrap max-w-full ">
                                    <div class="px-2 py-2 align-top">
                                        
                                            <!-- Profile -->
                                            <div class="flex   gap-x-3">
                                                
                                            
                                                <div class="grow">
                                                     
                                                    <p class="text-sm text-gray-500"> 
                                                        @if($review->review_status == "rejected")
                                                            <span class="font-bold text-red-500">Rejected</span>
                                                        @elseif($review->review_status == "changes_requested")
                                                            <span class="font-bold text-amber-500">Changes Requested</span>
                                                        @elseif ($review->review_status == "approved")
                                                            <span class="font-bold text-lime-500">Approved</span>
                                                        @elseif ($review->review_status == "reviewed")
                                                            <span class="font-bold text-green-500">Reviewed</span>
                                                        @endif
                                                        at {{ \Carbon\Carbon::parse($review->created_at)->format('d M, h:i A') }}
                                                        @if(Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('system access reviewer') )
                                                        by {{ $review->creator ? $review->creator->name : '' }}
                                                        @endif

                                                    </p> 
                                                </div>
                                            </div>
                                            <!-- End Profile -->
                                            
                                            <!-- About -->
                                            <div class=" ">

                                                {{-- Reviewer Notes --}}
                                                <div class="space-y-1">
                                                    <p class="text-sm font-medium text-gray-800">Reviewer Notes</p>
                                                    <p class="text-sm text-gray-700">{{ $review->project_review }}</p>
                                                </div>
                                            
                                                @if(!empty($review)
                                                    //  && $review->review_status == "rejected"
                                                     )
                                                    {{-- Attachments --}}
                                                    @php
                                                    $groups = collect($review->attachments ?? [])
                                                        ->sortByDesc('created_at')
                                                        ->groupBy(fn($a) => $a->created_at->format('M d, Y h:i A'));
                                                    @endphp

                                                    @if($groups->isNotEmpty())
                                                        <div class="mt-4">
                                                            <label class="inline-block text-sm font-medium text-gray-800">Attachments</label>

                                                            <div class="hs-accordion-group divide-y divide-gray-100 rounded-xl border border-gray-200 mt-2">
                                                            @foreach($groups as $date => $attachments)
                                                                @php
                                                                $idx = $loop->iteration;
                                                                $accordionId = "att-{$idx}";
                                                                $panelId = "att-panel-{$idx}-{$review->id}";
                                                                @endphp

                                                                <div class="hs-accordion" id="{{ $accordionId }}">
                                                                    <button
                                                                        type="button"
                                                                        class="hs-accordion-toggle w-full px-4 py-3 flex items-center justify-between text-left text-sm font-semibold text-gray-800 hover:text-blue-600 focus:outline-none"
                                                                        aria-controls="{{ $panelId }}"
                                                                    >
                                                                        <span class="inline-flex items-center gap-x-2">
                                                                        {{-- plus/minus icon swap --}}
                                                                        <svg class="hs-accordion-active:hidden block size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5v14" />
                                                                        </svg>
                                                                        <svg class="hs-accordion-active:block hidden size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                                                                        </svg>
                                                                        {{ $date }}
                                                                        </span>
                                                                    </button>

                                                                    <div id="{{ $panelId }}" class="hs-accordion-content hidden overflow-hidden transition-[height] duration-300">
                                                                        <div class="p-4 grid grid-cols-1   gap-3">
                                                                            @foreach($attachments as $file)
                                                                                @php
                                                                                $fileId   = $file->id;
                                                                                $fileName = basename($file->attachment);
                                                                                $fileUrl  = asset('storage/uploads/'.$review->id.'/' . $file->attachment);
                                                                                @endphp

                                                                                <div class="flex items-center justify-between gap-3 border border-gray-200 rounded-lg p-2">
                                                                                <div class="flex items-center gap-3 min-w-0">
                                                                                    {{-- @if(isImageMimeForReview($fileName))
                                                                                    <div class="w-14 h-14 flex-none overflow-hidden rounded-md bg-gray-50">
                                                                                        <img src="{{ $fileUrl }}" alt="{{ $fileName }}" class="w-full h-full object-cover">
                                                                                    </div>
                                                                                    @else --}}
                                                                                    <div class="w-14 h-14 flex items-center justify-center bg-gray-100 rounded-md">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625A3.375 3.375 0 0016.125 8.25h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H5.625A1.125 1.125 0 004.5 3.375v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9" />
                                                                                        </svg>
                                                                                    </div>
                                                                                    {{-- @endif --}}

                                                                                    <div class="flex flex-col gap-0.5 min-w-0">
                                                                                    <div class="truncate text-sm font-medium text-slate-900">{{ $fileName }}</div>
                                                                                    {{-- (optional) file size, type, etc. --}}
                                                                                    </div>
                                                                                </div>

                                                                                <div class="flex items-center gap-3">
                                                                                    <a href="{{ $fileUrl }}" download="{{ $fileName }}" class="inline-flex p-2 rounded hover:bg-gray-100" title="Download">
                                                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352z"/></svg>
                                                                                    </a>

                                                                                    @if(Auth::user()->hasRole('Admin'))
                                                                                    <button
                                                                                        type="button"
                                                                                        class="inline-flex p-2 rounded hover:bg-gray-100 text-black"
                                                                                        onclick="confirm('Are you sure you want to remove this attachment?') || event.stopImmediatePropagation()"
                                                                                        wire:click.prevent="removeUploadedAttachment({{ $fileId }})"
                                                                                        title="Remove"
                                                                                    >
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                                                        <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                                                                        </svg>
                                                                                    </button>
                                                                                    @endif
                                                                                </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <p class="mt-3 text-sm text-gray-500">No attachments.</p>
                                                    @endif



                                                    {{-- Required Updates --}}
                                                    @php
                                                    // Eager-load to avoid N+1 when rendering names
                                                    $review->loadMissing([
                                                        'required_document_updates.document_type',
                                                        'required_attachment_updates.document_type',
                                                    ]);

                                                    $docTypeNames = $review->required_document_updates
                                                        ?->pluck('document_type.name')
                                                        ->filter()
                                                        ->unique()
                                                        ->values()
                                                        ->all() ?? [];

                                                    $attDocTypeNames = $review->required_attachment_updates
                                                        ?->pluck('document_type.name')
                                                        ->filter()
                                                        ->unique()
                                                        ->values()
                                                        ->all() ?? [];
                                                    @endphp

                                                    <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <svg class="w-4 h-4 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 .5a1 1 0 0 1 .894.553l8 16A1 1 0 0 1 18 18.5H2a1 1 0 0 1-.894-1.447l8-16A1 1 0 0 1 10 .5zM9 7v5h2V7H9zm1 8a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z"/>
                                                            </svg>
                                                            <h3 class="text-sm font-semibold text-amber-900">Required updates before resubmission</h3>
                                                        </div>

                                                        <ul class="space-y-2 text-sm text-amber-900">
                                                            @if($review->requires_project_update)
                                                            <li class="flex gap-2">
                                                                <span class="inline-flex mt-0.5 w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                                <span><strong>Project details:</strong> Update and save changes to the project information.</span>
                                                            </li>
                                                            @endif

                                                            @if($review->requires_document_update)
                                                            <li class="flex gap-2">
                                                                <span class="inline-flex mt-0.5 w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                                <span>
                                                                <strong>Project documents:</strong> Add at least one new document
                                                                @if(!empty($docTypeNames))
                                                                    for the following type(s): <em>{{ implode(', ', $docTypeNames) }}</em>.
                                                                @else
                                                                    (any document type is acceptable).
                                                                @endif
                                                                </span>
                                                            </li>
                                                            @endif

                                                            @if($review->requires_attachment_update)
                                                            <li class="flex gap-2">
                                                                <span class="inline-flex mt-0.5 w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                                <span>
                                                                <strong>Attachments:</strong> Upload at least one new attachment to an existing project document
                                                                @if(!empty($attDocTypeNames))
                                                                    (required on document type(s): <em>{{ implode(', ', $attDocTypeNames) }}</em>).
                                                                @else
                                                                    (any document is acceptable).
                                                                @endif
                                                                </span>
                                                            </li>
                                                            @endif

                                                            @if(!$review->requires_project_update && !$review->requires_document_update && !$review->requires_attachment_update)
                                                            <li class="flex gap-2">
                                                                <span class="inline-flex mt-0.5 w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                                <span>No specific updates required by the reviewer.</span>
                                                            </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                @endif


                                            
                                                
                                                

                                                @if(Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('system access reviewer') )
                                                    @if($review->project->status !== "approved" && $review->review_status == "approved" && !empty( $next_reviewer->user ) && !empty(  $review->project ) )
                                                    <!-- Profile -->
                                                    <div class="  p-2  ">
                                                        
                                                        
                                                        <div class="grow">
                                                            <h1 class="text-sm font-medium text-gray-800 ">
                                                                Next Reviewer: {{ $next_reviewer->user->name ? $next_reviewer->user->name : '' }}
                                                            </h1>
                                                            
                                                        
                                                            <span class="block text-sm text-gray-500 ">
                                                                Review Status: 
                                                                @if($next_reviewer->review_status == "approved")
                                                                    <span class="font-bold text-lime-500">{{ ucfirst($next_reviewer->review_status) }} </span> 
                                                                @elseif($next_reviewer->review_status == "rejected")
                                                                    <span class="font-bold text-red-500">{{ ucfirst($next_reviewer->review_status) }} </span> 

                                                                @else 
                                                                    <span class="font-bold text-yellow-500">{{ ucfirst($next_reviewer->review_status) }} </span> 

                                                                @endif
                                                                
                                                            </span>

                                                            <span class="block text-sm text-gray-500   ">
                                                                @if($next_reviewer->review_status == "pending") <!-- if review status is pending, it means the review due date must be seen here -->
                                                                    Expected <span class="font-bold text-blue-800 ">review</span> on or before <br>  
                                                                    <strong>
                                                                        {{ \Carbon\Carbon::parse($project->reviewer_due_date)->format('M d, Y h:i A') }}
                                                                    </strong> 
                                                                @endif
                                                            </span>

    
                                                            <ul class="mt-2 flex flex-col gap-y-3">
                                                                <li class="flex items-center gap-x-2.5">
                                                                    <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                                                    <a class="text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2  " href="mailto:{{ $review->reviewer->email }}">
                                                                        {{ $next_reviewer->user->email }}
                                                                    </a>
                                                                </li>
                                                            
                                                            
                                                            </ul>

                                                        

                                                        </div>
                                                    </div>
                                                    <!-- End Profile -->
                                                    @endif
                                                @endif


                                            </div>
                                            <!-- End About -->
                                            
 
                                    </div>
                                </td>
                                <!-- ./ Reviewer Information -->


                                

                                



                            </tr>
                        @endforeach
                    @elseif(empty($project) && empty($project_document))
                         <tr>
                            <th scope="col" colspan="3" class="px-6 py-6 text-start">
                                <div class="text-start">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                                    Please select a project to view reviews
                                    </span>
                                </div>
                            </th>
                        </tr>
                    @else
                        <tr>
                            <th scope="col" colspan="3" class="px-6 py-6 text-start">
                                <div class="text-start">
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
                {{ $reviews->links() }}

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
                        {{ count($reviews) > 0 ? 'of '.$reviews->total()  : '' }}
                    </p>
                </div>


            </div>
            <!-- End Footer -->


            </div>
        </div>
        </div>
    </div>
    <!-- End Card -->




    <!-- Project list modal-->
    @teleport('body')
        <div x-show="showModal" x-trap="showModal" class="relative z-50 " aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <!-- <div class="fixed inset-0 z-10 w-screen overflow-y-auto py-10"> -->
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto py-10">
                <div class="flex justify-center p-4 sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div @click.outside="showModal = false" class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="w-full px-1 pt-1" x-data="{
                                searchPosts(event) {
                                    document.getElementById('searchInput').focus();
                                    event.preventDefault();
                                }
                            }">
                                <form action="" autocomplete="off">
                                    <input
                                    autocomplete="off"
                                    wire:model.live.throttle.500ms="project_search" type="text" id="searchInput"
                                    name="searchInput"
                                    class="block w-full flex-1 py-2 px-3 mt-2 outline-none border-none rounded-md bg-slate-100"
                                    placeholder="Search for project name ..." @keydown.slash.window="searchPosts" />
                                </form>
                                <div class="mt-2 w-full overflow-hidden rounded-md bg-white">
 
                                    @if(!empty($results) && count($results) > 0)
                                        <div class=" py-2 px-3 border-b border-slate-200 text-sm font-medium text-slate-500">

                                            All Projects <strong>(Click to select a project)</strong>

                                        </div>

                                        @foreach ($results as $result)
                                            <div class="cursor-pointer py-2 px-3 hover:bg-slate-100 bg-white border border-gray-200 shadow-sm rounded-xl mb-1"
                                            wire:click="search_project('{{  $result->id }}')"
                                            @click="showModal = false"
                                            >
                                                <p class="text-sm font-medium text-gray-600 cursor-pointer flex items-center gap-3">
                                                    

                                                    <div class="max-w-full text-wrap ">
                                                        <div class="px-2 py-2   text-wrap">
                                                                




                                                            <span class="text-sm text-gray-600 ">
                                                                <strong>{{ $result->name }}</strong>
                                                                <hr>
                                                                <span class="text-blue-500">{{ $result->project_reviews->count() ?? 0 }} reviews</span> 
                                                                <hr>
                                                                {{ $result->federal_agency }}
                                                                <hr>
                                                                {{ $result->location }}
                                                            </span> 

                                                                    

                                                        </div>
                                                    </div>

                                                    

                                                    <div class="max-w-full size-auto whitespace-nowrap  ">
                                                        <div class="px-2 py-2   max-h-52 text-wrap overflow-auto">
                                                            <span class="text-sm text-gray-600  ">
                                                                {{ $result->description ? $result->description : '' }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                </p>
                                            </div>
                                        @endforeach

                                    @else
                                        <div class=" py-2 px-3 border-b border-slate-200 text-sm font-medium text-slate-500">

                                            <div class="mb-2 bg-red-50 border-s-4 border-red-500 p-4 " role="alert" tabindex="-1" aria-labelledby="hs-bordered-red-style-label">
                                                <div class="flex">
                                                    <div class="shrink-0">
                                                        <!-- Icon -->
                                                        <span class="inline-flex justify-center items-center size-8 rounded-full border-4 border-red-100 bg-red-200 text-red-800 ">
                                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M18 6 6 18"></path>
                                                                <path d="m6 6 12 12"></path>
                                                            </svg>
                                                        </span>
                                                        <!-- End Icon -->
                                                    </div>
                                                    <div class="ms-3">
                                                        <h3 id="hs-bordered-red-style-label" class="text-gray-800 font-semibold ">
                                                            Project not found
                                                        </h3>
                                                        <p class="text-sm text-gray-700 ">

                                                            Search for name, description, agency or related data
                                                        </p>
                                                    </div>



                                                </div>
                                            </div>



                                        </div>
                                    @endif
 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endteleport
    <!-- ./ Project list modal -->



    <!-- Project document list modal-->
    @teleport('body')
        <div x-show="showDocumentModal" x-trap="showDocumentModal" class="relative z-50 " aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <!-- <div class="fixed inset-0 z-10 w-screen overflow-y-auto py-10"> -->
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto py-10">
                <div class="flex justify-center p-4 sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div @click.outside="showDocumentModal = false" class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="w-full px-1 pt-1" x-data="{
                                    (event) {
                                    document.getElementById('searchInput').focus();
                                    event.preventDefault();
                                }
                            }">
                                <form action="" autocomplete="off">
                                    <input
                                    autocomplete="off"
                                    wire:model.live.throttle.500ms="project_document_search" type="text" id="searchInput"
                                    name="searchInput"
                                    class="block w-full flex-1 py-2 px-3 mt-2 outline-none border-none rounded-md bg-slate-100"
                                    placeholder="Search for project document name ..." @keydown.slash.window="searchPosts" />
                                </form>
                                <div class="mt-2 w-full overflow-hidden rounded-md bg-white">
 
                                    @if(!empty($project_document_results) && count($project_document_results) > 0)
                                        <div class=" py-2 px-3 border-b border-slate-200 text-sm font-medium text-slate-500">

                                            All Project Documents <strong>(Click to select a project)</strong>

                                        </div>

                                        @foreach ($project_document_results as $result)
                                            <div class="cursor-pointer py-2 px-3 hover:bg-slate-100 bg-white border border-gray-200 shadow-sm rounded-xl mb-1"
                                            wire:click="search_project_document('{{  $result->id }}')"
                                            @click="showDocumentModal = false"
                                            >
                                                <p class="text-sm font-medium text-gray-600 cursor-pointer flex items-center gap-3">
                                                    

                                                    <div class="max-w-full text-wrap ">
                                                        <div class="px-2 py-2   text-wrap">
                                                                




                                                            <span class="text-sm text-gray-600 ">
                                                                <strong>{{ $result->document_type->name ?? null  }}</strong>
                                                                <hr>
                                                                <span class="text-blue-500">{{ $result->project_reviews->count() ?? 0 }} reviews</span> 
                                                                <hr>
                                                                {{ $result->project->name ?? null }}
                                                                {{--  
                                                                <hr>
                                                                {{ $result->location }} --}}
                                                            </span> 

                                                                    

                                                        </div>
                                                    </div>

                                                    

                                                    {{-- <div class="max-w-full size-auto whitespace-nowrap  ">
                                                        <div class="px-2 py-2   max-h-52 text-wrap overflow-auto">
                                                            <span class="text-sm text-gray-600  ">
                                                                {{ $result->description ? $result->description : '' }}
                                                            </span>
                                                        </div>
                                                    </div> --}}

                                                </p>
                                            </div>
                                        @endforeach

                                    @else
                                        <div class=" py-2 px-3 border-b border-slate-200 text-sm font-medium text-slate-500">

                                            <div class="mb-2 bg-red-50 border-s-4 border-red-500 p-4 " role="alert" tabindex="-1" aria-labelledby="hs-bordered-red-style-label">
                                                <div class="flex">
                                                    <div class="shrink-0">
                                                        <!-- Icon -->
                                                        <span class="inline-flex justify-center items-center size-8 rounded-full border-4 border-red-100 bg-red-200 text-red-800 ">
                                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M18 6 6 18"></path>
                                                                <path d="m6 6 12 12"></path>
                                                            </svg>
                                                        </span>
                                                        <!-- End Icon -->
                                                    </div>
                                                    <div class="ms-3">
                                                        <h3 id="hs-bordered-red-style-label" class="text-gray-800 font-semibold ">
                                                            Project document not found
                                                        </h3>
                                                        <p class="text-sm text-gray-700 ">

                                                            Search for name of the document
                                                        </p>
                                                    </div>



                                                </div>
                                            </div>



                                        </div>
                                    @endif
 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endteleport
    <!-- ./ Project document list modal -->



    <!-- Loaders -->
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
    <!-- Loaders -->


    <!--  Loaders -->
        {{-- wire:target="table"   --}}
        {{-- <div wire:loading 
            class="p-0 m-0"
            style="padding: 0; margin: 0;">
            <div class="absolute right-4 top-4 z-50 inline-flex items-center gap-2 px-4 py-3 rounded-md text-sm text-white bg-blue-600 border border-blue-700 shadow-md animate-pulse mb-4 mx-3">
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
        </div> --}}

         

    <!-- ./  Loaders -->





</div>
<!-- End Table Section -->
