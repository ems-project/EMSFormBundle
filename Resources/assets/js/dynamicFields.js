import {addDynamicChoiceSelect} from "./dynamicFields/dynamicChoiceSelect";

export function addDynamicFields(form)
{
    Array.from(form.getElementsByClassName("dynamic-choice-select")).forEach(function(item) {
        addDynamicChoiceSelect(item);
    });
}

window.dynamicFields = function (form) {
    addDynamicFields(form);
};
