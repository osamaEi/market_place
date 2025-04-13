import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: true,
    enabledTransports: ['wss']
 });
 
 // Connection status logs
 window.Echo.connector.pusher.connection.bind('connecting', () => {
    console.log('Connection Status:', {
        state: 'Connecting',
        url: window.Echo.connector.pusher.connection.options.url,
        timestamp: new Date().toISOString()
    });
 });
 
 window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Connection Status:', {
        state: 'Connected',
        socketId: window.Echo.socketId(),
        timestamp: new Date().toISOString()
    });
 });
 
 window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('Connection Error:', {
        error,
        config: window.Echo.options,
        timestamp: new Date().toISOString()
    });
 });
 
 window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('Connection Status: Disconnected');
 });
 
 // Channel subscription test
 window.Echo.channel('stories')
    .listen('.story.created', (e) => {
        console.log('Event Received:', {
            type: '.story.created',
            data: e,
            timestamp: new Date().toISOString()
        });
    });