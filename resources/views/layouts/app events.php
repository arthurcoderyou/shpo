
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

            // for project document updates without actual notifications shown
            window.Echo.private('project-document')
                .listen('.reviewed',(e) => {
                    Livewire.dispatch('projectDocumentUpdated');
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


            window.Echo.private("attachments")
                .listen('.created', (e) => {

                    console.log(e.message); 

                    const attachment_id = e.attachment_id;
 
                    Livewire.dispatch('attachmentCreated');
 
                    const isCreator = currentUser.id == e.auth_id;

                    const shouldNotify =  isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.url, "View Attachments");
                    }
                })
                .listen('.deleted', (e) => {

                    console.log(e.message); 
 
 
                    Livewire.dispatch('attachmentDeleted');
 
                    const isCreator = currentUser.id == e.auth_id;

                    const shouldNotify =  isCreator;

                    if (shouldNotify) {
                        showNewDiscussionAlert(e.message, e.url, "View Attachments");
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

            // console.log(currentUser.id);
        </script>   
        <!-- ./ Discussion Listeners -->
        @endauth
         
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
        <script>

            function closeSidebarDetails() {
                document
                .querySelectorAll('#desktop-sidebar details[open]')
                .forEach(d => d.open = false); // or d.removeAttribute('open')
            }

            function setSidebarCollapsed(state){
                const aside = document.getElementById('desktop-sidebar');
                const iconCollapse = document.getElementById('icon-collapse');
                const iconExpand = document.getElementById('icon-expand');
                if(!aside) return;

                // Attribute for Tailwind data-variants
                aside.setAttribute('data-collapsed', state ? 'true' : 'false');

                // Fallback width classes (in case data-variant isn’t picked up)
                aside.classList.toggle('w-72', !state);
                aside.classList.toggle('w-20', !!state);

                // Hide text labels when collapsed (elements with .label)
                document.querySelectorAll('#desktop-sidebar .label').forEach(el=>{
                    el.classList.toggle('hidden', !!state);
                });

                // Flip icons
                if(iconCollapse && iconExpand){
                    if(state){ // collapsed
                    iconCollapse.classList.add('hidden');
                    iconExpand.classList.remove('hidden');
                    } else {   // expanded
                    iconCollapse.classList.remove('hidden');
                    iconExpand.classList.add('hidden');
                    }
                }

                // NEW: when collapsing, close all <details>
                // if (state) closeSidebarDetails();

                try { localStorage.setItem('sidebar-collapsed', state ? '1' : '0'); } catch(e){}
            }

            function toggleSidebarCollapsed(){
                const aside = document.getElementById('desktop-sidebar');
                const isCollapsed = aside?.getAttribute('data-collapsed') === 'true';
                setSidebarCollapsed(!isCollapsed);
            }

            document.addEventListener('DOMContentLoaded', ()=>{
                const saved = (typeof localStorage !== 'undefined' && localStorage.getItem('sidebar-collapsed') === '1');
                setSidebarCollapsed(saved);
            });

            // Listen for Livewire `navigate` events
            document.addEventListener('livewire:navigated', () => {
                const saved = (typeof localStorage !== 'undefined' && localStorage.getItem('sidebar-collapsed') === '1');
                setSidebarCollapsed(saved);

            });


            
        </script>