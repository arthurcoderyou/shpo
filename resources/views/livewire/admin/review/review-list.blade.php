<?php
    function isImageMimeForReview($filename) {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $imageExtensions);
    }
?>


<!-- Table Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto"

    x-data="{
        showModal: false,  
        handleKeydown(event) {
            if (event.keyCode == 191) {
                this.showModal = true; 
            }
            if (event.keyCode == 27) { 
                this.showModal = false; 
                $wire.search = '';
            }

        },
        saerch_project() {
            this.showModal = false;
            {{-- $wire.search = '';  --}}
        }
    }"
>

    <script src="https://cdn.jsdelivr.net/npm/@preline/remove-element@2.6.0/index.min.js"></script>
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


                    @if( 
                       request()->routeIs('review.index'))
                    <button
                        @click="showModal = true" type="button"
                        @keydown.window="handleKeydown" 
                        class="py-2 px-3 inline-flex min-w-12 items-center gap-x-2 border-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50   "
                        {{-- href="{{ route('schedule.index') }}"> --}}
                        >
                         
                        {{ !empty($project->name) ? $project->name : "Search Project" }} 

                        <svg class="shrink-0 size-[.8em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                    </button>
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

                    {{-- @if( Auth::user()->can('activity log delete') || Auth::user()->hasRole('DSI God Admin')) 
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
                                href="{{ route('review.index') }}"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-orange-500 text-white shadow-sm hover:bg-orange-900 hover:text-orange-600 hover:border-orange-500 focus:outline-orange-500 focus:text-orange-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M370.7 133.3C339.5 104 298.9 88 255.8 88c-77.5 .1-144.3 53.2-162.8 126.9-1.3 5.4-6.1 9.2-11.7 9.2H24.1c-7.5 0-13.2-6.8-11.8-14.2C33.9 94.9 134.8 8 256 8c66.4 0 126.8 26.1 171.3 68.7L463 41C478.1 25.9 504 36.6 504 57.9V192c0 13.3-10.7 24-24 24H345.9c-21.4 0-32.1-25.9-17-41l41.8-41.7zM32 296h134.1c21.4 0 32.1 25.9 17 41l-41.8 41.8c31.3 29.3 71.8 45.3 114.9 45.3 77.4-.1 144.3-53.1 162.8-126.8 1.3-5.4 6.1-9.2 11.7-9.2h57.3c7.5 0 13.2 6.8 11.8 14.2C478.1 417.1 377.2 504 256 504c-66.4 0-126.8-26.1-171.3-68.7L49 471C33.9 486.1 8 475.4 8 454.1V320c0-13.3 10.7-24 24-24z"/></svg>
                            </a> 
                        @endif
                            
                    {{-- @if( !empty($project_id)  )  --}}
                        <button
                             
                            wire:click="generatePdf"
                            {{ count($selected_records) == 0 ? 'disabled' : '' }}
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-blue-500 text-white shadow-sm hover:bg-blue-50 hover:text-blue-600 hover:border-blue-500 focus:outline-blue-500 focus:text-blue-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                             
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

 

                                <td class="size-auto text-wrap max-w-full ">
                                    <div class="px-2 py-2">

                                        <!-- Check if it is an admin review-->
                                        @if($review->admin_review == true)

                                            <!-- Profile -->
                                            <div class="flex items-center gap-x-3">
                                                    
                                                
                                                <div class="grow">
                                                    <h1 class="text-lg font-medium text-black ">
                                                        {{ $review->project_review }}
                                                    </h1>
                                                    <p class="text-sm text-gray-600 ">
                                                        Project Status : {!! $review->getStatus() !!}
                                                        
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        Project updated 
                                                        at {{ \Carbon\Carbon::parse($review->created_at)->format('d M, h:i A') }}  
                                                    </p>
                                                </div>
                                            </div>
                                            <!-- End Profile -->


                                           

                                            {{-- <!-- About -->
                                            <div class="mt-2">

                                                <p class="text-sm text-gray-600 ">
                                                    {{ $review->project_review }}
                                                </p>
                                            </div> --}}

                                            



                                            @if($review->project->status !== "approved" && $review->review_status == "approved" && !empty( $next_reviewer->user ) && !empty(  $review->project ))
                                            <!-- Profile -->
                                            <div class="  p-2  ">
                                                
                                                
                                                <div class="grow">
                                                    <h1 class="text-lg font-medium text-gray-800 ">
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

                                            
                                        
                                        <!-- if it is a reviewer review , show review details -->
                                        @elseif(!$review->isSubmitterReview() && $review->review_status !== "submitted") 
                                            <!-- Profile -->
                                            <div class="flex items-center gap-x-3">
                                                
                                            
                                                <div class="grow">
                                                    <h1 class="text-lg font-medium text-gray-800 ">
                                                        Reviewed by {{ $review->reviewer->name }}
                                                    </h1>
                                                    <p class="text-sm text-gray-600 ">
                                                        Project Status : {!! $review->getStatus() !!}
                                                        
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        Review 
                                                        @if($review->review_status == "rejected")
                                                            <span class="font-bold text-red-500">Rejected</span>
                                                        @elseif ($review->review_status == "approved")
                                                            <span class="font-bold text-lime-500">Approved</span>
                                                        @endif
                                                        at {{ \Carbon\Carbon::parse($review->created_at)->format('d M, h:i A') }} by {{ $review->creator ? $review->creator->name : '' }}
                                                    </p>

                                                   

                                                </div>
                                            </div>
                                            <!-- End Profile -->
                                            
                                            <!-- About -->
                                            <div class="mt-2">

                                                <p class="text-sm text-gray-600 ">
                                                    Reviewer Notes: 
                                                </p>
                                                <p class="text-sm text-gray-600 ">
                                                    {{ $review->project_review }}
                                                </p>
                                            

                                                <div>


                                                    @php

                                                        if (!empty($review->attachments)) {
                                                            $existingFiles = $review->attachments
                                                                ->sortByDesc('created_at') // Ensure newest files appear first
                                                                ->groupBy(function ($attachment) {
                                                                    return $attachment->created_at->format('M d, Y h:i A'); // Group by date
                                                                })
                                                                ->map(function ($attachments) {
                                                                    return $attachments->map(function ($attachment) {
                                                                        return [
                                                                            'id' => $attachment->id,
                                                                            'name' => basename($attachment->attachment), // File name
                                                                            'path' => asset('storage/uploads/review_attachments/' . $attachment->attachment), // Public URL
                                                                        ];
                                                                    })->toArray();
                                                                })->toArray();
                                                        }
                                                        

                                                        
                                                    @endphp

                                                    @if(isset($existingFiles) && count($existingFiles) > 0)
                                                        <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                            Attachments
                                                        </label>

                                                        @php($index = 1)
                                                        @foreach($existingFiles as $date => $attachments)
                                                            

                                                            <div class="hs-accordion-group">

                                                                @if( $index == 1)
                                                                    <div class="hs-accordion {{ $loop->first ? 'active' : '' }}" id="attachment-{{ $date }}">
                                                                        <button type="button" class="hs-accordion-toggle hs-accordion-active:text-blue-600 py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none " aria-expanded="true" aria-controls="hs-basic-collapse-one ">
                                                                            <svg class="hs-accordion-active:hidden block size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path d="M5 12h14"></path>
                                                                            <path d="M12 5v14"></path>
                                                                            </svg>
                                                                            <svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path d="M5 12h14"></path>
                                                                            </svg>
                                                                            {{ $date }}
                                                                        </button>


                                                                        <div id="hs-basic-collapse-one" class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="attachment-{{ $date }}">
                                                                        

                                                                            <div class="dz-flex dz-flex-wrap dz-gap-x-10 dz-gap-y-2 dz-justify-start dz-w-full  ">
                                                                                @foreach($attachments as $file)
                                                                                    <div class="dz-flex dz-items-center dz-justify-between dz-gap-2 dz-border dz-rounded dz-border-gray-200 dz-w-full dz-h-auto dz-overflow-hidden ">
                                                                                        <div class="dz-flex dz-items-center dz-gap-3">
                                                                                            @if(isImageMimeForReview($file['name']))
                                                                                                <div class="dz-flex-none dz-w-14 dz-h-14">
                                                                                                    <img src="{{ $file['path'] }}" class="dz-object-fill dz-w-full dz-h-full" alt="{{ $file['name'] }}">
                                                                                                </div>
                                                                                            @else
                                                                                                <div class="dz-flex dz-justify-center dz-items-center dz-w-14 dz-h-14 dz-bg-gray-100 ">
                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="dz-w-8 dz-h-8 dz-text-gray-500">
                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                                                                    </svg>
                                                                                                </div>
                                                                                            @endif
                                                                                            <div class="dz-flex dz-flex-col dz-items-start dz-gap-1">
                                                                                                <div class="dz-text-center dz-text-slate-900 dz-text-sm dz-font-medium ">{{ $file['name'] }}</div>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="dz-flex dz-items-center dz-mr-3">
                                                                                            <a href="{{ $file['path'] }}" download="{{ $file['path'] }}"
                                                                                            class="inline"
                                                                                            >   
                                                                                                

                                                                                                <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>
                                                                                            </a>
                                                                                        </div>


                                                                                        @if(Auth::user()->hasRole('Admin'))
                                                                                            <div class="dz-flex dz-items-center dz-mr-3">
                                                                                                <button type="button" 
                                                    
                                                                                                onclick="confirm('Are you sure, you want to remove this attachment?') || event.stopImmediatePropagation()"
                                                                                                wire:click.prevent="removeUploadedAttachment({{ $file['id'] }})"
                                                    
                                                                                                
                                                                                                >   
                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="dz-w-6 dz-h-6 dz-text-black ">
                                                                                                        <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                                                                                    </svg>
                                                                                                    
                                                                                                </button>
                                                                                            </div>
                                                                                        @endif



                                                                                    </div>
                                                                                @endforeach
                                                                            </div>



                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    
                                                                    <div class="hs-accordion" id="attachment-{{ $index }}">
                                                                        <button type="button" class="hs-accordion-toggle hs-accordion-active:text-blue-600 py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none " aria-expanded="false" aria-controls="hs-basic-collapse-{{ $index }}">
                                                                            <svg class="hs-accordion-active:hidden block size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                                <path d="M5 12h14"></path>
                                                                                <path d="M12 5v14"></path>
                                                                            </svg>
                                                                            <svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                                <path d="M5 12h14"></path>
                                                                            </svg>
                                                                            {{ $date }}
                                                                        </button>
                                                                        <div id="hs-basic-collapse-{{ $index }}" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="attachment-{{ $index }}">
                                                                            

                                                                            <div class="dz-flex dz-flex-wrap dz-gap-x-10 dz-gap-y-2 dz-justify-start dz-w-full  ">
                                                                                @foreach($attachments as $file)
                                                                                    <div class="dz-flex dz-items-center dz-justify-between dz-gap-2 dz-border dz-rounded dz-border-gray-200 dz-w-full dz-h-auto dz-overflow-hidden ">
                                                                                        <div class="dz-flex dz-items-center dz-gap-3">
                                                                                            @if(isImageMimeForReview($file['name']))
                                                                                                <div class="dz-flex-none dz-w-14 dz-h-14">
                                                                                                    <img src="{{ $file['path'] }}" class="dz-object-fill dz-w-full dz-h-full" alt="{{ $file['name'] }}">
                                                                                                </div>
                                                                                            @else
                                                                                                <div class="dz-flex dz-justify-center dz-items-center dz-w-14 dz-h-14 dz-bg-gray-100 ">
                                                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="dz-w-8 dz-h-8 dz-text-gray-500">
                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                                                                    </svg>
                                                                                                </div>
                                                                                            @endif
                                                                                            <div class="dz-flex dz-flex-col dz-items-start dz-gap-1">
                                                                                                <div class="dz-text-center dz-text-slate-900 dz-text-sm dz-font-medium">{{ $file['name'] }}</div>
                                                                                                </div>
                                                                                        </div>
                                                                                        <div class="dz-flex dz-items-center dz-mr-3">
                                                                                            <a href="{{ $file['path'] }}" download="{{ $file['path'] }}"
                                                                                            class="inline"
                                                                                            >   
                                                                                                
                                                
                                                                                                <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>
                                                                                            </a>
                                                                                        </div>

                                                                                        


                                                                                    </div>
                                                                                @endforeach
                                                                            </div>



                                                                        </div>
                                                                    </div>
                                                                @endif

                                                                
                                                            
                                                                
                                                            </div>

                                                            @php($index++)
                                                        @endforeach

                                                    @endif


                                                </div>


                                            
                                                <ul class="mt-2 flex flex-col gap-y-3">
                                                    <li class="flex items-center gap-x-2.5">
                                                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                                        <a class="text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2  " href="mailto:{{ $review->reviewer->email }}">
                                                            {{ $review->reviewer->email }}
                                                        </a>
                                                    </li>
                                                
                                                
                                                </ul>
                                                

                                                 
                                                @if($review->project->status !== "approved" && $review->review_status == "approved" && !empty( $next_reviewer->user ) && !empty(  $review->project ) )
                                                <!-- Profile -->
                                                <div class="  p-2  ">
                                                    
                                                    
                                                    <div class="grow">
                                                        <h1 class="text-lg font-medium text-gray-800 ">
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


                                            </div>
                                            <!-- End About -->
                                            

 


                                        <!-- if it is a submitter review , show submitted details -->
                                        @else 


                                            <!-- Profile -->
                                            <div class="flex items-center gap-x-3">
                                                
                                            
                                                <div class="grow">
                                                    <h1 class="text-lg font-medium text-blue-800 ">
                                                        @if($review->review_status == "submitted")
                                                            Submitted
                                                        @elseif($review->review_status == "re_submitted")
                                                            Re-submitted
                                                        @endif 
                                                        by {{ $review->reviewer->name }}
                                                    </h1>
                                                    <p class="text-sm text-gray-600 ">
                                                        Project Status : {!! $review->project->getStatus() !!}
                                                        
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        Project <span class="font-bold text-yellow-500">Submitted</span> 
                                                        at {{ \Carbon\Carbon::parse($review->created_at)->format('d M, h:i A') }}  
                                                    </p>
                                                </div>
                                            </div>
                                            <!-- End Profile -->

                                            <!-- About -->
                                            <div class="mt-2">
 
                                                <p class="text-sm text-gray-600 ">
                                                    {{ $review->project_review }}
                                                </p>
                                            </div>


                                        @endif

                                    </div>
                                </td>



                                


                                <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        {{-- <!-- show -->
                                        <a href="{{ route('project.show',['project' => $review->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-lime-600 text-white hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none">
                                             
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                                        </a>


                                        <!-- edit -->
                                        <a href="{{ route('project.edit',['project' => $review->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"/></svg>
                                        </a> --}}

                                        <!-- delete -->

                                        {{-- @if( Auth::role()->can('role delete')  ||  Auth::role()->hasRole('DSI God Admin')) --}}

                                        @if($review->project->creator->id == Auth::user()->id )
                                            @if($review->viewed == false)
                                                <button
                                                onclick="confirm('Mark this as viewed?') || event.stopImmediatePropagation()" title="Mark as Viewed"
                                                wire:click.prevent="mark_as_viewed({{ $review->id }})"
                                                type="button" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-lime-600 text-white hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none">
                                                    
                                                    Mark as Viewed
                                                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                                                </button>
                                            @else   
                                                <span class="text-sm text-gray-600 ">
                                                    Viewed
                                                </span>
                                            @endif

                                        @elseif(!Auth::user()->hasRole('User'))
                                            @if($review->viewed == false)
                                                <span class="text-sm text-gray-600 ">
                                                    Not Viewed by {{ $review->project->creator->name }}
                                                </span>
                                            @else   
                                                <span class="text-sm text-gray-600 ">
                                                    Viewed at 
                                                    <strong>{{ \Carbon\Carbon::parse($review->updated_at)->format('M d, Y h:i A') }}</strong> 
                                                    by 
                                                    <strong>{{ $review->project->creator->name }}</strong> 
                                                </span>
                                            @endif
                                        @endif


                                        {{-- @endif --}}



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




     <!-- Schedule modal-->
    @teleport('body')
        <div x-show="showModal" x-trap="showModal" class="relative z-10 " aria-labelledby="modal-title" role="dialog"
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
                                                wire:click="saerch_project('{{  $result->id }}')"
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
    <!-- ./ Schedule modal-->









</div>
<!-- End Table Section -->
