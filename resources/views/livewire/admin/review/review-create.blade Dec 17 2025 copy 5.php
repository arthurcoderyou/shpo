<!-- Card Section -->
<div id="review_create" class=" px-4 py-6 sm:px-6 lg:px-8  mx-auto"  >

    {{-- <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div> --}}


        <!-- If it is open review and not accepted yet -->
        @if(!empty($project_reviewer) && $project_reviewer->slot_type == 'open' && empty($project_reviewer->user_id) )


           <!-- Review Acceptance Card -->
            <div class="col-span-12 mb-10
                
                ">  
                <div class="rounded-2xl border border-slate-200 ">


                    <div class="  p-4"> 
                        <h2 class="text-lg font-semibold text-gray-800 ">
                            Open Review Request
                        </h2>

                        <a 
                        href="{{ route('project.document.reviewer.index',[
                                    'project' => $project->id,
                                    'project_document' => $project_document->id
                                ]) }}"
                        wire:navigate class="text-sm font-semibold text-sky-800 hover:underline ">
                            Click here to add/update/delete reviewers of the project
                        </a> 



                        <!-- Review Decision -->
                        <fieldset class="col-span-12">
                            {{-- <legend class="text-sm font-semibold text-slate-900">Review Project Document</legend> --}}
                            <p class="mt-1 text-xs text-slate-500">If you click on review, you will be accepting the open review request</p>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <!-- Accept Button -->
                                <button
                                    type="button"
                                    onclick="confirm('Are you sure you want to accept this project document for review? If you proceed, you will be assigned as the official reviewer for the open review request.') || event.stopImmediatePropagation()"
                                    wire:click.prevent="open_review_project_document({{ $project_document->id }})"
                                    @class([
                                        'flex w-full items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-semibold transition',
                                        'border-emerald-300 bg-emerald-50 text-emerald-800 ring-2 ring-emerald-200' => $review_status === 'approved',
                                        'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' => $review_status !== 'approved',
                                    ])
                                >
                                    <svg class="size-4 text-emerald-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.2 7.2a1 1 0 01-1.42 0l-3-3a1 1 0 111.42-1.42l2.29 2.29 6.49-6.49a1 1 0 011.42 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Accept
                                </button>

                                <!-- Cancel Button -->
                                <a  
                                    {{-- wire:navigate --}}
                                    href="{{ 
                                        route('project-document.index',[
                                            'review_status' => 'open_review'
                                        ])
                                    }}"
                                    @class([
                                        'flex w-full items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-semibold transition',
                                        'border-rose-300 bg-rose-50 text-rose-800 ring-2 ring-rose-200' => $review_status === 'rejected',
                                        'border-slate-200 bg-white text-slate-600 hover:bg-slate-50' => $review_status !== 'rejected',
                                    ])
                                >
                                    <svg class="size-4 text-rose-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M6.22 6.22a.75.75 0 011.06 0L10 8.94l2.72-2.72a.75.75 0 111.06 1.06L11.06 10l2.72 2.72a.75.75 0 11-1.06 1.06L10 11.06l-2.72 2.72a.75.75 0 11-1.06-1.06L8.94 10 6.22 7.28a.75.75 0 010-1.06z" clip-rule="evenodd"/>
                                    </svg>
                                    Cancel
                                </a>
                            </div>


                        

                        </fieldset>

                                
                    </div>
                </div>
            </div>
            <!-- End Review Card -->

        
        @endif

        <div class="grid grid-cols-12 gap-3">

            @if(!empty($project_reviewer))
                @if(($project_reviewer->order !== 1 &&  !empty($project_reviewer->user_id)) || ($project_reviewer->slot_type == 'person' && !empty($project_reviewer->user_id) ) )
                    <!-- Review Card -->
                    <div class="col-span-12 
                        
                        ">  
                        <div class="rounded-2xl border border-slate-200 ">


                            <div class="  p-4">
                                @if(!empty($project_reviewer))
                                    @if($project_reviewer->order !== 1 || ($project_reviewer->slot_type == 'person' && !empty($project_reviewer->user_id) ))

                                        <div class="flex justify-between" 
                                        
                                        x-data="{ 
                                            openRereview: false,
                                            openPreReview: false,
                                             
                                        }"
                                        
                                        >
 
                                            <div> 
                                                <h2 class="text-lg font-semibold text-gray-800 ">
                                                    Submit a Review
                                                </h2>

                                                <a 
                                                href="{{ route('project.document.reviewer.index',[
                                                            'project' => $project->id,
                                                            'project_document' => $project_document->id
                                                        ]) }}"
                                                wire:navigate class="text-sm font-semibold text-sky-800 hover:underline ">
                                                    Click here to add/update/delete reviewers of the project
                                                </a>    
                                            </div>

                                            

                                        </div>



                                        @if($has_pending_request)
                                            <p class="block text-sm font-medium text-gray-800 mt-2.5 ">
                                                You have pending request. Please wait for the administrators response.
                                            </p>

                                        <!-- check if you had already reviewed this or not-->
                                        @elseif($project_document->status !== "approved") 


                                            
                                            <!-- check if you are a reviewer for the project-->
                                            @if(!empty( $project_reviewer ) && $project_reviewer->user_id == Auth::user()->id && 
                                            $project_reviewer->review_status == "pending")
                                                <div class="grid grid-cols-12 space-y-3  mt-2.5"
                                                 x-data="signaturePad" 
                                                            x-init="init()" 
                                                            wire:ignore 
                                                            class="relative"
                                                            wire:key="signature-pad-{{ $this->project_document->id ?? 'pad' }}"
                                                
                                                
                                                >
                                                    
            


                                                    <!-- Review Decision -->
                                                    <fieldset class="col-span-12">
                                                        <legend class="text-sm font-semibold text-slate-900">Review decision</legend>
                                                        <p class="mt-1 text-xs text-slate-500">Choose an outcome and leave guidance for the submitter.</p>

                                                        <div class="mt-3 grid gap-3 sm:grid-cols-2">


                                                            
                                                            @if($project_document->isLastRemainingReviewer(Auth::user()->id))
                                                            <!-- Approve -->
                                                            <div>
                                                                <input id="review_status_approved" 
                                                                    name="review_status" 
                                                                    type="radio" 
                                                                    value="approved"
                                                                    wire:model.live="review_status" class="peer sr-only" />
                                                                <label for="review_status_approved"
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
                                                                        <span class="mt-0.5 block text-xs text-slate-500">Mark as approved and complete this review.</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            @else
                                                            <!-- Reviewed -->
                                                            <div>
                                                                <input id="review_status_reviewed" 
                                                                    autofocus
                                                                    name="review_status" 
                                                                    type="radio" 
                                                                    value="reviewed"
                                                                    wire:model.live="review_status" class="peer sr-only" />
                                                                <label for="review_status_reviewed"
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
                                                                        <span class="block text-sm font-semibold text-emerald-800">Reviewed</span>
                                                                        <span class="mt-0.5 block text-xs text-slate-500">Mark as reviewed and complete this review.</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            @endif

                                                            <!-- Changes Requested -->
                                                            <div>
                                                                <input id="review_status_changes" 
                                                                 autofocus
                                                                    name="review_status" 
                                                                    type="radio" 
                                                                    value="changes_requested"
                                                                    wire:model.live="review_status" class="peer sr-only" />
                                                                <label for="review_status_changes"
                                                                        class="flex w-full items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition
                                                                                hover:bg-slate-50
                                                                                peer-focus-visible:outline-none peer-focus-visible:ring-2 peer-focus-visible:ring-slate-300
                                                                                peer-checked:border-amber-300 peer-checked:ring-2 peer-checked:ring-amber-200">
                                                                    <span class="inline-flex size-5 shrink-0 items-center justify-center rounded-full ring-1 ring-inset ring-amber-300">
                                                                        <!-- exclamation icon -->
                                                                        <svg class="size-3 text-amber-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                            <path fill-rule="evenodd" d="M10 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM9 3a1 1 0 012 0v9a1 1 0 11-2 0V3z" clip-rule="evenodd"/>
                                                                        </svg>
                                                                    </span>
                                                                    <span>
                                                                        <span class="block text-sm font-semibold text-amber-800">Changes Requested</span>
                                                                        <span class="mt-0.5 block text-xs text-slate-500">
                                                                            Requires updates before resubmission. Detail the required changes below.
                                                                        </span>
                                                                    </span>
                                                                </label>
                                                            </div>

                                                            {{-- <!-- Reject -->
                                                            <div>
                                                                <input id="review_status_rejected" 
                                                                    name="review_status" 
                                                                    type="radio" 
                                                                    value="rejected"
                                                                    wire:model.live="review_status" class="peer sr-only" />
                                                                <label for="review_status_rejected"
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
                                                                        <span class="mt-0.5 block text-xs text-slate-500">Decline this document. Provide reasoning in notes.</span>
                                                                    </span>
                                                                </label>
                                                            </div> --}}
                                                        </div>

                                                        @error('review_status')
                                                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                                        @enderror
 

                                                    </fieldset>

                                                    <!-- Divider -->
                                                    <div class="col-span-12">
                                                        <div class="py-3 flex items-center text-sm text-slate-800 before:flex-1 before:border-t before:border-slate-200 before:me-6 after:flex-1 after:border-t after:border-slate-200 after:ms-6">
                                                            Review notes
                                                        </div>
                                                    </div>

                                                    <!-- Notes -->
                                                    <div class="col-span-12">
                                                        <label for="project_review" class="mb-1 block text-sm font-medium text-slate-700">Notes to submitter (required for “Changes Requested”)</label>

                                                        <textarea autofocus id="project_review" name="project_review" wire:model="project_review" rows="5"
                                                                    class="block w-full resize-y rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm
                                                                        placeholder:text-slate-400
                                                                        focus:border-slate-300 focus:ring-2 focus:ring-slate-300
                                                                        disabled:opacity-60 disabled:cursor-not-allowed"
                                                                    placeholder="List the exact changes needed (e.g., upload updated site plan, correct lot number, add photo evidence, etc.)."></textarea>

                                                        @error('project_review')
                                                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                                        @enderror

                                                        <p class="mt-2 text-xs text-slate-500">
                                                            Submitter will be notified and blocked from resubmitting until changes are addressed.
                                                        </p>
                                                    </div>
                                                    
                                                    

                                                    <!-- Actions -->
                                                    <div class="col-span-12 mt-4 flex flex-wrap items-center gap-2"
                                                    
                                                    
                                                    
                                                    >

                                                        

                                                        @if($project_document->isLastRemainingReviewer(Auth::user()->id))
                                                            {{-- <button type="button"
                                                                
                                                                @click="saveImage()"

                                                                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700">
                                                                Save Image
                                                            </button> --}}

                                                            <button type="button"
                                                                onclick="confirm('Are you sure, you want to save this review?') || event.stopImmediatePropagation()"  
                                                                wire:click.prevent="save"
                                                                {{-- @click="saveImage()" --}}

                                                                {{-- @click="
                                                                    if (!confirm('Are you sure you want to save this review?')) return;
                                                                    saveImage();
                                                                " --}}

                                                                class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700">
                                                                Save 
                                                            </button>
                                                        @else
                                                            <button type="button"
                                                                    onclick="confirm('Are you sure, you want to save this review?') || event.stopImmediatePropagation()"
                                                                    wire:click.prevent="save"
                                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700">
                                                                Save decision
                                                            </button>
                                                        @endif





                                                        <a href="{{ route('project.project-document.show',[
                                                            'project' => $project_document->project_id,
                                                            'project_document' => $project_document->id,
                                                        ]) }}"
                                                            class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                                            Cancel
                                                        </a>
                                                    </div>

            

                                                    <div class="space-y-2 col-span-12     ">
                                                        <label for="attachments" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                            Attachments (Optional)
                                                        </label>
                                
                                                        <livewire:dropzone
                                                            wire:model="attachments"
                                                            :rules="['file', 'mimes:png,jpeg,jpg,pdf,docx,xlsx,csv,txt,zip', 'max:20480']"
                                                            :multiple="true" />
                                
                                
                                                        @error('attachments')
                                                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                                        @enderror
                                
                                
                                                    </div>


                                                </div>
                                            @elseif($project_reviewer->review_status !== "pending")
                                                <p class="block text-sm font-medium text-gray-800 mt-2.5 ">
                                                    Review Submitted 
                                                </p>
                                            
                                            @else 
                                                <!-- For not reviewers -->
                                                    
                                                <p class="block text-sm font-medium text-gray-800 mt-2.5 ">
                                                    You are not the current reviewer for this project document. 
                                                </p>

                                            @endif
                                        @else 
                                            <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                                The project has passed all reviews and has been fully approved.
                                            </p>
                                        @endif
                                    @elseif($project_reviewer->order == 1)
                                        
                                        <h2 class="text-lg font-semibold text-gray-800 ">
                                            Initial Review 
                                        </h2>

                                        <a 
                                        href="{{ route('project.document.reviewer.index',[
                                                    'project' => $project->id,
                                                    'project_document' => $project_document->id
                                                ]) }}"
                                        wire:navigate class="text-sm font-semibold text-sky-800 hover:underline ">
                                            Click here to add/update/delete reviewers of the project
                                        </a>

                                        <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                            Save the project document information to mark it as approved.
                                        </p>

                                    @endif

                                @elseif($project_document->status == "approved")
                                    
                                    <h2 class="text-lg font-semibold text-lime-800 ">
                                        Project Document Review is approved by all reviewers
                                    </h2>

                                    {{-- <a 
                                    href="{{ route('project.document.reviewer.index',[
                                                'project' => $project->id,
                                                'project_document' => $project_document->id
                                            ]) }}"
                                    wire:navigate class="text-sm font-semibold text-sky-800 hover:underline ">
                                        Click here to add/update/delete reviewers of the project
                                    </a>

                                    <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                        Save the project document information to mark it as approved.
                                    </p> --}}
                            
                                @else
                                    
                                    <h2 class="text-lg font-semibold text-gray-800 ">
                                        There is something wrong on the reviewers
                                    </h2>

                                    {{-- <a 
                                    href="{{ route('project.document.reviewer.index',[
                                                'project' => $project->id,
                                                'project_document' => $project_document->id
                                            ]) }}"
                                    wire:navigate class="text-sm font-semibold text-sky-800 hover:underline ">
                                        Click here to add/update/delete reviewers of the project
                                    </a>

                                    <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                        Save the project document information to mark it as approved.
                                    </p> --}}
                            
                                @endif

                            </div>
                        </div>
                    </div>
                    <!-- End Review Card -->
                @endif

            @elseif($project_document->status == "approved")
                <div class="col-span-12">
                    <div class="rounded-2xl border border-slate-200 "> 
                        <div class="  p-4">
                            <h2 class="text-lg font-semibold text-lime-800 ">
                                Project Document Review is approved by all reviewers
                            </h2>
                        </div>
                    </div>
                </div>
            @endif
            

            @if(
                (
                    !Auth::user()->hasPermissionTo('system access reviewer') || 
                    (Auth::user()->hasPermissionTo('system access reviewer') && Auth::user()->hasPermissionTo('system access admin'))
                ) && 
                (!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id) 
            )
            <!-- Project Document Information Card -->
            <div class="col-span-12  ">  
                <div class="rounded-2xl border border-slate-200 ">


                    <div class="  p-4">

                    
                        <h2 class="text-lg font-semibold text-gray-800 ">

                            @if(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id) 
                                Review Project Document Information 
                            @else
                                Update Project Document Information
                            @endif
                        </h2>

                        <a 
                        href="{{ route('project.document.reviewer.index',[
                                    'project' => $project->id,
                                    'project_document' => $project_document->id
                                ]) }}"
                        wire:navigate class="text-sm font-semibold text-sky-800 hover:underline ">
                            Click here to add/update/delete reviewers of the project
                        </a>

                        @if(Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('system access admin')    )
                            {{-- <div class="space-y-2 col-span-12   ">
                                <label for="allotted_review_time_hours" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                    Project Allotted Review hours
                                </label>

                                <input
                                type="number"
                                autofocus autocomplete="allotted_review_time_hours"
                                wire:model="allotted_review_time_hours"
                                id="allotted_review_time_hours"  class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder=""> 

                                @error('allotted_review_time_hours')
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror


                            </div>
                            --}}


                            <div class="space-y-2 col-span-12">
                                <label for="rc_number" class="inline-block text-sm font-medium text-gray-800 mt-2.5">
                                    Project RC number
                                </label>

                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm font-medium">
                                        RC#:
                                    </span>

                                    <input
                                        
                                        autocomplete="rc_number"
                                        wire:model="rc_number"
                                        id="rc_number"
                                        class="py-2 px-3 pl-14 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                                        placeholder="">
                                </div>

                                @error('rc_number')
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

    
                            <div class="col-span-12 space-y-4 py-6">
                                <!-- Header -->
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-800">Project References</h2>
                                    <p class="text-sm text-gray-500">Projects that are reference to this project</p>
                                </div>

                                <!-- Search Input -->
                                <div>
                                    <label for="project-search" class="block text-sm font-medium text-gray-700 mb-1">
                                        Search for Project Name or RC #
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
                                @if(!empty($project_documents))
                                    <div class="border border-gray-200 rounded-lg bg-gray-50 shadow-inner max-h-60 overflow-y-auto divide-y divide-gray-100">
                                        @foreach($project_documents as $project_reference)
                                            <div
                                                wire:click="addProjectDocumentReference({{ $project_reference->id }})"
                                                class="px-4 py-3 hover:bg-sky-100 cursor-pointer text-sm transition"
                                            >
                                                <p class="font-semibold text-gray-800">
                                                    Document :{{ $project_reference->document_type->name }}
                                                </p>
                                                <div class="text-gray-600">
                                                    <p><span class="font-medium">Project :</span> {{ $project_reference->project->name }}</p>
                                                    <p class="font-medium">Lot #:{{ $project_reference->project->lot_number }}</p>
                                                    <p><span class="font-medium">RC #:</span> {{ $project_reference->rc_number }}</p>
                                                    
                                                    {{-- <p><span class="font-medium">Project #:</span> {{ $project_reference->project_number }}</p> --}}
                                                    {{-- <p><span class="font-medium">Location:</span> {{ $project_reference->location }}</p>
                                                    <p><span class="font-medium">Type:</span> {{ $project_reference->type }}</p>
                                                    <p><span class="font-medium">Agency:</span> {{ $project_reference->federal_agency }}</p> --}}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Selected Projects -->
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Selected Project References</h3>

                                    @if(!empty($selectedProjectDocuments))
                                        <ul class="space-y-2">
                                            @foreach($selectedProjectDocuments as $index => $selected_project)
                                                <li class="flex items-start justify-between bg-sky-50 border border-sky-200 rounded-lg px-4 py-3 text-sm text-sky-900 shadow-sm">
                                                    <div>
                                                        <p class="font-semibold">Document :{{ $selected_project['document_name'] ?? '' }}</p>
                                                        <p class="font-medium">Project :{{ $selected_project['project_name'] ?? ''}}</p>
                                                        <p class="font-medium">Lot #:{{ $selected_project['lot_number'] ?? '' }}</p>
                                                        <p class="font-medium">RC#: {{ $selected_project['rc_number'] ?? '' }}</p>
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
                        @endif 



                        @if( Auth::user()->can('system access global admin')
                        //  ||  Auth::user()->can('system access admin')  
                        
                        )
                            <div class="grid grid-cols-12 gap-x-2  col-span-12  ">

                                <div class="space-y-2 col-span-12 mt-1 ">
                                    <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                        Set the expected time and date interval for the submitter to submit an update for the project after review
                                    </label>
                                </div>


                                <div class="space-y-2 col-span-12 sm:col-span-3  ">
                                    <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                        {{-- Submitter duration interval count --}}
                                        Count
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
                                        {{-- Submitter duration interval count type --}}
                                        Interval
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
                                        {{-- Submitter expected response due date --}}
                                        Date
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
                                        Set the expected time and date for the reviewer to submit a review for the project
                                    </label>
                                </div>


                                <div class="space-y-2 col-span-12 sm:col-span-3  ">
                                    <label for="reviewer_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                        {{-- Reviewer duration interval count --}}
                                        Count
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
                                        {{-- Reviewer duration interval count type --}}
                                        Interval
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
                                        {{-- Reviewer expected response due date --}}
                                        Date
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
                        <div class="space-y-2 col-span-12   ">
                            <div class="mt-5 flex justify-center gap-x-2">

                            


                                <a href="{{ route('project-document.index') }}" 
                                wire:navigate
                                class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-600 text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Cancel
                                </a>
                                

                                @if(!empty($project_reviewer) && $project_reviewer->user_id == Auth::user()->id)
                                    <button type="button"
                                    onclick="confirm('Are you sure, you want to save this project information details? This will also approve the review of the project') || event.stopImmediatePropagation()"
                                    wire:click.prevent="update_project"
                                    class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-lime-600 text-white hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none">
                                        Save
                                    </button>
                                @else
                                    <button type="button"
                                    onclick="confirm('Are you sure, you want to save this project information details?') || event.stopImmediatePropagation()"
                                    wire:click.prevent="update_project"
                                    class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-lime-600 text-white hover:bg-lime-700 focus:outline-none focus:bg-lime-700 disabled:opacity-50 disabled:pointer-events-none">
                                        Save
                                    </button>
                                @endif


                                

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- End Project Document Information Card -->
            @endif

            
            
        
        </div> 

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
                        Searching for project references...
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


        {{-- wire:target="addProjectDocumentReference"   --}} 
        <div wire:loading  wire:target="addProjectDocumentReference"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                       Adding Project Document...
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

        {{-- wire:target="review_status"   --}} 
        {{-- <div wire:loading  wire:target="review_status"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                       Updating review status...
                    </div>
                </div>
            </div>

            
        </div> --}}

        {{-- wire:target="project_status"   --}} 
        <div wire:loading  wire:target="project_status"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                       Updating project review status...
                    </div>
                </div>
            </div>

            
        </div>

        {{-- wire:target="rfi"   --}} 
        <div wire:loading  wire:target="rfi"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                       Updating project review status...
                    </div>
                </div>
            </div>

            
        </div>

        {{-- wire:target="requires_document_update"   --}} 
        <div wire:loading  wire:target="requires_document_update"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                       Updating project review status...
                    </div>
                </div>
            </div>

            
        </div>


        {{-- wire:target="requires_attachment_update"   --}} 
        <div wire:loading  wire:target="requires_attachment_update"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                       Updating project review status...
                    </div>
                </div>
            </div>

            
        </div>

        {{-- wire:target="requires_attachment_update"   --}} 
        <div wire:loading  wire:target="confirmReview"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                       Loading...
                    </div>
                </div>
            </div>

            
        </div>



        


    <!--  ./ Loaders -->


     @if($confirmingReview)
        <form wire:submit.prevent="saveReview" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
                <h2 class="text-lg font-semibold text-gray-600">Confirm Review</h2>
                <p class="text-sm text-gray-700 mt-2">
                    This action <strong>cannot be undone</strong>. Please enter your password to confirm.
                </p>

                <div class="mt-4" x-data="{ show: false }">
                    <label for="passwordConfirm" class="block text-sm font-medium text-gray-700">Your Password</label>
                    <div class="relative mt-1">
                        <input :type="show ? 'text' : 'password'" wire:model.defer="passwordConfirm" id="passwordConfirm"
                            class="block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="button"
                                x-on:click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm text-gray-600 hover:text-gray-900 focus:outline-none"
                                tabindex="-1">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.045 10.045 0 014.724-5.735M6.182 6.182l11.636 11.636M17.818 17.818L6.182 6.182"/>
                            </svg>
                        </button>
                    </div>
                    @if($passwordError)
                        <p class="text-sm text-red-500 mt-1">{{ $passwordError }}</p>
                    @endif
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button wire:click="$set('confirmingReview', false)"
                            type="button"
                            class="px-4 py-2 text-sm rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm rounded-md text-white bg-red-600 hover:bg-red-700">
                        Confirm Delete
                    </button>
                </div>
            </div>
        </form>
    @endif


    <script>

        document.addEventListener('alpine:init', () => {
    

            Alpine.data('signaturePad', () => ({
                canvas: null,
                ctx: null,
                drawing: false,
                last: { x: 0, y: 0 },

                init() {
                this.canvas = this.$el.querySelector('#sig-canvas');
                this.ctx = this.canvas.getContext('2d', { willReadFrequently: false });

                const setSize = () => {
                    // Get CSS pixel size
                    const rect = this.canvas.getBoundingClientRect();
                    const dpr = window.devicePixelRatio || 1;

                    // Set the internal pixel buffer to match CSS size * DPR
                    this.canvas.width = Math.max(1, Math.floor(rect.width * dpr));
                    this.canvas.height = Math.max(1, Math.floor(rect.height * dpr));

                    // Reset transform then scale so we can draw using CSS pixel coords
                    this.ctx.setTransform(1, 0, 0, 1, 0, 0);
                    this.ctx.scale(dpr, dpr);

                    // Style
                    this.ctx.lineWidth = 2;
                    this.ctx.lineCap = 'round';
                    this.ctx.strokeStyle = '#111827';
                };

                // Initial size + on resize
                setSize();
                new ResizeObserver(setSize).observe(this.canvas);

                // Pointer Events (unified mouse/touch/pen)
                this.canvas.addEventListener('pointerdown', (e) => {
                    this.canvas.setPointerCapture(e.pointerId);
                    const p = this._pos(e);
                    this.drawing = true;
                    this.last = p;
                }, { passive: true });

                this.canvas.addEventListener('pointermove', (e) => {
                    if (!this.drawing) return;
                    const p = this._pos(e);
                    this.ctx.beginPath();
                    this.ctx.moveTo(this.last.x, this.last.y);
                    this.ctx.lineTo(p.x, p.y);
                    this.ctx.stroke();
                    this.last = p;
                }, { passive: true });

                const end = () => { this.drawing = false; };
                this.canvas.addEventListener('pointerup', end, { passive: true });
                this.canvas.addEventListener('pointercancel', end, { passive: true });

                // Livewire clear hook
                window.addEventListener('signature:clear', () => this.clearCanvas());
                },

                _pos(e) {
                const r = this.canvas.getBoundingClientRect();
                // Return CSS pixel coords; we scaled the context so 1 unit = 1 CSS pixel
                return { x: e.clientX - r.left, y: e.clientY - r.top };
                },

                clearCanvas() {
                    // Clear using CSS pixel coords (context is scaled already)
                    const r = this.canvas.getBoundingClientRect();
                    this.ctx.clearRect(0, 0, r.width, r.height);
                },

                // saveImage() {
                //     // Ensure crisp export: toDataURL uses the internal pixel buffer (already sized to DPR)
                //     const data = this.canvas.toDataURL('image/png');
                //     this.$wire.signatureData = data;
                //     this.$wire.saveImage();
                // }

                async saveImage() {
                    this.saving = true;
                    try {
                        const dataUrl = this.canvas.toDataURL('image/png'); // "data:image/png;base64,...."
                        await $wire.set('signatureData', dataUrl);          // ensure Livewire receives it first
                        await $wire.saveImage();                                 // now run your Livewire save()
                    } finally {
                        this.saving = false;
                    }
                },

            }));
        });

    </script>
 
</div>
<!-- End Card Section -->
