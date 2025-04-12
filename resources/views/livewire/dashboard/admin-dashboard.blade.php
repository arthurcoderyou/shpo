 
<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-10   mx-auto">
    <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div>
     
    <!-- Grid -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">

      <!-- Card -->
      <div class="flex flex-col bg-white border shadow-sm rounded-xl ">
        <div class="p-4 md:p-5 flex justify-between gap-x-3">
          <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 ">
              Users Role update pending
            </p>
            <div class="mt-1 flex items-center gap-x-2">
              <h3 class="text-xl sm:text-2xl font-medium text-gray-800 ">
                {{ $usersUpdatePending ?? 0 }}
              </h3>

                
            </div>
          </div>
          <div class="shrink-0 flex justify-center items-center size-[46px] bg-red-600 text-white rounded-full ">
             
            <svg class="shrink-0 size-5"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M304 128a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM49.3 464l349.5 0c-8.9-63.3-63.3-112-129-112l-91.4 0c-65.7 0-120.1 48.7-129 112zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3z"/></svg>
          
          
          </div>
        </div>

        <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl " 
        href="{{ route('user.index',['selected_role' => 'no_role','role_request' => 'user']) }}">
          View
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <!-- End Card -->

      <!-- Card -->
      <div class="flex flex-col bg-white border shadow-sm rounded-xl ">
        <div class="p-4 md:p-5 flex justify-between gap-x-3">
          <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 ">
              Reviewer Role update pending
            </p>
            <div class="mt-1 flex items-center gap-x-2">
              <h3 class="text-xl sm:text-2xl font-medium text-gray-800 ">
                {{ $reviewersUpdatePending ?? 0 }}
              </h3>

              



            </div>
          </div>
          <div class="shrink-0 flex justify-center items-center size-[46px] bg-blue-600 text-white rounded-full ">
            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
        </div>

        <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl " 
        href="{{ route('user.index',['selected_role' => 'no_role','role_request' => 'reviewer']) }}">
          View
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <!-- End Card -->


      {{-- <!-- Card -->
      <div class="flex flex-col bg-white border shadow-sm rounded-xl ">
        <div class="p-4 md:p-5 flex justify-between gap-x-3">
          <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 ">
              Total users
            </p>
            <div class="mt-1 flex items-center gap-x-2">
              <h3 class="text-xl sm:text-2xl font-medium text-gray-800 ">
                {{ $usersAllTime ?? 0 }}
              </h3>

              @if($userChangeStatus == "up")
                <span class="flex items-center gap-x-1 text-green-600">
                  <svg class="inline-block size-5 self-center" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
                  <span class="inline-block text-lg">
                    {{ $userPercentage }}
                  </span>
                </span>
              @elseif ($userChangeStatus == "down")
                <span class="flex items-center gap-x-1 text-red-600">
                  <svg class="inline-block size-4 self-center" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 17 13.5 8.5 8.5 13.5 2 7"/><polyline points="16 17 22 17 22 11"/></svg>
                  <span class="inline-block text-lg">
                    {{ $userPercentage }}
                  </span>
                </span>

              @endif
              



            </div>
          </div>
          <div class="shrink-0 flex justify-center items-center size-[46px] bg-blue-600 text-white rounded-full ">
            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
        </div>

        <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl " 
        href="{{ route('user.index') }}">
          View
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <!-- End Card --> --}}

      <!-- Card -->
      <div class="flex flex-col bg-white border shadow-sm rounded-xl ">
        <div class="p-4 md:p-5 flex justify-between gap-x-3">
          <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 ">
              Project pending Review
            </p>
            <div class="mt-1 flex items-center gap-x-2">
              <h3 class="text-xl sm:text-2xl font-medium text-gray-800 ">
                {{ $projects_for_review ?? 0 }}
              </h3>
  
            </div>
          </div>
          <div class="shrink-0 flex justify-center items-center size-[46px] bg-yellow-500 text-white rounded-full  ">
            
            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M75 75L41 41C25.9 25.9 0 36.6 0 57.9L0 168c0 13.3 10.7 24 24 24l110.1 0c21.4 0 32.1-25.9 17-41l-30.8-30.8C155 85.5 203 64 256 64c106 0 192 86 192 192s-86 192-192 192c-40.8 0-78.6-12.7-109.7-34.4c-14.5-10.1-34.4-6.6-44.6 7.9s-6.6 34.4 7.9 44.6C151.2 495 201.7 512 256 512c141.4 0 256-114.6 256-256S397.4 0 256 0C185.3 0 121.3 28.7 75 75zm181 53c-13.3 0-24 10.7-24 24l0 104c0 6.4 2.5 12.5 7 17l72 72c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-65-65 0-94.1c0-13.3-10.7-24-24-24z"/></svg>
          </div>
        </div>

        <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl " 
        href="{{ route('project.in_review') }}">
          View 
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <!-- End Card -->
  
 
      <!-- Card -->
      <div class="flex flex-col bg-white border shadow-sm rounded-xl ">
        <div class="p-4 md:p-5 flex justify-between gap-x-3">
          <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 ">
              Project Pending Update
            </p>
            <div class="mt-1 flex items-center gap-x-2">
              <h3 class="text-xl sm:text-2xl font-medium text-gray-800 ">
                {{ $pending_update_projects ?? 0 }}
              </h3>
  
            </div>
          </div>
          <div class="shrink-0 flex justify-center items-center size-[46px] bg-yellow-800 text-white rounded-full  ">
            
            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152L0 424c0 48.6 39.4 88 88 88l272 0c48.6 0 88-39.4 88-88l0-112c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 112c0 22.1-17.9 40-40 40L88 464c-22.1 0-40-17.9-40-40l0-272c0-22.1 17.9-40 40-40l112 0c13.3 0 24-10.7 24-24s-10.7-24-24-24L88 64z"/></svg>
          </div>
        </div>

        <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl " 
        href="{{ route('project.pending_project_update') }}">
          View 
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <!-- End Card -->


      <!-- Card -->
      <div class="flex flex-col bg-white border shadow-sm rounded-xl ">
        <div class="p-4 md:p-5 flex justify-between gap-x-3">
          <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 ">
              Project Reviews
            </p>
            <div class="mt-1 flex items-center gap-x-2">
              <h3 class="text-xl sm:text-2xl font-medium text-gray-800 ">
                {{ $project_reviews ?? 0 }}
              </h3>
  
            </div>
          </div>
          <div class="shrink-0 flex justify-center items-center size-[46px] bg-orange-500 text-white rounded-full  ">
            
            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
          </div>
        </div>

        <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl " 
        href="{{ route('review.index') }}">
          View 
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <!-- End Card -->


      <!-- Card -->
      <div class="flex flex-col bg-white border shadow-sm rounded-xl ">
        <div class="p-4 md:p-5 flex justify-between gap-x-3">
          <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 ">
              Projects
            </p>
            <div class="mt-1 flex items-center gap-x-2">
              <h3 class="text-xl sm:text-2xl font-medium text-gray-800 ">
                {{ $all_projects ?? 0 }}
              </h3>
  
            </div>
          </div>
          <div class="shrink-0 flex justify-center items-center size-[46px] bg-blue-400 text-white rounded-full  ">
            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M336 0c-26.5 0-48 21.5-48 48l0 92.1 71.4 118.4c2.5-1.6 5.4-2.5 8.6-2.5l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-3.5 0 73.8 122.4c12.4 20.6 12.9 46.3 1.2 67.3c-.4 .8-.9 1.6-1.4 2.3L592 512c26.5 0 48-21.5 48-48l0-224c0-26.5-21.5-48-48-48l-24 0 0-72c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 72-40 0 0-144c0-26.5-21.5-48-48-48L336 0zm32 64l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32c0-8.8 7.2-16 16-16zM352 176c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32zm160 96c0-8.8 7.2-16 16-16l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32zm16 80l32 0c8.8 0 16 7.2 16 16l0 32c0 8.8-7.2 16-16 16l-32 0c-8.8 0-16-7.2-16-16l0-32c0-8.8 7.2-16 16-16zM224 188.9L283.8 288 223 288l-48 64-24.6-41.2L224 188.9zm29.4-44.2C247.1 134.3 236 128 224 128s-23.1 6.3-29.4 16.7L5.1 458.9c-6.5 10.8-6.7 24.3-.7 35.3S22 512 34.5 512l379.1 0c12.5 0 24-6.8 30.1-17.8s5.8-24.5-.7-35.3L253.4 144.7z"/></svg>
            

          </div>
        </div>

        <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl " 
        href="{{ route('project.index') }}">
          View 
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <!-- End Card -->

      <!-- Card -->
      <div class="flex flex-col bg-white border shadow-sm rounded-xl ">
        <div class="p-4 md:p-5 flex justify-between gap-x-3">
          <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 ">
              Projects In Review
            </p>
            <div class="mt-1 flex items-center gap-x-2">
              <h3 class="text-xl sm:text-2xl font-medium text-gray-800 ">
                {{ $in_review_projects ?? 0 }}
              </h3>
  
            </div>
          </div>
          <div class="shrink-0 flex justify-center items-center size-[46px] bg-gray-500 text-white rounded-full  ">
            
            <svg class="shrink-0 size-5"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
            
          </div>
        </div>

        <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl " 
        href="{{ route('project.index',['project_status' => 'in_review']) }}">
          View 
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <!-- End Card -->

      <!-- Card -->
      <div class="flex flex-col bg-white border shadow-sm rounded-xl ">
        <div class="p-4 md:p-5 flex justify-between gap-x-3">
          <div>
            <p class="text-xs uppercase tracking-wide text-gray-500 ">
              Approved Projects
            </p>
            <div class="mt-1 flex items-center gap-x-2">
              <h3 class="text-xl sm:text-2xl font-medium text-gray-800 ">
                {{ $approved_projects ?? 0 }}
              </h3>
  
            </div>
          </div>
          <div class="shrink-0 flex justify-center items-center size-[46px] bg-lime-500 text-white rounded-full  ">
            
            <svg class="shrink-0 size-5"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M323.8 34.8c-38.2-10.9-78.1 11.2-89 49.4l-5.7 20c-3.7 13-10.4 25-19.5 35l-51.3 56.4c-8.9 9.8-8.2 25 1.6 33.9s25 8.2 33.9-1.6l51.3-56.4c14.1-15.5 24.4-34 30.1-54.1l5.7-20c3.6-12.7 16.9-20.1 29.7-16.5s20.1 16.9 16.5 29.7l-5.7 20c-5.7 19.9-14.7 38.7-26.6 55.5c-5.2 7.3-5.8 16.9-1.7 24.9s12.3 13 21.3 13L448 224c8.8 0 16 7.2 16 16c0 6.8-4.3 12.7-10.4 15c-7.4 2.8-13 9-14.9 16.7s.1 15.8 5.3 21.7c2.5 2.8 4 6.5 4 10.6c0 7.8-5.6 14.3-13 15.7c-8.2 1.6-15.1 7.3-18 15.2s-1.6 16.7 3.6 23.3c2.1 2.7 3.4 6.1 3.4 9.9c0 6.7-4.2 12.6-10.2 14.9c-11.5 4.5-17.7 16.9-14.4 28.8c.4 1.3 .6 2.8 .6 4.3c0 8.8-7.2 16-16 16l-97.5 0c-12.6 0-25-3.7-35.5-10.7l-61.7-41.1c-11-7.4-25.9-4.4-33.3 6.7s-4.4 25.9 6.7 33.3l61.7 41.1c18.4 12.3 40 18.8 62.1 18.8l97.5 0c34.7 0 62.9-27.6 64-62c14.6-11.7 24-29.7 24-50c0-4.5-.5-8.8-1.3-13c15.4-11.7 25.3-30.2 25.3-51c0-6.5-1-12.8-2.8-18.7C504.8 273.7 512 257.7 512 240c0-35.3-28.6-64-64-64l-92.3 0c4.7-10.4 8.7-21.2 11.8-32.2l5.7-20c10.9-38.2-11.2-78.1-49.4-89zM32 192c-17.7 0-32 14.3-32 32L0 448c0 17.7 14.3 32 32 32l64 0c17.7 0 32-14.3 32-32l0-224c0-17.7-14.3-32-32-32l-64 0z"/></svg>
          </div>
        </div>

        <a class="py-3 px-4 md:px-5 inline-flex justify-between items-center text-sm text-gray-600 border-t border-gray-200 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 rounded-b-xl " 
        href="{{ route('project.index',['project_status' => 'approved']) }}">
          View 
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </a>
      </div>
      <!-- End Card -->






    </div>
    <!-- End Grid --> 

    <!-- User Report -->
    <div class="relative overflow-hidden">
        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10 ">
            <div class="text-center ">

                <h1 class="text-4xl sm:text-6xl font-bold text-gray-800 ">
                    <span class="text-sky-500">Users</span> Report
                </h1>

                <div class="mt-7 sm:mt-12 mx-auto max-w-full relative">
                    <div class="grid grid-cols-12 gap-6">


                        <div class="col-span-12  md:col-span-6">
                            <canvas wire:ignore id="data_user_count_table_report" class="w-auto h-auto "></canvas>
                        </div>
 
                        <div class="col-span-12 md:col-span-6">
                            <canvas wire:ignore id="data_user_registered_count_table_report" class="w-auto h-auto "></canvas>
                        </div>
                        

                    </div>

                </div>
 
            </div>
        </div>

        <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10 ">
          <div class="text-center ">

              <h1 class="text-4xl sm:text-6xl font-bold text-gray-800 ">
                  <span class="text-sky-500">Projects</span> Report
              </h1>

              <div class="mt-7 sm:mt-12 mx-auto max-w-full relative">
                  <div class="grid grid-cols-12 gap-6">

                    <div class="col-span-12 md:col-span-6">
                        <canvas wire:ignore id="data_project_count_per_status_table_report" class="w-auto h-auto "></canvas>
                    </div> 

                    <div class="col-span-12 md:col-span-6">
                        <canvas wire:ignore id="data_project_count_per_month_table_report" class="w-auto h-auto "></canvas>
                    </div>
                  
                    <div class="col-span-12  md:col-span-4">
                        <canvas wire:ignore id="data_project_average_approval_time_table_report" class="w-auto h-auto "></canvas>
                    </div>


                    <div class="col-span-12  md:col-span-4">
                        <canvas wire:ignore id="data_project_average_response_time_table_report" class="w-auto h-auto "></canvas>
                    </div>

                    <div class="col-span-12  md:col-span-4">
                        <canvas wire:ignore id="data_project_average_review_time_table_report" class="w-auto h-auto "></canvas>
                    </div>
                    
                    
                      

                  </div>

              </div>

          </div>
      </div>



    </div>
    <!-- ./User Report -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- User Report  -->
    <script>

      /* Total User per Role Report  */
        const ctx_data_user_count_table_report = document.getElementById('data_user_count_table_report');

        const data_user_count_table = @json($data_user_count_table );
        const user_count_table_labels = data_user_count_table.map(item => item.label);
        const user_count_table_values = data_user_count_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_user_count_table_report, {
            type: 'bar',
            data: {
            labels: user_count_table_labels,
            datasets: [{
                label: '# of Total Users',
                data: user_count_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {
            scales: {
                y: {
                  beginAtZero: true,
                  suggestedMin: 0,   // Ensure small values are visible
                 // Adjust based on your range
                  ticks: {
                      stepSize: 5.0005, // Set a step size suitable for small values
                      callback: function(value) {
                          return value.toFixed(5); // Display full decimal precision
                      }
                  }
                }
            }
            }
        });
      /* ./ Total User per Role Report  */



      /* Total Registered User Report  */
        const ctx_data_user_registered_count_table_report = document.getElementById('data_user_registered_count_table_report');

        const data_user_registered_count_table = @json($data_user_registered_count_table );
        const user_registered_count_table_labels = data_user_registered_count_table.map(item => item.label);
        const user_registered_count_table_values = data_user_registered_count_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_user_registered_count_table_report, {
            type: 'bar',
            data: {
            labels: user_registered_count_table_labels,
            datasets: [{
                label: '# of Total Registered Users',
                data: user_registered_count_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {
            scales: {
                y: {
                  beginAtZero: true,
                  suggestedMin: 0,   // Ensure small values are visible
                 // Adjust based on your range
                  ticks: {
                      stepSize: 5.0005, // Set a step size suitable for small values
                      callback: function(value) {
                          return value.toFixed(5); // Display full decimal precision
                      }
                  }
                }
            }
            }
        });
      /* ./ Total Registered User Report  */


    </script>
    <!-- ./ User Report  -->


     <!-- Project Report  -->
     <script>

      /* Total Project per Status  */
        const ctx_data_project_count_per_status_table_report = document.getElementById('data_project_count_per_status_table_report');

        const data_project_count_per_status_table = @json($data_project_count_per_status_table );
        const project_count_per_status_table_labels = data_project_count_per_status_table.map(item => item.label);
        const project_count_per_status_table_values = data_project_count_per_status_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_project_count_per_status_table_report, {
            type: 'bar',
            data: {
            labels: project_count_per_status_table_labels,
            datasets: [{
                label: '# of Total Projects',
                data: project_count_per_status_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {
            scales: {
                y: {
                  beginAtZero: true,
                  suggestedMin: 0,   // Ensure small values are visible
                 // Adjust based on your range
                  ticks: {
                      stepSize: 5.0005, // Set a step size suitable for small values
                      callback: function(value) {
                          return value.toFixed(5); // Display full decimal precision
                      }
                  }
                }
            }
            }
        });
      /* ./ Total Project per Status  */


      /* Total Project per month */
        const ctx_data_project_count_per_month_table_report = document.getElementById('data_project_count_per_month_table_report');

        const data_project_count_per_month_table = @json($data_project_count_per_month_table );
        const project_count_per_month_table_labels = data_project_count_per_month_table.map(item => item.label);
        const project_count_per_month_table_values = data_project_count_per_month_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_project_count_per_month_table_report, {
            type: 'bar',
            data: {
            labels: project_count_per_month_table_labels,
            datasets: [{
                label: '# of Total Projects per Month',
                data: project_count_per_month_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {
            scales: {
                y: {
                  beginAtZero: false,
                  suggestedMin: 0,   // Ensure small values are visible
                  // max: Math.max(...project_count_per_month_table_values) // Adjust based on your range
                  ticks: {
                      stepSize: 5.0005, // Set a step size suitable for small values
                      callback: function(value) {
                          return value.toFixed(5); // Display full decimal precision
                      }
                  }
                }
            }
            }
        });
      /* ./ Total Project per month */
 
      /* Average Project Approval Time  */
        const ctx_data_project_average_approval_time_table_report = document.getElementById('data_project_average_approval_time_table_report');

        const data_project_average_approval_time_table = @json($data_project_average_approval_time_table );
        const project_average_approval_time_table_labels = data_project_average_approval_time_table.map(item => item.label);
        const project_average_approval_time_table_values = data_project_average_approval_time_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_project_average_approval_time_table_report, {
            type: 'line',
            data: {
            labels: project_average_approval_time_table_labels,
            datasets: [{
                label: 'Average Project Approval time in Hours',
                data: project_average_approval_time_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {


              animations: {
                tension: {
                  duration: 1000,
                  easing: 'linear',
                  from: 1,
                  to: 0,
                  loop: false
                }
              },

              scales: {
                  y: {
                    beginAtZero: true, 
                    suggestedMin: 0,   // Ensure small values are visible
                   // Adjust based on your range
                    ticks: {
                        stepSize: 5.0005, // Set a step size suitable for small values
                        callback: function(value) {
                            return value.toFixed(5); // Display full decimal precision
                        }
                    }



                  }
              }
            }
        });
      /* ./ Average Project Approval Time  */


      /* Average Project Response Time  */
        const ctx_data_project_average_response_time_table_report = document.getElementById('data_project_average_response_time_table_report');

        const data_project_average_response_time_table = @json($data_project_average_response_time_table );
        const project_average_response_time_table_labels = data_project_average_response_time_table.map(item => item.label);
        const project_average_response_time_table_values = data_project_average_response_time_table.map(item => item.value);

            // console.log(data);

        new Chart(ctx_data_project_average_response_time_table_report, {
            type: 'line',
            data: {
            labels: project_average_response_time_table_labels,
            datasets: [{
                label: 'Average Project Response time in Hours',
                data: project_average_response_time_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {


              animations: {
                tension: {
                  duration: 1000,
                  easing: 'linear',
                  from: 1,
                  to: 0,
                  loop: false
                }
              },

              scales: {
                  y: {
                    beginAtZero: true, 
                    suggestedMin: 0,   // Ensure small values are visible
                   // Adjust based on your range
                    ticks: {
                        stepSize: 5.0005, // Set a step size suitable for small values
                        callback: function(value) {
                            return value.toFixed(5); // Display full decimal precision
                        }
                    }



                  }
              }
            }
        });
      /* ./ Average Project Response Time  */

      /* Average Project Review Time  */
        const ctx_data_project_average_review_time_table_report = document.getElementById('data_project_average_review_time_table_report');

        const data_project_average_review_time_table = @json($data_project_average_review_time_table );
        const project_average_review_time_table_labels = data_project_average_review_time_table.map(item => item.label);
        const project_average_review_time_table_values = data_project_average_review_time_table.map(item => item.value); 

            // console.log(data);

        new Chart(ctx_data_project_average_review_time_table_report, {
            type: 'line',
            data: {
            labels: project_average_review_time_table_labels,
            datasets: [{
                label: 'Average Project Review time in Hours',
                data: project_average_review_time_table_values,
                borderWidth: 1,
                backgroundColor: '#0ea5e9', // Set bar color to orange
                borderColor: 'blue',    // Optional: Set border color to orange
            }]
            },
            options: {

              responsive: true,
              maintainAspectRatio: false,
              animations: {
                tension: {
                  duration: 1000,
                  easing: 'linear',
                  from: 1,
                  to: 0,
                  loop: false
                }
              },

              scales: {
                  y: {
                    beginAtZero: true, 
                    suggestedMin: 0,   // Ensure small values are visible
                     
                    ticks: {
                      // autoSkip: false, // Ensure all values are shown

                        stepSize: 5.0005, // Set a step size suitable for small values
                        callback: function(value) {
                            return value.toFixed(5); // Display full decimal precision
                        }
                    }



                  }
              }
            }
        });
      /* ./ Average Project Review Time  */
 
    </script>
    <!-- ./ Project Report  -->





</div>
<!-- End Card Section -->
 
