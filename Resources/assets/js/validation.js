import {setNissInszValidation} from "./validation/niss";

export function addValidation(form)
{
    Array.from(form.getElementsByClassName("niss-insz")).forEach(function(item) {
        setNissInszValidation(item);
    });
}

window.formValidation = function (form) {
    addValidation(form);
};





