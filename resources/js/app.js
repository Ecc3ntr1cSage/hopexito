import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import productStudio from './mockup-studio';
Alpine.data('productStudio', productStudio);
window.Alpine = Alpine;
Livewire.start();

