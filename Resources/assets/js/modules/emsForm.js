import {addValidation, disableCopyPaste} from '../validation';
import {addDynamicFields, replaceFormFields} from '../dynamicFields';
import {encodingHelper, formHelper, securityHelper} from '../helpers';
import 'url-polyfill';
import 'formdata-polyfill'

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

    isValid()
    {
        return this.elementIframe !== null && this.elementForm !== null && this.elementMessage !== null;
    }

    init()
    {
        if (this.isValid()) {
            let message = {'instruction': 'form'};
            this.postMessage(message);
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

        let data = encodingHelper.jsonParse(e.data);

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
                replaceFormFields(data.response, Object.values(encodingHelper.jsonParse(data.dynamicFields)));
                addDynamicFields(this.elementForm.querySelector('form'), this);
                break;
            default:
               return;
        }
    }

    onSubmitForm(e)
    {
        e.preventDefault();

        formHelper.disablingSubmitButton(e.target);

        let data = formHelper.getObjectFromFormData(e.target);

        let message = {
            'instruction': 'submit',
            'form': data,
            'token': securityHelper.createToken(data['form[_token]'], this.difficulty)
        };

        this.postMessage(message);
    }

    onDynamicFieldChange(data)
    {
        let message = {
            'instruction': 'dynamic',
            'data': data
        };

        this.postMessage(message);
    }

    postMessage(message)
    {
        this.elementIframe.contentWindow.postMessage(JSON.stringify(message), this.origin);
    }
}
