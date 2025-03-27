 
<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">
    <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div>
    
  <!-- Grid -->
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <!-- Projects that needs to be reviewed because project_reviewer->review_status = pending -->
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-800">
      <div class="p-4 md:p-5 flex justify-between gap-x-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">
            Projects pending review
          </p>
          <div class="mt-1 flex items-center gap-x-2">
            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">
              {{ $projects_for_review ?? 0 }}
            </h3>
 
          </div>
        </div>
        <div class="shrink-0 flex justify-center items-center size-[46px] bg-red-500 text-white rounded-full  ">
            
          <svg class="shrink-0 size-5"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M256 32c14.2 0 27.3 7.5 34.5 19.8l216 368c7.3 12.4 7.3 27.7 .2 40.1S486.3 480 472 480L40 480c-14.3 0-27.6-7.7-34.7-20.1s-7-27.8 .2-40.1l216-368C228.7 39.5 241.8 32 256 32zm0 128c-13.3 0-24 10.7-24 24l0 112c0 13.3 10.7 24 24 24s24-10.7 24-24l0-112c0-13.3-10.7-24-24-24zm32 224a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>

        </div>
      </div>

      <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" 
      href="{{ route('project.in_review',['review_status' => 'pending']) }}">
        View 
        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
      </a>
    </div>
    <!-- End Projects that needs to be reviewed because project_reviewer->review_status = pending  -->


    <!-- Projects that are currently in review by the review . project_reviewer->status == true  -->
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-800">
      <div class="p-4 md:p-5 flex justify-between gap-x-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">
            Projects In Review
          </p>
          <div class="mt-1 flex items-center gap-x-2">
            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">
              {{ $in_review_projects ?? 0 }}
            </h3>
 
          </div>
        </div>
        <div class="shrink-0 flex justify-center items-center size-[46px] bg-gray-500 text-white rounded-full  ">
           
          <svg class="shrink-0 size-5"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
          
        </div>
      </div>

      <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" 
      href="{{ route('project.in_review', ) }}">
        View 
        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
      </a>
    </div>
    <!-- End Projects that are currently in review by the review . project_reviewer->status == true  -->


    <!-- Card -->
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-800">
      <div class="p-4 md:p-5 flex justify-between gap-x-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">
            Projects
          </p>
          <div class="mt-1 flex items-center gap-x-2">
            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">
              {{ $all_projects ?? 0 }}
            </h3>
 
          </div>
        </div>
        <div class="shrink-0 flex justify-center items-center size-[46px] bg-blue-400 text-white rounded-full  ">
          <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M336 0c-26.5 0-48 21.5-48 48l0 92.1 71.4 118.4c2.5-1.6 5.4-2.5 8.6-2.5l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-3.5 0 73.8 122.4c12.4 20.6 12.9 46.3 1.2 67.3c-.4 .8-.9 1.6-1.4 2.3L592 512c26.5 0 48-21.5 48-48l0-224c0-26.5-21.5-48-48-48l-24 0 0-72c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 72-40 0 0-144c0-26.5-21.5-48-48-48L336 0zm32 64l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32c0-8.8 7.2-16 16-16zM352 176c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32zm160 96c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32zm16 80l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32c0-8.8 7.2-16 16-16zM224 188.9L283.8 288 223 288l-48 64-24.6-41.2L224 188.9zm29.4-44.2C247.1 134.3 236 128 224 128s-23.1 6.3-29.4 16.7L5.1 458.9c-6.5 10.8-6.7 24.3-.7 35.3S22 512 34.5 512l379.1 0c12.5 0 24-6.8 30.1-17.8s5.8-24.5-.7-35.3L253.4 144.7z"/></svg>
           

        </div>
      </div>

      <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" 
      href="{{ route('project.index') }}">
        View 
        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
      </a>
    </div>
    <!-- End Card -->

    

    <!-- Card -->
    <div class="flex flex-col bg-white border shadow-sm rounded-xl dark:bg-neutral-900 dark:border-neutral-800">
      <div class="p-4 md:p-5 flex justify-between gap-x-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-neutral-500">
            Approved Projects
          </p>
          <div class="mt-1 flex items-center gap-x-2">
            <h3 class="text-xl sm:text-2xl font-medium text-gray-800 dark:text-neutral-200">
              {{ $approved_projects ?? 0 }}
            </h3>
 
          </div>
        </div>
        <div class="shrink-0 flex justify-center items-center size-[46px] bg-lime-500 text-white rounded-full  ">
           
          <svg class="shrink-0 size-5"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M323.8 34.8c-38.2-10.9-78.1 11.2-89 49.4l-5.7 20c-3.7 13-10.4 25-19.5 35l-51.3 56.4c-8.9 9.8-8.2 25 1.6 33.9s25 8.2 33.9-1.6l51.3-56.4c14.1-15.5 24.4-34 30.1-54.1l5.7-20c3.6-12.7 16.9-20.1 29.7-16.5s20.1 16.9 16.5 29.7l-5.7 20c-5.7 19.9-14.7 38.7-26.6 55.5c-5.2 7.3-5.8 16.9-1.7 24.9s12.3 13 21.3 13L448 224c8.8 0 16 7.2 16 16c0 6.8-4.3 12.7-10.4 15c-7.4 2.8-13 9-14.9 16.7s.1 15.8 5.3 21.7c2.5 2.8 4 6.5 4 10.6c0 7.8-5.6 14.3-13 15.7c-8.2 1.6-15.1 7.3-18 15.2s-1.6 16.7 3.6 23.3c2.1 2.7 3.4 6.1 3.4 9.9c0 6.7-4.2 12.6-10.2 14.9c-11.5 4.5-17.7 16.9-14.4 28.8c.4 1.3 .6 2.8 .6 4.3c0 8.8-7.2 16-16 16l-97.5 0c-12.6 0-25-3.7-35.5-10.7l-61.7-41.1c-11-7.4-25.9-4.4-33.3 6.7s-4.4 25.9 6.7 33.3l61.7 41.1c18.4 12.3 40 18.8 62.1 18.8l97.5 0c34.7 0 62.9-27.6 64-62c14.6-11.7 24-29.7 24-50c0-4.5-.5-8.8-1.3-13c15.4-11.7 25.3-30.2 25.3-51c0-6.5-1-12.8-2.8-18.7C504.8 273.7 512 257.7 512 240c0-35.3-28.6-64-64-64l-92.3 0c4.7-10.4 8.7-21.2 11.8-32.2l5.7-20c10.9-38.2-11.2-78.1-49.4-89zM32 192c-17.7 0-32 14.3-32 32L0 448c0 17.7 14.3 32 32 32l64 0c17.7 0 32-14.3 32-32l0-224c0-17.7-14.3-32-32-32l-64 0z"/></svg>
        </div>
      </div>

      <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" 
      href="{{ route('project.index',['project_status' => 'approved']) }}">
        View 
        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
      </a>
    </div>
    <!-- End Card -->

 
  </div>
  <!-- End Grid --> 
</div>
<!-- End Card Section -->