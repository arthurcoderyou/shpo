 
<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto grid grid-cols-12 gap-x-2">
    {{-- The whole world belongs to you. --}}
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA70BOfcc1ELmwAEmY-rFNkbNauIXT79cA&libraries=places"></script>
    
    <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class=" col-span-12  "  >
        <!-- Card -->
        <div class="bg-gray-50 rounded-xl shadow ">
            <div class="p-4">

                <!-- Project Details Section -->
                <div class="bg-white rounded-2xl shadow p-6 mb-6 space-y-6">
                    
                    {{-- <!-- Highlight: Editing Project Work Plan Document -->
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded-md shadow-sm">
                        <p class="text-sm font-semibold text-yellow-800 tracking-wide">
                            ⚠️ You are currently editing the <span class="underline">Project Work Plan Document</span>.
                        </p>
                    </div> --}}

                    <div class="flex justify-between items-start flex-wrap gap-6">

                        <!-- Left: Main Info -->
                        <div class="space-y-4">
                            <h1 class="text-2xl font-semibold text-sky-800">
                                <span class="text-gray-500">Project Document:</span> {{ $project_document->document_type->name }} 
                            </h1>

                            @if(Auth::user()->hasRole('Reviewer') && $project->status !== "draft")
                                <a href="{{ route('project.review',['project' => $project->id ]) }}"
                                    wire:navigate
                                class="inline-block text-sm text-white bg-sky-600 hover:bg-sky-700 font-medium px-4 py-2 rounded shadow transition">
                                    Click to Review
                                </a>
                            @endif

                            <h2 class="text-xl font-bold text-gray-800">
                                <a class="hover:text-sky-500" href="{{ route('project.show',['project' => $project->id]) }}"
                                    wire:navigate
                                    >
                                    <span class="text-gray-500">Project:</span> {{ $project->name }}
                                </a>
                            </h2>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('project.show',['project' => $project->id]) }}"
                                    wire:navigate
                                class="px-3 py-1.5 text-sm font-medium rounded-lg bg-sky-500 text-white hover:bg-sky-700 transition">
                                    View Project
                                </a>

                                <a href="{{ route('project.edit',['project' => $project->id]) }}"
                                    wire:navigate
                                class="px-3 py-1.5 text-sm font-medium rounded-lg bg-blue-500 text-white hover:bg-blue-700 transition">
                                    Edit Project
                                </a>

                                @if($project->status !== "draft")
                                    <a href="#discussion"
                                    class="px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-500 text-gray-600 hover:border-gray-400 hover:text-gray-500 transition">
                                        Discussion
                                    </a>
                                @endif

                                <a href="#project_logs"
                                class="px-3 py-1.5 text-sm font-medium rounded-lg border border-teal-500 text-teal-600 hover:border-teal-400 hover:text-teal-500 transition">
                                    Logs
                                </a>
                            </div>

                            <!-- Metadata -->
                            <p class="text-sm text-gray-600">{{ $project->federal_agency }}</p>
                            <p class="text-sm text-blue-600 font-medium">{{ $project->type }}</p>

                            @unless(Auth::user()->hasRole('User'))
                                <div class="space-y-1">
                                    <p class="text-sm text-green-600 font-medium">
                                        Project #: {{ $project->project_number ?? 'NOT SET' }}
                                    </p>
                                    <p class="text-sm text-yellow-600 font-medium">
                                        SHPO #: {{ $project->shpo_number ?? 'NOT SET' }}
                                    </p>
                                </div>
                            @endunless
                        </div>

                        <!-- Right: Status Info -->
                        <div class="space-y-4 text-sm text-gray-700">
                            <div>
                                <p><strong>Project Status:</strong> {!! $project->getStatus() !!}</p>

                                @if($project->status !== "approved" && !empty($project->project_reviewer))
                                    <p><strong>Review Status:</strong> {!! $project->project_reviewer->getReviewStatus() !!}</p>
                                    <p><strong>Currently Reviewed by:</strong><br>{{ $project->project_reviewer->user->name }}</p>
                                @endif
                            </div>

                            @if($project->status !== "approved" && $project->status !== "draft")
                                <hr>

                                <div>
                                    <p class="font-medium">Update Schedule:
                                        @if (\Carbon\Carbon::parse($project->submitter_due_date)->isPast())
                                            <span class="text-red-500 font-semibold">Overdue</span>
                                        @else
                                            <span class="text-lime-600 font-semibold">On Time</span>
                                        @endif
                                    </p>
                                    <p>Expected update submission:<br>
                                        <strong class="text-gray-800">{{ \Carbon\Carbon::parse($project->submitter_due_date)->format('M d, Y h:i A') }}</strong>
                                    </p>
                                </div>

                                @unless(Auth::user()->hasRole('User'))
                                    <hr>

                                    <div>
                                        <p class="font-medium">Review Schedule:
                                            @if (\Carbon\Carbon::parse($project->reviewer_due_date)->isPast())
                                                <span class="text-red-500 font-semibold">Overdue</span>
                                            @else
                                                <span class="text-lime-600 font-semibold">On Time</span>
                                            @endif
                                        </p>
                                        <p>Expected review by:<br>
                                            <strong class="text-gray-800">{{ \Carbon\Carbon::parse($project->reviewer_due_date)->format('M d, Y h:i A') }}</strong>
                                        </p>
                                    </div>
                                @endunless
                            @endif
                        </div>
                    </div>


                    <!-- Project Description -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Project Description</h3>
                        <div class="text-sm text-gray-700 bg-gray-50 p-4 rounded border border-gray-200 mb-2">
                            {{ $project->description }}
                        </div>

 
                    </div>


                    <!-- About -->
                    <div class="mt-4">
                         
                        @php
                            function isImageMime($filename) {
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                return in_array($extension, $imageExtensions);
                            }
                        @endphp
                        

                        @if(isset($existingFiles) && count($existingFiles) > 0)
                            @php
                                $index = 1;
                            @endphp

                            @foreach($existingFiles as $date => $project_documents)

                                

                                <div class="hs-accordion-group">
                                    <div class="hs-accordion" id="attachment-{{ $index }}">
                                        <button type="button" class="hs-accordion-toggle 
                                            @if( $index == 1) active @endif
                                            hs-accordion-active:text-blue-600 py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none"
                                            aria-expanded="false" aria-controls="hs-basic-collapse-{{ $index }}">
                            
                                            <svg class="hs-accordion-active:hidden block  size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"></path>
                                                <path d="M12 5v14"></path>
                                            </svg>

                                            <svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"></path>
                                            </svg>
                                            {{ $date }}
                                        </button>
                            
                                        <div id="hs-basic-collapse-{{ $index }}" class="hs-accordion-content 
                                            @if($index !== 1) hidden @endif
                                            w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="attachment-{{ $index }}">
                                            
                                            <div class="dz-flex dz-flex-wrap dz-gap-x-10 dz-gap-y-2 dz-justify-start dz-w-full">
                                                @foreach ($project_documents as $attachment)

                                                    @php 
                                                     
                                                    $attachment_file = route('ftp.download', ['id' => $attachment['id']]);

                                                     
                                                    
                                                    @endphp

                                                    {{-- <?php $attachment_file = asset('storage/uploads/project_attachments/' . $attachment['attachment']); ?> --}}



                                                    <div class="dz-flex dz-items-center dz-justify-between dz-gap-2 dz-border dz-rounded dz-border-gray-200 dz-w-full">
                                                        <div class="dz-flex dz-items-center dz-gap-3">
                                                            @if(isImageMime($attachment_file))
                                                                <div class="dz-flex-none dz-w-14 dz-h-14">
                                                                    <img src="{{ $attachment_file }}" class="dz-object-fill dz-w-full dz-h-full" alt="{{ $attachment_file }}">
                                                                </div>
                                                            @else
                                                                <div class="dz-flex dz-justify-center dz-items-center dz-w-14 dz-h-14 dz-bg-gray-100 ">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="dz-w-8 dz-h-8 dz-text-gray-500">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                                    </svg>
                                                                </div>
                                                            @endif
                                                            <div class="dz-flex dz-flex-col dz-items-start dz-gap-1">
                                                                <div class="dz-text-center dz-text-slate-900 dz-text-sm dz-font-medium">{{ $attachment['attachment'] }}</div>
                                                            </div>
                                                        </div>
                            
                                                        <div class="dz-flex dz-items-center dz-mr-3">
                                                            <a href="{{ route('ftp.download', ['id' => $attachment['id']]) }}" 
                                                                wire:navigate
                                                                class="px-3 py-2 text-xs font-medium text-center text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300">
                                                               
                                                                 {{-- download="{{ $attachment['attachment'] }}" class="inline"> --}}
                                                                <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                    <path fill="#ffffff" d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/>
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php($index++)
                            @endforeach
                    
                        @else
                            <div class="hs-accordion-group">
                                <div class="hs-accordion" id="attachment-index">

                                    <div  class=" 
                                        py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none"
                                        >
                             
                                        Attachments
                                    </div>



                                    <div class="flex items-center justify-center gap-2 border border-gray-200 rounded w-full h-14 bg-gray-100">
                                        <div class="text-center text-slate-900 text-sm font-medium">
                                            No Attachments Found. <a href="" class="text-sky-500 hover:text-blue-500">Click to add files</a>
                                        </div>
                                    </div>

                        
                                </div>
                            </div>
                        @endif

                        @if(Auth::user()->can('system access global admin') || Auth::user()->can('project add attachment'))
                            <div class="py-4">

                                @if($project->allow_project_submission == true)
                                    <!-- Add new project document -->
                                    <a href="{{ route('project.project_document.edit_attachments',['project' => $project->id,'project_document' => $project_document->id]) }}" target="_blank" class="py-1 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border  bg-blue-500 text-white hover:bg-blue-700 focus:outline-hidden focus:border-blue-400  disabled:opacity-50 disabled:pointer-events-none">
                                        Add/Edit attachments
                                    </a>
                                    <!-- Add new project document -->
                                @else
                                    <!-- Add new project document -->
                                    <button disabled class="py-1 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border disabled:opacity-50 bg-blue-500 text-white hover:bg-blue-700 focus:outline-hidden focus:border-blue-400  disabled:opacity-50 disabled:pointer-events-none">
                                        Add/Edit attachments
                                    </button>
                                    <!-- Add new project document -->
                                @endif

                            </div>
                        @endif
                        


                        <ul class="mt-5 flex flex-col gap-y-3">
                                
                            
                            <li class="flex items-center gap-x-2.5">
                                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M304 128a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM49.3 464l349.5 0c-8.9-63.3-63.3-112-129-112l-91.4 0c-65.7 0-120.1 48.7-129 112zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3z"/></svg>
                                <p class="text-[13px] text-gray-500   hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 " href="#">
                                    Submitted by {{ $project->updator->name }}
                                </p>
                            </li>

                            <li class="flex items-center gap-x-2.5">
                                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                <a class="text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 " 
                                href="mailto:{{ $project->updator->email }}">
                                    {{ $project->updator->email }}
                                </a>
                            </li>
                        
                            
                        </ul> 
 

                    </div>
 
                    <!-- End About -->
                    <form class="text-end" wire:submit="submit">

                        
                        @if($project->status !== "approved")

                            @if(
                                Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || 
                                (Auth::user()->can('system access user') && $project->created_by == Auth::id() )
                            )

                                <a href="{{ route('project.edit',['project' => $project->id]) }}"
                                    wire:navigate
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Cancel
                                </a>

                                
                                <button {{ $project->allow_project_submission ? '' : 'disabled' }} type="button"
                                    onclick="confirm('Are you sure, you want to submit this project?') || event.stopImmediatePropagation()"
                                    wire:click.prevent="submit_project({{ $project->id }})"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Submit
                                </button> 

                                


                            @endif

                        @else
                            <p class="text-sm text-lime-600  ">
                            Project is approved
                            </p>
                        @endif
                        
                    </form>

                </div>

  
                        
            </div> 
        </div>
        <!-- End Card -->
    </div>
 

</div>
