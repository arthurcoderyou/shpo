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
                    Re-Review Request 
                    {{-- {{ !empty($project) ? "on Project".$project->name : "" }} {{ !empty($project_document) ? "on Document".$project_document->document_type->name : "" }} --}}
                </h2>
                <p class="text-sm text-gray-600  ">
                    NOTE: Select a project/ document to enable export of reviews
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
                            class="text-nowrap overflow-hidden py-2 px-3 inline-flex min-w-12 items-center gap-x-2 border-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50   "
                            {{-- href="{{ route('schedule.index') }}"> --}}
                            >
                            
                            {{ !empty($project->name) ? $project->name : "Search Project" }} 

                            <svg class="shrink-0 size-[.8em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                        </button>


                        @if(!empty($project))
                            <button
                                @click="showDocumentModal = true" type="button"
                                @keydown.window="handleKeydown" 
                                class="text-nowrap overflow-hidden py-2 px-3 inline-flex min-w-12 items-center gap-x-2 border-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50   "
                                {{-- href="{{ route('schedule.index') }}"> --}}
                                >
                                
                                {{ !empty($project_document->document_type->name) ? $project_document->document_type->name : "Search Project Document" }} 

                                <svg class="shrink-0 size-[.8em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                            </button>
                        @endif
 

                    @endif


                     

                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="status" class="text-nowrap py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                            <option value="">Status</option> 
                            <option value="submitted">Submitted</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option> 
                        </select>
                    </div>
                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="sort_by" class="text-nowrap py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
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
                        @if(request()->routeIs('review.index'))
                            <a
                                href="{{ route('re-review.index') }}"
                                wire:navigate
                                class="text-nowrap py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-orange-500 text-white shadow-sm hover:bg-orange-900 hover:text-orange-600 hover:border-orange-500 focus:outline-orange-500 focus:text-orange-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M370.7 133.3C339.5 104 298.9 88 255.8 88c-77.5 .1-144.3 53.2-162.8 126.9-1.3 5.4-6.1 9.2-11.7 9.2H24.1c-7.5 0-13.2-6.8-11.8-14.2C33.9 94.9 134.8 8 256 8c66.4 0 126.8 26.1 171.3 68.7L463 41C478.1 25.9 504 36.6 504 57.9V192c0 13.3-10.7 24-24 24H345.9c-21.4 0-32.1-25.9-17-41l41.8-41.7zM32 296h134.1c21.4 0 32.1 25.9 17 41l-41.8 41.8c31.3 29.3 71.8 45.3 114.9 45.3 77.4-.1 144.3-53.1 162.8-126.8 1.3-5.4 6.1-9.2 11.7-9.2h57.3c7.5 0 13.2 6.8 11.8 14.2C478.1 417.1 377.2 504 256 504c-66.4 0-126.8-26.1-171.3-68.7L49 471C33.9 486.1 8 475.4 8 454.1V320c0-13.3 10.7-24 24-24z"/></svg>
                            </a> 
                        @endif
                            
                    {{-- @if( !empty($project_id)  )  --}}
                        <button
                             
                            wire:click="generatePdf"
                            {{ count($selected_records) == 0 ? 'disabled' : '' }}
                            class="text-nowrap py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-blue-500 text-white shadow-sm hover:bg-blue-50 hover:text-blue-600 hover:border-blue-500 focus:outline-blue-500 focus:text-blue-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                             
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M216 0h80c13.3 0 24 10.7 24 24v168h87.7c17.8 0 26.7 21.5 14.1 34.1L269.7 378.3c-7.5 7.5-19.8 7.5-27.3 0L90.1 226.1c-12.6-12.6-3.7-34.1 14.1-34.1H192V24c0-13.3 10.7-24 24-24zm296 376v112c0 13.3-10.7 24-24 24H24c-13.3 0-24-10.7-24-24V376c0-13.3 10.7-24 24-24h146.7l49 49c20.1 20.1 52.5 20.1 72.6 0l49-49H488c13.3 0 24 10.7 24 24zm-124 88c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20zm64 0c0-11-9-20-20-20s-20 9-20 20 9 20 20 20 20-9 20-20z"/></svg>

                            PDF 
                        </button>
                     {{-- @endif --}}

                        
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
                            Reason
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Status
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Requested to 
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Reqeusted by
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Project
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
                            Created
                            </span>
                        </div>
                    </th>
 
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 "
                
                    
                 
                
                >

                     


                    @if(!empty($reviews) && count($reviews) > 0  )
                        @foreach ($reviews as $review)
                            <tr
                                data-row 
                                x-data="{
                                    {{-- roles:@js($row['roles'] ?? []), --}}
                                    {{-- userId:@js($row['user_id'] ?? null), --}}

                                    openResponse: false,
                                    response_notes: '',
                                    response(reviewId,response_status){
                                        
                                        if(this.reviewId===null ) return;
                                        
                                        @this.response({{ $review->id }}, response_status,this.response_notes);
                                        
                                        openResponse=false;

                                    }

                                }"

            

                                data-uid="{{ $review->id }}"
                            
                            >
 


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
                                <td class="w-fill text-nowrap align-top px-2 py-3   ">
                                    <div class="flex items-center justify-between space-x-2">
                                        <div class="flex items-center gap-1">
  

                                            <button
                                                    type="button"
                                                    @click="openResponse = true"
                                                    class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-2.5 py-1 text-xs font-medium text-sm font-semibold text-white shadow hover:bg-sky-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-700 transition"
                                                >
                                                    {{-- <!-- User-add icon -->
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 19l2 2 4-4M17 11a4 4 0 10-8 0 4 4 0 008 0zm-9 8a7 7 0 1114 0v1H8v-1z"/>
                                                    </svg> --}}
                                                    Respond
                                                     
                                                </button>

                                                <!-- ======================== -->
                                                <!-- 📋 Pre-Review Modal -->
                                                <!-- ======================== -->
                                                <div
                                                    x-show="openResponse"
                                                    x-cloak
                                                    x-transition.opacity
                                                    @keydown.escape.window="openResponse = false"
                                                    @click.self="openResponse = false"
                                                    class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                                                    aria-modal="true" role="dialog"
                                                >
                                                    <!-- Modal Box -->
                                                    <div
                                                        x-transition
                                                        @click.stop
                                                        class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden"
                                                    >
                                                        <!-- Header -->
                                                        <div class="flex items-center justify-between border-b bg-sky-50 px-5 py-3">
                                                            <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                                                                <svg class="w-5 h-5 text-sky-600" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor" stroke-width="1.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M15 19l2 2 4-4M17 11a4 4 0 10-8 0 4 4 0 008 0zm-9 8a7 7 0 1114 0v1H8v-1z"/>
                                                                </svg>
                                                                Response on Re-Review request
                                                            </h3>
                                                            <button
                                                                @click="openResponse = false"
                                                                class="text-slate-500 hover:text-slate-700 transition"
                                                            >
                                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                    stroke-width="1.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        <!-- Body -->
                                                        <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">

                                                            <!-- Select additional reviewer(s) -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-slate-700">
                                                                    {{-- Requested by {{ !empty($review->project_reviewer_requested_by->user) ? $review->project_reviewer_requested_by->user->name : 'Requestor' }} --}}
                                                                    Reason:
                                                                </label>
                                                                <div class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600 text-sm text-wrap"> 
                                                                    <p>
                                                                        {{ $review->reason ?? 'No Added Reason' }}
                                                                    </p>
                                                                   

                                                                    <p class="text-xs text-slate-500 mt-1">
                                                                        Created {{ \Carbon\Carbon::parse($review->created_at)->format('d M, h:i A') }}
                                                                    </p>

                                                                </div>
                                                                
                                                            </div>



                                                            <fieldset class="col-span-12">
                                                                <legend class="text-sm font-semibold text-slate-900">Review decision</legend>
                                                                <p class="mt-1 text-xs text-slate-500">Choose an outcome and leave guidance for the submitter.</p>

                                                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
 
                                                                    <!-- Approve -->
                                                                    <div>
                                                                        <input id="response_status_approved_{{ $review->id }}" 
                                                                            name="response_status_{{ $review->id }}" 
                                                                            type="radio" 
                                                                            value="approved"
                                                                            wire:model.live="response_status" class="peer sr-only" />
                                                                        <label for="response_status_approved_{{ $review->id }}"
                                                                                class="flex w-full items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition
                                                                                        hover:bg-slate-50
                                                                                        peer-focus-visible:outline-none peer-focus-visible:ring-2 peer-focus-visible:ring-slate-300
                                                                                        peer-checked:border-emerald-300 peer-checked:ring-2 peer-checked:ring-emerald-200">
                                                                            <span class="inline-flex size-5 shrink-0 items-center justify-center rounded-full ring-1 ring-inset ring-emerald-300">
                                                                                <svg class="size-3 text-emerald-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.2 7.2a1 1 0 01-1.42 0l-3-3a1 1 0 111.42-1.42l2.29 2.29 6.49-6.49a1 1 0 011.42 0z" clip-rule="evenodd"/>
                                                                                </svg>
                                                                            </span>
                                                                            <span>
                                                                                <span class="block text-sm font-semibold text-emerald-800">Approve</span>
                                                                                <span class="mt-0.5 block text-xs text-slate-500">Approve the request</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                     
                                                                     
                                                                    <!-- Reject -->
                                                                    <div>
                                                                        <input id="response_status_rejected_{{ $review->id }}" 
                                                                            name="response_status_{{ $review->id }}" 
                                                                            type="radio" 
                                                                            value="rejected"
                                                                            wire:model.live="response_status" class="peer sr-only" />
                                                                        <label for="response_status_rejected_{{ $review->id }}"
                                                                                class="flex w-full items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition
                                                                                hover:bg-slate-50
                                                                                peer-focus-visible:outline-none peer-focus-visible:ring-2 peer-focus-visible:ring-slate-300
                                                                                peer-checked:border-rose-300 peer-checked:ring-2 peer-checked:ring-rose-200">
                                                                            <span class="inline-flex size-5 shrink-0 items-center justify-center rounded-full ring-1 ring-inset ring-rose-300">
                                                                                <!-- x icon -->
                                                                                <svg class="size-3 text-rose-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                                    <path fill-rule="evenodd" d="M6.22 6.22a.75.75 0 011.06 0L10 8.94l2.72-2.72a.75.75 0 111.06 1.06L11.06 10l2.72 2.72a.75.75 0 11-1.06 1.06L10 11.06l-2.72 2.72a.75.75 0 11-1.06-1.06L8.94 10 6.22 7.28a.75.75 0 010-1.06z" clip-rule="evenodd"/>
                                                                                </svg>
                                                                            </span>
                                                                            <span>
                                                                                <span class="block text-sm font-semibold text-rose-800">Reject</span>
                                                                                <span class="mt-0.5 block text-xs text-slate-500">Decline the request</span>
                                                                            </span>
                                                                        </label>
                                                                    </div>


                                                                </div>

                                                                @error('response_status')
                                                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                                                @enderror
        

                                                            </fieldset>



                                                             
                                                            <!-- Instructions -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-slate-700">Response Notes</label>
                                                                <textarea
                                                                    wire:model.live="response_notes"
                                                                    class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600 text-sm"
                                                                    rows="4"
                                                                    placeholder="Tell them what to check, e.g. verify attachments, confirm compliance, etc."
                                                                ></textarea>

                                                                @error('response_notes')
                                                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                                                @enderror
                                                            </div>

                                                            {{-- <!-- Options -->
                                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl border bg-slate-50 p-3">
                                                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                                                    <input
                                                                        type="checkbox"
                                                                        x-model="$wire.pre.block_my_review"
                                                                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-600"
                                                                    />
                                                                    Block my review until they finish
                                                                </label>

                                                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                                                    <input
                                                                        type="checkbox"
                                                                        x-model="$wire.pre.notify_all"
                                                                        class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-600"
                                                                    />
                                                                    Notify all reviewers by email
                                                                </label> 
                                                            </div>--}}
                                                        </div>

                                                        <!-- Footer -->
                                                        <div class="border-t px-5 py-3 flex justify-end gap-2 bg-slate-50">
                                                            <button
                                                                type="button"
                                                                @click="openResponse = false"
                                                                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                                                            >
                                                                Cancel
                                                            </button>

                                                            <button
                                                                type="button"
                                                                @click="$wire.set('review_id', {{ $review->id }}); $wire.submitResponse();"
                                                                wire:loading.attr="disabled"
                                                                class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 transition disabled:opacity-50"
                                                            >
                                                                <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor" stroke-width="1.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 018-8" />
                                                                </svg>
                                                                Send Response
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                             

                                        </div>
                                    </div>
                                </td>
                                <!-- ./ Actions -->


                                <!-- Reason -->
                                <td class="px-2 py-2 align-top">
                                    <div class="max-w-48 line-clamp-2 text-slate-800 text-xs">
                                        {{ $review->reason }}
                                    </div>
                                </td>
                                

                                
 
                                <!-- Status-->
                                <td class="px-2 py-2 align-top">
                                    <div class="flex items-center gap-3">

                                        @if($review->status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                                Rejected
                                            </span>
                                        @elseif($review->status === 'submitted')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                                                Submitted
                                            </span>
                                        @elseif($review->status === 'approved')
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                Approved
                                            </span>
                                        @endif

                                        
                                    </div>
                                </td>
                                <!-- ./ Status-->


                                  
                                <!-- Requested to -->
                                <td class="px-2 py-2 align-top">
                                    <div class="max-w-48 line-clamp-2 text-slate-800 text-xs">
                                        {{ !empty($review->project_reviewer_requested_to->user) ? $review->project_reviewer_requested_to->user->name : 'Requestor' }}
                                    </div>
                                </td>

                                 <!-- Requested to -->
                                <td class="px-2 py-2 align-top">
                                    <div class="max-w-48 line-clamp-2 text-slate-800 text-xs">
                                        {{ !empty($review->project_reviewer_requested_by->user) ? $review->project_reviewer_requested_by->user->name : 'Requested' }}
                                    </div>
                                </td>

                                <!-- Project  -->
                                <td class="px-2 py-2 align-top">
                                    <div class="max-w-48 line-clamp-2 text-slate-800 text-xs">
                                        {{ !empty($review->project) ? $review->project->name : 'Project' }}
                                    </div>
                                </td>

                                <!-- Project Document -->
                                <td class="px-2 py-2 align-top">
                                    <div class="max-w-48 line-clamp-2 text-slate-800 text-xs">
                                        {{ !empty($review->project->project_document->document_type) ? $review->project->project_document->document_type->name : 'Project Document' }}
                                    </div>
                                </td>




                                <!-- Created -->
                                <td class="px-2 py-2 align-top">
                                    <div class="max-w-48 line-clamp-2 text-slate-800 text-xs">
                                        {{ \Carbon\Carbon::parse($review->created_at)->format('d M, h:i A') }}
                                    </div>
                                </td>



                            </tr>
                        @endforeach
                    {{-- @elseif(empty($project) && empty($project_document))
                         <tr>
                            <th scope="col" colspan="3" class="px-6 py-6 text-start">
                                <div class="text-start">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                                    Please select a project to view reviews
                                    </span>
                                </div>
                            </th>
                        </tr> --}}
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



    <!--  Loaders -->
        {{-- wire:target="table"   --}}
        <div wire:loading 
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
        </div>

         

    <!-- ./  Loaders -->





</div>
<!-- End Table Section -->
