import {addValidation, disableCopyPaste} from "../validation";
import {addDynamicFields, replaceFormFields} from "../dynamicFields";
import helpers from '../helpers/emsForm';
import 'url-polyfill';

export const DEFAULT_CONFIG = {
    idIframe: 'ems-form-iframe',
    idForm: 'ems-form',
    idMessage: 'ems-message',
    onLoad: null
};

export function defaultCheck() {
    let elementIframe = document.getElementById(DEFAULT_CONFIG.idIframe);
    let elementForm = document.getElementById(DEFAULT_CONFIG.idForm);
    let elementMessage = document.getElementById(DEFAULT_CONFIG.idMessage);

    return null !== elementIframe && null !== elementForm && null !== elementMessage;
}

export class emsForm
{
    constructor(options)
    {
        let config = Object.assign({}, DEFAULT_CONFIG, options);
        this.elementIframe = document.getElementById(config.idIframe);
        this.elementForm = document.getElementById(config.idForm);
        this.elementMessage = document.getElementById(config.idMessage);
        this.onLoad = config.onLoad;

        if (this.elementIframe !== null) {
            const url = new URL(this.elementIframe.getAttribute('src'));
            this.origin = url.origin;

            window.addEventListener( 'message', evt => this.onMessage(evt) );
        }
    }

    init()
    {
        if (defaultCheck()) {
            this.postMessage({'instruction': 'form'});
        }
    }

    insertForm(response)
    {
        let parser = new DOMParser;
        let dom = parser.parseFromString('<!doctype html><body>' + response, 'text/html');

        this.elementForm.innerHTML = dom.body.innerHTML;

        let form = this.elementForm.querySelector('form');
        form.addEventListener('submit', evt => this.onSubmitForm(evt));

        addValidation(form);
        disableCopyPaste(form);
        addDynamicFields(form, this);
        if (typeof this.onLoad === 'function') {
            this.onLoad();
        }
    }

    onMessage(e)
    {
        if (e.origin !== this.origin) {
            return;
        }

        let data = helpers.jsonParse(e.data);

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
                replaceFormFields(data.response, Object.values(helpers.jsonParse(data.dynamicFields)));
                addDynamicFields(this.elementForm.querySelector('form'), this);
                break;
            default:
               return;
        }
    }

    onSubmitForm(e)
    {
        e.preventDefault();

        helpers.disablingSubmitButton(e.target);

        let data = helpers.getArrayFromFormData(e.target);

        let msg = {
            'instruction': 'submit',
            'form': data,
            'token': helpers.createToken(data['form[_token]'], this.difficulty)
        };

        this.postMessage(msg);
    }

    onDynamicFieldChange(data)
    {
        let msg = {
            'instruction': 'dynamic',
            'data': data
        };

        this.postMessage(msg);
    }

    postMessage(msg)
    {
        this.elementIframe.contentWindow.postMessage(JSON.stringify( msg ), this.origin);
    }
}
