const DEFAULT_CONFIG = {
    "domains": []
};

class emsReceiver {
    constructor(options) {
        let config = Object.assign({}, DEFAULT_CONFIG, options);
        this.domains = config.domains;
        window.addEventListener("message", evt => this.onMessage(evt));
    }

    onMessage(message) {
        if ( !this.domains.includes(message.origin) ) {
            return;
        }

        let data = JSON.parse(message.data);

        switch (data.instruction) {
            case "form":
                this.ajax("/form/ap10-ap10_contact/instance", message);
                break;
            case "submit": {
                let urlEncoded = [];
                for (let key in data.form) {
                    urlEncoded.push(encodeURI(key.concat('=').concat(data.form[key])));
                }
                this.ajax("/form/ap10-ap10_contact/instance", message, urlEncoded.join('&'));
                break;
            }
            default:
                return;
        }
    }

    ajax(url, message, postData = null) {
        let xhr = new XMLHttpRequest();
        xhr.open(postData ? "POST" : "GET", url, true);
        xhr.setRequestHeader("Content-Type", postData ? "application/x-www-form-urlencoded" : "application/json");
        xhr.onload = function() {
            if (xhr.status === 200) {
                message.source.postMessage(xhr.responseText, message.origin);
            }
        };
        xhr.send(postData);
    }
}

export default emsReceiver;