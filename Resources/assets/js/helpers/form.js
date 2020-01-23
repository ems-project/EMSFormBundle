import 'formdata-polyfill'

export default class
{
    static getObjectFromFormData(form)
    {
        let data = {};
        let formData = new FormData(form);
        formData.forEach(function(value, key){
            data[key] = value;
        });

        console.log('port1', data);

        return data;
    }

    static getFormDataFromObject(obj)
    {
        console.log('port2', obj);
        let formData = new FormData();
        Object.entries(obj).forEach(([key,value])=>{
            let filename = value.name;
            if (filename !== undefined) {
                formData.append(key, value, filename);
                return;
            }

            formData.append(key, value);
        });

        return formData;
    }

    static disablingSubmitButton(form)
    {
        let submits = form.getElementsByClassName('submit');
        Array.prototype.forEach.call(submits, function(submit) {
            submit.disabled = true;
        });
    }
}
