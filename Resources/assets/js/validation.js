import {setNissInszValidation} from "./validation/niss";
import {addMaxLengthCounter} from "./validation/maxLengthCounter";
import {setBelgiumPhoneValidation} from "./validation/belgiumPhone";

export function addValidation(form)
{
    Array.from(form.getElementsByClassName("niss-insz")).forEach(function(item) {
        setNissInszValidation(item);
    });
    Array.from(form.getElementsByClassName("counter")).forEach(function(item) {
        addMaxLengthCounter(item);
    });
    Array.from(form.getElementsByClassName("phone")).forEach(function(item) {
        setBelgiumPhoneValidation(item);
    })
}

window.formValidation = function (form) {
    addValidation(form);
};





