import {addDynamicFields, replaceFormFields} from "../dynamicFields";
import {default as emsReceiver} from "./emsReceiver";
import {emsForm} from "./emsForm";

export const DEFAULT_CONFIG = {
    idForm: 'wrapper-form'
};

export class emsFormDebug {
    constructor(options) {
        let config = Object.assign({}, DEFAULT_CONFIG, options);
        this.elementForm = document.getElementById(config.idForm);
        this.ajaxUrl = window.location.pathname.replace(/\/debug\/form\//g, '/debug/ajax/');
    }
    onDynamicFieldChange(data) {
        let xhr = new XMLHttpRequest();
        xhr.addEventListener("load", evt => emsFormDebug.onResponse(evt, xhr, this));

        xhr.open("POST", this.ajaxUrl);
        xhr.setRequestHeader("Content-Type",  "application/x-www-form-urlencoded");
        emsReceiver.addHashCashHeader(data, xhr);
        xhr.send(emsReceiver.urlEncodeData(data));
    }
    static onResponse(evt, xhr, emsFormInstance) {
        if (xhr.status !== 200) {
            return;
        }
        let data = emsForm.jsonParse(xhr.responseText);

        if (!data) {
            return;
        }
        
        if (data.instruction === 'dynamic') {
            replaceFormFields(data.response, Object.values(emsForm.jsonParse(data.dynamicFields)));
            addDynamicFields(emsFormInstance.elementForm.querySelector('form'), emsFormInstance);
        }
    }
}
