import {setNissInszValidation} from "./validation/niss";
import {addMaxLengthCounter} from "./validation/maxLengthCounter";
import {setBelgiumPhoneValidation} from "./validation/belgiumPhone";
import {setRepeatedValidation} from "./validation/repeated";
import {preventCopyPaste} from "./validation/copyPaste";
import {setBelgiumCompanyNumberValidation} from "./validation/belgiumCompanyNumber";

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
    });
    Array.from(form.getElementsByClassName("repeated")).forEach(function(item) {
        setRepeatedValidation(item);
    });
    Array.from(form.getElementsByClassName("company_number")).forEach(function(item) {
        setBelgiumCompanyNumberValidation(item);
    });
}

export function disableCopyPaste(form)
{
    Array.from(form.getElementsByClassName("email_with_confirmation")).forEach(function(item) {
        preventCopyPaste(item);
    })
}

window.formValidation = function (form) {
    addValidation(form);
    disableCopyPaste(form);
};





