require('intersection-observer');
IntersectionObserver.prototype.POLL_INTERVAL = 100;
require('./bootstrap');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


// import Echo from 'laravel-echo';
// var e = new Echo({
//     broadcaster: 'socket.io',
//     host: window.location.hostname + ':6001'
// });

