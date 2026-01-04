import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';


const notyf = new Notyf({
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




// console.log('created_projects : ' + window.currentUser.myCreatedProjects);


setTimeout(() => {

    window.addEventListener('alert', (event) => {
        let data = event.detail;

        // Swal.fire({
        //     position: data.position || 'center',
        //     icon: data.type || 'info',
        //     title: data.title || 'Alert',
        //     showConfirmButton: false,
        //     timer: data.timer || 1500
        // });

        // if (data.type === "success") {
        //     // You can re-enable Notyf if needed
        //     notyf.success(data.title);
        // }

        // toastr.options = {
        //     closeButton: true,
        //     progressBar: true,
        //     timeOut: 5000,
        //     extendedTimeOut: 1000,
        //     positionClass: "toast-top-right",
        //     escapeHtml: false // IMPORTANT: allow HTML
        // };

        // toastr.options = {
        //     "closeButton": true,
        //     "debug": false,
        //     "newestOnTop": false,
        //     "progressBar": false,
        //     "positionClass": "toast-top-right",
        //     "preventDuplicates": false,
        //     "onclick": null,
        //     "showDuration": "300",
        //     "hideDuration": "1000",
        //     "timeOut": "5000",
        //     "extendedTimeOut": 1000,
        //     "showEasing": "swing",
        //     "hideEasing": "linear",
        //     "showMethod": "fadeIn",
        //     "hideMethod": "fadeOut",
        //     escapeHtml: false // IMPORTANT: allow HTML
        //   }
        
    

        if (data.type === "success") { 
            notyf.success(data.title); 
        } else if (data.type === "error") {
            notyf.success(data.title);
        }

        initFlowbite();
    });




    /** Pusher Events */
  
    // window.Echo.private("users")
    //     .listen('.created', (e) => {

    //         console.log(e.message);

    //         Livewire.dispatch('userCreated');
    

    //         // const hasRole = window.currentUser.roles.includes('Global Administrator');
    //         // const hasPermission = window.currentUser.permissions.includes('user create');


    //         // if (hasRole) {
    //         //     notyf.success(e.message); 
    //         // }else if(hasPermission){
    //         //     notyf.success(e.message); 
    //         // }  
    //         notyf.success(e.message); 
    //         console.log(e.message);

    //         initFlowbite();
    //     }).listen('.updated', (e) => {

    //         console.log(e.message);

    //         Livewire.dispatch('userUpdated');
    

    //         const hasRole = window.currentUser.roles.includes('Global Administrator');
    //         const hasPermission = window.currentUser.permissions.includes('user edit');


    //         if (hasRole) {
    //             notyf.success(e.message); 
    //         }else if(hasPermission){
    //             notyf.success(e.message); 
    //         }


    //         initFlowbite();
    //     }).listen('.deleted', (e) => {

    //         console.log(e.message);

    //         Livewire.dispatch('userDeleted');
    

    //         const hasRole = window.currentUser.roles.includes('Global Administrator');
    //         const hasPermission = window.currentUser.permissions.includes('user delete');


    //         if (hasRole) {
    //             notyf.success(e.message); 
    //         }else if(hasPermission){
    //             notyf.success(e.message); 
    //         } 


    //         initFlowbite();
    //     });
      
     
    // Each browser connects to its own private channel: users.{id}
    window.Echo.private(`system.${window.currentUser.id}`)
        .listen('.event', (e) => {
            // e.message, e.type from the event class
            // console.log('Targeted notification:', e);

            let message = e.message || '';

            if (e.link) {
                message += ` — <a href="${e.link}" class="notyf-link" target="_blank">Open</a>`;
            }

            Livewire.dispatch('systemEvent');

            if(e.show_notification_popup){ // for notification listed to user but not on their 
                if (e.notification_type === 'success') {
                    // notyf.success(e.message);
    

                    notyf.open({
                        type: 'success',
                        message: message,
                    });


                } else if (e.notification_type === 'error') {
                    // notyf.error(e.message);

                    notyf.open({
                        type: 'error',
                        message: message,
                    });

                } else if (e.notification_type === 'info') {
                    // notyf.error(e.message);

                    notyf.open({
                        type: 'info',
                        message: message,
                    });

                } else if (e.notification_type === 'primary') {
                    // notyf.error(e.message);

                    notyf.open({
                        type: 'primary',
                        message: message,
                    });

                } else {
                    notyf.open({
                        type: 'warning',
                        message: message,
                    });
                }
            }
                

            initFlowbite();
        });
        
    // window.Echo.private(`projects`)
    //     .listen('.update', (e) => {
    //         Livewire.dispatch('projectEvent');
    //     }); 

    //  window.Echo.private(`project_documents`)
    //     .listen('.update', (e) => {
            
    //     });

    /** project.discussions.global */
        window.Echo.private("project.discussions.global")
            .listen('.create', (e) => {

                console.log(e.message);

                Livewire.dispatch('projectDiscussionAdded');


                const projectId = e.project_id; 
                const pageProjectId = window.pageProjectId ? Number(window.pageProjectId) : null;

                // If we're on a project page, ignore events for other projects
                if (pageProjectId && projectId !== pageProjectId) return;

                const is_private = e.is_private;

                
                const isAdmin = currentUser.permissions.includes('system access admin') ;
                const isGlobalAdmin = currentUser.permissions.includes('system access global admin');
                console.log(isAdmin);
                const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                const isCreator = currentUser.myCreatedProjects.includes(projectId);
                const onProjectPage = projectId == pageProjectId;

                console.log(onProjectPage);

                const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator || onProjectPage);

                if (shouldNotify) {
                    // showNewDiscussionAlert(e.message, e.project_url, "View Project");

                    let message = e.message || '';

                    if (e.project_url) {
                        message += ` — <a href="${e.project_url}" class="notyf-link" target="_blank">Open</a>`;
                    }

                    notyf.open({
                        type: 'info',
                        message: message,
                    });

                }
            }).listen('.edited', (e) => {

                console.log(e.message);

                Livewire.dispatch('projectDiscussionEdited');


                const projectId = e.project_id;
                const pageProjectId = window.pageProjectId ? Number(window.pageProjectId) : null;

                // If we're on a project page, ignore events for other projects
                if (pageProjectId && projectId !== pageProjectId) return;

                const is_private = e.is_private;

                
                const isAdmin = currentUser.permissions.includes('system access admin') ;
                const isGlobalAdmin = currentUser.permissions.includes('system access global admin');
                const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                const isCreator = currentUser.myCreatedProjects.includes(projectId);

                const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                if (shouldNotify) {
                    // showNewDiscussionAlert(e.message, e.project_url, "View Project");

                    let message = e.message || '';

                    if (e.project_url) {
                        message += ` — <a href="${e.project_url}" class="notyf-link" target="_blank">Open</a>`;
                    }

                    notyf.open({
                        type: 'info',
                        message: message,
                    });

                }

            }).listen('.deleted', (e) => {

                console.log(e.message);

                Livewire.dispatch('projectDiscussionDeleted');


                const projectId = e.project_id; 
                const pageProjectId = window.pageProjectId ? Number(window.pageProjectId) : null;

                // If we're on a project page, ignore events for other projects
                if (pageProjectId && projectId !== pageProjectId) return;  


                const is_private = e.is_private;

                
                const isAdmin = currentUser.permissions.includes('system access admin') ;
                const isGlobalAdmin = currentUser.permissions.includes('system access global admin');
                const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                const isCreator = currentUser.myCreatedProjects.includes(projectId);

                const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                if (shouldNotify) {
                    // showNewDiscussionAlert(e.message, e.project_url, "View Project");

                    let message = e.message || '';

                    if (e.project_url) {
                        message += ` — <a href="${e.project_url}" class="notyf-link" target="_blank">Open</a>`;
                    }

                    notyf.open({
                        type: 'info',
                        message: message,
                    });

                }

            }).listen('.reply', (e) => {

                console.log(e.message);

                Livewire.dispatch('projectDiscussionReplied');


                const projectId = e.project_id; 
                const pageProjectId = window.pageProjectId ? Number(window.pageProjectId) : null;

                // If we're on a project page, ignore events for other projects
                if (pageProjectId && projectId !== pageProjectId) return;


                const is_private = e.is_private;

                
                const isAdmin = currentUser.permissions.includes('system access admin') ;
                const isGlobalAdmin = currentUser.permissions.includes('system access global admin');
                const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                const isCreator = currentUser.myCreatedProjects.includes(projectId);

                const shouldNotify = is_private ? (isAdmin || isReviewer) : (isAdmin || isReviewer || isCreator);

                if (shouldNotify) {
                    // showNewDiscussionAlert(e.message, e.project_url, "View Project");

                    let message = e.message || '';

                    if (e.project_url) {
                        message += ` — <a href="${e.project_url}" class="notyf-link" target="_blank">Open</a>`;
                    }

                    notyf.open({
                        type: 'info',
                        message: message,
                    });

                }
            });
    /** ./ project.discussions.global */


    /** permission */
        // 1) Always listen to the user-wide permission channel
        window.Echo.private(`permission`)
            .listen('.event', (e) => {
                console.log('[p all-wide]');
 
                // trigger the event to reload components
                Livewire.dispatch('permissionEvent');
                initFlowbite();
            });

 
    /** ./ permission */

    /** role */
        // 1) Always listen to the user-wide role channel
        window.Echo.private(`role`)
            .listen('.event', (e) => {
                console.log('[p all-wide]');
 
                // trigger the event to reload components
                Livewire.dispatch('roleEvent');
                initFlowbite();
            });

 
    /** ./ role */

    /** user */
        // 1) Always listen to the user-wide user channel
        window.Echo.private(`user`)
            .listen('.event', (e) => {
                console.log('[p all-wide]');
 
                // trigger the event to reload components
                Livewire.dispatch('userEvent');
                initFlowbite();
            });
 
    /** ./ user */

    /** document_type */
        // 1) Always listen to the document_type-wide document_type channel
        window.Echo.private(`document_type`)
            .listen('.event', (e) => {
                console.log('[p all-wide]');
 
                // trigger the event to reload components
                Livewire.dispatch('documentTypeEvent');
                initFlowbite();
            });
 
    /** ./ document_type */


     /** project */
        // 1) Always listen to the user-wide project channel
        window.Echo.private(`project`)
            .listen('.event', (e) => {
                // console.log('[p all-wide]');
                // console.log('Model id: ' + e.modelId); // defines the connected instance 
                
                const projectId = e.modelId;

                const isAdmin = currentUser.permissions.includes('system access admin') ;
                const isGlobalAdmin = currentUser.permissions.includes('system access global admin');
                const isReviewer = currentUser.reviewerOfProjects.includes(projectId);
                const isCreator = currentUser.myCreatedProjects.includes(projectId);

                const shouldNotify = (isGlobalAdmin || isAdmin || isReviewer || isCreator);

                if (shouldNotify) {
                    // showNewDiscussionAlert(e.message, e.project_url, "View Project");

                    // trigger the event to reload components
                    Livewire.dispatch('projectEvent');

                    console.log('reloaded');


                }

                 

                initFlowbite();
            });

 
    /** ./ project */

    /** projectDocument */
        // 1) Always listen to the user-wide projectDocument channel
        window.Echo.private(`project_document`)
            .listen('.event', (e) => {
                console.log('[p all-wide]');
                
 
                // trigger the event to reload components
                Livewire.dispatch('projectDocumentEvent');
                initFlowbite();
            });

 
    /** ./ projectDocument */


     /** projectTimer */
        // 1) Always listen to the user-wide projectTimer channel
        window.Echo.private(`project_timer`)
            .listen('.event', (e) => {
                console.log('[p all-wide]');
 
                // trigger the event to reload components
                Livewire.dispatch('projectTimerEvent');
                initFlowbite();
            });

 
    /** ./ projectTimer */


     /** reviewer */
        // 1) Always listen to the user-wide reviewer channel
        window.Echo.private(`reviewer`)
            .listen('.event', (e) => {
                console.log('[p all-wide]');
 
                // trigger the event to reload components
                Livewire.dispatch('reviewerEvent');
                initFlowbite();
            });

 
    /** ./ reviewer */



}, 500);