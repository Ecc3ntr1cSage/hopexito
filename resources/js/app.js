import './bootstrap';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import productStudio from './mockup-studio';
import Clipboard from "@ryangjchandler/alpine-clipboard";
import Typewriter from '@marcreichel/alpine-typewriter';
Alpine.plugin(Typewriter);
Alpine.plugin(Clipboard);
Alpine.data('productStudio', productStudio);
window.Alpine = Alpine;
Livewire.start();

