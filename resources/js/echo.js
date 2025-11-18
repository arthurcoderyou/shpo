import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;



// window.Echo = new Echo({
//     broadcaster: 'reverb',
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     wsHost: import.meta.env.VITE_REVERB_HOST,
//     wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
//     wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
 

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     wsHost: window.location.hostname,
//     wsPort: 6002,
//     wssPort: 6002,
//     forceTLS: window.location.protocol === 'https:',
//     encrypted: true,
//     disableStats: true, // optional, prevents sending usage stats to Pusher
//     enabledTransports: ['ws', 'wss'], // optional, defines transport options
// });




/**PUSHER (uncomment if using pusher as the connection)   */
 

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
});




window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('Pusher connection error:', err);

    if (err.error && err.error.code === 4004) {
        console.warn('Over capacity — Pusher returned 4004');
    }

    if (err.error) {
        // Fallback UI/alert
        alert('Real-time service is currently unavailable.');
    }
});


/** ./ PUSHER */




/**REVERB (uncomment if using reverb as the connection) 
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

window.Echo.connector.connection.addEventListener('error', (event) => {
    console.error('Reverb connection error:', event);

    // Reverb-specific error handling (if available)
    alert('Real-time service (Reverb) is currently unavailable.');
});
/** ./ REVERB */


