import './bootstrap';
import Alpine from 'alpinejs';
import Echo from 'laravel-echo';

window.Alpine = Alpine;
Alpine.start();

import Echo from "laravel-echo";

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: 's3w3thzezulgp5g0e5bs',
    wsHost: 'hypersale.dev.tqnia.me',
    wsPort: 7020,
    wssPort: 7020,
    forceTLS: true,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});


console.log(window.Echo.connector.pusher.connection.state);

// Listen for story events
window.Echo.channel('stories')
    .listen('.story.created', (e) => {
        console.log('Story Created:', e);
        document.getElementById('events').innerHTML += `<p>New story created: ${JSON.stringify(e)}</p>`;
    });

