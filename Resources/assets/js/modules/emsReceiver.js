const DEFAULT_CONFIG = {
    "id": false,
    "domains": [],
};

class emsReceiver {
    constructor(options) {
        let config = Object.assign({}, DEFAULT_CONFIG, options);
        this.domains = config.domains;
        this.id = config.id;
        this.lang = document.documentElement.lang;
        this.basePath = window.location.pathname.replace(/\/iframe\/.*/g, '');

        if (this.id !== false) {
            window.addEventListener("message", evt => this.onMessage(evt));
        }
    }
    static jsonParse(string) {
        try {
            return JSON.parse(string);
        } catch (e) {
            return false;
        }
    }
    onMessage(message) {
        if ( !this.domains.includes(message.origin) ) {
            return;
        }

        let data = emsReceiver.jsonParse(message.data);

        if (!data) {
            return;
        }

        let xhr = new XMLHttpRequest();
        xhr.addEventListener("load", evt => emsReceiver.onResponse(evt, xhr, message));

        switch (data.instruction) {
            case "form": {
                xhr.open("GET", this.basePath+"/form/"+this.id+'/'+this.lang);
                xhr.setRequestHeader("Content-Type",  "application/json");
                xhr.send();
                break;
            }
            case "submit": {
                xhr.open("POST", this.basePath+"/form/"+this.id+"/"+this.lang);
                xhr.setRequestHeader("Content-Type",  "application/x-www-form-urlencoded");
                emsReceiver.addHashCashHeader(data, xhr);
                xhr.send(emsReceiver.urlEncodeData(data.form));
                break;
            }
            case "dynamic": {
                xhr.open("POST", this.basePath+"/ajax/"+this.id+"/"+this.lang);
                xhr.setRequestHeader("Content-Type",  "application/x-www-form-urlencoded");
                emsReceiver.addHashCashHeader(data, xhr);
                xhr.send(emsReceiver.urlEncodeData(data.data));
                break;
            }
            default:
                return;
        }
    }
    static onResponse(evt, xhr, message) {
        if (xhr.status === 200) {
            message.source.postMessage(xhr.responseText, message.origin);
        }
    }
    static urlEncodeData(data) {
        let urlEncoded = [];
        for (let key in data) {
            urlEncoded.push(encodeURI(key.concat('=').concat(data[key])));
        }
        return urlEncoded.join('&');
    }
    static addHashCashHeader(data, xhr) {
        if ('token' in data) {
            let token = data.token;
            xhr.setRequestHeader('x-hashcash', [token.hash, token.nonce, token.data].join('|'));
        }
    }
}

export default emsReceiver;
