import './bootstrap';
import './mockup-preview';
import Alpine from 'alpinejs';
import Clipboard from "@ryangjchandler/alpine-clipboard";
import Typewriter from '@marcreichel/alpine-typewriter';
Alpine.plugin(Typewriter);
Alpine.plugin(Clipboard);
window.Alpine = Alpine;
Alpine.start();

