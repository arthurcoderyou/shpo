<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div>

    
        <!-- Card -->
        <div class="bg-white rounded-xl shadow dark:bg-neutral-900">


            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                    Submit a Review  
                    
                    </h2>

                    <a href="{{ route('project.reviewer.index',['project' => $project->id]) }}" class="text-sm font-semibold text-sky-800 hover:underline dark:text-neutral-200">
                        Click here to add/update/delete reviewers of the project
                    </a>
 

                    
                     
                    @if(!empty($project->shpo_number))
                        
                   
                        <!-- check if you had already reviewed this or not-->
                        @if($project->status !== "approved")
                            @if(!empty($project->getProjectReviewerByUser(Auth::user()->id)))
                                <!-- check if you are the reviewer -->
                                @if($project->getCurrentReviewer()->user_id == Auth::user()->id)
                                    @if($project->allow_project_submission == true) <!-- allow_project_submission true means the review had been submitted, and false means the review is not yet submitted -->
                                        <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                            You had already submitted a review for this project. Waiting for user reply.
                                        </p> 
                                    @endif 
                                @else 
                                    <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                        You had already submitted a review for this project.  
                                    </p>
        
                                @endif
        
                            @else 
                                <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                    You are not a reviewer for this project. 
                                </p>

                            @endif
                        @else 
                            <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                The project has passed all reviews and has been fully approved.
                            </p>
                        @endif
                    @else 

                        <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Please update the project's SHPO number.
                        </p>
                    @endif
                    
                </div>
                <!-- End Col -->

                @if(!empty($project->shpo_number))
                    @if($project->allow_project_submission == false && $project->status !== "approved") <!-- if the submission for user is not allowed, it means a feedback is needed by the reviewer and this is opened --> 
                        

                        

                        <!-- check if this user is the current reviewer for this project -->
                        @if($project->getCurrentReviewer()->user_id == Auth::user()->id)
                            <!-- Grid -->
                            <div class="grid grid-cols-12 gap-x-2  ">
                                
                                
                                


                                <div class="space-y-2 col-span-12   ">
                                    <label for="project_review" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                        Review
                                    </label>

                                    <textarea
                                    
                                    autofocus autocomplete="project_review"
                                    wire:model="project_review"
                                    id="project_review"  class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder=""></textarea>

                                    @error('project_review')
                                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                    @enderror


                                </div>

                                


                            

                                <div class="space-y-2 col-span-12     ">
                                    <label for="attachments" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
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
                                            <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                                Set the time line for the user to send a response to the review
                                            </label>
                                        </div>
    

                                        <div class="space-y-2 col-span-12 sm:col-span-3  ">
                                            <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                                Submitter duration
                                            </label>

                                            <input
                                            min="1"
                                            autofocus autocomplete="submitter_response_duration"
                                            wire:model.live="submitter_response_duration"
                                            
                                            id="submitter_response_duration" type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">

                                            @error('submitter_response_duration')
                                                <p class="text-sm text-red-600 mt-2">{{ $message  }}</p>
                                            @enderror


                                        </div>

                                        <div class="space-y-2 col-span-12 sm:col-span-3  ">
                                            <label for="submitter_response_duration_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                                Submitter duration type
                                            </label>

                                            <select 
                                            autofocus autocomplete="submitter_response_duration_type"
                                            wire:model.live="submitter_response_duration_type"
                                            id="submitter_response_duration_type" 
                                            class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
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
                                            <label for="submitter_due_date" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                                Submitter response due date
                                            </label>

                                            <input readonly 
                                            {{-- autofocus autocomplete="submitter_due_date"
                                            wire:model.live="submitter_due_date" --}}
                                            value="{{ \Carbon\Carbon::parse($submitter_due_date)->format('d M, h:i A') }}"
                                            id="submitter_due_date" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">

                                            @error('submitter_due_date')
                                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                            @enderror


                                        </div>

                                        <div class="space-y-2 col-span-12 mt-1 ">
                                            <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                                Set the time line for the reviewer to review the project
                                            </label>
                                        </div>


                                        <div class="space-y-2 col-span-12 sm:col-span-3  ">
                                            <label for="reviewer_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                                Reviewer duration
                                            </label>
                    
                                            <input
                                            min="1"
                                            autofocus autocomplete="reviewer_response_duration"
                                            wire:model.live="reviewer_response_duration"
                                            id="reviewer_response_duration" type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">
                    
                                            @error('reviewer_response_duration')
                                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                            @enderror
                    
                    
                                        </div>
                    
                                        <div class="space-y-2 col-span-12 sm:col-span-3  ">
                                            <label for="reviewer_response_duration_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                                Reviewer duration type
                                            </label>
                    
                                            <select 
                                            autofocus autocomplete="reviewer_response_duration_type"
                                            wire:model.live="reviewer_response_duration_type"
                                            id="reviewer_response_duration_type" 
                                            class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
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
                                            <label for="reviewer_due_date" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                                Reviewer response due date
                                            </label>
                    
                                            <input readonly 
                                            {{-- autocomplete="reviewer_due_date"
                                            wire:model.live="reviewer_due_date" --}}    
                                            value="{{ \Carbon\Carbon::parse($reviewer_due_date)->format('d M, h:i A') }}"
                                            id="reviewer_due_date" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">
                    
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
                
                @else


                    <!-- Grid -->
                    <div class="grid grid-cols-12 gap-x-2  ">
                                
                                 

                        <div class="space-y-2 col-span-12   ">
                            <label for="shpo_number" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                Project SHPO number
                            </label>

                            <input
                            
                            autofocus autocomplete="shpo_number"
                            wire:model="shpo_number"
                            id="shpo_number"  class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder=""> 

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


                @endif

            </div>
        </div>
        <!-- End Card -->
     
</div>
<!-- End Card Section -->
