// Create an instance of Notyf
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
            duration: 10000,
            dismissible: true
        },
        {
            type: 'warning',
            background: 'orange',
            icon: {
                className: 'material-icons',
                tagName: 'i',
                text: 'warning'
            },
            duration: 10000,
            dismissible: true
        },
        {
            type: 'error',
            background: 'indianred',
            duration: 10000,
            dismissible: true
        }
    ]
});



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
    /*
    window.Echo.private("users")
        .listen('.created', (e) => {

            console.log(e.message);

            Livewire.dispatch('userCreated');
    

            const hasRole = window.currentUser.roles.includes('Global Administrator');
            const hasPermission = window.currentUser.permissions.includes('user create');


            if (hasRole) {
                notyf.success(e.message); 
            }else if(hasPermission){
                notyf.success(e.message); 
            }  


            initFlowbite();
        }).listen('.updated', (e) => {

            console.log(e.message);

            Livewire.dispatch('userUpdated');
    

            const hasRole = window.currentUser.roles.includes('Global Administrator');
            const hasPermission = window.currentUser.permissions.includes('user edit');


            if (hasRole) {
                notyf.success(e.message); 
            }else if(hasPermission){
                notyf.success(e.message); 
            }


            initFlowbite();
        }).listen('.deleted', (e) => {

            console.log(e.message);

            Livewire.dispatch('userDeleted');
    

            const hasRole = window.currentUser.roles.includes('Global Administrator');
            const hasPermission = window.currentUser.permissions.includes('user delete');


            if (hasRole) {
                notyf.success(e.message); 
            }else if(hasPermission){
                notyf.success(e.message); 
            } 


            initFlowbite();
        });
      */
     
    window.Echo.private(`attachment.${userId}`)
        .listen('.attachment.created', (e) => {
            // e: { type, message, meta }
            // session_notyf.open(e.type, e.message);

            console.log(e.message);

            // Livewire.dispatch('userCreated');
    

            // const hasRole = window.currentUser.roles.includes('Global Administrator');
            // const hasPermission = window.currentUser.permissions.includes('user create');


            // if (hasRole) {
            //     notyf.success(e.message); 
            // }else if(hasPermission){
            //     notyf.success(e.message); 
            // }  
            if(e.type == 'success'){
                notyf.success(e.message); 
            }


            initFlowbite();

        })
        .listen('.attachment.copied', (e) => {
            // e: { type, message, meta }
            // session_notyf.open(e.type, e.message);

            console.log(e.message);

            // Livewire.dispatch('userCreated');
    

            // const hasRole = window.currentUser.roles.includes('Global Administrator');
            // const hasPermission = window.currentUser.permissions.includes('user create');


            // if (hasRole) {
            //     notyf.success(e.message); 
            // }else if(hasPermission){
            //     notyf.success(e.message); 
            // }  
            if(e.type == 'success'){
                notyf.success(e.message); 
            }


            initFlowbite();

        })
        .listen('.attachment.copied', (e) => {
            // e: { type, message, meta }
            // session_notyf.open(e.type, e.message);

            console.log(e.message);

            // Livewire.dispatch('userCreated');
    

            // const hasRole = window.currentUser.roles.includes('Global Administrator');
            // const hasPermission = window.currentUser.permissions.includes('user create');


            // if (hasRole) {
            //     notyf.success(e.message); 
            // }else if(hasPermission){
            //     notyf.success(e.message); 
            // }  
            if(e.type == 'success'){
                notyf.success(e.message); 
            }


            initFlowbite();

        });

 


}, 500);
 