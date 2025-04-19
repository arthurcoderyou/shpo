<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- sweetalert -->
        {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

        <link rel="icon" type="image/png" href="{{ asset('images/GEPA_LOGO.png') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/livewire-dropzone/livewire-dropzone.css') }}"> 

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
 



        {{-- <!-- The callback parameter is required, so we use console.debug as a noop -->
        <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA70BOfcc1ELmwAEmY-rFNkbNauIXT79cA&callback=console.debug&libraries=maps,marker&v=beta">
        </script>
 
        <style>
            /* Always set the map height explicitly to define the size of the div
            * element that contains the map. */
            gmp-map {
                height: 100%;
            }

            /* Optional: Makes the sample page fill the window. */
            html,
            body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
        </style> --}}

        {{-- Open Layer --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css" />
        <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
        {{-- ./Open layer --}}

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @yield('script')

        <!-- Discussion Listeners -->
        <script type="module">

            function showNewDiscussionAlert(message, link) {
                const alertElement = document.getElementById('discussion-alert');
                const messageElement = alertElement.querySelector('h3');
                messageElement.innerHTML = `${message}`;

                const linkElement = alertElement.querySelector('a');
                linkElement.href = link;
                linkElement.innerHTML = 'View Project';
                
                alertElement.classList.remove('hidden');
                alertElement.classList.add('opacity-100');

                setTimeout(() => {
                    alertElement.classList.add('hidden');
                }, 10000);
            }


            const currentUser = {
                id: @json(auth()->user()->id),
                roles: @json(auth()->user()->getRoleNames()), // returns ['Admin', ...]
                reviewerOfProjects: @json(auth()->user()->reviewed_projects->pluck('id')), // assuming relation exists
                myCreatedProjects: @json(auth()->user()->created_projects->pluck('id')), // or custom way
            };
        
            window.Echo.private("project.discussions.global")
                .listen('.create', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('projectDiscussionAdded');


                    const projectId = e.project_id;
                    const is_private = e.is_private;

                    
                    const isAdmin = currentUser.roles.includes('Admin') || currentUser.roles.includes('DSI God Admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url);
                    }
                }).listen('.edited', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('projectDiscussionEdited');


                    const projectId = e.project_id;
                    const is_private = e.is_private;


                    const isAdmin = currentUser.roles.includes('Admin') || currentUser.roles.includes('DSI God Admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url);
                    }
                }).listen('.deleted', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('projectDiscussionDeleted');


                    const projectId = e.project_id;
                    const is_private = e.is_private;


                    const isAdmin = currentUser.roles.includes('Admin') || currentUser.roles.includes('DSI God Admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url);
                    }
                }).listen('.reply', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('projectDiscussionReplied');

                    const projectId = e.project_id;
                    const is_private = e.is_private;

                    const isAdmin = currentUser.roles.includes('Admin') || currentUser.roles.includes('DSI God Admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url);
                    }
                });




        </script>
        <!-- ./ Discussion Listeners -->

         


        <!-- Inside the <head> -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-white">
        <!-- Notification Alert -->
        <div id="dismiss-alert" class="hidden transition duration-300 bg-teal-50 border border-teal-200 text-sm text-teal-800 rounded-lg p-4 fixed top-4 right-4 z-50" role="alert" tabindex="-1" aria-labelledby="hs-dismiss-button-label">

            <div class="flex">
              <div class="shrink-0">
                <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                  <path d="m9 12 2 2 4-4"></path>
                </svg>
              </div>
              <div class="ms-2">
                <h3 id="hs-dismiss-button-label" class="text-sm font-medium">
                  File has been successfully uploaded.
                </h3>
              </div>
              <div class="ps-3 ms-auto">
                <div class="-mx-1.5 -my-1.5">
                  <button type="button" class="inline-flex bg-teal-50 rounded-lg p-1.5 text-teal-500 hover:bg-teal-100 focus:outline-hidden focus:bg-teal-100 " data-hs-remove-element="#dismiss-alert">
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
        <!-- ./ Notification Alert -->


        <!-- Discussion Alert -->
        <div id="discussion-alert" class="hidden transition duration-300 bg-blue-50 border border-blue-200 text-sm text-blue-800 rounded-lg p-4 fixed top-4 right-4 z-50" role="alert" tabindex="-1" aria-labelledby="hs-dismiss-button-label">

            <div class="flex">
              <div class="shrink-0">
                <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path>
                  <path d="m9 12 2 2 4-4"></path>
                </svg>
              </div>
              <div class="ms-3 flex-1">
                <h3 id="hs-dismiss-button-label" class="text-sm font-medium">
                    <!-- JS inserts message here -->
                </h3>
                <a href="" target="_blank" class="inline-block mt-2 text-blue-700 font-medium underline hover:text-blue-900">
                    View Project →
                </a>
            </div>
              <div class="ps-3 ms-auto">
                <div class="-mx-1.5 -my-1.5">
                  <button type="button" class="inline-flex bg-blue-50 rounded-lg p-1.5 text-blue-500 hover:bg-blue-100 focus:outline-hidden focus:bg-blue-100 " data-hs-remove-element="#discussion-alert">
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
        <!-- ./ Discussion Alert -->

        <div class="min-h-screen  ">
 
            <div class="bg-gray-100 text-sm text-gray-700 py-2">
                <div class="max-w-[85rem] mx-auto px-4   sm:px-6">
                    For feedback or comments, please email <a href="mailto:portal@khlgassociates.com" class="text-blue-600 underline">portal@khlgassociates.com</a>.
                </div>
                
            </div> 


            @auth
                <livewire:layout.navigation />
            @endauth
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow sm:hidden">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif


            


            <!-- Page Content -->

            <main class=" ">
                {{ $slot }}
            </main>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                if (window.Livewire) {
                    window.Livewire.restart();
                }
            });
        </script>

        @include('sweetalert::alert')
        <!-- Push custom scripts from views -->
        @stack('scripts')  <!-- This will include any scripts pushed to the stack -->

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dismissAlert = document.getElementById('dismiss-alert');
                const dismissButton = document.querySelector('[data-hs-remove-element="#dismiss-alert"]');
        
                if (dismissButton && dismissAlert) {
                    dismissButton.addEventListener('click', function () {
                        // Add 'hidden' class to hide the alert
                        dismissAlert.classList.add('hidden');
                    });
                }


                const discussionAlert = document.getElementById('discussion-alert');
                const discussionButton = document.querySelector('[data-hs-remove-element="#discussion-alert"]');
        
                if (discussionButton && discussionAlert) {
                    discussionButton.addEventListener('click', function () {
                        // Add 'hidden' class to hide the alert
                        discussionAlert.classList.add('hidden');
                    });
                }


            });
        </script>

        <!-- Before the closing </body> tag -->
        @livewireScripts
    </body>
</html>
