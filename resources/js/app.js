import './bootstrap';
import productStudio from './mockup-studio';
import Alpine from 'alpinejs';
import Clipboard from "@ryangjchandler/alpine-clipboard";
import Typewriter from '@marcreichel/alpine-typewriter';
Alpine.plugin(Typewriter);
Alpine.plugin(Clipboard);
Alpine.data('productStudio', productStudio);
window.Alpine = Alpine;
Alpine.start();

