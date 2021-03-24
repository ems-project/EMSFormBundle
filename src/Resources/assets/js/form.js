import {emsForm, defaultCheck} from "./modules/emsForm";

window.emsForm = emsForm;
document.addEventListener('DOMContentLoaded', defaultLoad);

export function defaultLoad() {
    if (defaultCheck()) {
        let form = new emsForm();
        let formActivated = false;
        form.elementIframe.onload = function() { if (formActivated) return; formActivated = true; form.init(); };

        const iframeDoc = form.elementIframe.contentDocument || form.elementIframe.contentWindow.document;
        if (  iframeDoc.readyState  == 'complete' && !formActivated ) {
            form.init();
        }
    }
}
