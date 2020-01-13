import {Encoding, Form, Security} from '../helpers';

const DEFAULT_CONFIG = {
    "id": false,
    "domains": [],
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

        if (this.id !== false) {
            window.addEventListener("message", evt => this.onMessage(evt));
        }
    }

    onMessage(message)
    {
        if ( !this.domains.includes(message.origin) ) {
            return;
        }

        let data = ((typeof message.data === 'string' || message.data instanceof String)) ? Encoding.jsonParse(message.data) : message.data;

        if (!data) {
            return;
        }

        let xhr = new XMLHttpRequest();
        xhr.addEventListener("load", evt => this.onResponse(evt, xhr, message));

        switch (data.instruction) {
            case "form": {
                xhr.open("GET", this.basePath + '/form/' + this.id + '/' + this.lang);
                xhr.setRequestHeader("Content-Type",  "application/json");
                xhr.send();
                break;
            }

            case "submit-without-file": {
                xhr.open("POST", this.basePath + '/form/' + this.id + '/' + this.lang);
                xhr.setRequestHeader("Content-Type",  "application/x-www-form-urlencoded");
                Security.addHashCashHeader(data, xhr);
                xhr.send(Encoding.urlEncodeData(data.form));
                break;
            }

            case "submit": {
                let url = this.basePath + '/form/' + this.id + '/' + this.lang;
                let dataForm = Form.getFormDataFromObject(data.form);

                xhr.open("POST", url, true);
                Security.addHashCashHeader(data, xhr);
                xhr.send(dataForm);
                break;
            }

            case "dynamic": {
                xhr.open("POST", this.basePath + '/ajax/' + this.id + '/' + this.lang);
                xhr.setRequestHeader("Content-Type",  "application/x-www-form-urlencoded");
                Security.addHashCashHeader(data, xhr);
                xhr.send(Encoding.urlEncodeData(data.data));
                break;
            }

            default:
                return;
        }
    }

    onResponse(evt, xhr, message)
    {
        console.log('onResponse', xhr.responseText, message.origin)
        if (xhr.status === 200) {
            message.source.postMessage(xhr.responseText, message.origin);
        }
    }

}
