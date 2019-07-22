import emsReceiver from './modules/emsReceiver';

document.addEventListener('DOMContentLoaded', onLoad);

export function onLoad() {
    let metaDomains = document.head.querySelector('meta[property="domains"]');
    new emsReceiver({'domains': JSON.parse(metaDomains.dataset.list) });
}