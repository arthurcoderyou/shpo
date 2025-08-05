<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>


        {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script> --}}

        <!-- Include Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <!-- Include Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 
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
        <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
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
    <body  class="font-sans antialiased bg-white " 
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
 
   


    </body>
</html>
