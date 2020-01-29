import {encodingHelper, securityHelper} from '../helpers';

const DEFAULT_CONFIG = {
    'id': false,
    'domains': [],
};

export class emsReceiver
{
    constructor(options)
    {
        let config = Object.assign({}, DEFAULT_CONFIG, options);

        this.domains = config.domains;
        this.id = config.id;
        this.lang = document.documentElement.lang;
        this.basePath = window.location.pathname.replace(/\/iframe\/.*/g, '');
        this.port2 = null;

        if (this.id === false) {
            return;
        }

        window.addEventListener("message", e =>
        {
            if(this.hasValidOrigin(e)) {
                this.initPort(e);
            }
        });
    }

    hasValidOrigin(messageEvent)
    {
        if(this.port2 === null) {
            return this.domains.includes(messageEvent.origin);
        }
        return true;
    }

    initPort(messageEvent)
    {
        if(this.port2 === null) {
            this.port2 = messageEvent.ports[0];
        }

        this.port2.onmessage = this.onMessage.bind(this);
    }

    onMessage(messageEvent)
    {
        let data = messageEvent.data;

        if (!data) {
            return;
        }

        let xhr = new XMLHttpRequest();
        xhr.addEventListener('load', evt => this.onResponse(evt, xhr));

        switch (data.instruction)
        {
            case 'form': {
                xhr.open('GET', `${this.basePath}/form/${this.id}/${this.lang}`);
                xhr.setRequestHeader('Content-Type',  'application/json');
                xhr.send();
                break;
            }

            case 'submit': {
                xhr.open('POST', `${this.basePath}/form/${this.id}/${this.lang}`);
                xhr.setRequestHeader('Content-Type',  'application/x-www-form-urlencoded');
                securityHelper.addHashCashHeader(data, xhr);
                xhr.send(encodingHelper.urlEncodeData(data.form));
                break;
            }

            case 'dynamic': {
                xhr.open('POST', `${this.basePath}/ajax/${this.id}/${this.lang}`);
                xhr.setRequestHeader('Content-Type',  'application/x-www-form-urlencoded');
                securityHelper.addHashCashHeader(data, xhr);
                xhr.send(encodingHelper.urlEncodeData(data.data));
                break;
            }

            default:
                return;
        }
    }

    onResponse(progressEvent, xhr)
    {
        if (xhr.status === 200) {
            this.port2.postMessage(xhr.responseText);
        }
    }
}
