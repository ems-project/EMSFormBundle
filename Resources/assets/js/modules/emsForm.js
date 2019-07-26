import {addValidation} from "../validation";

export const DEFAULT_CONFIG = {
    'iframe': 'ems-form-iframe',
    'form': 'ems-form'
};

export function defaultCheck()
{
    let iframe = document.getElementById(DEFAULT_CONFIG.iframe);
    let form = document.getElementById(DEFAULT_CONFIG.form);

    return null !== iframe && null !== form;
}

export class emsForm {
    constructor(options) {
        let config = Object.assign({}, DEFAULT_CONFIG, options);
        this.iframe = document.getElementById(config.iframe);
        this.form = document.getElementById(config.form);

        if (this.iframe !== null) {
            const url = new URL(this.iframe.getAttribute('src'));
            this.origin = url.origin;

            window.addEventListener( 'message', evt => this.onMessage(evt) );
        }
    }
    isValid() {
        return this.iframe !== null && this.form !== null;
    }
    init() {
        if (this.isValid()) {
            this.postMessage({'instruction': 'form'});
        }
    }
    insertForm(response) {
        if (null === this.form) {
            return;
        }

        let parser = new DOMParser;
        let dom = parser.parseFromString('<!doctype html><body>' + response.trim(), 'text/html');

        this.form.innerHTML = dom.body.textContent;

        let form = this.form.querySelector('form');
        form.addEventListener('submit', evt => this.onSubmitForm(evt));

        addValidation(form);
    }
    onMessage(e) {
        if (e.origin !== this.origin) {
            return;
        }

        let data = JSON.parse(e.data);

        switch (data.instruction) {
            case 'form':
            case 'validation-error':
                this.insertForm(data.response);
                break;
            case 'submitted':
                this.form.innerHTML = data.response;
                break;
            default:
               return;
        }
    }
    onSubmitForm(e) {
        e.preventDefault();

        let formData = new FormData(e.target);
        let data = {};
        formData.forEach(function(value, key){
            data[key] = value;
        });

        this.postMessage({'instruction': 'submit', 'form': data})
    }
    postMessage(msg)
    {
        this.iframe.contentWindow.postMessage(JSON.stringify( msg ), this.origin);
    }
}

