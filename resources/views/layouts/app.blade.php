<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>


        <script>
            window.currentUser = {
                id: {{ Auth::id()  ?? 0 }} ,
                roles: {!! json_encode(Auth::user()?->getRoleNames() ?? []) !!},
                permissions: {!! json_encode(Auth::user()?->getAllPermissions()->pluck('name') ?? []) !!} ,
                reviewerOfProjects: {!! json_encode(Auth::user()?->reviewed_projects->pluck('id') ?? []) !!},
                myCreatedProjects: {!! json_encode(Auth::user()?->created_projects->pluck('id') ?? []) !!},
                 

                

            }; 
            // console.log('created_projects : ' + window.currentUser.myCreatedProjects);


            // getting page model id information
                // // Only set this if the current route has a {project} parameter
                // window.pageProjectId = @json(optional(request()->route('project'))->id ?? request()->route('project') ?? null);
                // // console.log('pageProjectId : ' + pageProjectId);

                // // permission
                // window.pagePermissionId = @json(optional(request()->route('permission'))->id ?? request()->route('permission') ?? null);
                // // console.log(window.pagePermissionId); // uncomment to know the value of the id 
                // // // user
                // window.pageUserId = @json(optional(request()->route('user'))->id ?? request()->route('user') ?? null);
                // console.log(window.pageUserId);

                
            // ./ getting page model id information
        </script>
        
         
        {{--
        <!-- Arc GIS-->
            <!-- Load Calcite components from CDN -->
            <script type="module" src="https://js.arcgis.com/calcite-components/3.2.1/calcite.esm.js"></script>

            <!-- Load the ArcGIS Maps SDK for JavaScript from CDN -->
            <link rel="stylesheet" href="https://js.arcgis.com/4.33/esri/themes/light/main.css" />
            <script src="https://js.arcgis.com/4.33/"></script>

            <!-- Load Map components from CDN-->
            <script type="module" src="https://js.arcgis.com/4.33/map-components/"></script>
        <!-- ./ Arc GIS-->
         --}}

         
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- sweetalert -->
        {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

        <link rel="icon" type="image/png" href="{{ asset('images/GEPA_LOGO.png') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        {{-- <link rel="stylesheet" href="{{ asset('vendor/livewire-dropzone/livewire-dropzone.css') }}">  --}}

        <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.7.1.js" defer></script>
        <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js" defer></script>


        {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script> --}}

        <!-- Include Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <!-- Include Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr" defer></script>
 
        <script>
            window.userId = @json(auth()->id());
        </script>


        {{-- <style>
            .filepond--credits {
                display: none !important;
            }
        </style> --}}


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
        <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js" defer></script>
        {{-- ./Open layer --}}

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
        

        {{-- Tailwind keyframes (use any of: @layer utilities in a CSS file, or add to your Tailwind config) --}}
        <style> 
            @keyframes progress { 0%{transform:translateX(-100%)} 50%{transform:translateX(60%)} 100%{transform:translateX(180%)} }
            @keyframes spin-slow { to { transform: rotate(360deg) } }

            
            /* Make the intl-tel-input wrapper full width */
            .iti {
                width: 100%;
                display: block;
            }

            /* Ensure the actual input fills the wrapper */
            .iti input.iti__tel-input,
            .iti input[type="tel"] {
                width: 100%;
            }

        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @filepondScripts






        

        <!-- Inside the <head> -->
        @livewireStyles
    </head>
    <body  class="font-sans antialiased bg-white overflow-y-auto scroll-smooth" 
        @if(isset($project)) data-project-id="{{ $project->id }}" @endif 
        >
  
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



        

        {{-- <div class="min-h-screen  ">
 
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
        </div> --}}




          
    <div class="bg-gray-100 text-sm text-gray-700 py-2">
        <div class="max-w-full mx-auto px-4   sm:px-6">
            For feedback or comments, please email <a href="mailto:portal@khlgassociates.com" class="text-blue-600 underline">portal@khlgassociates.com</a>.
        </div>
        
    </div> 


    <div x-data="{ sidebarOpen: false }" class="flex " x-cloak> 

        {{-- @auth --}}
        <!-- Sidebar -->
        <div 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-64 bg-blue-500 text-white transform transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:inset-0"
        >
            <div class="flex items-center justify-between px-4 py-4 border-b border-r border-opacity-10 border-blue-700 bg-white">
                {{-- <h1 class="text-xl font-semibold">🎾 SHPO</h1> --}}
                <h1 class="text-xl font-semibold ">
                    <img src="{{ asset('images/logo-ghrd.png') }}" alt="SHPO Logo">
                </h1>
                <button @click="sidebarOpen = false" class="md:hidden">
                    <svg class="w-6 h-6 text-blue-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <livewire:layout.nav />
        </div>
        {{-- @endauth --}}


        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
 
            <!-- Top Nav -->
            <header class="flex items-center justify-between bg-white shadow px-4 py-3">
                <div class="flex items-center gap-3">

                    @auth
                    <!-- Minimize nav button on mobile screen-->
                    <button @click="sidebarOpen = true" class="md:hidden">
                        <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    @endauth
                    
                    
                    <h2 class="text-lg font-semibold text-gray-800 sm:flex sm:space-x-2 sm:items-center">

                        <img src="{{ asset('images/GEPA_LOGO.png') }}" class="size-10" alt="SHPO Logo">
                        <span class="hidden sm:block text-blue-600">
                            SHPO
                        </span>
                    </h2>
                </div>


                <livewire:layout.profile-nav />
                
            </header>


            <!-- Main content -->
            <main class="flex-1 min-w-0">
                <div class="max-w-full mx-auto min-h-[87vh]"> 
                    {{ $slot }}
                </div>

                {{-- {{ $slot }} --}}

                {{-- @auth
                    <livewire:components.help-widget />
                @endauth --}}
            </main>
             

        </div>

 
    </div>

    <livewire:admin.layout.navigation.topbar.notification-bar />
 
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
        

        <!-- Full Page Loader -->
        <div id="full-page-loader" aria-live="polite" role="status">
            <div style="
                background:#111827;color:#fff;padding:20px 24px;border-radius:14px;
                box-shadow:0 10px 30px rgba(0,0,0,.35);display:flex;gap:12px;align-items:center;min-width:280px;text-align:center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" aria-hidden="true"
                    style="animation:spin 1s linear infinite;">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity=".25"></circle>
                <path fill="currentColor" d="M4 12a8 8 0 0 1 8-8v8z" opacity=".75"></path>
                </svg>
                <div style="font-size:14px;font-weight:600;">Loading data...</div>
            </div>
        </div>
        <!-- ./ Full Page Loader -->

        <!-- Script for Full Page Loader -->
        <script>
            (function() {
                const root = document.documentElement;
                const loader = document.getElementById('full-page-loader');
                // Make sure the page starts in "loading" state
                root.classList.add('loading');

                function showLoader() {
                if (!loader) return;
                    loader.removeAttribute('data-hidden');
                    root.classList.add('loading');
                }
                function hideLoader() {
                    if (!loader) return;
                    loader.setAttribute('data-hidden', 'true');
                    // allow transition to complete before re-enabling scroll
                    setTimeout(() => root.classList.remove('loading'), 300);
                }

                // 1) Hide after everything (images, CSS, JS) is loaded
                window.addEventListener('load', function() {
                    // If you need to wait for *your* init scripts, call hideLoader() after they finish instead.
                    hideLoader();
                });

                // 2) Show immediately when user reloads or navigates away (gives instant feedback)
                window.addEventListener('beforeunload', function() {
                    showLoader();
                });

                // 3) If you use Livewire navigate or Turbo-like partial nav, handle those too (optional)
                document.addEventListener('livewire:navigating', showLoader);
                document.addEventListener('livewire:navigated', hideLoader);

                // Expose manual control for your custom init
                window.__pageLoader = { show: showLoader, hide: hideLoader };
            })();
        </script>
        <!-- ./ Script for Full Page Loader -->    


        <script>
            // Runs after Livewire finishes a wire:navigate swap
            document.addEventListener('livewire:navigated', () => {
                const el = document.querySelector('[autofocus]');
                if (el) {
                el.focus();
                // Optional: select text
                if (typeof el.select === 'function') el.select();
                }
            });
        </script> 
         


        {{-- Session notifications --}}
        <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
        
        <script> 

            const session_notyf = new Notyf({
                duration: 1000,
                position: {
                    x: 'right',
                    y: 'top',
                },
                types: [
                    {
                        type: 'success',
                        background: 'green',
                        duration: 50000,
                        dismissible: true
                    },
                    {
                        type: 'warning',
                        background: 'orange', 
                        icon: { 
                            tagName: 'i',
                            text: 'warning',
                            className: 'notyf-icon material-icons', // add your own class + material icon class
                        },
                        duration: 50000,
                        dismissible: true
                    },
                    {
                        type: 'error',
                        background: 'indianred',
                        icon: { 
                            tagName: 'i',
                            text: 'error',
                            className: 'notyf-icon material-icons', // add your own class + material icon class
                        },
                        duration: 50000,
                        dismissible: true
                    },
                    {
                        type: 'info',
                        background: '#6366F1',
                        icon: { 
                            tagName: 'i',
                            text: 'info',
                            className: 'notyf-icon material-icons', // add your own class + material icon class
                        },
                        duration: 50000,
                        dismissible: true
                    }
                ]
            });

            @if(session('alert.error'))
                session_notyf.open({
                    type: 'error',
                    message: '{{ session('alert.error') }}'
                });
            @elseif(session('alert.success'))
                session_notyf.open({
                    type: 'success',
                    message: '{{ session('alert.success') }}'
                });
            @elseif(session('alert.warning'))
                session_notyf.open({
                    type: 'warning',
                    message: '{{ session('alert.warning') }}'
                });
            @elseif(session('alert.info'))
                session_notyf.open({
                    type: 'info',
                    message: '{{ session('alert.info') }}'
                });
            @endif

            // event listener of the event : notify
            document.addEventListener('livewire:init', () => {
                Livewire.on('notify', ({ type, message }) => {
                    session_notyf.open({ type, message });
                });
            });

        </script>

        {{-- ./ Session notifications --}}


    </body>

  











</html>
