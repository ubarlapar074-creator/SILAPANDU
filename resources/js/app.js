import Alpine from 'alpinejs';
import { NikahDraft, initNikLookup } from './nikah-draft.js';

window.NikahDraft = NikahDraft;
window.initNikLookup = initNikLookup;

document.addEventListener('submit', (event) => {
    event.target.querySelectorAll('[data-date-dmy]').forEach((input) => {
        const match = input.value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        if (match) input.value = `${match[3]}-${match[2]}-${match[1]}`;
    });
});

if (!window.__SIMANIS_ALPINE_STARTED__) {
    window.Alpine = Alpine;
    window.__SIMANIS_ALPINE_STARTED__ = true;
    Alpine.start();
}
