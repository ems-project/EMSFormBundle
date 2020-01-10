import {generate} from "hashcash-token";
import 'formdata-polyfill'

export default class
{
    static jsonParse(string)
    {
        try {
            return JSON.parse(string);
        } catch (e) {
            return false;
        }
    }

    static onResponse(evt, xhr, message)
    {
        if (xhr.status === 200) {
            message.source.postMessage(xhr.responseText, message.origin);
        }
    }

    static urlEncodeData(data)
    {
        let urlEncoded = [];
        for (let key in data) {
            urlEncoded.push(encodeURI(key.concat('=').concat(data[key])));
        }
        return urlEncoded.join('&');
    }

    static addHashCashHeader(data, xhr)
    {
        if ('token' in data) {
            let token = data.token;
            xhr.setRequestHeader('x-hashcash', [token.hash, token.nonce, token.data].join('|'));
        }
    }

    static createToken(crsfToken, difficulty)
    {
        if (0 === difficulty) {
            return false;
        }

        return generate({
            difficulty: parseInt(difficulty),
            data: crsfToken
        });
    }

    static getArrayFromFormData(form)
    {
        let data = {};
        let formData = new FormData(form);
        formData.forEach(function(value, key){
            data[key] = value;
        });

        return data;
    }

    static disablingSubmitButton(form)
    {
        let submits = form.getElementsByClassName('submit');
        Array.prototype.forEach.call(submits, function(submit) {
            submit.disabled = true;
        });
    }
}
