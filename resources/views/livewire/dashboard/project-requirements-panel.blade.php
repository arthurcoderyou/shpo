<div class="max-w-[85rem] mx-auto px-4 pt-2 sm:px-6">
    <div class="bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500" role="alert" tabindex="-1" aria-labelledby="hs-with-list-label">
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
                @if (isset($errors['no_reviewers']))
                  <li>
                    No project reviewers have been setup. Please <a href="{{ route('reviewers.create') }}" class="underline">setup reviewers</a> before proceeding.
                  </li>
                @endif

                @if (isset($errors['project_submission_times']))
                  <li>
                    Project submission times have not been set. Please <a href="{{ route('project-timers.edit') }}" class="underline">specify the open and close times</a>.
                  </li>
                  @if (isset($errors['project_submission_open_time']))
                    <li>
                      Project submission open time is required. Please <a href="{{ route('project-timers.edit') }}" class="underline">set the open time</a>.
                    </li>
                  @endif
                  @if (isset($errors['project_submission_close_time']))
                    <li>
                      Project submission close time is required. Please <a href="{{ route('project-timers.edit') }}" class="underline">set the close time</a>.
                    </li>
                  @endif
                @endif

                @if (isset($errors['project_submission_restrict_by_time']))
                  <li>
                    Project submission time restriction has not been set. Please <a href="{{ route('project-timers.edit') }}" class="underline">specify the time restriction</a>.
                  </li>
                @endif

                @if (isset($errors['outside_submission_time']))
                  <li>
                    Project submissions are currently outside the allowed time window. Please <a href="{{ route('project-timers.edit') }}" class="underline">check the submission times</a>.
                  </li>
                @endif
              </ul>
            </div>
          </div>
        </div>
    </div>
</div>