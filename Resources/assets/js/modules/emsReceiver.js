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
                let urlEncoded = [];
                for (let key in data.form) {
                    urlEncoded.push(encodeURI(key.concat('=').concat(data.form[key])));
                }

                xhr.open("POST", this.basePath+"/form/"+this.id+"/"+this.lang);
                xhr.setRequestHeader("Content-Type",  "application/x-www-form-urlencoded");

                if ('token' in data) {
                    let token = data.token;
                    xhr.setRequestHeader('x-hashcash', [token.hash, token.nonce, token.data].join('|'));
                }

                xhr.send(urlEncoded.join('&'));
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
}

export default emsReceiver;
