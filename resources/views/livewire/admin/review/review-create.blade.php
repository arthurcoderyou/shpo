<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    {{-- <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div> --}}

    
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
                                                        Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || 
                                                        (Auth::user()->can('user') && $project->created_by == Auth::id() )
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
                                    <label for="project_review" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                        Review
                                    </label>   

                                    <!-- Initial review section --> 
                                    <div class="grid space-y-3  mt-2.5">

                                        <div class="py-3 flex items-center text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 after:flex-1 after:border-t after:border-gray-200 after:ms-6 ">
                                           Select a Initial Review Status  
                                        </div>
                                       
                                         

                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5 mt-1">
                                            <input id="hs-checkbox-delete" name="hs-checkbox-delete" type="radio" class="border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" aria-describedby="hs-checkbox-delete-description" checked="">
                                            </div>
                                            <label for="hs-checkbox-delete" class="ms-3">
                                                <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">Reject and RFI</span>
                                                <span id="hs-checkbox-delete-description" class="block text-sm text-gray-600 dark:text-neutral-500">Reject Project Document and Request for Information</span>
                                            </label>
                                        </div>

 
                                        <fieldset>
                                        
                                            <div class="mx-3 space-y-6">
                                                <!-- Requirement 1: Project Update -->
                                                <div class="flex gap-3">
                                                    <div class="flex h-6 shrink-0 items-center">
                                                        <div class="group grid size-4 grid-cols-1">
                                                            <input id="require_project_update" name="require_project_update" type="checkbox"
                                                                class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                            <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                                                <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm/6">
                                                        <label for="require_project_update" class="font-medium text-gray-900">Project Update</label>
                                                        <p class="text-gray-500">Requires the user to update the project and its details.</p>
                                                    </div>
                                                </div>

                                                <!-- Requirement 2: Project Document Update -->
                                                <div class="flex gap-3">
                                                    <div class="flex h-6 shrink-0 items-center">
                                                        <div class="group grid size-4 grid-cols-1">
                                                            <input id="require_document_update" name="require_document_update" type="checkbox"
                                                                class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                            <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                                                <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm/6">
                                                        <label for="require_document_update" class="font-medium text-gray-900">Project Document Update</label>
                                                        <p class="text-gray-500">
                                                            Requires the user to update the project and add required documents  ".
                                                        </p>
                                                    </div>
                                                </div>


                                                <div class="mx-3 space-y-6">

                                                    @foreach($documentTypes as $type)
                                                        <div class="flex gap-3">
                                                            <div class="flex h-6 shrink-0 items-center">
                                                                <div class="group grid size-4 grid-cols-1">
                                                                    <input id="require_document_update" name="require_document_update" type="checkbox"
                                                                        class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                                    <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                                                        <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                        <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                            <div class="text-sm/6">
                                                                <label for="require_document_update" class="font-medium text-gray-900">{{ $type->name }}</label>
                                                                <!--<p class="text-gray-500">
                                                                    Requires the user to update the project and add required documents  ".
                                                                </p> -->
                                                            </div>
                                                        </div>
                                                    @endforeach 

                                                   

                                                </div>
                                                

                                                <!-- Requirement 3: Project Document Attachment Update -->
                                                <div class="flex gap-3">
                                                    <div class="flex h-6 shrink-0 items-center">
                                                        <div class="group grid size-4 grid-cols-1">
                                                            <input id="require_attachment_update" name="require_attachment_update" type="checkbox"
                                                                class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                            <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                                                <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm/6">
                                                        <label for="require_attachment_update" class="font-medium text-gray-900">Project Document Attachment Update</label>
                                                        <p class="text-gray-500">
                                                            Requires the user to update the project, its document "<span class="font-semibold text-indigo-600">name_of_project_document</span>", and upload new attachments.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                        </fieldset>

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
                                    <!-- ./ Initial review section  -->

                                    <!-- Document review section -->
                                    <div class="grid space-y-3  mt-2.5">

                                        <div class="py-3 flex items-center text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 after:flex-1 after:border-t after:border-gray-200 after:ms-6 ">
                                           Select a Project Document Review Status  
                                        </div>
                                       
                                         

                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5 mt-1">
                                            <input id="hs-checkbox-delete" name="hs-checkbox-delete" type="radio" class="border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" aria-describedby="hs-checkbox-delete-description" checked="">
                                            </div>
                                            <label for="hs-checkbox-delete" class="ms-3">
                                                <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">Reject and RFI</span>
                                                <span id="hs-checkbox-delete-description" class="block text-sm text-gray-600 dark:text-neutral-500">Reject Project Document and Request for Information</span>
                                            </label>
                                        </div>

 
                                        <fieldset>
                                        
                                            <div class="mx-3 space-y-6">
                                                <!-- Requirement 1: Project Update -->
                                                <div class="flex gap-3">
                                                    <div class="flex h-6 shrink-0 items-center">
                                                        <div class="group grid size-4 grid-cols-1">
                                                            <input id="require_project_update" name="require_project_update" type="checkbox"
                                                                class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                            <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                                                <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm/6">
                                                        <label for="require_project_update" class="font-medium text-gray-900">Project Update</label>
                                                        <p class="text-gray-500">Requires the user to update the project and its details.</p>
                                                    </div>
                                                </div>

                                                <!-- Requirement 2: Project Document Update -->
                                                <div class="flex gap-3">
                                                    <div class="flex h-6 shrink-0 items-center">
                                                        <div class="group grid size-4 grid-cols-1">
                                                            <input id="require_document_update" name="require_document_update" type="checkbox"
                                                                class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                            <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                                                <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm/6">
                                                        <label for="require_document_update" class="font-medium text-gray-900">Project Document Update</label>
                                                        <p class="text-gray-500">
                                                            Requires the user to update the project and its document "<span class="font-semibold text-indigo-600">name_of_project_document</span>".
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Requirement 3: Project Document Attachment Update -->
                                                <div class="flex gap-3">
                                                    <div class="flex h-6 shrink-0 items-center">
                                                        <div class="group grid size-4 grid-cols-1">
                                                            <input id="require_attachment_update" name="require_attachment_update" type="checkbox"
                                                                class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                            <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                                                <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm/6">
                                                        <label for="require_attachment_update" class="font-medium text-gray-900">Project Document Attachment Update</label>
                                                        <p class="text-gray-500">
                                                            Requires the user to update the project, its document "<span class="font-semibold text-indigo-600">name_of_project_document</span>", and upload new attachments.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                        </fieldset>

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
                                    <!-- ./ Document review section -->


                                    <!-- Final review section -->
                                    <div class="grid space-y-3  mt-2.5">

                                        <div class="py-3 flex items-center text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 after:flex-1 after:border-t after:border-gray-200 after:ms-6 ">
                                           Select a Final Review Status  
                                        </div>
                                       
                                         

                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5 mt-1">
                                            <input id="hs-checkbox-delete" name="hs-checkbox-delete" type="radio" class="border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" aria-describedby="hs-checkbox-delete-description" checked="">
                                            </div>
                                            <label for="hs-checkbox-delete" class="ms-3">
                                                <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">Reject and RFI</span>
                                                <span id="hs-checkbox-delete-description" class="block text-sm text-gray-600 dark:text-neutral-500">Reject Project Document and Request for Information</span>
                                            </label>
                                        </div>

 
                                        <fieldset>
                                        
                                            <div class="mx-3 space-y-6">
                                                <div class="flex gap-3">
                                                    <div class="flex h-6 shrink-0 items-center">
                                                        <div class="group grid size-4 grid-cols-1">
                                                        <input id="comments" aria-describedby="comments-description" name="comments" type="checkbox" checked class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                        <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                                            <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm/6">
                                                        <label for="comments" class="font-medium text-gray-900">Require Update on the Project Details</label>
                                                        <p id="comments-description" class="text-gray-500">Get notified when someones posts a comment on a posting.</p>
                                                    </div>
                                                </div>
                                                
                                                
                                            </div>
                                        </fieldset>

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
                                    <!-- ./ Final review section -->


                                    <!-- Final review section -->
                                    <div class="grid space-y-3  mt-2.5">

                                        <div class="py-3 flex items-center text-sm text-gray-800 before:flex-1 before:border-t before:border-gray-200 before:me-6 after:flex-1 after:border-t after:border-gray-200 after:ms-6 ">
                                           Select a Final Review Status  
                                        </div>
                                       
                                         

                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5 mt-1">
                                            <input id="hs-checkbox-delete" name="hs-checkbox-delete" type="radio" class="border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 checked:border-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" aria-describedby="hs-checkbox-delete-description" checked="">
                                            </div>
                                            <label for="hs-checkbox-delete" class="ms-3">
                                                <span class="block text-sm font-semibold text-gray-800 dark:text-neutral-300">Reject and RFI</span>
                                                <span id="hs-checkbox-delete-description" class="block text-sm text-gray-600 dark:text-neutral-500">Reject Project Document and Request for Information</span>
                                            </label>
                                        </div>

 
                                        <fieldset>
                                        
                                            <div class="mx-3 space-y-6">
                                                <div class="flex gap-3">
                                                    <div class="flex h-6 shrink-0 items-center">
                                                        <div class="group grid size-4 grid-cols-1">
                                                        <input id="comments" aria-describedby="comments-description" name="comments" type="checkbox" checked class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                        <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                                            <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm/6">
                                                        <label for="comments" class="font-medium text-gray-900">Require Update on the Project Details</label>
                                                        <p id="comments-description" class="text-gray-500">Get notified when someones posts a comment on a posting.</p>
                                                    </div>
                                                </div>
                                                
                                                
                                            </div>
                                        </fieldset>

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
                                    <!-- ./ Final review section -->

 

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




                                @if(Auth::user()->can('system access global')  || Auth::user()->can('system access reviewer') )
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
                                <a href="{{ route('project.index.review-pending.all-linked') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
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
                                <a href="{{ route('project.index.review-pending.all-linked') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Cancel 
                                </a>
                            </div>
                        @endif 



                    @else 

                        <div class="mt-5 flex justify-center gap-x-2">
                            <a href="{{ route('project.index.review-pending.all-linked') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                                Cancel {{--  --}}
                            </a>
                        </div>

                    @endif
                
                @elseif($project->getCurrentReviewer()->user_id == Auth::user()->id)


                    <!-- Grid -->
                    <div class="grid grid-cols-12 gap-x-2 gap-y-2 ">
                                
                                 

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
 

                        <div class="col-span-12 space-y-4">
                            <div class="bg-white rounded-2xl shadow border border-gray-200">
                                <div class="p-6 space-y-6">

                                    <!-- Header -->
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-800">Project References</h2>
                                        <p class="text-sm text-gray-500">Projects that are reference to this project</p>
                                    </div>

                                    <!-- Search Input -->
                                    <div>
                                        <label for="project-search" class="block text-sm font-medium text-gray-700 mb-1">
                                            Search for Project Name or SHPO #
                                        </label>
                                        <input
                                            id="project-search"
                                            type="text"
                                            wire:model.live="query"
                                            placeholder="Type to search..."
                                            class="w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500 text-sm px-4 py-2 shadow-sm"
                                        >
                                    </div>

                                    <!-- Search Results -->
                                    @if(!empty($projects))
                                        <div class="border border-gray-200 rounded-lg bg-gray-50 shadow-inner max-h-60 overflow-y-auto divide-y divide-gray-100">
                                            @foreach($projects as $project_reference)
                                                <div
                                                    wire:click="addProjectReference({{ $project_reference->id }})"
                                                    class="px-4 py-3 hover:bg-sky-100 cursor-pointer text-sm transition"
                                                >
                                                    <p class="font-semibold text-gray-800">
                                                        {{ $project_reference->name }}
                                                    </p>
                                                    <div class="text-gray-600">
                                                        <p><span class="font-medium">SHPO #:</span> {{ $project_reference->shpo_number }}</p>
                                                        <p><span class="font-medium">Project #:</span> {{ $project_reference->project_number }}</p>
                                                        <p><span class="font-medium">Location:</span> {{ $project_reference->location }}</p>
                                                        <p><span class="font-medium">Type:</span> {{ $project_reference->type }}</p>
                                                        <p><span class="font-medium">Agency:</span> {{ $project_reference->federal_agency }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Selected Projects -->
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Selected Project References</h3>

                                        @if(!empty($selectedProjects))
                                            <ul class="space-y-2">
                                                @foreach($selectedProjects as $index => $selected_project)
                                                    <li class="flex items-start justify-between bg-sky-50 border border-sky-200 rounded-lg px-4 py-3 text-sm text-sky-900 shadow-sm">
                                                        <div>
                                                            <p class="font-semibold">{{ $selected_project['name'] }}</p>
                                                            <p>SHPO #: {{ $selected_project['shpo_number'] }}</p>
                                                        </div>
                                                        <button
                                                            wire:click="removeProjectReference({{ $index }})"
                                                            class="text-red-500 hover:text-red-600 ml-4 text-lg"
                                                            title="Remove"
                                                        >
                                                            &times;
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-sm text-gray-400">No projects selected.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>





                    </div>
                    <!-- End Grid -->
    

                    <div class="mt-5 flex justify-center gap-x-2">
                        <a href="{{ route('project.index.review-pending.all-linked') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
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
                        <a href="{{ route('project.index.review-pending.all-linked') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                            Cancel
                        </a>
                         
                         
                    </div>

                @endif

            </div>
        </div>
        <!-- End Card -->

    <!--  Loaders -->
         

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
                        Saving review...
                    </div>
                </div>
            </div>

            
        </div>


        {{-- wire:target="update_project"   --}}
        <div wire:loading  wire:target="update_project"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Updating Project...
                    </div>
                </div>
            </div>

            
        </div>

        {{-- wire:target="query"   --}} 
        <div wire:loading  wire:target="query"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Searching for projects...
                    </div>
                </div>
            </div>

            
        </div>

        {{-- wire:target="query"   --}}
        {{-- <div wire:loading wire:target="query"
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
                    Loading lists...
                </div> 
            </div>
        </div> --}}


        {{-- wire:target="addProjectReference"   --}} 
        <div wire:loading  wire:target="addProjectReference"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                       Adding Project...
                    </div>
                </div>
            </div>

            
        </div>


        {{-- wire:target="removeProjectReference"   --}} 
        <div wire:loading  wire:target="removeProjectReference"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                       Removing Project...
                    </div>
                </div>
            </div>

            
        </div>



        


    <!--  ./ Loaders -->
     
</div>
<!-- End Card Section -->
