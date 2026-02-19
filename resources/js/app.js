import '../../vendor/masmerise/livewire-toaster/resources/js';

import Swal from 'sweetalert2';
window.Swal = Swal;

import moment from 'moment';
window.moment = moment;

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';

import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

// Opsional: Lampirkan ke window agar bisa dipanggil langsung di Blade jika perlu
window.Chart = Chart;
