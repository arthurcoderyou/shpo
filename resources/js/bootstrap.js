import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

// // Reset timers on user interaction
// ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'].forEach(evt =>
//     document.addEventListener(evt, () => {
//         if (window.userId) {
//             axios.post('/user-activity', { userId: window.userId })
//                 .catch(error => console.error("Activity post error:", error));
//         }
//     })
// );

// let inactivityTimeout;
// const inactivityLimit = 180000; // 3 minutes (in milliseconds)

// function resetInactivityTimer() {
//     clearTimeout(inactivityTimeout);
//     inactivityTimeout = setTimeout(() => {
//         // No activity for 3 minutes
//         if (window.userId) {
//             axios.post('/user-activity', { userId: window.userId })
//                 .then(() => {
//                     console.log("User inactive: auto-logout or handled.");
//                     // Optionally redirect or reload or emit Livewire event
//                     // location.href = "/logout"; // or trigger Livewire.emit('autoLogout')
//                 })
//                 .catch(error => console.error("Inactivity logout failed:", error));
//         }
//     }, inactivityLimit);
// }

// // List of events that reset the timer
// ['mousemove', 'keypress', 'click', 'scroll', 'touchstart'].forEach(evt =>
//     document.addEventListener(evt, resetInactivityTimer)
// );

// window.onload = resetInactivityTimer;

