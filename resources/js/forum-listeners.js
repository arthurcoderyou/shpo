// resources/js/forum-listeners.js
import Echo from 'laravel-echo';

// forum-listeners.js
import { showAlert } from './alert';





window.Echo.channel('forums')
    .listen('.create', (e) => {
        console.log(e.message);
        Livewire.dispatch('forumCreated');
        showAlert(e.message);
    });

window.Echo.channel('forums')
    .listen('.update', (e) => {
        console.log(e.message);
        Livewire.dispatch('forumUpdated');
        showAlert(e.message);
    });

window.Echo.channel('forums')
    .listen('.delete', (e) => {
        console.log(e.message);
        Livewire.dispatch('forumDeleted');
        showAlert(e.message);
    });




// window.Echo.channel('project_discussions')
//     .listen('.create', (e) => {
//         console.log(e.message);
//         Livewire.dispatch('projectDiscussionAdded');
//         showAlert(e.message);
//     });


