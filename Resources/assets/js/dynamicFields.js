import {addDynamicChoiceSelect} from "./dynamicFields/dynamicChoiceSelect";

export function addDynamicFields(form, ajaxUrl)
{
    Array.from(form.getElementsByClassName("dynamic-choice-select")).forEach(function(item) {
        addDynamicChoiceSelect(item, ajaxUrl);
    });
}

window.dynamicFields = function (form, ajaxUrl) {
    addDynamicFields(form, ajaxUrl);
};
