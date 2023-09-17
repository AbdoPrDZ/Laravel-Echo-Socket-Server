/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

import Echo from 'laravel-echo';
import io from 'socket.io-client';

window.io = io;

window.Echo = new Echo({
    client: io,
    broadcaster: 'socket.io',
    host: window.location.hostname + ":6001",
    logToConsole: true,
    transports: ['websocket'],
    auth: {
        headers: {
            'Authorization': 'Bearer 2|9xjc76eOWsJ459sws1W5yl47VoJ8UjFOtkltjPNGf0752ef7'
        }
    }
});
window.onevent = window.Echo.connector.socket.onevent;
window.Echo.connector.socket.onevent = function(...args) {
    console.log('event', 'args', args);
    window.onevent.call(this, ...args);
    packet.data = ["*"].concat(args);
    window.onevent.call(this, ...args);
}

window.Echo.connector.socket.onAny((...args) => {
    console.log('event', 'args', args);
});
window.Echo.connector.socket.on('*', (...args) => {
    console.log('event', 'args', args);
});
window.Echo.connector.socket.on('event', (...args) => {
    console.log('event', 'args', args);
});

window.Echo.connector.socket.on('connect', (d) => {
    console.log('connected', d);
});
window.Echo.connector.socket.on('error', (d) => {
    console.log('error', d);
});
window.channel = window.Echo.channel('private-messages');
window.channel.listen('MessageLoadEvent', (event) => {
    console.log('event', event);
})
