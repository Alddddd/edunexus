import './bootstrap';

import Alpine from 'alpinejs';

import 'flowbite';

import { Html5Qrcode } from "html5-qrcode";

window.Html5Qrcode = Html5Qrcode;

console.log('Html5Qrcode loaded:', window.Html5Qrcode);

window.Alpine = Alpine;

document.addEventListener('wheel', (event) => {
    const target = event.target;

    if (
        target instanceof HTMLInputElement &&
        target.type === 'number' &&
        document.activeElement === target
    ) {
        event.preventDefault();
    }
}, { passive: false });

Alpine.start();
