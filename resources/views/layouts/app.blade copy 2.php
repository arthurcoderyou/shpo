<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @yield('script')
        @auth
        <!-- Discussion Listeners -->
        <script type="module">

            const pageProjectId = document.getElementById('project-wrapper')?.dataset.projectId;



            function showNewDiscussionAlert(message, link, linkText) {
                const alertElement = document.getElementById('discussion-alert');
                const messageElement = alertElement.querySelector('h3');
                messageElement.innerHTML = `${message}`;

                const linkElement = alertElement.querySelector('a');
                linkElement.href = link;
                linkElement.innerHTML = linkText;
                
                alertElement.classList.remove('hidden');
                alertElement.classList.add('opacity-100');

                setTimeout(() => {
                    alertElement.classList.add('hidden');
                }, 10000);
            }


            function showAlert(message) {
                const alertElement = document.getElementById('dismiss-alert');
                const messageElement = alertElement.querySelector('h3');
                messageElement.innerHTML = message;
                alertElement.classList.remove('hidden');
                alertElement.classList.add('opacity-100');

                setTimeout(() => {
                    alertElement.classList.add('hidden');
                }, 10000);

                        
            }



            const currentUser = {
                id: @json(auth()->user()->id),
                roles: @json(auth()->user()->getRoleNames()), // returns ['Admin', ...]
                permissions: @json(auth()->user()->getAllPermissions()->pluck('name')),
                reviewerOfProjects: @json(auth()->user()->reviewed_projects->pluck('id')), // assuming relation exists
                myCreatedProjects: @json(auth()->user()->created_projects->pluck('id')), // or custom way
            };
            
            /** notifications */
            window.Echo
                .private('notifications')
                    .listen('.created', (e) => {
                        console.log(e.message);
                        Livewire.dispatch('notificationsCreated');
                        // showAlert(e.message); // Assuming showAlert() is a global function
                    })
                    .listen('.updated', (e) => {
                        console.log(e.message);
                        Livewire.dispatch('notificationsUpdated');
                        // showAlert(e.message); // Assuming showAlert() is a global function
                    })
                    .listen('.deleted', (e) => {
                        console.log(e.message);
                        Livewire.dispatch('notificationsDeleted');
                        // showAlert(e.message); // Assuming showAlert() is a global function
                    });

            /** users */
            window.Echo.private('users')
                .listen('.created', (e) => {
                    console.log(e.message);
                    Livewire.dispatch('userCreated');

                    const shouldNotify =  currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin') || currentUser.permissions.includes('user list view') ;

                    if (shouldNotify) {
                        showAlert(e.message);
                    }

  

                }).listen('.updated', (e) => {
                    console.log(e.message);
                    Livewire.dispatch('userUpdated');

                    const shouldNotify =  currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin') || currentUser.permissions.includes('user list view') ;

                    if (shouldNotify) {
                        showAlert(e.message);
                    }


                }).listen('.deleted', (e) => {
                    console.log(e.message);
                    Livewire.dispatch('userDeleted');

                    const shouldNotify =  currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin') || currentUser.permissions.includes('user list view') ;

                    if (shouldNotify) {
                        showAlert(e.message);
                    }
                });

            /** ./ users */

            /** roles */
            window.Echo.private('roles')
                .listen('.created', (e) => {
                    console.log(e.message);
                    Livewire.dispatch('roleCreated');

                    const shouldNotify =  currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin') || currentUser.permissions.includes('role list view') ;

                    if (shouldNotify) {
                        showAlert(e.message);
                    }

  

                }).listen('.updated', (e) => {
                    console.log(e.message);
                    Livewire.dispatch('roleUpdated');

                    const shouldNotify =  currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin') || currentUser.permissions.includes('role list view') ;

                    if (shouldNotify) {
                        showAlert(e.message);
                    }


                }).listen('.deleted', (e) => {
                    console.log(e.message);
                    Livewire.dispatch('roleDeleted');

                    const shouldNotify =  currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin') || currentUser.permissions.includes('role list view') ;

                    if (shouldNotify) {
                        showAlert(e.message);
                    }
                });

            /** ./ roles */


            /** permissions */
            window.Echo.private('permissions')
                .listen('.created', (e) => {
                    console.log(e.message);
                    Livewire.dispatch('permissionCreated');

                    const shouldNotify =  currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin') || currentUser.permissions.includes('permission list view') ;

                    if (shouldNotify) {
                        showAlert(e.message);
                    }

  

                }).listen('.updated', (e) => {
                    console.log(e.message);
                    Livewire.dispatch('permissionUpdated');

                    const shouldNotify =  currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin') || currentUser.permissions.includes('permission list view') ;

                    if (shouldNotify) {
                        showAlert(e.message);
                    }


                }).listen('.deleted', (e) => {
                    console.log(e.message);
                    Livewire.dispatch('permissionDeleted');

                    const shouldNotify =  currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin') || currentUser.permissions.includes('permission list view') ;

                    if (shouldNotify) {
                        showAlert(e.message);
                    }
                });

            /** ./ permissions */


        
            /** project.discussions.global */
                window.Echo.private("project.discussions.global")
                    .listen('.create', (e) => {

                        console.log(e.message);

                        Livewire.dispatch('projectDiscussionAdded');


                        const projectId = e.project_id;
                        const is_private = e.is_private;

                        
                        const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                        const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                        const isCreator = currentUser.myCreatedProjects.includes(projectId);

                        const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                        if (shouldNotify) {
                            showNewDiscussionAlert(e.message, e.project_url, "View Project");
                        }
                    }).listen('.edited', (e) => {

                        console.log(e.message);

                        const projectId = e.project_id;

                        // Prevent running logic if on a project-specific page with a mismatched projectId
                        if (pageProjectId && projectId != pageProjectId) return;



                        Livewire.dispatch('projectDiscussionEdited');

 
                        const is_private = e.is_private;


                        const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                        const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                        const isCreator = currentUser.myCreatedProjects.includes(projectId);

                        const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                        if (shouldNotify) {
                            showNewDiscussionAlert(e.message, e.project_url, "View Project");
                        }
                    }).listen('.deleted', (e) => {

                        console.log(e.message);
                        const projectId = e.project_id;

                        // Prevent running logic if on a project-specific page with a mismatched projectId
                        if (pageProjectId && projectId != pageProjectId) return;


                        Livewire.dispatch('projectDiscussionDeleted');

 
                        const is_private = e.is_private;


                        const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                        const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                        const isCreator = currentUser.myCreatedProjects.includes(projectId);

                        const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                        if (shouldNotify) {
                            showNewDiscussionAlert(e.message, e.project_url, "View Project");
                        }
                    }).listen('.reply', (e) => {

                        console.log(e.message);

                        const projectId = e.project_id;

                        // Prevent running logic if on a project-specific page with a mismatched projectId
                        if (pageProjectId && projectId != pageProjectId) return;


                        Livewire.dispatch('projectDiscussionReplied');
 
                        const is_private = e.is_private;

                        const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                        const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                        const isCreator = currentUser.myCreatedProjects.includes(projectId);

                        const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                        if (shouldNotify) {
                            showNewDiscussionAlert(e.message, e.project_url, "View Project");
                        }
                    });
            /** ./ project.discussions.global */

            window.Echo.private("project.timer")
                .listen('.updated', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('projectTimerUpdated');

 
                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin'); 

                    const shouldNotify =  (isAdmin || isReviewer) ;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_timer_url, "View Project Timer");
                    }
                });


                
            window.Echo.private("document.type")
                .listen('.created', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('documentTypeCreated');

  
                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin'); 

                    const shouldNotify =  (isAdmin || isReviewer) ;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.document_type_url, "View Document Types");
                    }
                }).listen('.updated', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('documentTypeUpdated');


                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin'); 

                    const shouldNotify =  (isAdmin || isReviewer) ;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.document_type_url, "View Document Types");
                    }
                }).listen('.deleted', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('documentTypeDeleted');


                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin'); 

                    const shouldNotify =  (isAdmin || isReviewer) ;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.document_type_url, "View Document Types");
                    }
                });


            window.Echo.private("reviewer")
                .listen('.created', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('reviewerCreated');

  
                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin'); 

                    const shouldNotify =  (isAdmin || isReviewer) ;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.reviewer_url, "View Reviewers");
                    }
                }).listen('.updated', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('reviewerUpdated');


                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin'); 

                    const shouldNotify =  (isAdmin || isReviewer) ;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.reviewer_url, "View Reviewers");
                    }
                }).listen('.deleted', (e) => {

                    console.log(e.message);

                    Livewire.dispatch('reviewerDeleted');


                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin'); 

                    const shouldNotify =  (isAdmin || isReviewer) ;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.reviewer_url, "View Reviewers");
                    }
                });

            window.Echo.private("project")
                .listen('.created', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;



                    Livewire.dispatch('projectCreated');

   

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url, "View Project");
                    }
                }).listen('.updated', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;


                    Livewire.dispatch('projectUpdated');

 

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url, "View Project");
                    }
                }).listen('.deleted', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;


                    Livewire.dispatch('projectDeleted');

 

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url, "View Projects");
                    }
                }).listen('.submitted', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;


                    Livewire.dispatch('projectSubmitted');

 

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    // const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isReviewer = currentUser.roles.includes('Reviewer');
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url, "View Projects");
                    }
                }).listen('.queued', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;


                    Livewire.dispatch('projectQueued');


                    // const projectId = e.project_id; 

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url, "View Projects");
                    }
                });

            window.Echo.private("project_document")
                .listen('.created', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;


                    Livewire.dispatch('projectDocumentCreated');

   

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url, "View Project Document");
                    }
                }).listen('.updated', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;


                    Livewire.dispatch('projectDocumentUpdated');


                    // const projectId = e.project_id; 

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url, "View Project Document");
                    }
                }).listen('.deleted', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;


                    Livewire.dispatch('projectDocumentDeleted');


                    // const projectId = e.project_id; 

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.project_url, "View Project");
                    }
                });


            window.Echo.private("project_reviewer")
                .listen('.created', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;


                    Livewire.dispatch('projectReviewerCreated');

   

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    // const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isReviewer = currentUser.roles.includes('Reviewer');
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.reviewer_url, "View Project Reviewers");
                    }
                }).listen('.updated', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;


                    Livewire.dispatch('projectReviewerUpdated');

 

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    // const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isReviewer = currentUser.roles.includes('Reviewer');
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;
                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.reviewer_url, "View Project Reviewers");
                    }
                }).listen('.deleted', (e) => {

                    console.log(e.message);

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;


                    Livewire.dispatch('projectReviewerDeleted');

 

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    // const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isReviewer = currentUser.roles.includes('Reviewer');
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.reviewer_url, "View Project Reviewers");
                    }
                });


             window.Echo.private("review")
                .listen('.created', (e) => {

                    console.log(e.message); 

                    const projectId = e.project_id;

                    // Prevent running logic if on a project-specific page with a mismatched projectId
                    if (pageProjectId && projectId != pageProjectId) return;

                    Livewire.dispatch('projectReviewCreated');

   

                    const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                    // const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                    const isReviewer = currentUser.roles.includes('Reviewer');
                    const isCreator = currentUser.myCreatedProjects.includes(projectId);

                    const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.reviewer_url, "View Project Reviewers");
                    }
                })
                // .listen('.updated', (e) => {

                //     console.log(e.message);

                //     Livewire.dispatch('projectReviewerUpdated');


                //     const projectId = e.project_id; 

                //     const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                //     // const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                //     const isReviewer = currentUser.roles.includes('Reviewer');
                //     const isCreator = currentUser.myCreatedProjects.includes(projectId);

                //     const shouldNotify =  (isAdmin || isReviewer) || isCreator;
                //     if (shouldNotify) {
                //         showNewDiscussionAlert(e.message, e.reviewer_url, "View Project Reviewers");
                //     }
                // }).listen('.deleted', (e) => {

                //     console.log(e.message);

                //     Livewire.dispatch('projectReviewerDeleted');


                //     const projectId = e.project_id; 

                //     const isAdmin = currentUser.permissions.includes('system access admin') || currentUser.permissions.includes('system access global admin');
                //     // const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                //     const isReviewer = currentUser.roles.includes('Reviewer');
                //     const isCreator = currentUser.myCreatedProjects.includes(projectId);

                //     const shouldNotify =  (isAdmin || isReviewer) || isCreator;

                //     if (shouldNotify) {
                //         showNewDiscussionAlert(e.message, e.reviewer_url, "View Project Reviewers");
                //     }
                // })
                ;

            
        </script>
        <!-- ./ Discussion Listeners -->
        @endauth
         


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










    <script>
      // Simple toggle for mobile sidebar
      function toggleSidebar(open) {
          const sidebar = document.getElementById('mobile-drawer');
          const backdrop = document.getElementById('backdrop');
          if (open) {
              sidebar.classList.remove('translate-x-[-100%]');
              backdrop.classList.remove('hidden');
              document.body.classList.add('overflow-hidden');
          } else {
              sidebar.classList.add('translate-x-[-100%]');
              backdrop.classList.add('hidden');
              document.body.classList.remove('overflow-hidden');
          }
      }

      function toggleOffcanvas(open) {
        const panel = document.getElementById('notif-offcanvas');
        const backdrop = document.getElementById('offcanvas-backdrop');
        if (open) {
          panel.classList.remove('translate-x-full');
          backdrop.classList.remove('hidden');
          document.body.classList.add('overflow-hidden');
        } else {
          panel.classList.add('translate-x-full');
          backdrop.classList.add('hidden');
          document.body.classList.remove('overflow-hidden');
        }
      }


      function toggleProfileLg(){
        const dd = document.getElementById('profile-dd-lg');
        dd.classList.toggle('hidden');

        const dd_mobile = document.getElementById('profile-dd-lg-mobile');
        dd_mobile.classList.toggle('hidden');

      }

    </script>



    <!-- Mobile top bar -->
    <header class="lg:hidden flex items-center justify-between gap-2 px-4 py-3 border-b bg-white sticky top-0 z-40">
        <button aria-label="Open navigation" class="inline-flex items-center justify-center rounded-xl border px-3 py-2 hover:bg-gray-50 active:scale-95 transition" onclick="toggleSidebar(true)">
        <!-- Hamburger -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        </button>
        {{-- <div class="font-semibold">Your App</div> --}}
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-ghrd.png') }}" alt="Logo" class="h-8" />
        </div>
        <div class="flex items-center gap-3">
            
            @guest
                <a href="#" class="inline-flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-lg border hover:bg-gray-50">Sign in</a>
            @endguest
            
           

            @auth
                <button class="relative rounded-full p-2 hover:bg-gray-100" onclick="toggleOffcanvas(true)">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C8.67 6.165 8 7.388 8 9v5.159c0 .538-.214 1.055-.595 1.436L6 17h5"/></svg>
                    <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                </button>
                <div class="relative" id="profile-lg-parent">
                    <button class="flex items-center gap-2 rounded-full border px-2 py-1 hover:bg-gray-50" onclick="toggleProfileLg()">
                    <img src="https://i.pravatar.cc/32" class="w-8 h-8 rounded-full" />
                    <span class="hidden sm:inline text-sm font-medium">Arthur</span>
                    </button>
                    <div id="profile-dd-lg-mobile" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-xl shadow-lg text-sm">
                    <a href="#" class="block px-4 py-2 hover:bg-gray-50">Profile</a>
                    <a href="#" class="block px-4 py-2 hover:bg-gray-50">Settings</a>
                    <a href="#" class="block px-4 py-2 text-rose-600 hover:bg-gray-50">Sign out</a>
                    </div>
                </div>
            @endauth

            
        </div>


    </header>

    <!-- Layout wrapper -->
    <div class="flex">
        <!-- Static sidebar (desktop) -->
        <aside class="hidden lg:flex lg:flex-col lg:w-72 lg:min-h-screen lg:sticky lg:top-0 lg:border-r bg-white">
            <!-- Brand -->
            <div class="px-5 py-4 border-b">
                <div class="flex items-center gap-3">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold">A</span>
                <div>
                    <div class="font-semibold">SHPO</div>
                    <div class="text-xs text-slate-500">Admin Portal</div>
                </div>
                </div>
            </div>
                <!-- Sidebar nav -->
            <nav class="flex-1 overflow-y-auto px-3 py-4">
                <p class="px-3 text-xs font-semibold text-slate-400">MAIN</p>
                <ul class="mt-2 space-y-1">
                <li>
                    <details class="group">
                    <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer hover:bg-indigo-50 hover:text-indigo-700">
                        <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                        Dashboard
                        </span>
                        <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <ul class="ml-6 mt-1 space-y-1 text-sm">
                        <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Analytics</a></li>
                        <li>
                        <details>
                            <summary class="flex items-center justify-between px-3 py-1.5 rounded cursor-pointer hover:bg-gray-50">
                            <span>Reports</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </summary>
                            <ul class="ml-5 mt-1 space-y-1">
                            <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Monthly</a></li>
                            <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Yearly</a></li>
                            </ul>
                        </details>
                        </li>
                    </ul>
                    </details>
                </li>
                <li>
                    <details class="group">
                    <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer hover:bg-indigo-50 hover:text-indigo-700">
                        <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h8m-8 4h6"/></svg>
                        Tasks
                        </span>
                        <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <ul class="ml-6 mt-1 space-y-1 text-sm">
                        <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Active</a></li>
                        <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Archived</a></li>
                    </ul>
                    </details>
                </li>
                </ul>
                <p class="mt-6 px-3 text-xs font-semibold text-slate-400">DOCUMENTS</p>
                <ul class="mt-2 space-y-1">
                <li>
                    <details class="group">
                    <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer hover:bg-indigo-50 hover:text-indigo-700">
                        <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 10-8 0v4M5 11h14l-1 10H6L5 11z"/></svg>
                        Documents
                        </span>
                        <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <ul class="ml-6 mt-1 space-y-1 text-sm">
                        <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">All Docs</a></li>
                        <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Shared</a></li>
                        <li>
                        <details>
                            <summary class="flex items-center justify-between px-3 py-1.5 rounded cursor-pointer hover:bg-gray-50">
                            <span>Templates</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </summary>
                            <ul class="ml-5 mt-1 space-y-1">
                            <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Legal</a></li>
                            <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Finance</a></li>
                            </ul>
                        </details>
                        </li>
                    </ul>
                    </details>
                </li>
                </ul>
            </nav>
            <div class="px-4 py-4 border-t">
                <button class="w-full inline-flex items-center justify-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                Logout
                </button>
            </div>
        </aside>

        <!-- Mobile drawer -->
        <div id="backdrop" class="lg:hidden fixed inset-0 bg-black/50 hidden z-40" onclick="toggleSidebar(false)"></div>
        <aside id="mobile-drawer" class="lg:hidden fixed inset-y-0 left-0 w-72 bg-white border-r z-50 translate-x-[-100%] transition-transform duration-200 ease-out">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <div class="flex items-center gap-3">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold">A</span>
                <div>
                    <div class="font-semibold">Your App</div>
                    <div class="text-xs text-slate-500">Admin Portal</div>
                </div>
                </div>
                <button aria-label="Close navigation" class="rounded-xl border p-2 hover:bg-gray-50" onclick="toggleSidebar(false)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Mobile sidebar navigation -->
            <nav class="px-3 py-4 h-[calc(100vh-64px)] overflow-y-auto flex-1">
    
            <!-- Sidebar nav -->
            
            <p class="px-3 text-xs font-semibold text-slate-400">MAIN</p>
            <ul class="mt-2 space-y-1">
                <li>
                <details class="group">
                    <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer hover:bg-indigo-50 hover:text-indigo-700">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                        Dashboard
                    </span>
                    <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <ul class="ml-6 mt-1 space-y-1 text-sm">
                    <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Analytics</a></li>
                    <li>
                        <details>
                        <summary class="flex items-center justify-between px-3 py-1.5 rounded cursor-pointer hover:bg-gray-50">
                            <span>Reports</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <ul class="ml-5 mt-1 space-y-1">
                            <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Monthly</a></li>
                            <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Yearly</a></li>
                        </ul>
                        </details>
                    </li>
                    </ul>
                </details>
                </li>
                <li>
                <details class="group">
                    <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer hover:bg-indigo-50 hover:text-indigo-700">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h8m-8 4h6"/></svg>
                        Tasks
                    </span>
                    <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <ul class="ml-6 mt-1 space-y-1 text-sm">
                    <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Active</a></li>
                    <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Archived</a></li>
                    </ul>
                </details>
                </li>
            </ul>
            <p class="mt-6 px-3 text-xs font-semibold text-slate-400">DOCUMENTS</p>
            <ul class="mt-2 space-y-1">
                <li>
                <details class="group">
                    <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer hover:bg-indigo-50 hover:text-indigo-700">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 10-8 0v4M5 11h14l-1 10H6L5 11z"/></svg>
                        Documents
                    </span>
                    <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <ul class="ml-6 mt-1 space-y-1 text-sm">
                    <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">All Docs</a></li>
                    <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Shared</a></li>
                    <li>
                        <details>
                        <summary class="flex items-center justify-between px-3 py-1.5 rounded cursor-pointer hover:bg-gray-50">
                            <span>Templates</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <ul class="ml-5 mt-1 space-y-1">
                            <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Legal</a></li>
                            <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Finance</a></li>
                        </ul>
                        </details>
                    </li>
                    </ul>
                </details>
                </li>
            </ul> 
            <div class="px-4 py-4 border-t">
                <button class="w-full inline-flex items-center justify-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                Logout
                </button>
            </div>
    
            </nav>


        </aside>

        <!-- Main content -->
        <main class="flex-1 min-w-0">

            <!-- Desktop top bar -->
            <div class="hidden lg:flex items-center justify-between px-6 py-3 border-b bg-white sticky top-0 z-40">
                <div class="flex items-center gap-3">
                    <img src="https://dummyimage.com/120x40/4f46e5/ffffff&text=Logo" alt="Logo" class="h-8" />
                </div>
                <div class="flex items-center gap-3">
                    <button class="relative rounded-full p-2 hover:bg-gray-100" onclick="toggleOffcanvas(true)">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C8.67 6.165 8 7.388 8 9v5.159c0 .538-.214 1.055-.595 1.436L6 17h5"/></svg>
                    <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div class="relative" id="profile-lg-parent">
                    <button class="flex items-center gap-2 rounded-full border px-2 py-1 hover:bg-gray-50" onclick="toggleProfileLg()">
                        <img src="https://i.pravatar.cc/32" class="w-8 h-8 rounded-full" />
                        <span class="hidden sm:inline text-sm font-medium">Arthur</span>
                    </button>
                    <div id="profile-dd-lg" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-xl shadow-lg text-sm">
                        <a href="#" class="block px-4 py-2 hover:bg-gray-50">Profile</a>
                        <a href="#" class="block px-4 py-2 hover:bg-gray-50">Settings</a>
                        <a href="#" class="block px-4 py-2 text-rose-600 hover:bg-gray-50">Sign out</a>
                    </div>
                    </div>
                </div>
            </div>


            <!-- Header w/ breadcrumb + section (tabs) -->
            <div class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-sm py-3" aria-label="Breadcrumb">
                    <a href="#" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                    Home
                    </a>
                    <span class="text-slate-400">/</span>
                    <a href="#" class="text-slate-500 hover:text-slate-700">Projects</a>
                    <span class="text-slate-400">/</span>
                    <span class="font-medium text-slate-800">Overview</span>
                </nav>
                <!-- Section header -->
                <div class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                    <h1 class="text-xl sm:text-2xl font-semibold">Projects Overview</h1>
                    <p class="text-sm text-slate-500">Track active items, deadlines, and recent activity.</p>
                    </div>
                    <div class="flex items-center gap-2">
                    <button class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Project
                    </button>
                    <button class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                        Filters
                    </button>
                    </div>
                </div>
                <!-- Section tabs -->
                <div class="flex items-center gap-1">
                    <a href="#" class="px-3 py-2 text-sm rounded-lg font-medium bg-indigo-600 text-white">Overview</a>
                    <a href="#" class="px-3 py-2 text-sm rounded-lg font-medium hover:bg-gray-100">Active</a>
                    <a href="#" class="px-3 py-2 text-sm rounded-lg font-medium hover:bg-gray-100">Archived</a>
                    <a href="#" class="px-3 py-2 text-sm rounded-lg font-medium hover:bg-gray-100">Settings</a>
                </div>
                </div>
            </div>

            <!-- Page content -->
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </section>
        </main>
    </div>
 






  <!-- Offcanvas Notifications -->
  <div id="offcanvas-backdrop" class="hidden fixed inset-0 bg-black/40 z-50" onclick="toggleOffcanvas(false)"></div>
    <aside id="notif-offcanvas" class="fixed inset-y-0 right-0 w-full sm:w-[28rem] bg-white border-l z-50 translate-x-full transition-transform duration-200 ease-out">
      <div class="h-full flex flex-col">
        <div class="px-4 py-3 border-b flex items-center justify-between">
          <h3 class="font-semibold">Notifications</h3>
          <button class="rounded-lg border px-2 py-1 text-sm hover:bg-gray-50" onclick="toggleOffcanvas(false)">Close</button>
        </div>
        <div class="flex-1 overflow-y-auto divide-y">
          <div class="px-4 py-4">
            <p class="font-medium">Project Beta deadline moved</p>
            <p class="text-slate-500 text-sm">New due date: Sep 30. The client requested more time due to updated scope and stakeholder availability.</p>
          </div>
          <div class="px-4 py-4">
            <p class="font-medium">New comment from Kent</p>
            <p class="text-slate-500 text-sm">“Please review the UI spec and confirm acceptance criteria by EOD. Add notes to the task if anything is unclear.”</p>
          </div>
          <div class="px-4 py-4">
            <p class="font-medium">Document approved — Requirements.pdf</p>
            <p class="text-slate-500 text-sm">Version 1.8 has been approved by Legal and Finance. You can proceed with the vendor onboarding package.</p>
          </div>
        </div>
      </div>
    </aside> 
  </div>
































        {{--
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                if (window.Livewire) {
                    window.Livewire.restart();
                }
            });
        </script>
  --}}
        @include('sweetalert::alert')

        <!-- There is something wrong with the scripts that disrupts the map -->
        {{-- <script>
            let inactivityTime = function () {
                let inactivityTimeout, alertTimeout;
                const inactivityLimit = 600000; // 1 hour
                // const inactivityLimit = 60000; // 1 minutes

                const warningTime = 30000; // 30 seconds before logout

                const startTimers = () => {
                    clearTimeout(inactivityTimeout);
                    clearTimeout(alertTimeout);

                    alertTimeout = setTimeout(() => {
                        const confirmLogout = confirm(
                            "You've been inactive for a while.\nWould you like to stay logged in?"
                        );
                        if (!confirmLogout) {
                            // window.Livewire.emit('autoLogout');

                            Livewire.dispatch('autoLogout');

                        } else {
                            startTimers(); // Restart timers if user cancels logout
                        }
                    }, inactivityLimit - warningTime);

                    inactivityTimeout = setTimeout(() => {
                        // window.Livewire.emit('autoLogout');

                        Livewire.dispatch('autoLogout');

                    }, inactivityLimit);
                };

                // Reset timers on user interaction
                ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'].forEach(evt =>
                    document.addEventListener(evt, startTimers)
                );

                startTimers(); // Start on page load
            };

            window.onload = inactivityTime;
        </script>   --}}



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

    </body>
</html>
