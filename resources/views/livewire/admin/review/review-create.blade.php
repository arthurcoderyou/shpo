<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div>

    
        <!-- Card -->
        <div class="bg-white rounded-xl shadow ">


            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 ">
                    Submit a Review  
                    
                    </h2>

                    <a href="{{ route('project.reviewer.index',['project' => $project->id]) }}" class="text-sm font-semibold text-sky-800 hover:underline ">
                        Click here to add/update/delete reviewers of the project
                    </a>
 

                    
                     
                    @if(!empty($project->shpo_number))
                        
                   
                        <!-- check if you had already reviewed this or not-->
                        @if($project->status !== "approved")
                            
                            @php 
                                $project_reviewer = $project->checkIfUserIsProjectReviewer(Auth::user()->id);
                            @endphp


                            <!-- check if you are a reviewer for the project-->
                            @if(!empty( $project_reviewer ))


                                <!-- check if you are the current reviewer -->
                                @if($project->getCurrentReviewer()->user_id == Auth::user()->id)
                                    @if($project->allow_project_submission == true) <!-- allow_project_submission true means the review had been submitted, and false means the review is not yet submitted -->
                                        <p class="block text-sm font-medium text-gray-800 mt-2.5 ">
                                            You had already submitted a review for this project. Waiting for user reply.
                                        </p> 
                                    @endif 
                                @else 
                                    <!-- for not current reviewers but still a reviewer -->


                                    <!-- if you are not the current reviewer but had already reviewed the project -->
                                    @if($project_reviewer->review_status !== "pending")
                                        <p class="block text-sm font-medium text-gray-800 mt-2.5 ">
                                            You are not the current reviewer but you had already submitted a review for this project.  
                                        </p>
                                    @else   
                                        <p class="block text-sm font-medium text-gray-800 mt-2.5 ">
                                            You are not the current reviewer and you had not yet submitted a review for this project.  
                                        </p>
                                    @endif
                                    
        
                                @endif
        
                            @else 
                                <!-- For not reviewers -->
                                    
                                <p class="block text-sm font-medium text-gray-800 mt-2.5 ">
                                    You are not a reviewer for this project. 
                                </p>

                            @endif
                        @else 
                            <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                The project has passed all reviews and has been fully approved.
                            </p>
                        @endif
                    @elseif($project->getCurrentReviewer()->user_id == Auth::user()->id)

                        <p class=" text-sm font-medium text-gray-800 mt-2.5 block">
                            Please update the project's SHPO number.
                        </p>
                    @else
                        <p class=" text-sm font-medium text-gray-800 mt-2.5 block">
                            You are not the current reviewer for the project
                        </p>
                    @endif
                    
                </div>
                <!-- End Col -->

                @if(!empty($project->shpo_number))
                    @if($project->allow_project_submission == false && $project->status !== "approved") <!-- if the submission for user is not allowed, it means a feedback is needed by the reviewer and this is opened --> 
                        

                        

                        <!-- check if this user is the current reviewer for this project -->
                        @if($project->getCurrentReviewer()->user_id == Auth::user()->id)


                            @php    
                                $current_project_reviewer = $project->getCurrentReviewer();
                            @endphp

                            <!-- Grid -->
                            <div class="grid grid-cols-12 gap-x-2  ">

                                <div class="space-y-2 col-span-12   ">
                                    <label for="project_review" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                        Project Document for Review
                                    </label>


                                    <ul class="divide-y divide-gray-200 border border-gray-200 rounded-lg overflow-hidden bg-white">
                                        @if (!empty($current_project_reviewer->project_document ))

                                            @php 
                                                $project_document = $current_project_reviewer->project_document;
                                            @endphp
                                            <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 transition flex items-start justify-between">
                                                <div>
                                                    <p class="text-base font-semibold text-gray-800">
                                                        {{ $project_document->document_type->name }}
                                                    </p>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $project_document->project_attachments->count() }} attachment{{ $project_document->project_attachments->count() !== 1 ? 's' : '' }} found
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Last updated {{ $project_document->updated_at->diffForHumans() }}
                                                    </p>
                                                </div>

                                                <div class="flex flex-col items-end gap-1 text-sm whitespace-nowrap">


                                                    <a target="_blank" href="{{ route('project.project_document', ['project' => $project->id, 'project_document' => $project_document->id]) }}"
                                                    class="text-gray-600 hover:underline hover:text-gray-800 flex items-center gap-x-1">
                                                        View
                                                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path d="m9 18 6-6-6-6" />
                                                        </svg>
                                                    </a>

                                                    @if(
                                                        Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasRole('Admin') || 
                                                        (Auth::user()->hasRole('User') && $project->created_by == Auth::id() )
                                                    )
                                                        <a href="{{ route('project.project_document', ['project' => $project->id, 'project_document' => $project_document->id]) }}"
                                                        class="text-blue-600 hover:underline hover:text-blue-800 flex items-center gap-x-1">
                                                            Update
                                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path d="m9 18 6-6-6-6" />
                                                            </svg>
                                                        </a>
                                                        {{-- <button type="button"
                                                            onclick="confirm('Are you sure you want to delete this record?') || event.stopImmediatePropagation()"
                                                            wire:click.prevent="delete({{ $project_document->id }})"
                                                            class="text-red-600 hover:underline hover:text-red-800 flex items-center gap-x-1">
                                                            Delete
                                                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                                <path d="m9 18 6-6-6-6" />
                                                            </svg>
                                                        </button> --}}
                                                    @endif 


                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                </div>



                                <div class="space-y-2 col-span-12   ">
                                    <!-- <label for="project_review" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                        Review
                                    </label> -->

                                    <!--
                                    <div class="grid space-y-3  mt-2.5">

                                        <div class="py-3 flex items-center text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 after:flex-1 after:border-t after:border-gray-200 after:ms-6 ">
                                           Select a Project Document Review Status  
                                        </div>

                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5 mt-1">
                                            <input id="hs-checkbox-delete" name="hs-checkbox-delete" type="radio" class="border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" aria-describedby="hs-checkbox-delete-description" checked="">
                                            </div>
                                            <label for="hs-checkbox-delete" class="ms-3">
                                                <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">Review</span>
                                                <span id="hs-checkbox-delete-description" class="block text-sm text-gray-600 dark:text-neutral-500">Submit a review on the project document</span>
                                            </label>
                                        </div>

                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5 mt-1">
                                            <input id="hs-checkbox-delete" name="hs-checkbox-delete" type="radio" class="border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" aria-describedby="hs-checkbox-delete-description" checked="">
                                            </div>
                                            <label for="hs-checkbox-delete" class="ms-3">
                                                <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">RFI</span>
                                                <span id="hs-checkbox-delete-description" class="block text-sm text-gray-600 dark:text-neutral-500">Request for Information</span>
                                            </label>
                                        </div>

                                         <div class="relative flex items-start">
                                            <div class="flex items-center h-5 mt-1">
                                            <input id="hs-checkbox-delete" name="hs-checkbox-delete" type="radio" class="border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" aria-describedby="hs-checkbox-delete-description" checked="">
                                            </div>
                                            <label for="hs-checkbox-delete" class="ms-3">
                                                <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">Approve Project Document</span>
                                                <span id="hs-checkbox-delete-description" class="block text-sm text-gray-600 dark:text-neutral-500">Approves the project document </span>
                                            </label>
                                        </div>

                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5 mt-1">
                                            <input id="hs-checkbox-delete" name="hs-checkbox-delete" type="radio" class="border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" aria-describedby="hs-checkbox-delete-description" checked="">
                                            </div>
                                            <label for="hs-checkbox-delete" class="ms-3">
                                                <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">Temporary Reject Document</span>
                                                <span id="hs-checkbox-delete-description" class="block text-sm text-gray-600 dark:text-neutral-500">Rejects the project document and enables project resubmission.</span>
                                            </label>
                                        </div>
 

                                    </div>
                                -->

                                    <div class="py-3 flex items-center text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 after:flex-1 after:border-t after:border-gray-200 after:ms-6 ">
                                        Review notes
                                    </div>

                                    <textarea
                                    
                                    autofocus autocomplete="project_review"
                                    wire:model="project_review" placeholder=""
                                    id="project_review"  class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder=""></textarea>

                                    @error('project_review')
                                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                    @enderror


                                </div>

                                  
                                <div class="space-y-2 col-span-12     ">
                                    <label for="attachments" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                        Attachments
                                    </label>
            
                                    <livewire:dropzone
                                        wire:model="attachments"
                                        :rules="['file', 'mimes:png,jpeg,jpg,pdf,docx,xlsx,csv,txt,zip', 'max:20480']"
                                        :multiple="true" />
            
            
                                    @error('attachments')
                                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                    @enderror
            
            
                                </div>




                                @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->hasRole('Reviewer') )
                                    <div class="grid grid-cols-12 gap-x-2  col-span-12  ">

                                        <div class="space-y-2 col-span-12 mt-1 ">
                                            <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                Set the time line for the user to send a response to the review
                                            </label>
                                        </div>
    

                                        <div class="space-y-2 col-span-12 sm:col-span-3  ">
                                            <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                Submitter duration
                                            </label>

                                            <input
                                            min="1"
                                            autofocus autocomplete="submitter_response_duration"
                                            wire:model.live="submitter_response_duration"
                                            
                                            id="submitter_response_duration" type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                                            @error('submitter_response_duration')
                                                <p class="text-sm text-red-600 mt-2">{{ $message  }}</p>
                                            @enderror


                                        </div>

                                        <div class="space-y-2 col-span-12 sm:col-span-3  ">
                                            <label for="submitter_response_duration_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                Submitter duration type
                                            </label>

                                            <select 
                                            autofocus autocomplete="submitter_response_duration_type"
                                            wire:model.live="submitter_response_duration_type"
                                            id="submitter_response_duration_type" 
                                            class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  ">
                                                <option selected="">Select type</option>
                                                <option value="day">Day</option>
                                                <option value="week">Week</option>
                                                <option value="month">Month</option>
                                            </select>

                                            @error('submitter_response_duration_type')
                                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                            @enderror


                                        </div>

                                        <div class="space-y-2 col-span-12 sm:col-span-6  ">
                                            <label for="submitter_due_date" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                Submitter response due date
                                            </label>

                                            <input readonly 
                                            {{-- autofocus autocomplete="submitter_due_date"
                                            wire:model.live="submitter_due_date" --}}
                                            value="{{ \Carbon\Carbon::parse($submitter_due_date)->format('d M, h:i A') }}"
                                            id="submitter_due_date" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                                            @error('submitter_due_date')
                                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                            @enderror


                                        </div>

                                        <div class="space-y-2 col-span-12 mt-1 ">
                                            <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                Set the time line for the reviewer to review the project
                                            </label>
                                        </div>


                                        <div class="space-y-2 col-span-12 sm:col-span-3  ">
                                            <label for="reviewer_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                Reviewer duration
                                            </label>
                    
                                            <input
                                            min="1"
                                            autofocus autocomplete="reviewer_response_duration"
                                            wire:model.live="reviewer_response_duration"
                                            id="reviewer_response_duration" type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">
                    
                                            @error('reviewer_response_duration')
                                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                            @enderror
                    
                    
                                        </div>
                    
                                        <div class="space-y-2 col-span-12 sm:col-span-3  ">
                                            <label for="reviewer_response_duration_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                Reviewer duration type
                                            </label>
                    
                                            <select 
                                            autofocus autocomplete="reviewer_response_duration_type"
                                            wire:model.live="reviewer_response_duration_type"
                                            id="reviewer_response_duration_type" 
                                            class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  ">
                                                <option selected="">Select type</option>
                                                <option value="day">Day</option>
                                                <option value="week">Week</option>
                                                <option value="month">Month</option>
                                            </select>
                    
                                            @error('reviewer_response_duration_type')
                                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                            @enderror
                    
                    
                                        </div>
                    
                    
                                        <div class="space-y-2 col-span-12 sm:col-span-6  ">
                                            <label for="reviewer_due_date" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                Reviewer response due date
                                            </label>
                    
                                            <input readonly 
                                            {{-- autocomplete="reviewer_due_date"
                                            wire:model.live="reviewer_due_date" --}}    
                                            value="{{ \Carbon\Carbon::parse($reviewer_due_date)->format('d M, h:i A') }}"
                                            id="reviewer_due_date" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">
                    
                                            @error('reviewer_due_date')
                                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                            @enderror
                    
                    
                                        </div>



                                        


                                        
                                        
                                    </div>
                                @endif


                                


                            </div>
                            <!-- End Grid -->
            

                            <div class="mt-5 flex justify-center gap-x-2">
                                <a href="{{ route('project.in_review') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Cancel
                                </a>
                                <button type="button"
                                onclick="confirm('Are you sure, you want to APPROVE this project and submit this review? This cannot be edited and will be emailed back to the sender of the project') || event.stopImmediatePropagation()"
                                wire:click.prevent="save('approved')"
                                class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-lime-600 text-white hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Approve
                                </button>

                                <button type="button"
                                onclick="confirm('Are you sure, you want to REJECT this project and submit this review? This cannot be edited and will be emailed back to the sender of the project') || event.stopImmediatePropagation()"
                                wire:click.prevent="save('rejected')"
                                class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Reject
                                </button>

                                

                            </div>
                        @else 
                            <div class="mt-5 flex justify-center gap-x-2">
                                <a href="{{ route('project.in_review') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Cancel 
                                </a>
                            </div>
                        @endif 



                    @else 

                        <div class="mt-5 flex justify-center gap-x-2">
                            <a href="{{ route('project.in_review') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                                Cancel {{--  --}}
                            </a>
                        </div>

                    @endif
                
                @elseif($project->getCurrentReviewer()->user_id == Auth::user()->id)


                    <!-- Grid -->
                    <div class="grid grid-cols-12 gap-x-2  ">
                                
                                 

                        <div class="space-y-2 col-span-12   ">
                            <label for="shpo_number" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                Project SHPO number
                            </label>

                            <input
                            
                            autofocus autocomplete="shpo_number"
                            wire:model="shpo_number"
                            id="shpo_number"  class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder=""> 

                            @error('shpo_number')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror


                        </div>
 


                    </div>
                    <!-- End Grid -->
    

                    <div class="mt-5 flex justify-center gap-x-2">
                        <a href="{{ route('project.in_review') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                            Cancel
                        </a>
                         
                        <button type="button"
                        onclick="confirm('Are you sure, you want to save project SHPO number?') || event.stopImmediatePropagation()"
                        wire:click.prevent="update_project"
                        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                            Save
                        </button>

                        

                    </div>

                @else
                    <div class="mt-5 flex justify-center gap-x-2">
                        <a href="{{ route('project.in_review') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                            Cancel
                        </a>
                         
                         
                    </div>

                @endif

            </div>
        </div>
        <!-- End Card -->
     
</div>
<!-- End Card Section -->
