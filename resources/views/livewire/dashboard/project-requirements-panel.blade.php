<div class="max-w-[85rem] mx-auto px-4 pt-2 sm:px-6">
    

    @if ( (!isset($errors['no_reviewers']) || $errors['no_reviewers'] == false)  && (!isset($errors['project_submission_times']) || $errors['project_submission_times'] == false ) )
      <div class="bg-green-50 border border-green-200 text-sm text-green-800 rounded-lg p-4 dark:bg-green-800/10 dark:border-green-900 dark:text-green-500" role="alert" id="requirement-panel-success">
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
            <div class="mt-2 text-sm text-green-700 dark:text-green-400">
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

    @else 

      <div id="requirement-panel-error" class="bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500" role="alert" tabindex="-1" aria-labelledby="hs-with-list-label">
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
              <div class="mt-2 text-sm text-red-700 dark:text-red-400">
                <ul class="list-disc space-y-1 ps-5">
                  @if (isset($errors['no_reviewers']) && $errors['no_reviewers'] == true)
                    @role('Admin|DSI God Admin')
                      <li>
                        No project reviewers have been setup. Please <a href="{{ route('reviewer.index') }}" class="underline">setup reviewers</a> before proceeding.
                      </li>
                    @else
                      <li>
                        No project reviewers have been setup. Please wait for the administrator to setup the reviewers.
                      </li>
                    @endrole
                  @endif

                  @if (isset($errors['project_submission_times']) && $errors['project_submission_times'] == true)
                    @role('Admin|DSI God Admin')
                      <li>
                        Project submission times have not been set. Please <a href="{{ route('project_timer.index') }}" class="underline">specify the open and close times</a>.
                      </li>
                    @else
                      <li>
                        Project submission times have not been set. Please wait for the administrator to set the submission times.
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