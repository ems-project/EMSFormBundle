import {setNissInszValidation} from "./validation/niss";
import {addMaxLengthCounter} from "./validation/maxLengthCounter";

export function addValidation(form)
{
    Array.from(form.getElementsByClassName("niss-insz")).forEach(function(item) {
        setNissInszValidation(item);
    });
    Array.from(form.getElementsByClassName("counter")).forEach(function(item) {
        addMaxLengthCounter(item);
    });
}

window.formValidation = function (form) {
    addValidation(form);
};





