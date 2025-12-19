import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import sort from '@alpinejs/sort';

import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    BarController,
    BarElement,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';

Chart.register(
    LineController,
    LineElement,
    PointElement,
    LinearScale,
    CategoryScale,
    BarController,
    BarElement,
    Tooltip,
    Legend,
    Filler
);

window.Chart = Chart;

document.addEventListener('livewire:init', () => {
    window.Alpine.plugin(sort);
});

Livewire.start();
