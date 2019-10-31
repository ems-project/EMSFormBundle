import {addValidation, disableCopyPaste} from "../validation";
import {addDynamicFields, replaceFormFields} from "../dynamicFields";
import {generate} from 'hashcash-token';

export const DEFAULT_CONFIG = {
    idIframe: 'ems-form-iframe',
    idForm: 'ems-form',
    idMessage: 'ems-message'
};

export function defaultCheck()
{
    let elementIframe = document.getElementById(DEFAULT_CONFIG.idIframe);
    let elementForm = document.getElementById(DEFAULT_CONFIG.idForm);
    let elementMessage = document.getElementById(DEFAULT_CONFIG.idMessage);

    return null !== elementIframe && null !== elementForm && null !== elementMessage;
}

export class emsForm {
    constructor(options) {
        let config = Object.assign({}, DEFAULT_CONFIG, options);
        this.elementIframe = document.getElementById(config.idIframe);
        this.elementForm = document.getElementById(config.idForm);
        this.elementMessage = document.getElementById(config.idMessage);

        if (this.elementIframe !== null) {
            const url = new URL(this.elementIframe.getAttribute('src'));
            this.origin = url.origin;

            window.addEventListener( 'message', evt => this.onMessage(evt) );
        }
    }
    isValid() {
        return this.elementIframe !== null && this.elementForm !== null && this.elementMessage !== null;
    }
    init() {
        if (this.isValid()) {
            this.postMessage({'instruction': 'form'});
        }
    }
    insertForm(response) {
        let parser = new DOMParser;
        let dom = parser.parseFromString('<!doctype html><body>' + response, 'text/html');

        this.elementForm.innerHTML = dom.body.innerHTML;

        let form = this.elementForm.querySelector('form');
        form.addEventListener('submit', evt => this.onSubmitForm(evt));

        addValidation(form);
        disableCopyPaste(form);
        addDynamicFields(form, this);
    }
    static jsonParse(string) {
        try {
            return JSON.parse(string);
        } catch (e) {
            return false;
        }
    }
    onMessage(e) {
        if (e.origin !== this.origin) {
            return;
        }

        let data = emsForm.jsonParse(e.data);

        if (!data) {
            return;
        }

        switch (data.instruction) {
            case 'form':
            case 'validation-error':
                this.insertForm(data.response);
                this.difficulty = parseInt(data.difficulty);
                break;
            case 'submitted':
                this.elementMessage.innerHTML = data.response;
                break;
            case 'dynamic':
                replaceFormFields(data.response, Object.values(emsForm.jsonParse(data.dynamicFields)));
                addDynamicFields(this.elementForm.querySelector('form'), this);
                break;
            default:
               return;
        }
    }
    onSubmitForm(e) {
        e.preventDefault();

        let submits = e.target.getElementsByClassName('submit');
        Array.prototype.forEach.call(submits, function(submit) {
            submit.disabled = true;
        });

        let formData = new FormData(e.target);
        let data = {};
        formData.forEach(function(value, key){
            data[key] = value;
        });
        this.postMessage({'instruction': 'submit', 'form': data, 'token': this.createToken(data['form[_token]'])});
    }
    onDynamicFieldChange(data) {
        this.postMessage({'instruction': 'dynamic', 'data': data});
    }
    postMessage(msg) {
        this.elementIframe.contentWindow.postMessage(JSON.stringify( msg ), this.origin);
    }
    createToken(crsfToken) {
        if (0 === this.difficulty) {
            return false;
        }

        return generate({
            difficulty: parseInt(this.difficulty),
            data: crsfToken
        });
    }
}

