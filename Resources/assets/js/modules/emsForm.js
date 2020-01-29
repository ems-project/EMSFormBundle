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
        this.channel = new MessageChannel();

        if (this.elementIframe !== null) {
            const url = new URL(this.elementIframe.getAttribute('src'));
            this.origin = url.origin;

            this.channel.port1.onmessage = this.onMessage.bind(this);
            this.elementIframe.contentWindow.postMessage('port2ChannelInit', this.origin, [this.channel.port2]);
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
            this.channel.port1.postMessage(message);
        }
    }

    insertForm(response)
    {
        let parser = new DOMParser;
        let dom = parser.parseFromString('<!doctype html><body>' + response, 'text/html');

        this.elementForm.innerHTML = dom.body.innerHTML;

        let form = this.elementForm.querySelector('form');
        form.addEventListener('submit', e => this.onSubmitForm(e));

        addValidation(form);
        disableCopyPaste(form);
        addDynamicFields(form, this);
        if (typeof this.onLoad === 'function') {
            this.onLoad();
        }
    }

    onMessage(messageEvent)
    {
        let data = encodingHelper.jsonParse(messageEvent.data);

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

    onSubmitForm(event)
    {
        event.preventDefault();

        formHelper.disablingSubmitButton(event.target);

        let data = formHelper.getObjectFromFormData(event.target);

        let message = {
            'instruction': 'submit',
            'form': data,
            'token': securityHelper.createToken(data['form[_token]'], this.difficulty)
        };

        this.channel.port1.postMessage(message);
    }

    onDynamicFieldChange(data)
    {
        let message = {
            'instruction': 'dynamic',
            'data': data
        };

        this.channel.port1.postMessage(message);
    }
}
