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

        return data;
    }

    static getFormDataFromObject(obj)
    {
        let formData = new FormData();
        Object.entries(obj).forEach(([key,value])=>{
            if(value.name !== undefined) {
                let filename = value.name;
                formData.set(key, value, filename);
                return;
            }

            formData.set(key, value);
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
