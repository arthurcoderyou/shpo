<div


>
    
    <div class="grid grid-cols-12 gap-3 text-start">
  
        <div class="col-span-12 
            
            ">  
            <div class="rounded-2xl border border-slate-200 ">


                <div class="  p-4"> 
                      
                    {{-- <div class="flex justify-between" 
                    
                    x-data="{ 
                        openRereview: false,
                        openPreReview: false,
                            
                    }"
                    
                    >

                        <div> 
                            <h2 class="text-lg font-semibold text-gray-800 ">
                                Review 
                            </h2>
   
                        </div>

                        

                    </div> --}}



                    <div class="grid grid-cols-12 space-y-3  mt-2.5"  
                         
                        class="relative"  
                    
                    >
                        



                        <!-- Review Decision -->
                        <fieldset class="col-span-12">
                            <legend class="text-sm font-semibold text-slate-900">{{ $project->name ?? "Project Unnamed" }}</legend>
                            <p class="mt-1 text-xs text-slate-500">{{ $project_document->document_type->name ?? "Document Unnamed" }}</p>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">


                                
                                @if($this->checkIfLastInReviewerList())
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

                        {{-- <div class="col-span-12 space-y-2">
                            <label for="attachments" class="inline-block text-sm font-medium text-gray-800">
                                Attachments (Optional)
                            </label>

                            <div class="relative">
                                <input
                                    id="attachments"
                                    type="file"
                                    wire:model="attachments"
                                    multiple
                                    accept=".png,.jpeg,.jpg,.pdf,.docx,.xlsx,.csv,.txt,.zip"
                                    class="block w-full text-sm text-gray-600
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-gray-100 file:text-gray-700
                                        hover:file:bg-gray-200
                                        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                />
                            </div>

                            <p class="text-xs text-gray-500">
                                Allowed file types: PNG, JPG, PDF, DOCX, XLSX, CSV, TXT, ZIP (Max 20MB per file)
                            </p>

                            @error('attachments.*')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div> --}}


                        <!-- Actions -->
                        <div class="col-span-12 mt-4 flex flex-wrap items-center gap-2"
                        
                        
                        
                        >

                            
                            <button type="button"
                                    onclick="confirm('Are you sure, you want to save this review?') || event.stopImmediatePropagation()"
                                    wire:click.prevent="save"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-green-900 px-4 py-2 text-sm font-medium text-white hover:bg-green-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-700">
                                Submit Review
                            </button> 





                        </div>



                    </div> 


                </div>

            </div>


        </div>
                
  
        
        
    
    </div> 




</div>
