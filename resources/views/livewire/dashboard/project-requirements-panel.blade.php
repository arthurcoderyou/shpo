<div class="max-w-[85rem] mx-auto px-4 pt-2 sm:px-6">
     
    @if (  
      (!isset($errors['no_reviewers']) || $errors['no_reviewers'] == false)  
      &&  (!isset($errors['document_types_missing_reviewers']) || $errors['document_types_missing_reviewers'] == false) 
      &&  (!isset($errors['no_initial_reviewers']) || $errors['no_initial_reviewers'] == false) 
      &&  (!isset($errors['no_final_reviewers']) || $errors['no_final_reviewers'] == false) 
      &&  (!isset($errors['project_submission_times']) || $errors['project_submission_times'] == false )
      &&  (!isset($errors['no_document_types']) || $errors['no_document_types'] == false)
      )

      @if(!request()->routeIs('reviewer.index')) <!-- route to not display the success message-->
        <div class="bg-green-50 border border-green-200 text-sm text-green-800 rounded-lg p-4 " role="alert" id="requirement-panel-success">
          <div class="flex">
            <div class="shrink-0">
              <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="ms-4">
              <h3 class="text-sm font-semibold">
                Project Submission Note
              </h3>
              <div class="mt-2 text-sm text-green-700 ">
                {{ $projectTimer->message_on_open_close_time }}
              </div>
            </div>

            <div class="ps-3 ms-auto">
                <div class="-mx-1.5 -my-1.5">
                <button type="button" class="inline-flex bg-green-50 rounded-lg p-1.5 text-green-500 hover:bg-green-100 focus:outline-hidden focus:bg-green-100 " data-hs-remove-element="#requirement-panel-success">
                    <span class="sr-only">Dismiss</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
            </div>


          </div>
        </div>
    @endif

    @if(request()->routeIs('reviewer.index') && (!isset($errors['no_reviewers']) || $errors['no_reviewers'] == false)  
      &&  (!isset($errors['document_types_missing_reviewers']) || $errors['document_types_missing_reviewers'] == false)  
      &&  (!isset($errors['no_initial_reviewers']) || $errors['no_initial_reviewers'] == false)
      &&  (!isset($errors['no_final_reviewers']) || $errors['no_final_reviewers'] == false)
      ) <!-- route to display the success message about the reviewers -->
        <div class="bg-green-50 border border-green-200 text-sm text-green-800 rounded-lg p-4 " role="alert" id="requirement-panel-success">
          <div class="flex">
            <div class="shrink-0">
              <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
            </div>
            <div class="ms-4">
              <h3 class="text-sm font-semibold">
                Reviewers are all set!
              </h3>
              <div class="mt-2 text-sm text-green-700 ">
                Reviewer order has been saved and is now reflected across the system.
              </div>
            </div>

            <div class="ps-3 ms-auto">
                <div class="-mx-1.5 -my-1.5">
                <button type="button" class="inline-flex bg-green-50 rounded-lg p-1.5 text-green-500 hover:bg-green-100 focus:outline-hidden focus:bg-green-100 " data-hs-remove-element="#requirement-panel-success">
                    <span class="sr-only">Dismiss</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
            </div>


          </div>
        </div>
    @endif


    @else 

      <div id="requirement-panel-error" class="bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4 " role="alert" tabindex="-1" aria-labelledby="hs-with-list-label">
        <div class="flex">
            <div class="shrink-0">
              <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="m15 9-6 6"></path>
                <path d="m9 9 6 6"></path>
              </svg>
            </div>
            <div class="ms-4">
              <h3 id="hs-with-list-label" class="text-sm font-semibold">
                Project submission is currently not allowed.
              </h3>
              <div class="mt-2 text-sm text-red-700 ">
                <ul class="list-disc space-y-1 ps-5">

                  {{-- No Initial Reviewers  --}}
                  @if (isset($errors['no_initial_reviewers']) && $errors['no_initial_reviewers'] === true)
                    @if(auth()->user()->can('system access global admin') || authorizeWithModulesByAllRequiredActions(['Reviewer']))
                      <li>
                        No <strong>initial</strong> reviewers have been assigned. Please
                        <a href="{{ route('reviewer.index') }}"
                        wire:navigate
                        class="underline">assign initial reviewers</a> to proceed.
                      </li>
                    @else
                      <li>
                        No <strong>initial</strong> reviewers have been assigned. Please wait for the administrator to assign them.
                      </li>
                    @endrole
                  @endif
                 

                  {{-- No Final Reviewers  --}}
                  @if (isset($errors['no_final_reviewers']) && $errors['no_final_reviewers'] === true)
                    @if(auth()->user()->can('system access global admin') || authorizeWithModulesByAllRequiredActions(['Reviewer']))
                      <li>
                        No <strong>final</strong> reviewers have been assigned. Please
                        <a href="{{ route('reviewer.index') }}" 
                        wire:navigate
                        class="underline">assign final reviewers</a> to proceed.
                      </li>
                    @else
                      <li>
                        No <strong>final</strong> reviewers have been assigned. Please wait for the administrator to assign them.
                      </li>
                    @endrole
                  @endif  

                 

                  @if ( $errors['no_administrators'] == true)
                    @if(auth()->user()->can('system access global admin') || authorizeWithModulesByAllRequiredActions(['User'])) 
                      <li>
                        No project administrators have been setup. Please <a href="{{ route('user.index') }}" wire:navigate class="underline">add a administrator</a> before proceeding.
                          
                      </li> 
                    @else
                      <li>
                        No project administrators have been setup. Please wait for the administrator to setup the administrators.
                      </li>
                    @endif
                  @endif



                  @if (isset($errors['no_document_types']) && $errors['no_document_types'] == true)
                    @if(auth()->user()->can('system access global admin') || authorizeWithModulesByAllRequiredActions(['Reviewer'])) 
                      <li>
                        No project reviewers have been setup. Please <a href="{{ route('reviewer.index') }}" wire:navigate class="underline">setup reviewers</a> before proceeding.
                        
                      </li> 
                    @else
                      <li>
                        No project reviewers have been setup. Please wait for the administrator to setup the reviewers.
                      </li>
                    @endif
                  @endif


                  @if (isset($errors['document_types_missing_reviewers']) && $errors['document_types_missing_reviewers'] === true)
                    @if(auth()->user()->can('system access global admin') || authorizeWithModulesByAllRequiredActions(['Reviewer']) ) 
                        <li>
                            Some document types have no project reviewers assigned.
                            Please <a href="{{ route('reviewer.index') }}" wire:navigate class="underline">set up reviewers</a> before proceeding.
                
                            @if (!empty($documentTypesWithoutReviewers))
                                <br>
                                <span class="text-sm text-red-500 font-semibold">Missing reviewers for:</span>
                                <ul class="list-disc ml-6 text-sm text-gray-700">
                                    @foreach ($documentTypesWithoutReviewers as $docName)
                                        <li>{{ $docName }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </li> 
                    @else
                        <li>
                            Some document types have no project reviewers assigned.
                            Please wait for the administrator to set them up.
                
                            @if (!empty($documentTypesWithoutReviewers))
                                <br>
                                <span class="text-sm text-red-500 font-semibold">Currently missing reviewers for:</span>
                                <ul class="list-disc ml-6 text-sm text-gray-700">
                                    @foreach ($documentTypesWithoutReviewers as $docName)
                                        <li>{{ $docName }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endrole
                  @endif
              

                  @if (isset($errors['project_submission_times']) && $errors['project_submission_times'] == true)
                    @if(auth()->user()->can('system access global admin') || authorizeWithModulesByAllRequiredActions(['Timer']))
                      <li>
                        Project submission times have not been set. Please <a href="{{ route('project_timer.index') }}" wire:navigate class="underline">specify the open and close times</a>.
                      </li>
                    @else
                      <li>
                        Project submission times have not been set. Please wait for the administrator to set the submission times.
                      </li>
                    @endrole
                  @endif


                  @if (isset($errors['no_document_types']) && $errors['no_document_types'] == true)
                    @if(auth()->user()->can('system access global admin') || authorizeWithModulesByAllRequiredActions(['Document Type']) )
                      <li>
                        No document types have been setup. Please <a href="{{ route('document_type.index') }}" wire:navigate class="underline">setup document types</a> before proceeding.
                      </li>
                    @else
                      <li>
                        No document types have been setup. Please wait for the administrator to setup the document types.
                      </li>
                    @endrole
                  @endif



                  
                </ul>
              </div>
            </div>

            <div class="ps-3 ms-auto">
                <div class="-mx-1.5 -my-1.5">
                <button type="button" class="inline-flex bg-red-50 rounded-lg p-1.5 text-red-500 hover:bg-red-100 focus:outline-hidden focus:bg-red-100 " data-hs-remove-element="#requirement-panel-error">
                    <span class="sr-only">Dismiss</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                    </svg>
                </button>
                </div>
            </div>


        </div>
      </div>

    @endif
  

</div>