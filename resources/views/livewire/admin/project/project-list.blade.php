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

    <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/@preline/remove-element@2.6.0/index.min.js"></script>
    

    <!-- Card -->
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden  ">
            <!-- Header -->
            <div class=" min-w-full px-3 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 ">
                <div>
                    <h2 class="text-nowrap text-xl font-semibold text-gray-800  inline-flex items-center gap-x-2">
                        {{ $title }} <span class="shrink-0 flex justify-center items-center size-[30px] text-sm bg-black text-white rounded-full  ">{{ $projects_count ?? 0 }}</span>

                    </h2>
                    {{-- <p class="text-sm text-gray-600 ">
                        {{ $subtitle }}
                    </p> --}}
                    </div>

                    <div>
                    <div class="inline-flex gap-x-2">


                        <input type="text" wire:model.live="search"
                            class="text-nowrap min-w-32 py-2 px-3 inline-flex items-center gap-x-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                            placeholder="Search">


                        {{-- @if(  Auth::user()->hasRole('Admin')) --}}
                        <button
                            @click="showModal = true, initMap" type="button"
                            @keydown.window="handleKeydown" 
                            class="text-nowrap min-w-44 py-2 px-3 inline-flex items-center gap-x-2 border-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50    "
                            {{-- href="{{ route('schedule.index') }}"> --}}
                            >
                                    
                            Search by Location 
                            
                            <svg class="shrink-0 size-4"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#6b7280" d="M256 0c17.7 0 32 14.3 32 32l0 34.7C368.4 80.1 431.9 143.6 445.3 224l34.7 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-34.7 0C431.9 368.4 368.4 431.9 288 445.3l0 34.7c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-34.7C143.6 431.9 80.1 368.4 66.7 288L32 288c-17.7 0-32-14.3-32-32s14.3-32 32-32l34.7 0C80.1 143.6 143.6 80.1 224 66.7L224 32c0-17.7 14.3-32 32-32zM128 256a128 128 0 1 0 256 0 128 128 0 1 0 -256 0zm128-80a80 80 0 1 1 0 160 80 80 0 1 1 0-160z"/></svg>
                        </button>
                        {{-- @endif --}}


                        <div class="text-nowrap inline-flex items-center gap-x-2">

                            <select wire:model.live="review_status" class="min-w-36 py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                                <option value="">Review Status</option> 
                                
                                @foreach ($review_status_options as $key => $value )
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach

                            </select>
                        </div>

                        <div class="text-nowrap inline-flex items-center gap-x-2">

                            <select wire:model.live="project_status" class="min-w-36 py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                                <option value="">Project Status</option> 
                                
                                @foreach ($project_status_options as $key => $value )
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach

                            </select>
                        </div>

                        <div class="text-nowrap inline-flex items-center gap-x-2">

                            <select wire:model.live="type" class="min-w-32 py-2 px-3 pe-9 max-w-32 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                                <option value="">Project type</option>
                                @if(!empty($project_types))
                                    @foreach ($project_types as $type_id => $type_name )
                                        <option>{{ $type_name }}</option> 
                                    @endforeach 
                                @endif
                            </select>
                        </div>

                        <div class="text-nowrapinline-flex items-center gap-x-2">

                            <select wire:model.live="sort_by" class="min-w-32 py-2 px-3 pe-9 max-w-32 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                                <option value="">Sort By</option>
                                <option>Name A - Z</option>
                                <option>Name Z - A</option>
                                <option>Federal Agency A - Z</option>
                                <option>Federal Agency Z - A</option>
                                <option>Description A - Z</option>
                                <option>Description Z - A</option>
                                <option>Nearest Submission Due Date</option>
                                <option>Farthest Submission Due Date</option>
                                <option>Nearest Reviewer Due Date</option>
                                <option>Farthest Reviewer Due Date</option>
                                <option>Latest Added</option>
                                <option>Oldest Added</option>
                                <option>Latest Updated</option>
                                <option>Oldest Updated</option>
                            </select>
                        </div>

                        {{-- @if( Auth::user()->can('activity log delete') || Auth::user()->hasRole('DSI God Admin'))  --}}
                        @if(  Auth::user()->hasRole('DSI God Admin') || 
                        // Auth::user()->can('project delete')
                        Auth::user()->hasRole('User')
                        
                        )
                            <button
                                onclick="confirm('Are you sure, you want to delete this records?') || event.stopImmediatePropagation()"
                                wire:click.prevent="deleteSelected"
                                {{ $count == 0 ? 'disabled' : '' }}
                                class=" text-nowrap py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-red-500 text-white shadow-sm hover:bg-red-50 hover:text-red-600 hover:border-red-500 focus:outline-red-500 focus:text-red-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                Delete ({{ $count }})
                            </button>
                        @endif

                        <a href="{{ route('project.create') }}"
                            class="text-nowrap py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-yellow-500 text-white shadow-sm hover:bg-yellow-50 hover:text-yellow-600   hover:border-yellow-500 focus:outline-yellow-500 focus:text-yellow-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z"/></svg>
                        </a>

                        <a href="{{ route('project.index') }}"
                            class="text-nowrap py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-sky-500 text-white shadow-sm hover:bg-sky-50 hover:text-sky-600   hover:border-sky-500 focus:outline-sky-500 focus:text-sky-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                            {{-- <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z"/></svg> --}}
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M370.7 133.3C339.5 104 298.9 88 255.8 88c-77.5 .1-144.3 53.2-162.8 126.9-1.3 5.4-6.1 9.2-11.7 9.2H24.1c-7.5 0-13.2-6.8-11.8-14.2C33.9 94.9 134.8 8 256 8c66.4 0 126.8 26.1 171.3 68.7L463 41C478.1 25.9 504 36.6 504 57.9V192c0 13.3-10.7 24-24 24H345.9c-21.4 0-32.1-25.9-17-41l41.8-41.7zM32 296h134.1c21.4 0 32.1 25.9 17 41l-41.8 41.8c31.3 29.3 71.8 45.3 114.9 45.3 77.4-.1 144.3-53.1 162.8-126.8 1.3-5.4 6.1-9.2 11.7-9.2h57.3c7.5 0 13.2 6.8 11.8 14.2C478.1 417.1 377.2 504 256 504c-66.4 0-126.8-26.1-171.3-68.7L49 471C33.9 486.1 8 475.4 8 454.1V320c0-13.3 10.7-24 24-24z"/></svg>
                        </a>


                    </div>
                </div>

                


            </div>
            <!-- End Header -->


            @if(!empty($location) || !empty($latitude) ||  !empty($longitude) )
                <!-- Location -->
                <div class="px-3 py-2 flex-row md:flex items-center justify-between my-1 ">
                                
                            
                                
                    <div>
                        <p class="text-sm  ">
                            
                            <strong>Location:</strong> <span class="italic text-blue-500 font-bold">{!! $location !!}</span>

 

                        </p> 

                    </div>
                    <div>
                        <p class="text-sm  ">
                            <strong>Latitude:</strong> <span class="italic text-blue-500 font-bold">{!! $latitude !!}</span> 
                        </p> 

                    </div>

                    <div>
                        <p class="text-sm  ">
                            <strong>Longitude:</strong> <span class="italic text-blue-500 font-bold">{!! $longitude !!}</span> 
                        </p> 

                    </div>



                </div>
                <!-- End Location -->
            @endif



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
                            Name & Agency
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Location
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Description
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Review Status
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

                    <th scope="col" class="px-6 py-3 text-end"></th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 ">

                    @if(!empty($projects) && count($projects) > 0)
                        @foreach ($projects as $project)
                            <tr>
                                <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <label for="project_{{ $project->id }}" class="flex">
                                            <input type="checkbox"
                                            wire:model="selected_records"
                                            wire:change="updateSelectedCount"
                                            class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                                            id="project_{{ $project->id }}"
                                            value="{{ $project->id }}"
                                            >
                                            <span class="sr-only">Checkbox</span>
                                        </label>
                                    </div>
                                </td>


                                <td class="size-auto  text-wrap max-w-96">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                            <div class="grow">
                                                <span class="block text-sm text-black ">{{ $project->name }}</span>
                                                <span class="block text-sm text-gray-500 ">{{ $project->federal_agency }}</span>
                                                <strong>
                                                    <span class="block text-sm text-green-800 ">{{ $project->type }}</span>
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="size-auto text-wrap max-w-48 overflow-x-auto">
                                    <div class="px-2 py-2">
                                        @if(!empty($project->location))
                                         
                                            <span class="block text-sm text-gray-500  max-h-40 overflow-y-auto">
                                                Loc: <span class="italic text-blue-500 font-bold">{{ $project->location }}</span>
                                                <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 
                                                inline-flex items-center gap-x-2
                                                ">
                                                    
                                                    <div class="hs-tooltip inline-block" onclick="copyLocationToClipboard('{{  $project->location }}')">
                                                        <svg class="shrink-0 size-3 hover:bg-sky-500 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M384 336l-192 0c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l140.1 0L400 115.9 400 320c0 8.8-7.2 16-16 16zM192 384l192 0c35.3 0 64-28.7 64-64l0-204.1c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1L192 0c-35.3 0-64 28.7-64 64l0 256c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64L0 448c0 35.3 28.7 64 64 64l192 0c35.3 0 64-28.7 64-64l0-32-48 0 0 32c0 8.8-7.2 16-16 16L64 464c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l32 0 0-48-32 0z"/></svg>
                                                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-sky-900 w-44 text-wrap  text-xs normal-case font-medium text-white rounded shadow-sm " role="tooltip">
                                                            <div>
                                                                Click to copy location
                                                            </div>
                                                            <div  class="font-bold text-sky-50 mt-2 bg-black p-2">
                                                                NOTE: The copy functionality will only work if you have selected records
                                                            </div>
                                                        </span>
                                                    </div>


                                                </span>
                                            </span>
                                        

                                        @endif
                                        @if(!empty($project->latitude))
                                            <span class="block text-sm text-gray-500  max-h-40 overflow-y-auto">
                                                Lat: <span class="italic text-blue-500 font-bold">{{ $project->latitude }}</span>
                                            </span>
                                        @endif
                                        @if(!empty($project->longitude))
                                            <span class="block text-sm text-gray-500  max-h-40 overflow-y-auto">
                                                Lng: <span class="italic text-blue-500 font-bold">{{ $project->longitude }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="size-auto text-wrap max-w-96 ">
                                    <div class="px-2 py-2">
                                        <span class="block text-sm text-gray-500  max-h-40 overflow-y-auto">
                                            {{ $project->description }}
                                        </span>
                                           
                                    </div>
                                </td>

                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">

                                             
                                            <!--  While the project is still in review-->
                                            @if($project->status == "draft")
                                                <span class="block text-sm text-gray-800 ">
                                                    Draft
                                                </span>
                                            @elseif($project->status !== "approved")

                                                @php  
                                                    $reviewer = $project->getCurrentReviewer(); 
 
                                                    // $review_status = $project->getReview($reviewer->id)->review_status;

                                                @endphp 

                                                <span class="block text-sm text-gray-800 ">
                                                    Reviewer: {{ $reviewer->user->name ? $reviewer->user->name : '' }}
                                                </span>
                                                
                                                
                                                <span class="block text-sm text-gray-500 ">
                                                    Review Status: 
                                                    @if($reviewer->review_status == "approved")
                                                        <span class="font-bold text-lime-500">{{ ucfirst($reviewer->review_status) }} </span> 
                                                    @elseif($reviewer->review_status == "rejected")
                                                        <span class="font-bold text-red-500">{{ ucfirst($reviewer->review_status) }} </span> 

                                                    @else 
                                                        <span class="font-bold text-yellow-500">{{ ucfirst($reviewer->review_status) }} </span> 

                                                    @endif
                                                      
                                                </span>

                                                <hr>
                                                <span class="block text-sm text-gray-500   ">
                                                    @if($reviewer->review_status == "pending") <!-- if review status is pending, it means the review due date must be seen here -->
                                                        Expected <span class="font-bold text-blue-800 ">review</span> on or before <br>  
                                                        <strong>
                                                            {{ \Carbon\Carbon::parse($project->reviewer_due_date)->format('M d, Y h:i A') }}
                                                        </strong>
                                                    @else  <!-- if review status is NOT pending, it means the review had been sent and the user response due date must be seen here -->
                                                        Expected <span class="font-bold text-blue-800 ">user response</span> on or before <br>  
                                                        <strong>
                                                            {{ \Carbon\Carbon::parse($project->submitter_due_date)->format('M d, Y h:i A') }}
                                                        </strong>
                                                    @endif
                                                </span>

                                            @else 
                                                <span class="block text-sm text-gray-800 ">
                                                    <span class="font-bold text-lime-500">Approved</span>
                                                </span>
                                            @endif 
                                                
                                            


                                        </div>
                                        </div>
                                    </div>
                                </td>



                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">

                                            <span class="block text-sm text-gray-800 ">
                                                {!! $project->getStatus() !!}
                                            </span>


                                            <span class="block text-sm text-gray-500 ">
                                                Updated at 
                                                <br> <strong>{{ \Carbon\Carbon::parse($project->updated_at)->format('M d, Y') }}</strong> 
                                                <br> <strong>{{ \Carbon\Carbon::parse($project->updated_at)->format('h:i A') }}</strong>
                                                <br>Created by 
                                                <br> <strong>{{ $project->creator ? $project->creator->name : '' }}</strong>
                                                
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </td>



                                <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">


                                        @if(request()->routeIs('project.in_review'))
                                            @if(Auth::user()->hasRole('DSI God Admin') ||  Auth::user()->can('project review')  )
                                                @if($project->status !== "approved")
                                                    @if($project->isNextReviewer())
                                                    
                                                        <!-- show -->
                                                        <a 
                                                        title="View and review project"
                                                        href="{{ route('project.review',['project' => $project->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-lime-600 text-white hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none">
                                                            
                                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                                                        </a>

                                                    @else 
                                                        <span class="block text-sm text-gray-800 ">
                                                            Currently reviewed by 
                                                        </span>
                                                        <span class="block text-sm text-gray-800 ">
                                                            {{ $project->getCurrentReviewerUser()->name }}
                                                        </span>
                                                    @endif
                                                @else 
                                                    <a 
                                                    title="Project Approved"
                                                    href="{{ route('project.review',['project' => $project->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-lime-600 text-white hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none">
                                                        
                                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                                                    </a>
                                                @endif
                                            @endif
                                        @else
                                            @if(Auth::user()->hasRole('DSI God Admin') ||  Auth::user()->can('reviewer list view')  )
                                        
                                            {{-- @if( Auth::user()->id == $project->created_by || Auth::user()->hasRole('Admin')  ) --}}
                                                <!-- show -->
                                                <a title="Show project reviewers"
                                                href="{{ route('project.reviewer.index',['project' => $project->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                                                    
                                                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M96 128a128 128 0 1 0 256 0A128 128 0 1 0 96 128zm94.5 200.2l18.6 31L175.8 483.1l-36-146.9c-2-8.1-9.8-13.4-17.9-11.3C51.9 342.4 0 405.8 0 481.3c0 17 13.8 30.7 30.7 30.7l131.7 0c0 0 0 0 .1 0l5.5 0 112 0 5.5 0c0 0 0 0 .1 0l131.7 0c17 0 30.7-13.8 30.7-30.7c0-75.5-51.9-138.9-121.9-156.4c-8.1-2-15.9 3.3-17.9 11.3l-36 146.9L238.9 359.2l18.6-31c6.4-10.7-1.3-24.2-13.7-24.2L224 304l-19.7 0c-12.4 0-20.1 13.6-13.7 24.2z"/></svg>
                                                </a>
                                            {{-- @endif  --}}
                                            @endif

                                            @if(Auth::user()->can('project submit'))
                                                <!-- submit -->
                                                @if( Auth::user()->id == $project->created_by  )
                                                    <button title="Submit project "
            
                                                        {{ $project->allow_project_submission == true ? '' : 'disabled' }}
                                                        onclick="confirm('Are you sure, you want to submit this project?') || event.stopImmediatePropagation()"
                                                        wire:click.prevent="submit_project({{ $project->id }})"
                                                        type="button" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                                                            
                                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M498.1 5.6c10.1 7 15.4 19.1 13.5 31.2l-64 416c-1.5 9.7-7.4 18.2-16 23s-18.9 5.4-28 1.6L284 427.7l-68.5 74.1c-8.9 9.7-22.9 12.9-35.2 8.1S160 493.2 160 480l0-83.6c0-4 1.5-7.8 4.2-10.8L331.8 202.8c5.8-6.3 5.6-16-.4-22s-15.7-6.4-22-.7L106 360.8 17.7 316.6C7.1 311.3 .3 300.7 0 288.9s5.9-22.8 16.1-28.7l448-256c10.7-6.1 23.9-5.5 34 1.4z"/></svg>
                                                        
                                                    </button>
                                                @endif
                                            @endif

                                            @if(Auth::user()->can('project view'))
                                            <!-- show -->
                                            <a title="Show project details and reviews"
                                            href="{{ route('project.show',['project' => $project->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-lime-600 text-white hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none">
                                                 

                                                {{-- <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48L48 64zM0 176L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-208L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>

                                                @if( Auth::user()->id == $project->created_by  )
                                                    @php 
                                                        $unseen_emails = 0;

                                                        $unseen_emails = \App\Models\Review::countUnseenEmails($project->id);
                                                    @endphp
                                                    <span class="inline-flex items-center py-0.5 px-1.5 rounded-full text-xs bg-white text-black font-bold"
                                                    title="{{ $unseen_emails ? $unseen_emails.' unseen emails' : 'Mail is empty' }}"
                                                    >{{ $unseen_emails ? $unseen_emails : 0 }}</span>
                                                @endif --}}

                                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M256 8C119 8 8 119.1 8 256c0 137 111 248 248 248s248-111 248-248C504 119.1 393 8 256 8zm0 110c23.2 0 42 18.8 42 42s-18.8 42-42 42-42-18.8-42-42 18.8-42 42-42zm56 254c0 6.6-5.4 12-12 12h-88c-6.6 0-12-5.4-12-12v-24c0-6.6 5.4-12 12-12h12v-64h-12c-6.6 0-12-5.4-12-12v-24c0-6.6 5.4-12 12-12h64c6.6 0 12 5.4 12 12v100h12c6.6 0 12 5.4 12 12v24z"/></svg>


                                            </a>
                                            @endif

                                            <hr class="my-2">
                                            @if(Auth::user()->can('project edit'))
                                                @if($project->allow_project_submission == true  )
                                                    <!-- edit -->
                                                    <a title="Edit project details " 
                                                    href="{{ route('project.edit',['project' => $project->id]) }}" 
                                                        class=" py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"/></svg>
                                                    </a>
                                                @else 
                                                    <button disabled class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"/></svg>
                                                    </button>
                                                @endif
                                            @endif
                                            <!-- delete -->
                                            {{-- @if( Auth::role()->can('role delete')  ||  Auth::role()->hasRole('DSI God Admin')) --}}

                                            @if(($project->status == "draft" && Auth::user()->id == $project->created_by )|| Auth::user()->can('project delete'))
                                                <button title="Delete project "
                                                onclick="confirm('Are you sure, you want to delete this record?') || event.stopImmediatePropagation()"
                                                wire:click.prevent="delete({{ $project->id }})"
                                                type="button" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.7 23.7 0 0 0 -21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0 -16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/></svg>
                                                </button>
                                            @endif
                                            {{-- @endif --}}

                                            <!-- override project approval -->
                                            @if(  Auth::user()->hasRole('DSI God Admin') && ($project->status == "approved" || $project->status == "submitted" || $project->status == "in_review" ) )
                                                @if($project->status == "approved") <!-- if project is in_review, show the rejection button for override-->
                                                    
                                                    @if(  Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('project restart override') )
                                                    <hr class="my-2">
                                                    <button  title="Project review restart override"
                                                        onclick="confirm('Are you sure you want to review again this project? This action will override all project reviewers and instantly reject both the project and all its reviews. This will restart the review process.') || event.stopImmediatePropagation()"
                                                        wire:click.prevent="restart_review_project({{ $project->id }})"
                                                        type="button" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-gray-600 bg-white text-gray-600 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none"> 
                                                        Restart
                                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M105.1 202.6c7.7-21.8 20.2-42.3 37.8-59.8c62.5-62.5 163.8-62.5 226.3 0L386.3 160 352 160c-17.7 0-32 14.3-32 32s14.3 32 32 32l111.5 0c0 0 0 0 0 0l.4 0c17.7 0 32-14.3 32-32l0-112c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 35.2L414.4 97.6c-87.5-87.5-229.3-87.5-316.8 0C73.2 122 55.6 150.7 44.8 181.4c-5.9 16.7 2.9 34.9 19.5 40.8s34.9-2.9 40.8-19.5zM39 289.3c-5 1.5-9.8 4.2-13.7 8.2c-4 4-6.7 8.8-8.1 14c-.3 1.2-.6 2.5-.8 3.8c-.3 1.7-.4 3.4-.4 5.1L16 432c0 17.7 14.3 32 32 32s32-14.3 32-32l0-35.1 17.6 17.5c0 0 0 0 0 0c87.5 87.4 229.3 87.4 316.7 0c24.4-24.4 42.1-53.1 52.9-83.8c5.9-16.7-2.9-34.9-19.5-40.8s-34.9 2.9-40.8 19.5c-7.7 21.8-20.2 42.3-37.8 59.8c-62.5 62.5-163.8 62.5-226.3 0l-.1-.1L125.6 352l34.4 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L48.4 288c-1.6 0-3.2 .1-4.8 .3s-3.1 .5-4.6 1z"/></svg>
                                                    </button>   
                                                    @endif


                                                @elseif($project->status == "submitted" || $project->status == "in_review")  <!-- if project is rejected, show the rejection button for override-->
                                                    
                                                    @if(  Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('project approval override ') )
                                                        <hr class="my-2">
                                                        <button  title="Project approval override"
                                                            onclick="confirm('Are you sure you want to approve this project? This action will override all project reviewers and instantly approve both the project and all its reviews.') || event.stopImmediatePropagation()"
                                                            wire:click.prevent="approve_project({{ $project->id }})"
                                                            type="button" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-gray-600 bg-white text-gray-600 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none"> 
                                                            Approve
                                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M313.4 32.9c26 5.2 42.9 30.5 37.7 56.5l-2.3 11.4c-5.3 26.7-15.1 52.1-28.8 75.2l144 0c26.5 0 48 21.5 48 48c0 18.5-10.5 34.6-25.9 42.6C497 275.4 504 288.9 504 304c0 23.4-16.8 42.9-38.9 47.1c4.4 7.3 6.9 15.8 6.9 24.9c0 21.3-13.9 39.4-33.1 45.6c.7 3.3 1.1 6.8 1.1 10.4c0 26.5-21.5 48-48 48l-97.5 0c-19 0-37.5-5.6-53.3-16.1l-38.5-25.7C176 420.4 160 390.4 160 358.3l0-38.3 0-48 0-24.9c0-29.2 13.3-56.7 36-75l7.4-5.9c26.5-21.2 44.6-51 51.2-84.2l2.3-11.4c5.2-26 30.5-42.9 56.5-37.7zM32 192l64 0c17.7 0 32 14.3 32 32l0 224c0 17.7-14.3 32-32 32l-64 0c-17.7 0-32-14.3-32-32L0 224c0-17.7 14.3-32 32-32z"/></svg>
                                                        </button>
                                                    @endif
                                                @endif
                                            @endif

                                        @endif <!-- ./ -->

                                        

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
                {{ $projects->links() }}

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
                        {{ count($projects) > 0 ? 'of '.$projects->total()  : '' }}
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
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-7xl">
                        <div 
                        {{-- @click.outside="showModal = false"  --}}
                        class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="w-full px-1 pt-1" x-data="{
                                searchPosts(event) {
                                    document.getElementById('searchInput').focus();
                                    event.preventDefault();
                                }
                            }">
                                {{-- <form action="" autocomplete="off">
                                    <input
                                    autocomplete="off"
                                    wire:model.live.throttle.500ms="location_search" type="text" id="searchInput"
                                    name="searchInput"
                                    class="block w-full flex-1 py-2 px-3 mt-2 outline-none border-none rounded-md bg-slate-100"
                                    placeholder="Search for project name ..." @keydown.slash.window="searchPosts" />
                                </form> --}}
                                <div class="mt-2 w-full overflow-hidden rounded-md bg-white">

                                     
                                    <div class=" py-2 px-3 border-b border-slate-200 text-sm font-medium text-slate-500">

                                        Search map <strong>(Click to OK 
                                            {{-- or out of the box  --}}
                                            to close dialog
                                            )</strong>

                                    </div>

                                    
                                    <div class="  py-2 px-3 hover:bg-slate-100 bg-white border border-gray-200 shadow-sm rounded-xl mb-1"
                                    {{-- wire:click="saerch_project('{{  $result->id }}')"
                                    @click="showModal = false" --}}
                                    >
                                         

                                        <div>
                                            <input
                                            autofocus autocomplete="location"
                                            wire:model.live="location"
                                            placeholder="Search location"
                                            id="search-box" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        
                                            <input type="hidden" id="latitude" wire:model.live="latitude">
                                            <input type="hidden" id="longitude" wire:model.live="longitude">
                                        </div>


                                        <div class="max-w-full text-wrap ">
                                            <div>
                            
                                                <div id="map" style="height: 500px; width: 100%;" wire:ignore></div>
                                            {{-- <button wire:click="saveLocation">Save Location</button> --}}
                                            </div>
                    
                    
                                            <div>
                                                @error('location')
                                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                                @enderror
                    
                                                @error('latitude')
                                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                                @enderror
                    
                                                @error('longitude')
                                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                                @enderror
                                            </div>





                                        </div>

                                            
                                        <button  type="button"
                                            @click="showModal = false" type="button"
                                            @keydown.window="handleKeydown"
                                            class="mt-1 py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                            OK
                                        </button> 

                                         
                                    </div>
                                             

                                         

    

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endteleport
    <!-- ./ Schedule modal-->




    <script>
        let map, marker, searchBox;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 13.4443, lng: 144.7937 }, // Centered on Guam
                zoom: 11,
                // mapTypeId: google.maps.MapTypeId.SATELLITE // ✅ Default to Satellite view
            });

            marker = new google.maps.Marker({
                position: map.getCenter(),
                map: map,
                draggable: true
            });

            const input = document.getElementById("search-box");
            searchBox = new google.maps.places.SearchBox(input);
            // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // searchBox.addListener("places_changed", function () {
            //     let places = searchBox.getPlaces();
            //     if (places.length == 0) return;

            //     let place = places[0];
            //     map.setCenter(place.geometry.location);
            //     marker.setPosition(place.geometry.location);


            //     @this.set('latitude', place.geometry.location.lat());
            //     @this.set('longitude', place.geometry.location.lng());
            //     @this.set('location', place.name);

                 

            // });

            searchBox.addListener("places_changed", function () {
                let places = searchBox.getPlaces();
                if (places.length == 0) return;

                let place = places[0];
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);

                let fullAddress = place.formatted_address || place.name; // Use full address if available

                @this.set('latitude', place.geometry.location.lat());
                @this.set('longitude', place.geometry.location.lng());
                @this.set('location', fullAddress); // ✅ Use full address instead of just name

                console.log("Updated Location:", fullAddress);
            });


            const geocoder = new google.maps.Geocoder();

            marker.addListener("dragend", function () {
                let lat = marker.getPosition().lat();
                let lng = marker.getPosition().lng();

                // Reverse geocode to get location name
                geocoder.geocode({ location: { lat, lng } }, function (results, status) {
                    if (status === "OK" && results[0]) {
                        let locationName = results[0].formatted_address;

                        // Send data to Livewire
                        @this.set('latitude', lat);
                        @this.set('longitude', lng);
                        @this.set('location', locationName);

                        console.log("Updated Location:", locationName);
                    } else {
                        console.error("Geocoder failed: " + status);
                    }
                });
            });
        }

        window.onload = initMap;

         
    </script>


    <script>
        function copyLocationToClipboard(textToCopy) {
            // Create a temporary textarea to hold the text
            let tempInput = document.createElement("textarea");
            tempInput.value = textToCopy;
            document.body.appendChild(tempInput);

            // Select and copy the text
            tempInput.select();
            document.execCommand("copy");

            // Remove the temporary input
            document.body.removeChild(tempInput);

            // Provide user feedback (optional)
            alert("Copied to clipboard: " + textToCopy);
        }
    </script>

</div>
<!-- End Table Section -->
